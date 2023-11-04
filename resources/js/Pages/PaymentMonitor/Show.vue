<template>
  <AppLayoutNoHeader title="Seguimiento de pagos">
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
            <el-tooltip content="Editar pago o transacción" placement="top">
              <Link :href="route('payment-monitors.edit', payment_monitor.data.id)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <Link :href="route('payment-monitors.create')">
              <PrimaryButton class="rounded-md">Registrar pago</PrimaryButton>
            </Link>

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
        {{ payment_monitor.data?.concept }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Pago o transacción
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Pago o transacción Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

          <p class="text-secondary col-span-2 mb-2">Información de oportuindad</p>
          <span class="text-gray-500">Folio</span>
          <a class="text-secondary hover:underline" :href="route('oportunities.show', payment_monitor.data.oportunity?.id )">
            <span>{{ payment_monitor.data.oportunity?.folio }} - {{ payment_monitor.data.oportunity?.name }}
            </span>
          </a>
          <span class="text-gray-500 my-2">Cliente</span>
          <span>{{ payment_monitor.data.oportunity.company?.business_name ?? '--' }}</span>
          <span class="text-gray-500 my-2">sucursal</span>
          <span>{{ payment_monitor.data.oportunity.companyBranch?.name ?? '--' }}</span>
          <span class="text-gray-500 my-2">Vendedor</span>
          <span>{{ payment_monitor.data.seller?.name }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Datos del pago</p>

            <span class="text-gray-500 my-1">Monto</span>
            <span>${{ payment_monitor.data.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            <span class="text-gray-500 my-1">Método de pago</span>
            <span>{{ payment_monitor.data.payment_method }}</span>
            <span class="text-gray-500 my-1">Concepto</span>
            <span>{{ payment_monitor.data.concept }}</span>
            <span class="text-gray-500 my-1">Fecha de pago</span>
            <span>{{ payment_monitor.data.paid_at }}</span>
            <span class="text-gray-500 my-1">Observaciones</span>
            <span>{{ payment_monitor.data.notes }}</span>

          <p class="text-secondary col-span-2 mb-2 mt-5">Archivos adjuntos</p>
          <div v-if=" payment_monitor.data.media?.length">
            <li v-for="file in payment_monitor.data.media" :key="file" class="flex items-center justify-between col-span-full">
              <a :href="file.original_url" target="_blank" class="flex items-center">
                <i :class="getFileTypeIcon(file.file_name)"></i>
                <span class="ml-2">{{ file.file_name }}</span>
              </a>
            </li>
          </div>
          <p class="text-sm text-gray-400" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos</p>

        </div>
      </div>
      <!-- ------------- Pago o transacción ends 1 ------------- -->

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
    payment_monitor: Object,
},
methods:{
    deleteItem() {
      this.$inertia.delete(route('payment-monitors.destroy', this.payment_monitor.data.id));
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
