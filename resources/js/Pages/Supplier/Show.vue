<template>
  <div>
    <AppLayoutNoHeader title="Proveedores">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Proveedor</label>
          <Link :href="route('suppliers.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="w-1/3">
            <el-select @change="$inertia.get(route('suppliers.show', selectedSupplier))" v-model="selectedSupplier" clearable filterable placeholder="Buscar producto"
              no-data-text="No hay proveedores en el catálogo" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in suppliers" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar proveedores')" content="Editar" placement="top">
              <Link :href="route('suppliers.edit', selectedSupplier)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>

            <Dropdown align="right" width="48" v-if="$page.props.auth.user.permissions.includes('Crear proveedores') && $page.props.auth.user.permissions.includes('Eliminar proveedores')">
              <template #trigger>
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Crear proveedores')" :href="route('suppliers.create')">
                  Crear nuevo proveedor
                </DropdownLink>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar proveedores')" @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ supplier.data.name }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 ">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Información general
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Datos Bancarios / Contactos
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="fetchSupplierItems(); tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Productos
          </p>
          <!-- <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Productos
          </p> -->
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:w-1/2 grid grid-cols-2 text-left p-4 md:ml-20 text-sm">
        <span class="text-gray-500">ID</span>
        <span>{{ supplier.data.id }}</span>

        <span class="col-span-2 mt-8 mb-2">Datos</span>

        <span class="text-gray-500 my-2">Nombre</span>
        <span>{{ supplier.data.name }}</span>
        <span class="text-gray-500 my-2">Nick name</span>
        <span>{{ supplier.data.nickname }}</span>
        <span class="text-gray-500 my-2">Dirección</span>
        <span>{{ supplier.data.address }}</span>
        <span class="text-gray-500 my-2">Código postal</span>
        <span>{{ supplier.data.post_code }}</span>
        <span class="text-gray-500 my-2">Teléfono</span>
        <span>{{ supplier.data.phone }}</span>
        <span class="text-gray-500 my-2">Agregado el</span>
        <span>{{ supplier.data.created_at }}</span>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------Sucursales starts 2 ------------- -->
      <section v-if="tabs == 2" class="lg:grid grid-cols-3 gap-x-5 md:mt-12 md:px-14 mb-6">
        <!-- <div class="grid grid-cols-2 gap-x-2 gap-y-1"> -->
          <h2 class="font-bold col-span-full mb-2">Cuentas bancarias</h2>
          <SupplierBankCard v-for="(bank, index) in supplier.data.banks" :key="index" :bank="bank" />
        <!-- </div> -->
        <!-- <div class="grid grid-cols-2 gap-x-2 gap-y-1"> -->
          <h2 class="font-bold col-span-full mb-2 mt-6">Contactos</h2>
          <SupplierContactCard v-for="(contact, index) in supplier.data.contacts" :key="index" :contact="contact" />
        <!-- </div> -->
      </section>

      <!-- ------------- Sucursales ends 2 ------------- -->

      <!-- -------------products starts 3 ------------- -->
      <div v-if="tabs == 3" class="p-7">
        <div v-if="!loading">
          <div v-if="rawMaterials?.length > 0" class="lg:grid grid-cols-4 mt-7 gap-5">
            <SupplierProductCard v-for="product in rawMaterials" :key="product" :product="product" />
          </div>
            <p v-else class="text-gray-500 text-center text-sm">No hay productos registrados a este proveedor</p>
        </div>
        <div v-else class="flex justify-center items-center pt-10">
          <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
        </div>
      </div>

      <!-- ------------- products ends 3 ------------- -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar proveedor </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div>
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
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
import SupplierContactCard from "@/Components/MyComponents/SupplierContactCard.vue";
import SupplierProductCard from "@/Components/MyComponents/SupplierProductCard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    return {
      selectedSupplier: "",
      // currentSupplier: null,
      tabs: 1,
      showConfirmModal: false,
      loading: false,
      rawMaterials: [],
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
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    SupplierBankCard,
    SupplierProductCard,
    SupplierContactCard,
    Link
  },
  methods: {
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("suppliers.destroy", this.supplier.data.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.suppliers.findIndex(
            (item) => item.id === this.supplier.data.id
          );
          if (index !== -1) {
            this.suppliers.splice(index, 1);
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
        this.$inertia.get(route('suppliers.index'));
      }
    },
    async fetchSupplierItems() {
      if ( this.rawMaterials.length <= 0) { //solo hace la peticion si no se han cargado
          this.loading = true;
            try {
              const response = await axios.get(route('raw-materials.fetch-supplier-items', {
                raw_materials_ids: this.supplier.data.raw_materials_id.join(',')
              }));
              
              if (response.status === 200) {
                console.log(response.data.items);
                  this.rawMaterials = response.data.items;
              }
          } catch (error) {
            console.log(error);
          } finally {
            this.loading = false;
          }
        }
    }
  },
  mounted() {
    this.selectedSupplier = this.supplier.data.id;
  },
};
</script>