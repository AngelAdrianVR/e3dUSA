<template>
  <AppLayoutNoHeader title="Llamada">
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
            <el-tooltip content="Editar interacción por llamada" placement="top">
              <Link :href="route('call-monitors.edit', call_monitor.data.id)">
                <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                    <i class="fa-solid fa-pen text-sm"></i>
                </button>
              </Link>
            </el-tooltip>
            <el-tooltip
            content="Registrar Interaccion por llamada"
            placement="top"
          >
            <Link :href="route('call-monitors.create')">
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
        {{ call_monitor.data?.concept }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Interaccion por llamada
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Interaccion llamada Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center self-start">

          <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>
          <span class="text-gray-500 my-2">Folio de oportunidad</span>
          <a class="text-secondary hover:underline" :href="route('oportunities.show', call_monitor.data.oportunity?.id)">
            <span>{{ call_monitor.data.oportunity?.folio ?? '--' }} - {{ call_monitor.data.oportunity?.name ?? '--' }}</span>
          </a>
          <span class="text-gray-500 my-2">Fecha de creación</span>
          <span>{{ call_monitor.data.created_at }}</span>
          <span class="text-gray-500 my-2">Vendedor</span>
          <span>{{ call_monitor.data.seller?.name }}</span>

          <p class="text-secondary col-span-2 mb-2">Información del cliente</p>

            <span class="text-gray-500 my-2">Cliente</span>
            <span>{{ call_monitor.data.company ?? call_monitor.data.company_name  }}</span>
            <span class="text-gray-500 my-2">Sucursal</span>
            <span>{{ call_monitor.data.companyBranch?.name ?? '--'  }}</span>
            <span class="text-gray-500 my-2">Contacto</span>
            <span>{{ call_monitor.data.contact?.name ?? '--' }}</span>
            <span class="text-gray-500 my-2">Teléfono</span>
            <span>{{ call_monitor.data.contact_phone }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 self-start">

            <p class="text-secondary col-span-2 mb-2">Interacción por llamada</p>

            <span class="text-gray-500 my-2">Fecha</span>
            <span>{{ call_monitor.data.date  }}</span>
            <span class="text-gray-500 my-2">Descripción</span>
            <span>{{ call_monitor.data.notes ?? 'Sin descripción'  }}</span>

        </div>
      </div>
      <!-- ------------- Interaccion llamada ends 1 ------------- -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar Registro de llamada </template>
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
    call_monitor: Object,
},
methods:{
    deleteItem() {
      this.$inertia.delete(route('call-monitors.destroy', this.call_monitor.data.id));
    },
},
};
</script>
