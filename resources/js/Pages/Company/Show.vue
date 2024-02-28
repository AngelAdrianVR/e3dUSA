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
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex overflow-x-auto pb-3 lg:pb-0">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="h-10 w-96 lg:w-auto p-2 cursor-pointer ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Información general
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Sucursales
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Productos
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 4" :class="tabs == 4 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Oportunidades
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 5" :class="tabs == 5 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Cotizaciones
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 6" :class="tabs == 6 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Seguimiento integral
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 7" :class="tabs == 7 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Proyectos
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 8" :class="tabs == 8 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Ordenes de venta
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 9" :class="tabs == 9 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            F. autorización de diseño
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:w-1/2 grid grid-cols-2 text-left p-4 md:ml-20 text-sm items-center">
        <span class="text-gray-500">ID</span>
        <span>{{ company.data.id }}</span>
        <span class="text-gray-500">Sucursales totales con las que cuenta el cliente</span>
        <span>{{ company.data.branches_number ?? '* No especificado' }}</span>

        <span class="col-span-2 mt-8 mb-2">Datos fiscales</span>

        <span class="text-gray-500 my-2">Razón social</span>
        <span>{{ company.data.business_name }}</span>
        <span class="text-gray-500 my-2">RFC</span>
        <span>{{ company.data.rfc }}</span>
        <span class="text-gray-500 my-2">Código postal</span>
        <span>{{ company.data.post_code }}</span>
        <span class="text-gray-500 my-2">Dirección</span>
        <span>{{ company.data.fiscal_address }}</span>
        <span class="text-gray-500 my-2">Vendedor</span>
        <p class="mr-2" :style="{ color: getColorHex(company.data.seller?.id) }">
          <i class="fa-solid fa-star"></i>
          {{ company.data.seller?.name ?? '* Sin información' }}
        </p>
        <span class="text-gray-500 my-2">Registro creado por</span>
        <span>{{ company.data.user?.name ?? '* Sin información' }}</span>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------Sucursales starts 2 ------------- -->
      <div v-if="tabs == 2" class="lg:grid grid-cols-2 gap-8 md:mt-12 md:px-14">
        <CompanyBranchCard :company_branches="company.data.company_branches" />
      </div>
      <!-- ------------- Sucursales ends 2 ------------- -->

      <!-- -------------Productos starts 3 ------------- -->
      <div v-if="tabs == 3" class="p-7">
        <p class="text-secondary">Productos registrados</p>
        <div class="grid lg:grid-cols-4 md:grid-cols-2 mt-7 gap-10">
          <CompanyProductCard v-for="company_product in company.data.catalogProducts" :key="company_product.id"
            :company_product="company_product" />
        </div>
      </div>
      <!-- ------------- Productos ends 3 ------------- -->

      <!-- -------------Oportunidades starts 4 ------------- -->
      <div v-if="tabs == 4" class="p-7 w-full mx-auto my-4">
        <div v-if="company.data.oportunities.length">
          <CompanyOportunityTable :oportunities="company.data.oportunities" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
          <p class="text-sm text-center">No hay oportunidades para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
        </div>
      </div>
      <!-- ------------- Oportunidades ends 4 ------------- -->

      <!-- -------------Cotizaciones starts 5 ------------- -->
      <div v-if="tabs == 5" class="p-7 w-full mx-auto my-4">
        <div v-if="hasQuotes()" class="overflow-x-auto">
          <CompanyQuoteTable :quotes="allQuotes" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
          <p class="text-sm text-center">No hay cotizaciones para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
        </div>

      </div>
      <!-- ------------- Cotizaciones ends 5 ------------- -->

      <!-- -------------Seguimiento integral starts 6 ------------- -->
      <div v-if="tabs == 6" class="p-7 w-full mx-auto my-4">
        <div v-if="company.data.clientMonitors?.length">
          <CompanyClientMonitorTable :client_monitors="company.data.clientMonitors" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
          <p class="text-sm text-center">No hay Seguimiento para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
        </div>
      </div>
      <!-- ------------- Seguimiento integral ends 6 ------------- -->

      <!-- -------------Proyectos starts 7 ------------- -->
      <div v-if="tabs == 7" class="p-7 w-full mx-auto my-4">
        <div v-if="company.data.projects?.length">
          <ProjectTable :projects="company.data.projects" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
          <p class="text-sm text-center">No hay proyectos para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
        </div>
      </div>
      <!-- ------------- Proyectos ends 7 ------------- -->

      <!-- -------------Ordenes de venta starts 8 ------------- -->
      <div v-if="tabs == 8" class="lg:p-7 w-full mx-auto my-4">
        <div v-if="company.data.company_branches?.some(branch => branch.sales?.length > 0)">
          <CompanySalesTable :company_sales="allSales" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
          <p class="text-sm text-center">No hay ordenes de venta de este cliente</p>
          <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
        </div>
      </div>
      <!-- ------------- Ordenes de venta ends 8 ------------- -->


      <!-- ------------- Formatos de autorización de diseño starts 9 ------------- -->
      <div v-if="tabs === 9" class="px-7 w-full my-4">
        <div class="flex justify-between items-center">
          <p class="text-secondary">Formatos de autorización de diseño</p>
          <PrimaryButton @click="$inertia.get(route('design-authorizations.create'))" class="self-start">Agregar formato</PrimaryButton>
        </div>
        <div class="mt-5 mx-auto" v-if="company.data.company_branches?.some(branch => branch.designAuthorizations?.length > 0)">
          <DesignAuthorizationTable :designAuthorizations="allDesignAuthorizations" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
          <p class="text-sm text-center">No hay Formatos de autorización de diseño de este cliente</p>
          <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-400/30"></i>
        </div>
      </div>
      <!-- ------------- Formatos de autorización de diseño ends 9 ------------- -->

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
import CompanyBranchCard from "@/Components/MyComponents/CompanyBranchCard.vue";
import CompanyProductCard from "@/Components/MyComponents/CompanyProductCard.vue";
import DesignAuthorizationTable from "@/Components/MyComponents/DesignAuthorizationTable.vue";
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
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    return {
      selectedCompany: "",
      // currentCompany: null,
      // currentCompanyProducts: null,
      tabs: 1,
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
    CompanyBranchCard,
    CompanyProductCard,
    Dropdown,
    DropdownLink,
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    CompanySalesTable,
    CompanyOportunityTable,
    CompanyClientMonitorTable,
    DesignAuthorizationTable,
    CompanyQuoteTable,
    ProjectTable,
    Link,
  },
  methods: {
    getColorHex(number) {
      if (number) {
        // Ajusta el tono (hue) en función del número proporcionado
        let tono = (number * 30) % 360;

        // Saturation y lightness se mantienen constantes para colores vibrantes
        let saturacion = 80;
        let luminosidad = 40;

        // Convierte de HSL a hexadecimal
        let colorHex = this.hslToHex(tono, saturacion, luminosidad);

        return colorHex;
      } else {

        return '#cccccc';
      }
    },
    // Función para convertir de HSL a hexadecimal
    hslToHex(h, s, l) {
      h /= 360;
      s /= 100;
      l = l > 40 ? 40 : l;
      l /= 100;

      let r, g, b;

      if (s === 0) {
        r = g = b = l;
      } else {
        const hue2rgb = (p, q, t) => {
          if (t < 0) t += 1;
          if (t > 1) t -= 1;
          if (t < 1 / 6) return p + (q - p) * 6 * t;
          if (t < 1 / 2) return q;
          if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
          return p;
        };

        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;

        r = hue2rgb(p, q, h + 1 / 3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1 / 3);
      }

      const toHex = x => {
        const hex = Math.round(x * 255).toString(16);
        return hex.length === 1 ? '0' + hex : hex;
      };

      return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
    },
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
    hasQuotes() {
      const tieneCotizaciones = this.company.data.company_branches
        .map((branch) => branch.quotes.length > 0)
        .some((tieneCotizacionesEnBranch) => tieneCotizacionesEnBranch);

      return tieneCotizaciones; // Devolverá true si hay cotizaciones en al menos un company_branch
    },
  },
  computed: {
    allSales() {
      // Recopila todas las ventas de todos los company_branches
      const sales = [];
      if (this.company.data && this.company.data.company_branches) {
        this.company.data.company_branches.forEach(branch => {
          if (branch.sales) {
            sales.push(...branch.sales);
          }
        });
      }
      return sales;
    },
    allDesignAuthorizations() {
      // Recopila todas las ventas de todos los company_branches
      const designAuthorizations = [];
      if (this.company.data && this.company.data.company_branches) {
        this.company.data.company_branches.forEach(branch => {
          if (branch.designAuthorizations) {
            designAuthorizations.push(...branch.designAuthorizations);
          }
        });
      }
      return designAuthorizations;
    },
    allQuotes() {
      // Recopila todas las ventas de todos los company_branches
      const quotes = [];
      if (this.company.data && this.company.data.company_branches) {
        this.company.data.company_branches.forEach(branch => {
          if (branch.quotes) {
            quotes.push(...branch.quotes);
          }
        });
      }
      return quotes;
    },
  },
  mounted() {
    this.selectedCompany = this.company.data.id;
    // tabs
    if (this.defaultTab != null) {
      this.tabs = parseInt(this.defaultTab);
    }
  },
};
</script>