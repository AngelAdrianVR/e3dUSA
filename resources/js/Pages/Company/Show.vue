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

        <span class="col-span-2 mt-8 mb-2">Datos fiscales</span>

        <span class="text-gray-500 my-2">Razón social</span>
        <span>{{ company.data.business_name }}</span>
        <span class="text-gray-500 my-2">RFC</span>
        <span>{{ company.data.rfc }}</span>
        <span class="text-gray-500 my-2">Código postal</span>
        <span>{{ company.data.post_code }}</span>
        <span class="text-gray-500 my-2">Dirección</span>
        <span>{{ company.data.fiscal_address }}</span>
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
          <table class="w-full mx-auto">
            <thead>
              <tr class="text-left">
                <th class="font-bold pb-5">
                  Folio <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
                </th>
                <th class="font-bold pb-5">
                  Creado por <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
                </th>
                <th class="font-bold pb-5">
                  Receptor <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
                </th>
                <th class="font-bold pb-5">
                  Autorizado por <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
                </th>
                <th class="font-bold pb-5">
                  Creado el <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
                </th>
              </tr>
            </thead>
            <tbody>
              <template v-for="branch in company.data.company_branches">
                <tr v-for="quote in branch.quotes" :key="quote.id" class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
                  @click="$inertia.get(route('quotes.show', quote.id))">
                  <td class="text-left text-secondary py-2 px-2 rounded-l-full">
                    {{ quote.folio }}
                  </td>
                  <td class="text-left py-2 px-2">
                    {{ quote.user ? quote.user.name : '' }}
                  </td>
                  <td class="text-left py-2 px-2">
                    <span class="py-1 px-4 rounded-full">{{ quote.receiver }}</span>
                  </td>
                  <td class="text-left py-2 px-2">
                    <span class="py-1 px-2">{{ quote.authorized_user_name ?? '--' }}</span>
                  </td>
                  <td class="text-left py-2 px-2 rounded-r-full">
                    {{ quote.created_at }}
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
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
    ProjectTable,
    Link,
  },
  methods: {
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
          // const index = this.companies.data.findIndex(
          //   (item) => item.id === this.company.data.id
          // );
          // if (index !== -1) {
          //   this.company.data.splice(index, 1);
          //   this.selectedCompany = "";
          // }
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