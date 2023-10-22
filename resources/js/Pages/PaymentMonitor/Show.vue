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

          <div class="flex items-center space-x-2">
            <el-tooltip content="Editar pago o transacción" placement="top">
              <Link :href="route('payment-monitors.edit', clientMonitorSelected)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <el-tooltip
            content="Registrar pago o transacción"
            placement="top"
          >
            <Link :href="route('payment-monitors.create')">
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
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')" @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
    </div>
    <p class="text-center font-bold text-lg mb-4">
        {{ currentClientMonitor?.folio }}
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
      <div v-if="tabs == 2" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

          <span class="text-gray-500">Creado por</span>
          <span>{{ currentOportunity?.folio }}</span>
          <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>
          <span class="text-gray-500 my-2">Folio de oportunidad</span>
          <span>{{ currentOportunity?.name }}</span>
          <span class="text-gray-500 my-2">Cliente</span>
          <span>{{ currentOportunity?.description }}</span>
          <span class="text-gray-500 my-2">Vendedor</span>
          <span>{{ currentOportunity?.user?.name }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Datos del pago</p>

            <span class="text-gray-500">Monto</span>
            <span>{{ currentOportunity?.folio }}</span>
            <span class="text-gray-500">Método de pago</span>
            <span>{{ currentOportunity?.folio }}</span>
            <span class="text-gray-500">Concepto</span>
            <span>{{ currentOportunity?.folio }}</span>
            <span class="text-gray-500">Fecha de pago</span>
            <span>{{ currentOportunity?.folio }}</span>
            <span class="text-gray-500">Observaciones</span>
            <span>{{ currentOportunity?.folio }}</span>

          <p class="text-secondary col-span-2 mt-7">Archivos adjuntos</p>

          <span class="text-gray-500 my-2">Nombre</span>
          <span>{{ currentOportunity?.contact?.name }}</span>

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
import { Link } from "@inertiajs/vue3";

export default {
data() {
    return{
        clientMonitorSelected: '',
        currentClientMonitor: null,
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
    Link
},
props:{
    client_monitor: Object,
    client_monitors: Array,
},
methods:{
    deleteItem() {
      this.$inertia.delete(route('payment-monitors.destroy', this.oportunitySelected));
    },
},
watch: {

},
mounted() {

  },
};
</script>
