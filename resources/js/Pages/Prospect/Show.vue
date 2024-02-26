<template>
  <AppLayoutNoHeader title="Detalles de prospecto">
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
          class="size-9 rounded-[10px] bg-[#D9D9D9]">
          <i class="fa-solid fa-pen text-sm"></i>
        </button>
        <SecondaryButton @click="turnIntoCustomer" class="!rounded-[10px]">
          Convertir a cliente
        </SecondaryButton>
        <Dropdown align="right" width="48" v-if="$page.props.auth.user.permissions.includes('Crear prospectos')
          || $page.props.auth.user.permissions.includes('Eliminar prospectos')">
          <template #trigger>
            <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">Más <i
                class="fa-solid fa-chevron-down text-[11px] ml-2"></i></button>
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
        <Quotes />
      </el-tab-pane>
    </el-tabs>

    <!-- confirmacion de eliminacion -->
    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title>
        Eliminar prospecto
      </template>
      <template #content>
        No se podrá recuperar la información. ¿Continuar con la eliminación?
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
