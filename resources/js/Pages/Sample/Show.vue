<template>
  <div>
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
            v-model="selectedSample"
            clearable
            filterable
            placeholder="Buscar muestra"
            no-data-text="No hay muestras registradas"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in samples.data"
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
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
            </Link>
          </el-tooltip>

          
              <el-tooltip
                v-if="
                  $page.props.auth.user.permissions.includes(
                    'Editar muestra'
                  ) && !currentSample?.returned_at && currentSample
                "
                content="Marcar como muestra devuelta por el cliente"
                placement="top"
              >
              <el-popconfirm
            v-if="
              $page.props.auth.user.permissions.includes('Muestra devuelta')
            "
            confirm-button-text="Si"
            cancel-button-text="No"
            icon-color="#FF0000"
            title="¿Continuar?"
            @confirm="returnedSample"
          >
            <template #reference>
                <button class="rounded-lg bg-primary text-white p-2 text-sm">
                  Muestra devuelta
                </button>
            </template>
          </el-popconfirm>
              </el-tooltip>

          <el-tooltip
            v-if="
              $page.props.auth.user.permissions.includes('Generar orden de venta en muestra') &&
              currentSample?.returned_at &&
              !currentSample?.sale_order_at
            "
            content="Marcar como muestra devuelta por el cliente"
            placement="top"
          >
            <button class="rounded-lg bg-primary text-white p-2 text-sm">
              Generar orden de venta
            </button>
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
              <button
                class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm"
              >
                Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
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
            <el-image
              style="height: 100%"
              :src="currentSample?.catalog_product?.media[0]?.original_url"
              fit="fit"
            >
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image>
            <div
              v-if="imageHovered"
              @click="
                openImage(
                  currentSample?.catalog_product?.media[0]?.original_url
                )
              "
              class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in"
            >
              <i
                class="fa-solid fa-magnifying-glass-plus text-white text-4xl"
              ></i>
            </div>
          </figure>

          <!-- ----------------------progress bar---------------------- -->
          <el-tooltip v-if="currentSample" content="Progreso para cerrar venta" placement="top">
            <div class="mt-8 ml-6 text-sm">
              <p v-if="currentSample?.status['label'] == 'Orden generada. Venta exitosa'" class="text-secondary text-center text-xs mb-1">¡Venta cerrada!</p>
              <div class="mb-5 border-2 border-[#b3b3b3] rounded-full">
                <div
                  v-if="currentSample?.status['label'] == 'Enviado. Esperando respuesta'"
                  class="h-[10px] bg-primary rounded-full w-1/3"></div>
                <div
                  v-else-if="currentSample?.status['label'] == 'Muestra devuelta'"
                  class="h-[10px] bg-primary rounded-full w-2/3"></div>
                <div
                  v-else
                  class="h-[10px] bg-green-600 rounded-full w-full"></div>
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
              <p class="w-1/3 text-[#9A9A9A]">ID de muestra</p>
              <p>{{ currentSample?.id }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Nombre de la muestra</p>
              <p>{{ currentSample?.name }}</p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Cantidad de muestras enviada</p>
              <p>
                {{ currentSample?.quantity }}
                {{ currentSample?.catalog_product?.measure_unit }}
              </p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Devuelta el</p>
              <p>{{ currentSample?.returned_at ?? "--" }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Orden generada el</p>
              <p class="text-[#4FC03D]">
                {{ currentSample?.sale_order_at ?? "--" }}
              </p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Comentarios/notas</p>
              <p>
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
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
              <p>{{ currentSample?.catalog_product?.id }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Características</p>
              <p>
                {{ currentSample?.catalog_product?.features.raw.join(", ") }}
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
    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedSample: "",
      currentSample: null,
      imageHovered: false,
      showConfirmModal: false,
      tabs: 1,
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    CancelButton,
    Link,
    DropdownLink,
    Dropdown,
    ConfirmationModal,
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
    returnedSample(){
      this.$inertia.put(route('samples.returned',this.selectedSample));
      this.$notify({
            title: "Éxito",
            message: 'Muestra devuelta por el cliente',
            type: "success",
          });
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
  watch: {
    selectedSample(newVal) {
      this.currentSample = this.samples.data.find((item) => item.id == newVal);
    },
  },
  mounted() {
    this.selectedSample = this.sample.id;
  },
};
</script>
