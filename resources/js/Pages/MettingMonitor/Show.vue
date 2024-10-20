<template>
  <AppLayoutNoHeader title="Seguimiento de pagos">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
      <div class="flex justify-between">
        <label class="text-lg">Seguimiento integral</label>
        <Link :href="route('client-monitors.index')"
          class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
        <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>
      <div class="flex justify-between">
        <span></span>
        <div class="flex items-center space-x-2">
          <el-tooltip content="Editar cita" placement="top">
            <Link :href="route('meeting-monitors.edit', metting_monitor.data.id)">
            <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
              <i class="fa-solid fa-pen text-sm"></i>
            </button>
            </Link>
          </el-tooltip>

          <Link :href="route('meeting-monitors.create')">
          <PrimaryButton class="rounded-md">Agendar cita</PrimaryButton>
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
          Cita
        </p>
      </div>
    </div>
    <!-- ------------- Cita Starts 1 ------------- -->
    <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
      <div class="grid grid-cols-2 gap-1 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center *:self-start">
        <p class="text-secondary col-span-2 mb-2">Información de oportuindad</p>
        <span class="text-gray-500">Folio</span>
        <a class="text-secondary hover:underline"
          :href="route('oportunities.show', metting_monitor.data.oportunity?.id)">
          <span>{{ metting_monitor.data.oportunity?.folio }} - {{ metting_monitor.data.oportunity?.name }}
          </span>
        </a>
        <p class="text-secondary col-span-2 mt-5 mb-2">Información de la cita</p>
        <span class="text-gray-500">Creado por</span>
        <span>{{ metting_monitor.data.seller.name }}</span>
        <span class="text-gray-500">Fecha</span>
        <span>{{ metting_monitor.data.meeting_date }}</span>
        <span class="text-gray-500">Hora</span>
        <span>{{ metting_monitor.data.time }}</span>
        <span class="text-gray-500">Cita</span>
        <span>{{ metting_monitor.data.meeting_via }}</span>
        <span class="text-gray-500">Ubicación</span>
        <span>{{ metting_monitor.data.location }}</span>
        <span class="text-gray-500">Descripción</span>
        <span>{{ metting_monitor.data.description }}</span>
      </div>
      <div class="grid grid-cols-2 gap-1 text-left p-4 md:ml-10 items-center self-start *:self-start">
        <p class="text-secondary col-span-2 mb-2">Información del cliente</p>

        <span class="text-gray-500">Cliente</span>
        <span>{{ metting_monitor.data.company?.business_name ?? 'Cita agendada a oportunidad sin cliente registrado'
          }}</span>
        <span class="text-gray-500">Contacto</span>
        <span>{{ metting_monitor.data.contact ? metting_monitor.data.contact?.name : metting_monitor.data.contact_name
          }}</span>
        <span class="text-gray-500">Sucursal</span>
        <span>{{ metting_monitor.data.companyBranch?.name ?? 'Cita agendada a oportunidad sin cliente registrado'
          }}</span>
        <span class="text-gray-500">Teléfono</span>
        <span>{{ metting_monitor.data.phone }}</span>
      </div>
    </div>

    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title> Eliminar Registro de cita </template>
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
    return {
      showConfirmModal: false,
      tabs: 1,
    }
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    ConfirmationModal,
    PrimaryButton,
    CancelButton,
    Link
  },
  props: {
    metting_monitor: Object,
  },
  methods: {
    deleteItem() {
      this.$inertia.delete(route('meeting-monitors.destroy', this.metting_monitor.data.id));
    },
  },
};
</script>
