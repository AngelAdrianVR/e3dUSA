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
            <el-select @change="$inertia.get(route('suppliers.show', selectedSupplier))" v-model="selectedSupplier"
              clearable filterable placeholder="Buscar producto" no-data-text="No hay proveedores en el catálogo"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in suppliers" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar proveedores')" content="Editar"
              placement="top">
              <Link :href="route('suppliers.edit', selectedSupplier)">
              <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
              </button>
              </Link>
            </el-tooltip>
            <Dropdown align="right" width="48"
              v-if="$page.props.auth.user.permissions.includes('Crear proveedores') && $page.props.auth.user.permissions.includes('Eliminar proveedores')">
              <template #trigger>
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center justify-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Crear proveedores')"
                  :href="route('suppliers.create')">
                  Crear nuevo proveedor
                </DropdownLink>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar proveedores')"
                  @click="showConfirmModal = true" as="button">
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

      <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
        <el-tab-pane label="Información general" name="1">
          <General :supplier="supplier.data" />
        </el-tab-pane>
        <el-tab-pane label="Contactos / info bancaria" name="2">
          <Contacts :supplier="supplier.data" />
        </el-tab-pane>
        <el-tab-pane label="Productos" name="3">
          <Products :supplier="supplier.data" />
        </el-tab-pane>
        <el-tab-pane label="Órdenes" name="4">
          <Orders :supplier="supplier.data" />
        </el-tab-pane>
      </el-tabs>

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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';
import General from "./Tabs/General.vue";
import Contacts from "./Tabs/Contacts.vue";
import Products from "./Tabs/Products.vue";
import Orders from "./Tabs/Orders.vue";

export default {
  data() {
    return {
      selectedSupplier: "",
      // currentSupplier: null,
      activeTab: '1',
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
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    Link,
    General,
    Contacts,
    Products,
    Orders,
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
  },
  mounted() {
    this.selectedSupplier = this.supplier.data.id;
    this.setTabInUrl();
  },
};
</script>