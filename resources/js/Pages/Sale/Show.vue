<template>
  <div>
    <AppLayoutNoHeader title="Órdenes de venta">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de venta</label>
          <Link
            :href="route('sales.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
          >
            <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        {{saleSelected}}
        {{currentSale}}
        <div class="flex justify-between">
          <el-select
            v-model="saleSelected"
            clearable
            filterable
            placeholder="Buscar órden de venta"
            no-data-text="No hay órdenes en el catálogo"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in sales.data"
              :key="item.id"
              :label="item.folio"
              :value="item.id"
            />
          </el-select>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Editar" placement="top">
              <Link :href="route('sales.edit', saleSelected)">
                <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                  <i class="fa-solid fa-pen text-sm"></i>
                </button>
              </Link>
            </el-tooltip>

            <Dropdown align="right" width="48">
              <template #trigger>
                <button
                  class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm"
                >
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('sales.create')">
                  Crear nueva órden
                </DropdownLink>
                <DropdownLink :href="route('sales.create')">
                  Certificado de calidad
                </DropdownLink>
                <DropdownLink :href="route('sales.create')">
                  Formato órden de ventas
                </DropdownLink>
                <DropdownLink :href="route('sales.create')">
                  Paquetes
                </DropdownLink>
                <DropdownLink @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ saleSelected.folio }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div
        class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 "
      >
        <div class="flex">
          <p
            @click="tabs = 1"
            :class="
              tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base"
          >
            Datos de la órden
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="tabs = 2"
            :class="
              tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
          >
            Productos
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc]">
        <div
            class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center"
        >
        <p class="text-secondary col-span-2 mb-2">Logística</p>

            <span class="text-gray-500">Paquetería</span>
            <span>{{ currentSale?.shipping_company }}</span>
            <span class="text-gray-500">Guía</span>
            <span>{{ currentSale?.traking_guide }}</span>
            <span class="text-gray-500">Costo de envío</span>
            <span>{{ currentSale?.freight_cost }}</span>

        <p class="text-secondary col-span-2 mb-2">Datos de la órden</p>

            <span class="text-gray-500">ID</span>
            <span>{{ currentSale?.id }}</span>
            <span class="text-gray-500 my-2">Solicitada por</span>
            <span>{{ currentSale?.user.name }}</span>
            <span class="text-gray-500 my-2">Solicitada el</span>
            <span>{{ currentSale?.supplier.name }}</span>
            <span class="text-gray-500 my-2">Medio de petición</span>
            <span>{{ currentSale?.created_at }}</span>
            <span class="text-gray-500 my-2">Prioridad</span>
            <span :class="currentSale?.status == 'Pendiente' ? 'text-amber-600' : 'text-green-600'">{{ currentSale?.status }}</span>
            <span class="text-gray-500 my-2">Operadores</span>
            <span>{{ currentSale?.authorized_at }}</span>
            <span class="text-gray-500 my-2">OCE</span>
            <span>{{ currentSale?.authorized_user_name }}</span>
            <span class="text-gray-500 my-2">Factira</span>
            <span>{{ currentSale?.expected_delivery_date }}</span>
            <span class="text-gray-500 my-2">Estatus</span>
            <span>{{ currentSale?.emited_at }}</span>
            <span class="text-gray-500 my-2">Notas</span>
        </div>
        <div
            class="grid grid-cols-2 text-left p-4 md:ml-10 items-center"
        >
            <p class="text-secondary col-span-2 mb-2">Datos del proveedor</p>

            <span class="text-gray-500">ID</span>
            <span>{{ currentSale?.supplier.id }}</span>
            <span class="text-gray-500 my-2">Nombre</span>
            <span>{{ currentSale?.supplier.name }}</span>
            <span class="text-gray-500 my-2">Dirección</span>
            <span>{{ currentSale?.supplier.address }}</span>
            <span class="text-gray-500 my-2">Código postal</span>
            <span>{{ currentSale?.supplier.post_code }}</span>
            <span class="text-gray-500 my-2">Teléfono</span>
            <span>{{ currentSale?.supplier.phone }}</span>

            <p class="text-secondary col-span-2 mt-7">Datos Bancarios</p>

            <span class="text-gray-500 my-2">Nombre del beneficiario</span>
            <span>{{ currentSale?.bank_information.beneficiary_name }}</span>
            <span class="text-gray-500 my-2">Número de cuenta</span>
            <span>{{ currentSale?.bank_information.accountNumber }}</span>
            <span class="text-gray-500 my-2">Clabe</span>
            <span>{{ currentSale?.bank_information.clabe }}</span>
            <span class="text-gray-500 my-2">Banco</span>
            <span>{{ currentSale?.bank_information.bank_name }}</span>

        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 products starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <p class="text-secondary">Productos Ordenados</p>
        <div class="grid lg:grid-cols-3 md:grid-cols-2 mt-7 gap-10">
          <RawMaterialCard
            v-for="product in currentSale?.products"
            :key="product.id"
            :raw_material="product"
          />
        </div>
      </div>

      <!-- ------------- tab 2 products ends ------------ -->

      <ConfirmationModal
        :show="showConfirmModal"
        @close="showConfirmModal = false"
      >
        <template #title> Eliminar Órden de venta </template>
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
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import RawMaterialCard from "@/Components/MyComponents/RawMaterialCard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      saleSelected: "",
      currentSale: null,
      tabs: 1,
      showConfirmModal: false,
    };
  },
  props: {
    sale: Object,
    sales: Array,
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    Link,
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    RawMaterialCard
  },
  methods: {
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("sales.destroy", this.currentSale?.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.sales.data.findIndex(
            (item) => item.id === this.currentSale.id
          );
          if (index !== -1) {
            this.sales.data.splice(index, 1);
            this.saleSelected = "";
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
    selectedSale(newVal) {
      this.currentSale = this.sales.data.find((item) => item.id == newVal);
    },
  },

mounted() {
    this.saleSelected = this.sale.id;
  },
};
</script>