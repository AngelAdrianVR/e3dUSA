<template>
  <AppLayoutNoHeader title="Interacciones WhastApp">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Seguimiento integral</label>
          <Link :href="route('client-monitors.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
            <span></span>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Editar Interaccion whatsapp" placement="top">
              <Link :href="route('whatsapp-monitors.edit', whatsapp_monitor.data.id)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <el-tooltip
            content="Registrar Interaccion whatsapp"
            placement="top"
          >
            <Link :href="route('whatsapp-monitors.create')">
              <PrimaryButton class="rounded-md">Crear</PrimaryButton>
            </Link>
          </el-tooltip>

            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
    </div>
    <p class="text-center font-bold text-lg mb-4">
        {{ whatsapp_monitor.data?.concept }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Interaccion whatsapp
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Interaccion whatsapp Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

          <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>
          <span class="text-gray-500 my-2">Folio de oportunidad</span>
          <a class="text-secondary hover:underline" :href="route('oportunities.show', whatsapp_monitor.data.oportunity?.id)"><span>{{ whatsapp_monitor.data.oportunity?.folio ?? '--' }}</span></a>
          <span class="text-gray-500 my-2">Fecha de creación</span>
          <span>{{ whatsapp_monitor.data.created_at }}</span>
          <span class="text-gray-500 my-2">Vendedor</span>
          <span>{{ whatsapp_monitor.data.seller?.name }}</span>

          <p class="text-secondary col-span-2 mb-2">Información del cliente</p>

            <span class="text-gray-500 my-2">Cliente</span>
            <span>{{ whatsapp_monitor.data.company ?? whatsapp_monitor.data.company_name  }}</span>
            <span class="text-gray-500 my-2">Contacto</span>
            <span>{{ whatsapp_monitor.data.contact ? whatsapp_monitor.data.contact?.name : whatsapp_monitor.data.contact_name    }}</span>
            <span class="text-gray-500 my-2">Teléfono</span>
            <span>{{ whatsapp_monitor.data.contact_phone }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">

            <p class="text-secondary col-span-2 mb-2">Interacción de whatsapp</p>

            <span class="text-gray-500 my-2">Fecha</span>
            <span>{{ whatsapp_monitor.data.date  }}</span>
            <span class="text-gray-500 my-2">Descripción</span>
            <span>{{ whatsapp_monitor.data.notes ?? 'Sin descripción'  }}</span>

          <p class="text-secondary col-span-2 mb-2 mt-5">Archivos adjuntos</p>
          <div v-if=" whatsapp_monitor.data.media?.length">
            <li v-for="file in whatsapp_monitor.data.media" :key="file" class="flex items-center justify-between col-span-full">
              <a :href="file.original_url" target="_blank" class="flex items-center">
                <i :class="getFileTypeIcon(file.file_name)"></i>
                <span class="ml-2">{{ file.file_name }}</span>
              </a>
            </li>
          </div>
          <p class="text-sm text-gray-400" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos</p>

        </div>
      </div>
      <!-- ------------- Interaccion whatsapp ends 1 ------------- -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar Registro de pago </template>
        <template #content> ¿Continuar con la eliminación? </template>
        <template #footer>
          <div>
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
data() {
    return{
        showConfirmModal: false,
        tabs: 1,
    }
},
components:{
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    ConfirmationModal,
    PrimaryButton,
    CancelButton,
    Link
},
props:{
    whatsapp_monitor: Object,
},
methods:{
    deleteItem() {
      this.$inertia.delete(route('whatsapp-monitors.destroy', this.whatsapp_monitor.data.id));
    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
},
};
</script>
