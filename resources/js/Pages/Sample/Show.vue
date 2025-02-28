<template>
  <div class="dark:text-white">
    <AllPageLoading v-if="loading" />
    <AppLayoutNoHeader title="Seguimiento de muestras -ver">
      <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Seguimiento de muestra</span>
        <Link :href="route('samples.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
        <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>

      <div class="flex justify-between mt-5 mx-14">
        <div class="md:w-1/3 mr-2">
          <el-select @change="this.$inertia.get(route('samples.show', selectedSample));" v-model="selectedSample"
            clearable filterable placeholder="Buscar muestra" no-data-text="No hay muestras registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in samples" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </div>

        <div class="flex items-center space-x-2">
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar muestra')" content="Editar"
            placement="top">
            <Link :href="route('samples.edit', selectedSample)">
            <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </button>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="
            !currentSample?.returned_at && currentSample?.will_back
          " content="Marcar como muestra devuelta por el cliente" placement="top">
            <button @click="returnedSampleModal = true" :disabled="!currentSample?.authorized_at"
              class="rounded-lg bg-primary text-white p-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
              Marcar como devuelta
            </button>
          </el-tooltip>
          <el-tooltip v-if="
            (currentSample?.status['label'] === 'Muestra devuelta'
              || currentSample?.status['label'] === 'Enviado. Esperando respuesta'
            )
          " content="Marca la muestra como enviada de nuevo con modificaciones" placement="top">
            <div>
              <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                @confirm="resentSample">
                <template #reference>
                  <button class="rounded-lg bg-secondary text-white p-2 text-sm">
                    Enviar m. modificada
                  </button>
                </template>
              </el-popconfirm>
            </div>
          </el-tooltip>
          <el-tooltip v-if="
            $page.props.auth.user.permissions.includes('Generar orden de venta en muestra')
            && (currentSample?.status['label'] === 'Muestra devuelta'
              || currentSample?.status['label'] === 'Enviado. Esperando respuesta'
              || currentSample?.status['label'] === 'Muestra enviada con modificaciones'
            )
          " content="Gener orden de venta si la venta fue cerrada" placement="top">
            <div>
              <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                @confirm="saleOrder">
                <template #reference>
                  <button class="rounded-lg bg-green-500 text-white p-2 text-sm">
                    Venta cerrada
                  </button>
                </template>
              </el-popconfirm>
            </div>
          </el-tooltip>
          <el-tooltip v-if="
            $page.props.auth.user.permissions.includes('Generar orden de venta en muestra')
            && (currentSample?.status['label'] === 'Muestra devuelta'
              || currentSample?.status['label'] === 'Enviado. Esperando respuesta'
              || currentSample?.status['label'] === 'Muestra enviada con modificaciones'
            )
          " content="Si no cerraste la venta, finaliza seguimiento sin generar una orden" placement="top">
            <div>
              <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                @confirm="finishSample">
                <template #reference>
                  <button class="rounded-lg bg-primary text-white p-2 text-sm">
                    Venta no cerrada
                  </button>
                </template>
              </el-popconfirm>
            </div>
          </el-tooltip>

          <Dropdown align="right" width="48" v-if="
            $page.props.auth.user.permissions.includes('Crear muestra') &&
            $page.props.auth.user.permissions.includes('Eliminar muestra')
          ">
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] dark:bg-[#202020] dark:text-white flex items-center justify-center text-sm">
                Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink :href="route('samples.create')" v-if="
                $page.props.auth.user.permissions.includes('Crear muestra')
              ">
                Resgistrar nueva muestra
              </DropdownLink>
              <DropdownLink @click="showConfirmModal = true" as="button" v-if="
                $page.props.auth.user.permissions.includes('Eliminar muestra')
              ">
                Eliminar
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>

      <p v-if="!currentSample?.authorized_at" class="text-red-600 font-bold my-2 py-1 text-center bg-red-200">
        MUESTRA NO AUTORIZADA
      </p>

      <el-steps :active="getCurrentStep" finish-status="success" class="w-2/3 mx-auto mt-8">
        <el-step title="Enviado. Esperando respuesta" />
        <el-step title="Muestra devuelta" />
        <el-step title="Muestra enviada con modificaciones" />
        <el-step v-if="!['Venta cerrada', 'Venta no concretada'].includes(sample.data.status.label)" title="Venta cerrada / Venta no concretada" />
        <el-step v-else-if="sample.data.status.label == 'Venta cerrada'" title="Venta cerrada" />
        <el-step v-else title="Venta no concretada" />
      </el-steps>

      <div class="lg:grid grid-cols-3 mt-12 border-b-2">
        <div class="px-14">
          <h2 class="text-xl font-bold text-center mb-6">
            {{ currentSample?.name }}
          </h2>
          <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
            class="w-full h-60 bg-[#D9D9D9] dark:bg-[#333333] rounded-lg relative flex items-center justify-center">
            <img v-if="currentSample?.catalog_product" class="object-contain h-60"
              :src="currentSample?.catalog_product?.media[currentImage]?.original_url" alt="">
            <img v-else class="object-contain h-60" :src="currentSample?.media[currentImage]?.original_url" alt="">

            <div v-if="imageHovered" @click="
              openImage(
                currentSample?.catalog_product?.media[currentImage]?.original_url
              )
              "
              class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
              <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
            </div>
          </figure>
          <div v-if="sample.data.media?.length > 1" class="mt-3 flex items-center justify-center space-x-3">
            <i @click="currentImage = index" v-for="(image, index) in sample.data.media?.length" :key="index"
              :class="index == currentImage ? 'text-black' : 'text-gray-300'"
              class="fa-solid fa-circle text-xs cursor-pointer"></i>
          </div>
          <!-- ----------------------progress bar---------------------- -->
          <el-tooltip content="Progreso para dar seguimiento a la muestra" placement="top">
            <div class="mt-14 ml-6 text-sm">
              <p class="text-secondary text-center text-xs mb-2">{{ currentSample?.status['description'] }}</p>

              <div class="mb-5 border border-gray1 rounded-full">
                <div :class="currentSample?.status['progress'] + ' ' + currentSample?.status['bg-color']"
                  class="h-[12px] rounded-full"></div>
              </div>
            </div>
          </el-tooltip>
        </div>
        <!-- ------------------------ tabs--------------------- -->
        <div class="col-span-2 border-2">
          <el-tabs v-if="currentSample" v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
            <el-tab-pane label="Seguimiento de muestra" name="1">
              <General :sample="currentSample" />
            </el-tab-pane>
            <el-tab-pane label="Información de producto" name="2">
              <ProductInfo :sample="currentSample" />
            </el-tab-pane>
            <el-tab-pane label="Cliente" name="3">
              <Client :sample="currentSample" />
            </el-tab-pane>
          </el-tabs>
        </div>
      </div>

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar muestra </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div class="">
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>

      <DialogModal :show="returnedSampleModal" @close="returnedSampleModal = false">
        <template #title>
          <h1 class="font-bold text-center">
            Marcar como muestra devuelta por el cliente
          </h1>
        </template>
        <template #content>
          <form @submit.prevent="returnedSample()">
            <div class="p-3 relative">
              <div>
                <InputLabel>
                  <div class="flex items-center space-x-2">
                    <span>Comentarios de retroalinmentación</span>
                    <el-tooltip placement="top">
                      <template #content>
                        <p>
                          Escribir un comentario de retroalimentación del cliente, como <br>
                          correcciones del producto o fecha esperada de generación de <br>
                          orden de compra por parte del cliente.
                        </p>
                      </template>
                      <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                      </div>
                    </el-tooltip>
                  </div>
                </InputLabel>
                <el-input v-model="form.comments" rows="3" maxlength="800" placeholder="..." show-word-limit
                  type="textarea" />
                <InputError :message="form.errors.comments" />
              </div>
            </div>
          </form>
        </template>
        <template #footer>
          <div class="flex justify-start space-x-1">
            <CancelButton @click="returnedSampleModal = false; form.reset()" :disabled="form.processing">
              Cancelar
            </CancelButton>
            <PrimaryButton @click="returnedSample" :disabled="form.processing">Enviar</PrimaryButton>
          </div>
        </template>
      </DialogModal>
    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import AllPageLoading from "@/Components/MyComponents/AllPageLoading.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import InputError from "@/Components/InputError.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Client from "./Tabs/Client.vue";
import General from "./Tabs/General.vue";
import ProductInfo from "./Tabs/ProductInfo.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      comments: null,
    });

    return {
      form,
      loading: false,
      selectedSample: "",
      currentImage: 0,
      currentSample: null,
      imageHovered: false,
      showConfirmModal: false,
      returnedSampleModal: false,
      activeTab: '1',
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    CancelButton,
    DropdownLink,
    Dropdown,
    ConfirmationModal,
    InputError,
    DialogModal,
    Link,
    AllPageLoading,
    General,
    ProductInfo,
    Client,
    InputLabel,
  },
  props: {
    catalog_product: Object,
    samples: Array,
    sample: Object,
  },
  computed: {
    getCurrentStep() {
      let statuses = [];
      if (this.sample.data.status.label == 'Venta no concretada') {
        statuses = [
          'Muestra no envida aún',
          'Enviado. Esperando respuesta',
          'Muestra devuelta',
          'Muestra enviada con modificaciones',
          'Venta no concretada',
          '',
        ];
      } else {
        statuses = [
          'Muestra no envida aún',
          'Enviado. Esperando respuesta',
          'Muestra devuelta',
          'Muestra enviada con modificaciones',
          'Venta cerrada',
          '',
        ];
      }

      return statuses.findIndex(i => i == this.sample.data.status['label']);
    },
  },
  methods: {
    handleClickInTab(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    setTabInUrl() {
      // Obtener la URL actual
      const currentURL = new URL(window.location.href);
      // Extraer el valor de 'currentTab' de los parámetros de búsqueda
      const currentTabFromURL = currentURL.searchParams.get('currentTab');

      if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
      }
    },
    showOverlay() {
      this.imageHovered = true;
    },
    hideOverlay() {
      this.imageHovered = false;
    },
    openImage(url) {
      window.open(url, "_blank");
    },
    returnedSample() {
      this.form.put(route("samples.returned", this.selectedSample), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Muestra devuelta por el cliente",
            type: "success",
          });

          this.form.reset();
          returnedSampleModal = false;
        },
      });
    },
    finishSample() {
      this.$inertia.put(route('samples.finish-sample', this.sample.data.id));
    },
    resentSample() {
      this.$inertia.put(route('samples.resent-sample', this.sample.data.id));
    },
    deleteItem() {
      this.form.delete(route("samples.destroy", this.currentSample?.id), {
        onError: (error) => {
          console.log(error);
          this.$notify({
            title: "Algo salió mal",
            message: '',
            type: "error",
          });
        },
      });
    },
    async saleOrder() {
      this.loading = true;
      try {
        const response = await axios.put(route('samples.sale-order', this.sample.data.id));

        if (response.status === 200) {
          this.$inertia.get(route('sales.create'), { sampleId: this.sample.data.id }); // manda el id al metodo de crear orden de venta
        }
      } catch (error) {
        console.log(error);
      }
    },
  },
  mounted() {
    this.selectedSample = this.sample.data.id;
    this.currentSample = this.sample.data;

    this.setTabInUrl();
  },
};
</script>
