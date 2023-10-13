<template>
  <AppLayoutNoHeader title="Seguimiento integral">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Seguimiento integral</label>
          <Link :href="route('client-monitors.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="md:w-1/3 mr-2">
            <el-select v-model="clientMonitorSelected" clearable filterable
              placeholder="Buscar Seguimiento" no-data-text="No hay oportuindades registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in client_monitors.data" :key="item.id" :label="item.folio" :value="item.id" />
            </el-select>
          </div>

          <div class="flex items-center space-x-2">
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar oportunidades') && tabs == 1" content="Editar oportunidad" placement="top">
              <Link :href="route('oportunities.edit', clientMonitorSelected)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <el-tooltip
            v-if="tabs == 3"
            content="Enviar un correo a prospecto"
            placement="top"
          >
            <Link :href="route('oportunity-tasks.create', clientMonitorSelected)">
              <PrimaryButton class="rounded-md">Enviar correo</PrimaryButton>
            </Link>
          </el-tooltip>

            <Dropdown align="right" width="48" v-if="$page.props.auth.user.permissions.includes(
              'Crear ordenes de venta'
            ) &&
              $page.props.auth.user.permissions.includes(
                'Eliminar ordenes de venta'
              )
              ">
              <template #trigger>
                <button v-if="tabs == 1 || tabs == 3" class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('payment-monitors.create')" v-if="tabs == 3 && $page.props.auth.user.permissions.includes('Registrar pagos en seguimiento integral')">
                  Registrar Pago
                </DropdownLink>
                <DropdownLink :href="route('meeting-monitors.create')" v-if="tabs == 3 && $page.props.auth.user.permissions.includes('Agendar citas en seguimiento integral')">
                  Agendar Cita
                </DropdownLink>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades') && tabs == 1
                  " @click="showConfirmModal = true" as="button">
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
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Correo electrónico
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Pago o transacción
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Cita
          </p>

        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Correo electrónico Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <p class="text-secondary col-span-2 mb-2">Información de correo electrónico</p>

          <span class="text-gray-500">Enviado por</span>
          <span>{{ currentOportunity?.folio }}</span>
          <span class="text-gray-500 my-2">Enviado a</span>
          <span>{{ currentOportunity?.name }}</span>
          <span class="text-gray-500 my-2">Asunto</span>
          <span>{{ currentOportunity?.description }}</span>
          <span class="text-gray-500 my-2">Enviado el</span>
          <span>{{ currentOportunity?.user?.name }}</span>

          <p class="mt-5 cursor-pointer"><i class="fa-regular fa-envelope text-sm text-primary mr-2"></i> Reenviar correo</p>
        </div>
      </div>
      <!-- ------------- Correo electrónico ends 1 ------------- -->

      <!-- ------------- Pago o transacción Starts 2 ------------- -->
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
      <!-- ------------- Pago o transacción ends 2 ------------- -->

      <!-- ------------- Cita Starts 3 ------------- -->
      <div v-if="tabs == 3" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">

          <p class="text-secondary col-span-2 mb-2">Información de la cita</p>
          <span class="text-gray-500">Creado por</span>
          <span>{{ currentOportunity?.folio }}</span>
          <span class="text-gray-500 my-2">Fecha</span>
          <span>{{ currentOportunity?.name }}</span>
          <span class="text-gray-500 my-2">ClieHorante</span>
          <span>{{ currentOportunity?.description }}</span>
          <span class="text-gray-500 my-2">Cita</span>
          <span>{{ currentOportunity?.user?.name }}</span>
          <span class="text-gray-500 my-2">Ubicación</span>
          <span>{{ currentOportunity?.user?.name }}</span>
          <span class="text-gray-500 my-2">Descripción</span>
          <span>{{ currentOportunity?.user?.name }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Información del cliente</p>

            <span class="text-gray-500">Cliente</span>
            <span>{{ currentOportunity?.folio }}</span>
            <span class="text-gray-500">Contacto</span>
            <span>{{ currentOportunity?.folio }}</span>
            <span class="text-gray-500">Sucursal</span>
            <span>{{ currentOportunity?.folio }}</span>
            <span class="text-gray-500">Teléfono</span>
            <span>{{ currentOportunity?.folio }}</span>
        </div>
      </div>
      <!-- ------------- Cita ends 3 ------------- -->
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
data() {
    return{
        clientMonitorSelected: '',
        currentClientMonitor: null,
        tabs: null,
    }
},
components:{
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    Link
},
props:{
    client_monitor: Object,
    client_monitors: Array,
},
methids:{

},
watch: {
    clientMonitorSelected(newVal) {
      this.currentClientMonitor = this.client_monitors.data.find((item) => item.id == newVal);
      this.clientMonitorSelected = this.currentClientMonitor?.id
    },
}
};
</script>

<style>

</style>