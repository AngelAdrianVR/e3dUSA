<template>
  <div>
    <AppLayoutNoHeader title="Proveedores">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1 dark:text-white">
        <div class="flex justify-between">
          <label class="text-lg">Proveedor</label>
          <Link :href="route('suppliers.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="w-1/3">
            <el-select @change="$inertia.get(route('suppliers.show', selectedSupplier))" v-model="selectedSupplier"
              clearable filterable no-data-text="No hay proveedores en el catálogo"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in suppliers" :key="item.id" :label="item.nickname" :value="item.id" />
            </el-select>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ supplier.data.nickname }}
      </p>

      <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
        <el-tab-pane label="Productos" name="1">
          <ProductsLimited :supplier="supplier.data" />
        </el-tab-pane>
        <el-tab-pane label="Órdenes" name="2">
          <Orders :supplier="supplier.data" />
        </el-tab-pane>
      </el-tabs>
    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import { Link } from "@inertiajs/vue3";
import ProductsLimited from "./Tabs/ProductsLimited.vue";
import Orders from "./Tabs/Orders.vue";

export default {
  data() {
    return {
      selectedSupplier: "",
      activeTab: '1',
    };
  },
  props: {
    supplier: Object,
    suppliers: Array,
  },
  components: {
    AppLayoutNoHeader,
    Link,
    ProductsLimited,
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
  },
  mounted() {
    this.selectedSupplier = this.supplier.data.id;
    this.setTabInUrl();
  },
};
</script>