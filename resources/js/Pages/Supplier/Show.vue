<template>
  <div>
    <AppLayoutNoHeader title="Proveedores">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Proveedor</label>
          <Link
            :href="route('suppliers.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
          >
            <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <el-select
            v-model="selectedSupplier"
            clearable
            filterable
            placeholder="Buscar producto"
            no-data-text="No hay proveedores en el catálogo"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in suppliers.data"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Editar" placement="top">
              <Link :href="route('suppliers.edit', selectedSupplier)">
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
                <DropdownLink :href="route('suppliers.create')">
                  Crear nuevo proveedor
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
        {{ supplier.name }}
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
            Información general
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="tabs = 2"
            :class="
              tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
          >
            Datos bancarios
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="tabs = 3"
            :class="
              tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
          >
            Productos
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div
        v-if="tabs == 1"
        class="md:w-1/2 grid grid-cols-2 text-left p-4 md:ml-20"
      >
        <span class="text-gray-500">ID</span>
        <span>{{ currentSupplier?.id }}</span>

        <span class="col-span-2 mt-8 mb-2">Datos</span>

        <span class="text-gray-500 my-2">Nombre</span>
        <span>{{ currentSupplier?.name }}</span>
        <span class="text-gray-500 my-2">Dirección</span>
        <span>{{ currentSupplier?.address }}</span>
        <span class="text-gray-500 my-2">Código postal</span>
        <span>{{ currentSupplier?.post_code }}</span>
        <span class="text-gray-500 my-2">Teléfono</span>
        <span>{{ currentSupplier?.phone }}</span>
        <span class="text-gray-500 my-2">Agregado el</span>
        <span>{{ currentSupplier?.created_at }}</span>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------Sucursales starts 2 ------------- -->
      <div v-if="tabs == 2" class="lg:grid grid-cols-2 gap-8 md:mt-12 md:px-14">
            <SupplierBankCard :banks="currentSupplier?.banks" />
      </div>

      <!-- ------------- Sucursales ends 2 ------------- -->

      <!-- -------------Matriz starts 3 ------------- -->
      <div v-if="tabs == 3" class="p-7">
        <p class="text-secondary">Productos registrados</p>
        <div class="grid lg:grid-cols-3 md:grid-cols-2 mt-7 gap-10">
          <!-- <CompanyProductCard
            v-for="company_product in company_products"
            :key="company_product.id"
            :company_product="company_product"
          /> -->
        </div>
      </div>

      <!-- ------------- Matriz ends 3 ------------- -->

      <ConfirmationModal
        :show="showConfirmModal"
        @close="showConfirmModal = false"
      >
        <template #title> Eliminar proveedor </template>
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
import SupplierBankCard from "@/Components/MyComponents/SupplierBankCard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedSupplier: "",
      currentSupplier: null,
      tabs: 1,
      showConfirmModal: false,
    };
  },
  props: {
    supplier: Object,
    suppliers: Array,
    // company_products: Array, Aqui van los productos registrados que se le compran al proveedor
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    Link,
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    SupplierBankCard
  },
  methods: {
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("suppliers.destroy", this.currentSupplier?.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.suppliers.data.findIndex(
            (item) => item.id === this.currentSupplier.id
          );
          if (index !== -1) {
            this.suppliers.data.splice(index, 1);
            this.selectedSupplier = "";
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
    selectedSupplier(newVal) {
      this.currentSupplier = this.suppliers.data.find((item) => item.id == newVal);
    },
  },

  mounted() {
    this.selectedSupplier = this.supplier.id;
  },
};
</script>