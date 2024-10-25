<template>
  <AppLayoutNoHeader title="Oportunidades">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1 dark:text-white">
      <div class="flex justify-between">
        <label class="text-lg">Oportunidades</label>
        <Link :href="route('oportunities.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
        <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>
      <div class="flex justify-between">
        <div class="md:w-1/3 mr-2">
          <el-select @change="$inertia.get(route('oportunities.show', oportunitySelected))" v-model="oportunitySelected"
            clearable filterable placeholder="Buscar oportunidades" no-data-text="No hay oportuindades registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in oportunities" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </div>
        <div class="flex items-center space-x-2">
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar oportunidades') && activeTab == 1"
            content="Editar oportunidad" placement="top">
            <Link :href="route('oportunities.edit', oportunitySelected)">
            <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
            </button>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Crear oportunidades') && activeTab == 1"
            content="Crear oportunidad" placement="top">
            <Link :href="route('oportunities.create')">
            <PrimaryButton class="rounded-md">Nueva oportunidad</PrimaryButton>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="activeTab == 2 && toBool(authUserPermissions[0])"
            content="Crear actividad en la oportunidad" placement="top">
            <Link :href="route('oportunity-tasks.create', oportunitySelected)">
            <PrimaryButton class="rounded-md">Nueva actividad</PrimaryButton>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="activeTab == 3" content="Enviar un correo a prospecto" placement="top">
            <Link :href="route('email-monitors.create', { opportunityId: currentOportunity?.id })">
            <PrimaryButton class="rounded-md">Interacción por correo</PrimaryButton>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="activeTab == 5 && currentOportunity?.finished_at"
            content="Genera la url para la encuesta de satisfacción" placement="top">
            <PrimaryButton @click="generateSurveyUrl" class="rounded-md">Generar url</PrimaryButton>
          </el-tooltip>

          <Dropdown align="right" width="48"
            v-if="activeTab == 3 && $page.props.auth.user.permissions.includes('Crear oportunidades') && $page.props.auth.user.permissions.includes('Eliminar oportunidades')">
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center justify-center text-sm">
                Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink :href="route('payment-monitors.create', { opportunityId: currentOportunity?.id })"
                v-if="$page.props.auth.user.permissions.includes('Registrar pagos en seguimiento integral')">
                Registrar Pago
              </DropdownLink>
              <DropdownLink :href="route('meeting-monitors.create', { opportunityId: currentOportunity?.id })"
                v-if="$page.props.auth.user.permissions.includes('Agendar citas en seguimiento integral')">
                Agendar Cita
              </DropdownLink>
              <DropdownLink :href="route('whatsapp-monitors.create', { opportunityId: currentOportunity?.id })"
                v-if="$page.props.auth.user.permissions.includes('Registrar interaccion whatsapp en seguimiento integral')">
                Interacción WhatsApp
              </DropdownLink>
              <DropdownLink :href="route('call-monitors.create', { opportunityId: currentOportunity?.id })"
                v-if="$page.props.auth.user.permissions.includes('Registrar llamada en seguimiento integral')">
                Registrar llamada
              </DropdownLink>
              <!-- <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades') && activeTab == 1 && toBool(authUserPermissions[3])
                " @click="showConfirmModal = true" as="button">
                Eliminar
              </DropdownLink> -->
            </template>
          </Dropdown>
        </div>
      </div>
      <div class="flex items-center justify-center space-x-5 mb-4">
        <p class="text-center font-bold text-lg">
          {{ currentOportunity?.folio }} - {{ currentOportunity?.name }}
        </p>
        <p :class="getColorStatus()" class="px-2 py-1 font-bold rounded-sm">
          {{ currentOportunity?.status }}
        </p>
      </div>
      <!-- ------------- tabs section starts ------------- -->
      <el-tabs v-if="currentOportunity" v-model="activeTab" class="mt-3" @tab-click="handleClickInTab">
        <el-tab-pane label="Info de oportunidad" name="1">
          <General :opportunity="currentOportunity" />
        </el-tab-pane>
        <el-tab-pane label="Actividades" name="2">
          <Activities :opportunity="currentOportunity" />
        </el-tab-pane>
        <el-tab-pane label="Seguimiento integral" name="3">
          <Monitor :opportunity="currentOportunity" />
        </el-tab-pane>
        <el-tab-pane label="Encuesta post venta" name="5">
          <Survey :opportunity="currentOportunity" />
        </el-tab-pane>
      </el-tabs>
    </div>

    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title> Eliminar oportunidad </template>
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
import Modal from "@/Components/Modal.vue";
import { Link } from "@inertiajs/vue3";
import General from "./Tabs/General.vue";
import Activities from "./Tabs/Activities.vue";
import Monitor from "./Tabs/Monitor.vue";
import Survey from "./Tabs/Survey.vue";

export default {
  data() {
    return {
      oportunitySelected: "",
      currentOportunity: null,
      activeTab: '1',
      showConfirmModal: false,
      showLostOportunityModal: false,
      showCreateSaleModal: false,
      status: null,
      lost_oportunity_razon: null,
    }
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    ConfirmationModal,
    CancelButton,
    Modal,
    Link,
    General,
    Activities,
    Monitor,
    Survey,
  },
  props: {
    oportunity: Object,
    oportunities: Object,
    users: Array,
  },
  methods: {
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    getColorStatus() {
      if (this.currentOportunity?.status == "Nueva") {
        return "bg-gray-300 text-[#97989A]";
      } else if (this.currentOportunity?.status == "Pendiente") {
        return "bg-[#F3FD85] text-[#C88C3C]";
      } else if (this.currentOportunity?.status == "Cerrada") {
        return "bg-[#FEDBBD] text-[#FD8827]";
      } else if (this.currentOportunity?.status == "Pagado") {
        return "bg-[#AFFDB2] text-[#37951F]";
      } else if (this.currentOportunity?.status == "Perdida") {
        return "bg-[#F7B7FC] text-[#9E0FA9]";
      } else {
        return "bg-transparent";
      }
    },
    generateSurveyUrl() {
      alert('http://127.0.0.1:8000/surveys/create/' + this.currentOportunity.id);
      // console.log('http://127.0.0.1:8000/surveys/create/' + this.currentOportunity.id);
    },
    handleClickInTab(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    setTabFromUrl() {
      // Obtener la URL actual
      const currentURL = new URL(window.location.href);
      // Extraer el valor de 'currentTab' de los parámetros de búsqueda
      const currentTabFromURL = currentURL.searchParams.get('currentTab');

      if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
      }
    },
    async deleteItem() {
      await this.$inertia.delete(route('oportunities.destroy', this.oportunitySelected));
      window.location.reload();
    },
  },
  mounted() {
    this.oportunitySelected = this.oportunity.data.id;
    this.currentOportunity = this.oportunity.data;
    this.status = this.oportunity?.data.status;

    this.setTabFromUrl();
  },
  computed: {
    authUserPermissions() {
      const permissions = this.currentOportunity?.users.find(item => item.id == this.$page.props.auth.user.id)?.pivot.permissions;
      if (permissions) {
        return JSON.parse(permissions);
      } else {
        return [0, 0, 0, 0, 0];
      }
    },
    uniqueAsignedNames() {
      const asignedNamesSet = new Set(); // Usamos un Set para nombres únicos.

      if (this.currentOportunity?.oportunityTasks.length) {

        // Recorremos las tareas y agregamos los nombres de los asignados al conjunto.
        this.currentOportunity?.oportunityTasks?.forEach((task) => {
          asignedNamesSet.add(task.asigned.name);
        });

        // Convertimos el conjunto a un array para mostrarlo en la plantilla.
        return Array.from(asignedNamesSet);
      }
    },
  },
};
</script>

<style>
.contenedor {
  display: flex;
  overflow-x: scroll;
  /* Permite el desplazamiento horizontal */
  white-space: nowrap;
  /* Evita el salto de línea de las secciones */
}

.seccion {
  flex: 0 0 22%;
  /* Establece el ancho de cada sección al 25% */
}

.contenedor::-webkit-scrollbar {
  width: 4px;
  /* Ancho de la barra de desplazamiento */
}

.contenedor::-webkit-scrollbar-thumb {
  background-color: #ccc;
  /* Color de la barra de desplazamiento */
  border-radius: 5px;
  /* Radio de los bordes de la barra de desplazamiento */
}
</style>