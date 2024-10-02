<template>
  <div>
    <AllPageLoading v-if="loading" />
    <AppLayoutNoHeader title="Seguimiento de muestras -ver">
      <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Seguimiento de muestra</span>
        <Link
          :href="route('samples.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
        >
          <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>
      <div class="flex justify-between mt-5 mx-14">
        <div class="md:w-1/3 mr-2">
          <el-select
            @change="this.$inertia.get(route('samples.show', selectedSample));"
            v-model="selectedSample"
            clearable
            filterable
            placeholder="Buscar muestra"
            no-data-text="No hay muestras registradas"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in samples"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </div>
        <div class="flex items-center space-x-2">
          <el-tooltip
            v-if="$page.props.auth.user.permissions.includes('Editar muestra')"
            content="Editar"
            placement="top"
          >
            <Link :href="route('samples.edit', selectedSample)">
              <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9]">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                  </svg>
              </button>
            </Link>
          </el-tooltip>

          <el-tooltip
            v-if="
            !currentSample?.returned_at && currentSample?.will_back
            "
            content="Marcar como muestra devuelta por el cliente"
            placement="top"
          >
            <button
              @click="returnedSampleModal = true"
              class="rounded-lg bg-primary text-white p-2 text-sm"
            >
              Marcar como devuelta
            </button>
          </el-tooltip>

          <el-tooltip
            v-if="
              (currentSample?.status['label'] === 'Muestra devuelta' 
              || currentSample?.status['label'] === 'Enviado. Esperando respuesta'
              )
            "
            content="Marca la muestra como enviada de nuevo con modificaciones"
            placement="top"
          >
            <div>
              <el-popconfirm
                confirm-button-text="Si"
                cancel-button-text="No"
                icon-color="#0355B5"
                title="¿Continuar?"
                @confirm="resentSample"
              >
                <template #reference>
                  <button class="rounded-lg bg-secondary text-white p-2 text-sm">
                    Enviar m. modificada
                  </button>
                </template>
              </el-popconfirm>
            </div>
          </el-tooltip>

          <el-tooltip
            v-if="
              $page.props.auth.user.permissions.includes('Generar orden de venta en muestra')
              && (currentSample?.status['label'] === 'Muestra devuelta' 
              || currentSample?.status['label'] === 'Enviado. Esperando respuesta'
              || currentSample?.status['label'] === 'Muestra enviada de nuevo con modificación'
              )
            "
            content="Gener orden de venta si la venta fue cerrada"
            placement="top"
          >
            <div>
              <el-popconfirm
                confirm-button-text="Si"
                cancel-button-text="No"
                icon-color="#0355B5"
                title="¿Continuar?"
                @confirm="saleOrder"
              >
                <template #reference>
                  <button class="rounded-lg bg-green-500 text-white p-2 text-sm">
                    Venta cerrada
                  </button>
                </template>
              </el-popconfirm>
            </div>
          </el-tooltip>

          <el-tooltip
            v-if="
              $page.props.auth.user.permissions.includes('Generar orden de venta en muestra')
              && (currentSample?.status['label'] === 'Muestra devuelta' 
              || currentSample?.status['label'] === 'Enviado. Esperando respuesta'
              || currentSample?.status['label'] === 'Muestra enviada de nuevo con modificación'
              )
            "
            content="Si no cerraste la venta, finaliza seguimiento sin generar una orden"
            placement="top"
          >
            <div>
              <el-popconfirm
                confirm-button-text="Si"
                cancel-button-text="No"
                icon-color="#0355B5"
                title="¿Continuar?"
                @confirm="finishSample"
              >
                <template #reference>
                  <button class="rounded-lg bg-primary text-white p-2 text-sm">
                    Venta no cerrada
                  </button>
                </template>
              </el-popconfirm>
            </div>
          </el-tooltip>

          <Dropdown
            align="right"
            width="48"
            v-if="
              $page.props.auth.user.permissions.includes('Crear muestra') &&
              $page.props.auth.user.permissions.includes('Eliminar muestra')
            "
          >
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center justify-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink
                :href="route('samples.create')"
                v-if="
                  $page.props.auth.user.permissions.includes('Crear muestra')
                "
              >
                Resgistrar nueva muestra
              </DropdownLink>
              <DropdownLink
                @click="showConfirmModal = true"
                as="button"
                v-if="
                  $page.props.auth.user.permissions.includes('Eliminar muestra')
                "
              >
                Eliminar
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>
      <div class="lg:grid grid-cols-3 mt-12 border-b-2">
        <div class="px-14">
          <h2 class="text-xl font-bold text-center mb-6">
            {{ currentSample?.name }}
          </h2>
          <figure
            @mouseover="showOverlay"
            @mouseleave="hideOverlay"
            class="w-full h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center"
          >
            <img v-if="currentSample?.catalog_product" class="object-contain h-60" :src="currentSample?.catalog_product?.media[currentImage]?.original_url" alt="">
            <img v-else class="object-contain h-60" :src="currentSample?.media[currentImage]?.original_url" alt="">

            <div
              v-if="imageHovered"
              @click="
                openImage(
                  currentSample?.catalog_product?.media[currentImage]?.original_url
                )
              "
              class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in"
            >
              <i
                class="fa-solid fa-magnifying-glass-plus text-white text-4xl"
              ></i>
            </div>
          </figure>
            <div v-if="sample.data.media?.length > 1" class="mt-3 flex items-center justify-center space-x-3">
              <i @click="currentImage = index" v-for="(image, index) in sample.data.media?.length" :key="index" 
                :class="index == currentImage ? 'text-black' : 'text-gray-300'" 
                class="fa-solid fa-circle text-xs cursor-pointer"></i>
            </div>

          <!-- ----------------------progress bar---------------------- -->
          <el-tooltip
            content="Progreso para dar seguimiento a la muestra"
            placement="top"
          >
            <div class="mt-14 ml-6 text-sm">
              <p class="text-secondary text-center text-xs mb-2">{{ currentSample?.status['description'] }}</p>

              <div class="mb-5 border border-gray1 rounded-full">
                <div :class="currentSample?.status['progress'] + ' ' + currentSample?.status['bg-color']" class="h-[12px] rounded-full"></div>
              </div>
            </div>
          </el-tooltip>
        </div>
        <!-- ------------------------------------------------------------------- -->

        <!-- ------------------------Information panel tabs--------------------- -->
        <div class="col-span-2 border-2">
          <div class="border-b-2 px-7 py-3 flex">
            <p
              @click="tabs = 1"
              :class="
                tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base leading-none"
            >
              Seguimiento de muestra
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p
              @click="tabs = 2"
              :class="
                tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
            >
              Información de producto
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p
              @click="tabs = 3"
              :class="
                tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
            >
              Cliente
            </p>
          </div>

          <!-- ---------------------- SEGUIMIENTO DE MUETSRA (1)------------------- -->
          <div v-if="tabs == 1" class="px-7 py-7 text-sm">
            <div class="flex space-x-2 mb-6">
              <p class="w-1/3 text-[#9A9A9A]">Muestra enviada el</p>
              <p>{{ currentSample?.sent_at }}</p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Folio de muestra</p>
              <p>{{ currentSample?.folio }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Nombre de la muestra</p>
              <p>{{ currentSample?.name }}</p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Cantidad de muestras enviada</p>
              <p>
                {{ currentSample?.quantity }}
                {{ currentSample?.catalog_product?.measure_unit ?? 'Unidades' }}
              </p>
            </div>
            <div v-if="currentSample?.devolution_date" class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Fecha esperada de devolución</p>
              <p class="bg-red-200 px-3">{{ currentSample?.devolution_date }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Devuelta el</p>
              <p :class="currentSample?.returned_at ? 'bg-green-200 px-3' : ''">{{ currentSample?.returned_at ?? "--" }}</p>
            </div>
            <div v-if="currentSample?.sale_order_at" class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Orden generada el</p>
              <p class="text-[#4FC03D]">
                {{ currentSample?.sale_order_at ?? "--" }}
              </p>
            </div>
            <div v-if="currentSample?.denied_at" class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Venta rechazada el</p>
              <p class="text-primary">
                {{ currentSample?.denied_at ?? "--" }}
              </p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Producto(s)</p>
              <p>
                <span v-for="product in currentSample?.products" :key="product"
                  >{{ product ?? "--" }},
                </span>
              </p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Comentarios/notas de seguimiento</p>
              <p class="bg-yellow-200 px-2">
                {{ currentSample?.comments ?? "--" }}
              </p>
            </div>
            <div class="flex space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Estatus</p>
              <p
                :class="
                  currentSample?.status['text-color'] +
                  ' ' +
                  currentSample?.status['border-color']
                "
                class="border rounded-full px-2"
              >
                {{ currentSample?.status.label }}
              </p>
            </div>
          </div>

          <!-- -------------------------- INFORMACION DE PRODUCTO (2)---------------------------- -->
          <div v-if="tabs == 2" class="px-7 py-7 text-sm">
            <div v-if="currentSample?.catalog_product?.id">
              <div class="flex mb-2 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
                <p>{{ currentSample?.catalog_product?.id }}</p>
              </div>
              <div class="flex mb-6 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Características</p>
                <p>
                  {{
                    currentSample?.catalog_product?.features?.raw ??
                    "--"
                  }}
                </p>
              </div>
              <div class="flex mb-2 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
                <p>{{ currentSample?.catalog_product?.part_number }}</p>
              </div>
              <div class="flex mb-6 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
                <p>{{ currentSample?.catalog_product?.measure_unit }}</p>
              </div>
              <div class="flex mb-6 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Costo de producción</p>
                <p class="text-[#4FC03D]">
                  {{ currentSample?.catalog_product?.cost.number_format }}
                </p>
              </div>
              <div class="flex mb-2 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">
                  Cantidad miníma permitida en almacén
                </p>
                <p>
                  {{ currentSample?.catalog_product?.min_quantity }}
                  {{ currentSample?.catalog_product?.measure_unit }}
                </p>
              </div>
              <div class="flex space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">
                  Cantidad máxima permitida en almacén
                </p>
                <p>
                  {{ currentSample?.catalog_product?.max_quantity }}
                  {{ currentSample?.catalog_product?.measure_unit }}
                </p>
              </div>
            </div>
            <p class="text-center text-sm text-gray-500" v-else> El producto de muestra no está registrado en catálogo de productos</p>
          </div>

          <!-- ------------------------CLIENTE (3)-------------------------->
          <div v-if="tabs == 3" class="px-7 py-7 text-sm">
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">ID del cliente</p>
              <p>{{ currentSample?.company_branch?.id }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Nombre</p>
              <p>
                {{ currentSample?.company_branch?.name }}
              </p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Dirección</p>
              <p>{{ currentSample?.company_branch?.address }}</p>
            </div>
            <div class="flex mb-8 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">C.P</p>
              <p>{{ currentSample?.company_branch?.post_code }}</p>
            </div>

            <p class="text-secondary mb-2">Contacto</p>

            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Nombre</p>
              <p>{{ currentSample?.contact?.name ?? "--" }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Correo</p>
              <p>{{ currentSample?.contact?.email ?? "--" }}</p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Teléfono</p>
              <p>{{ currentSample?.contact?.phone ?? "--" }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Cargo</p>
              <p>{{ currentSample?.contact?.charge ?? "--" }}</p>
            </div>
          </div>
        </div>
      </div>
      <ConfirmationModal
        :show="showConfirmModal"
        @close="showConfirmModal = false"
      >
        <template #title> Eliminar muestra </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div class="">
            <CancelButton @click="showConfirmModal = false" class="mr-2"
              >Cancelar</CancelButton
            >
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>

      <!-- -------------- Modal starts----------------------- -->
      <Modal :show="returnedSampleModal" @close="returnedSampleModal = false">
        <form @submit.prevent="returnedSample()">
          <div class="p-3 relative">
            <i
              @click="
                returnedSampleModal = false;
                form.reset();
              "
              class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"
            ></i>
            <i
              v-if="!helpDialog"
              @click="helpDialog = true"
              class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-3 top-3 cursor-pointer"
            ></i>

            <p class="font-bold text-center my-3">
              Marcar como muestra devuelta por el cliente
            </p>

            <div class="flex ml-3 px-9">
              <el-tooltip
                content="Comentarios de retroalinmentación"
                placement="top"
              >
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
                >
                  <i class="fa-solid fa-grip-lines"></i>
                </span>
              </el-tooltip>
              <textarea
                v-model="form.comments"
                class="textarea w-full"
                autocomplete="off"
                placeholder="Comentarios de retroalinmentación"
              ></textarea>
              <InputError :message="form.errors.comments" />
            </div>
            <div
              v-if="helpDialog"
              class="border border-[#0355B5] rounded-lg px-6 py-2 mt-5 mx-7 relative"
            >
              <i
                class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"
              ></i>
              <i
                @click="helpDialog = false"
                class="fa-solid fa-xmark cursor-pointer w-3 h-3 rounded-full text-secondary flex items-center justify-center absolute right-3 top-3 text-xs"
              ></i>
              <p class="text-secondary text-sm">
                Escribir un comentario de retroalimentación del cliente, como
                correcciones del producto o fecha esperada de generación de
                orden de compra por parte del cliente.
              </p>
            </div>

            <div class="flex justify-start space-x-3 pt-5 pb-1">
              <PrimaryButton>Enviar</PrimaryButton>
              <CancelButton
                @click="
                  returnedSampleModal = false;
                  form.reset();
                "
                >Cancelar</CancelButton
              >
            </div>
          </div>
        </form>
      </Modal>
      <!-- --------------------------- Modal ends ------------------------------------ -->
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
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from "axios";

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
      helpDialog: true,
      tabs: 1,
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
    Modal,
    Link,
    AllPageLoading  ,
  },
  props: {
    catalog_product: Object,
    catalog_products: Object,
    samples: Array,
    sample: Object,
  },
  methods: {
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

    finishSample() {
      this.$inertia.put(route('samples.finish-sample', this.sample.data.id));
    },
    resentSample() {
      this.$inertia.put(route('samples.resent-sample', this.sample.data.id));
    },

    async deleteItem() {
      try {
        const response = await axios.delete(
          route("samples.destroy", this.currentSample?.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.samples.data.findIndex(
            (item) => item.id === this.currentSample.id
          );
          if (index !== -1) {
            this.samples.data.splice(index, 1);
            this.selectedSample = "";
          }
        } else {
          this.$notify({
            title: "Algo salió mal",
            message: response.data.message,
            type: "error",
          });
        }
      } catch (err) {
        this.$notify({
          title: "Algo salió mal",
          message: err.message,
          type: "error",
        });
        console.log(err);
      } finally {
        this.showConfirmModal = false;
      }
    },
  },
  // watch: {
  //   selectedSample() {
  //     this.$inertia.get(route('samples.show', this.selectedSample));
  //   },
  // },
  mounted() {
    this.selectedSample = this.sample.data.id;
    this.currentSample = this.sample.data;
  },
};
</script>
