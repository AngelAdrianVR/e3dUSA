<template>
  <div>
    <AppLayoutNoHeader title="Clientes">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Cliente</label>
          <Link :href="route('companies.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="md:w-1/3">
            <el-select @change="$inertia.get(route('companies.show', selectedCompany))" v-model="selectedCompany"
              clearable filterable placeholder="Buscar cliente" no-data-text="No hay clientes en el catálogo"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in companies" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar clientes')" content="Editar"
              placement="top">
              <Link :href="route('companies.edit', selectedCompany)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>

            <Dropdown align="right" width="48"
              v-if="$page.props.auth.user.permissions.includes('Crear clientes') && $page.props.auth.user.permissions.includes('Eliminar clientes')">
              <template #trigger>
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('companies.create')"
                  v-if="$page.props.auth.user.permissions.includes('Crear clientes')">
                  Crear nuevo cliente
                </DropdownLink>

                <DropdownLink @click="showConfirmModal = true" as="button"
                  v-if="$page.props.auth.user.permissions.includes('Eliminar clientes')">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ company.data.business_name }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <el-tabs v-model="activeTab" class="mx-5" @tab-click="handleClick">
        <el-tab-pane label="Información general" name="1">
          <General :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Sucursales" name="2">
          <Branches :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Productos" name="3">
          <Products :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Oportunidades" name="4">
          <Opportunities :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Cotizaciones" name="5">
          <Quotes :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Seguimiento integral" name="6">
          <CustomerMonitor :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Proyectos" name="7">
          <Projects :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Ordenes de venta" name="8">
          <Sales :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label=" F. autorización de diseño" name="9">
          <DesignsFormat :company="company.data" />
        </el-tab-pane>
        <el-tab-pane label="Diseños exclusivos" name="10">
          <ExclusiveDesigns :company="company.data" />
        </el-tab-pane>
      </el-tabs>

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar cliente </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div class="">
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
// import CompanyBranchCard from "@/Components/MyComponents/CompanyBranchCard.vue";
// import CompanyProductCard from "@/Components/MyComponents/CompanyProductCard.vue";
import DesignAuthorizationTable from "@/Components/MyComponents/DesignAuthorizationTable.vue";
import ExclusiveDesignTable from "@/Components/MyComponents/ExclusiveDesignTable.vue";
import CompanyQuoteTable from "@/Components/MyComponents/CompanyQuoteTable.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import CompanyOportunityTable from "@/Components/MyComponents/CompanyOportunityTable.vue";
import CompanyClientMonitorTable from "@/Components/MyComponents/CompanyClientMonitorTable.vue";
import CompanySalesTable from "@/Components/MyComponents/CompanySalesTable.vue";
import ProjectTable from "@/Components/MyComponents/ProjectTable.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import General from "./Tabs/General.vue";
import Branches from "./Tabs/Branches.vue";
import Products from "./Tabs/Products.vue";
import Opportunities from "./Tabs/Opportunities.vue";
import Quotes from "./Tabs/Quotes.vue";
import CustomerMonitor from "./Tabs/CustomerMonitor.vue";
import Projects from "./Tabs/Projects.vue";
import Sales from "./Tabs/Sales.vue";
import DesignsFormat from "./Tabs/DesignsFormat.vue";
import ExclusiveDesigns from "./Tabs/ExclusiveDesigns.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    return {
      selectedCompany: "",
      allExclusiveDesigns: [],
      activeTab: '1',
      showConfirmModal: false,
      loading: false,
    };
  },
  props: {
    company: Object,
    companies: Array,
    defaultTab: Number,
  },
  components: {
    AppLayoutNoHeader,
    // CompanyBranchCard,
    // CompanyProductCard,
    Dropdown,
    DropdownLink,
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    CompanySalesTable,
    CompanyOportunityTable,
    CompanyClientMonitorTable,
    DesignAuthorizationTable,
    ExclusiveDesignTable,
    CompanyQuoteTable,
    ProjectTable,
    Link,
    General,
    Branches,
    Products,
    Opportunities,
    Quotes,
    CustomerMonitor,
    Projects,
    Sales,
    DesignsFormat,
    ExclusiveDesigns,
  },
  methods: {
    handleClick(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    // getColorHex(number) {
    //   if (number) {
    //     // Ajusta el tono (hue) en función del número proporcionado
    //     let tono = (number * 30) % 360;

    //     // Saturation y lightness se mantienen constantes para colores vibrantes
    //     let saturacion = 80;
    //     let luminosidad = 40;

    //     // Convierte de HSL a hexadecimal
    //     let colorHex = this.hslToHex(tono, saturacion, luminosidad);

    //     return colorHex;
    //   } else {

    //     return '#cccccc';
    //   }
    // },
    // // Función para convertir de HSL a hexadecimal
    // hslToHex(h, s, l) {
    //   h /= 360;
    //   s /= 100;
    //   l = l > 40 ? 40 : l;
    //   l /= 100;

    //   let r, g, b;

    //   if (s === 0) {
    //     r = g = b = l;
    //   } else {
    //     const hue2rgb = (p, q, t) => {
    //       if (t < 0) t += 1;
    //       if (t > 1) t -= 1;
    //       if (t < 1 / 6) return p + (q - p) * 6 * t;
    //       if (t < 1 / 2) return q;
    //       if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
    //       return p;
    //     };

    //     const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
    //     const p = 2 * l - q;

    //     r = hue2rgb(p, q, h + 1 / 3);
    //     g = hue2rgb(p, q, h);
    //     b = hue2rgb(p, q, h - 1 / 3);
    //   }

    //   const toHex = x => {
    //     const hex = Math.round(x * 255).toString(16);
    //     return hex.length === 1 ? '0' + hex : hex;
    //   };

    //   return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
    // },
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("companies.destroy", this.company.data.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });
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
          message: err.message + "Actualiza la página",
          type: "error",
        });
        console.log(err);
      } finally {
        this.showConfirmModal = false;
        this.$inertia.get(route('companies.index'));
      }
    },
    // hasQuotes() {
    //   const tieneCotizaciones = this.company.data.company_branches
    //     .map((branch) => branch.quotes.length > 0)
    //     .some((tieneCotizacionesEnBranch) => tieneCotizacionesEnBranch);

    //   return tieneCotizaciones; // Devolverá true si hay cotizaciones en al menos un company_branch
    // },
  },
  mounted() {
    this.selectedCompany = this.company.data.id;

    // Obtener la URL actual
    const currentURL = new URL(window.location.href);
    // Extraer el valor de 'currentTab' de los parámetros de búsqueda
    const currentTabFromURL = currentURL.searchParams.get('currentTab');

    if (currentTabFromURL) {
      this.activeTab = currentTabFromURL;
    }
  },
};
</script>