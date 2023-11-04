<template>
  <AppLayoutNoHeader title="Seguimiento de correos">
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

            <Link :href="route('email-monitors.create')">
              <PrimaryButton class="rounded-md">Enviar correo</PrimaryButton>
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

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Correo electrónico
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Email Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

          <p class="text-secondary col-span-2 mb-2">Información de oportuindad</p>
          <span class="text-gray-500">Folio</span>
          <a class="text-secondary hover:underline" :href="route('oportunities.show', email_monitor.data.oportunity?.id )">
            <span>{{ email_monitor.data.oportunity?.folio }} - {{ email_monitor.data.oportunity?.name }}
            </span>
          </a>

          <p class="text-secondary col-span-2 mt-5 mb-2">Información del correo electrónico</p>
          <span class="text-gray-500">Enviado por</span>
          <span>{{ email_monitor.data.seller?.name }}</span>
          <span class="text-gray-500 my-2">Cliente</span>
          <span>{{ email_monitor.data.company?.business_name ?? '--' }}</span>
          <span class="text-gray-500 my-2">Sucursal</span>
          <span>{{ email_monitor.data.companyBranch?.name ?? '--' }}</span>
          <span class="text-gray-500 my-2">Enviado a</span>
          <span>{{ email_monitor.data.contact_name }}</span>
          <span class="text-gray-500 my-2">Correo del contacto</span>
          <span>{{ email_monitor.data.contact_email }}</span>
          <span class="text-gray-500 my-2">Asunto</span>
          <span>{{ email_monitor.data.subject }}</span>
          <span class="text-gray-500 my-2">Contenido</span>
          <span>{{ email_monitor.data.content }}</span>
          <span class="text-gray-500 my-2">Enviado el</span>
          <span>{{ email_monitor.data.created_at }}</span>
        </div>
      </div>
      <!-- ------------- Email ends 1 ------------- -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar Registro de correo electrónico </template>
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
    email_monitor: Object,
},
methods:{
    deleteItem() {
      this.$inertia.delete(route('email-monitors.destroy', this.email_monitor.data.id));
    },
},
};
</script>
