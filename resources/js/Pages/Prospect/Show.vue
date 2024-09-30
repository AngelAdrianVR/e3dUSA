<template>
  <AppLayoutNoHeader title="Detalles de prospecto">
    <AllPageLoading v-if="loading" />
    <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
      <span>Prospectos</span>
      <Link :href="route('prospects.index')"
        class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
      <i class="fa-solid fa-xmark"></i>
      </Link>
    </div>
    <!-- buscador y botones -->
    <div class="flex justify-between mt-5 mx-2 lg:mx-14">
      <div class="md:w-1/3 mr-2">
        <el-select @change="$inertia.get(route('prospects.show', selectedProspect))" v-model="selectedProspect" clearable
          filterable placeholder="Buscar prospecto" no-data-text="No hay prospectos registrados"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in prospects" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
      </div>
      <div class="flex space-x-2 w-full justify-end">
        <button @click="$inertia.get(route('prospects.edit', prospect.data.id))"
          class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9]">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
          </svg>
        </button>
        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537" title="Se eliminará este prospecto y se creará un cliente con esta información. ¿Continuar?"
          @confirm="turnIntoCustomer()">
          <template #reference>
            <SecondaryButton class="!rounded-[10px]">
              Convertir a cliente
            </SecondaryButton>
          </template>
        </el-popconfirm>
        <Dropdown align="right" width="48" v-if="$page.props.auth.user.permissions.includes('Crear prospectos')
          || $page.props.auth.user.permissions.includes('Eliminar prospectos')">
          <template #trigger>
            <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center justify-center text-sm">
              Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
            </button>
          </template>
          <template #content>
            <DropdownLink :href="route('prospects.create')"
              v-if="$page.props.auth.user.permissions.includes('Crear prospectos')">
              Crear nuevo
            </DropdownLink>
            <DropdownLink @click="showConfirmModal = true" as="button"
              v-if="$page.props.auth.user.permissions.includes('Eliminar prospectos')">
              Eliminar
            </DropdownLink>
          </template>
        </Dropdown>
      </div>
    </div>

    <h1 class="text-center font-bold text-xl mt-8 mb-4">{{ prospect.data.name }}</h1>
    <!-- tabs -->
    <el-tabs v-model="activeTab" class="mx-5" @tab-click="handleClick">
      <el-tab-pane label="Información general" name="1">
        <General :prospect="prospect.data" />
      </el-tab-pane>
      <el-tab-pane label="Cotizaciones" name="2">
        <Quotes :prospect="prospect.data" />
      </el-tab-pane>
    </el-tabs>

    <!-- confirmacion de eliminacion -->
    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title>
        Eliminar prospecto
      </template>
      <template #content>
        También se eliminarán las cotizaciones relacionadas con el prospeto, no se podrá recuperar la información. ¿Continuar con la eliminación?
      </template>
      <template #footer>
        <div>
          <CancelButton @click="showConfirmModal = false" class="mr-1">Cancelar</CancelButton>
          <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import AllPageLoading from "@/Components/MyComponents/AllPageLoading.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import General from "./Tabs/General.vue";
import Quotes from "./Tabs/Quotes.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    return {
      selectedProspect: null,
      showConfirmModal: false,
      activeTab: '1',
      loading: false,
    };
  },
  components: {
    AppLayoutNoHeader,
    SecondaryButton,
    PrimaryButton,
    CancelButton,
    Link,
    Dropdown,
    DropdownLink,
    ConfirmationModal,
    General,
    Quotes,
    AllPageLoading,
  },
  props: {
    prospects: Object,
    prospect: Object,
    defaultTab: Number,
  },
  methods: {
    deleteItem() {
      this.$inertia.delete(route('prospects.destroy', this.prospect.data.id));
    },
    handleClick(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    async turnIntoCustomer() {
      try {
        this.loading = true
        const response = await axios.post(route('prospects.turn-into-customer', this.prospect.data.id));

        if (response.status === 200) {
          this.$inertia.get(route('companies.edit', response.data.company_id));
        }
      } catch (error) {
        console.log(error);
      }
    }
  },
  mounted() {
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
