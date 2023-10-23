<template>
  <AppLayoutNoHeader title="Oportunidades">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
      <div class="flex justify-between">
        <label class="text-lg">Oportunidades</label>
        <Link :href="route('oportunities.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
        <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>

      <div class="flex justify-between">
        <div class="md:w-1/3 mr-2">
          <el-select v-model="oportunitySelected" clearable filterable placeholder="Buscar oportunidades"
            no-data-text="No hay oportuindades registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in oportunities.data" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </div>

        <div class="flex items-center space-x-2">
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar oportunidades') && tabs == 1"
            content="Editar oportunidad" placement="top">
            <Link :href="route('oportunities.edit', oportunitySelected)">
            <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
              <i class="fa-solid fa-pen text-sm"></i>
            </button>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Crear oportunidades') && tabs == 1"
            content="Crear oportunidad" placement="top">
            <Link :href="route('oportunities.create')">
            <PrimaryButton class="rounded-md">Nueva oportunidad</PrimaryButton>
            </Link>
          </el-tooltip>
          <el-tooltip
            v-if="tabs == 2 && toBool(authUserPermissions[0])"
            content="Crear actividad en la oportunidad" placement="top">
            <Link :href="route('oportunity-tasks.create', oportunitySelected)">
            <PrimaryButton class="rounded-md">Nueva actividad</PrimaryButton>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="tabs == 3" content="Enviar un correo a prospecto" placement="top">
            <Link :href="route('oportunity-tasks.create', oportunitySelected)">
            <PrimaryButton class="rounded-md">Enviar correo</PrimaryButton>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="tabs == 5 && currentOportunity?.finished_at"
            content="Genera la url para la encuesta de satisfacción" placement="top">
            <PrimaryButton @click="generateSurveyUrl" class="rounded-md">Generar url</PrimaryButton>
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
              <DropdownLink :href="route('payment-monitors.create')"
                v-if="tabs == 3 && $page.props.auth.user.permissions.includes('Registrar pagos en seguimiento integral')">
                Registrar Pago
              </DropdownLink>
              <DropdownLink :href="route('meeting-monitors.create')"
                v-if="tabs == 3 && $page.props.auth.user.permissions.includes('Agendar citas en seguimiento integral')">
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
      {{ currentOportunity?.name }}
    </p>


    <!-- ------------- tabs section starts ------------- -->
    <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 overflow-x-auto">
      <div class="flex items-center justify-center">
        <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
          "
          class="h-10 w-40 lg:w-auto p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base text-center">
          Info de oportunidad
        </p>
        <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
        <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
          " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
          Actividades
        </p>
        <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
        <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
          "
          class="md:ml-3 h-10 w-[147px] lg:w-auto p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
          Seguimiento integral
        </p>
        <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
        <p @click="tabs = 4" :class="tabs == 4 ? 'bg-secondary-gray rounded-xl text-primary' : ''
          " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
          Historial
        </p>
        <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
        <p @click="tabs = 5" :class="tabs == 5 ? 'bg-secondary-gray rounded-xl text-primary' : ''
          "
          class="md:ml-3 h-10 w-48 lg:w-auto p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
          Encuesta post venta
        </p>
      </div>
    </div>
    <!-- ------------- tabs section ends ------------- -->

    <!-- ------------- Informacion general Starts 1 ------------- -->
    <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
      <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
        <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>

        <span class="text-gray-500">Folio</span>
        <span>{{ currentOportunity?.folio }}</span>
        <span class="text-gray-500 my-2">Nombre de la oportunidad</span>
        <span>{{ currentOportunity?.name }}</span>
        <span class="text-gray-500 my-2">Descripción</span>
        <span v-html="currentOportunity?.description"></span>
        <span class="text-gray-500 my-2">Creado por</span>
        <span>{{ currentOportunity?.user?.name }}</span>
        <span class="text-gray-500 my-2">Responsable</span>
        <span>{{ currentOportunity?.seller?.name }}</span>
        <!-- <span class="text-gray-500 my-2">Estatus</span>
          <p :class="getStatusStyles()" class="rounded-full px-2 py-1 w-1/2 text-center">{{ currentOportunity?.status }}</p> -->
        <span class="text-gray-500 my-2">Estatus</span>
        <div class="flex items-center space-x-4 relative">
          <!-- <i :class="getColorStatus()" class="fa-solid fa-circle absolute -left-3 top-4"></i> -->
          <el-select @change="status == 'Perdida' ? showLostOportunityModal = true : updateStatus()" class="lg:w-1/2 mt-2"
            v-model="status" clearable filterable placeholder="Seleccionar estatus"
            no-data-text="No hay estatus registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
        </div>
        <span class="text-gray-500 my-2">Probabilidad</span>
        <span>{{ currentOportunity?.probability }}%</span>
        <span class="text-gray-500 my-2">Fecha de inicio</span>
        <span>{{ currentOportunity?.start_date }}</span>
        <span class="text-gray-500 my-2">Fecha estimada de cierre</span>
        <span>{{ currentOportunity?.estimated_finish_date }}</span>
        <span v-if="currentOportunity?.lost_oportunity_razon" class="text-gray-500 my-2">Causa de pérdida</span>
        <span class="bg-red-300 py-1 px-2 rounded-full" v-if="currentOportunity?.lost_oportunity_razon">{{
          currentOportunity?.lost_oportunity_razon }}</span>
      </div>

      <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
        <p class="text-secondary col-span-2 mb-2">Usuarios</p>

        <div v-if="uniqueAsignedNames">
          <span v-for="asignedName in uniqueAsignedNames" :key="asignedName" class="text-gray-500">{{ asignedName
          }}</span>
          <span>{{ currentOportunity?.company_branch }}</span>
        </div>
        <p class="text-sm text-gray-400" v-else><i class="fa-solid fa-user-slash mr-3"></i>No hay tareas asignadas a
          usuarios</p>


        <p class="text-secondary col-span-2 mb-2 mt-5">Archivos adjuntos</p>
        <div v-if="currentOportunity?.media?.length">
          <li v-for="file in currentOportunity?.media" :key="file"
            class="flex items-center justify-between col-span-full">
            <a :href="file.original_url" target="_blank" class="flex items-center">
              <i :class="getFileTypeIcon(file.file_name)"></i>
              <span class="ml-2">{{ file.file_name }}</span>
            </a>
          </li>
        </div>
        <p class="text-sm text-gray-400" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos en
          esta oportunidad</p>



        <p class="text-secondary col-span-full mt-7 mb-2">Etiquetas</p>
        <div class="col-span-full flex space-x-3">
          <Tag v-for="(item, index) in currentOportunity?.tags" :key="index" :name="item.name" :color="item.color" />
        </div>

        <div class="flex items-center justify-end space-x-2 col-span-2 mr-4">
          <el-tooltip content="Agendar reunión" placement="top">
            <i @click="$inertia.get(route('meeting-monitors.create'))"
              class="fa-regular fa-calendar text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
          </el-tooltip>
          <el-tooltip content="Registrar pago" placement="top">
            <i @click="$inertia.get(route('payment-monitors.create'))"
              class="fa-solid fa-money-bill text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
          </el-tooltip>
          <el-tooltip content="Enviar correo" placement="top">
            <i class="fa-regular fa-envelope text-primary cursor-pointer text-lg px-3"></i>
          </el-tooltip>
        </div>

      </div>
    </div>
    <!-- ------------- Informacion general ends 1 ------------- -->

    <!-- -------------tab 2 atividades starts ------------- -->

    <div v-if="tabs == 2" class="contenedor text-left p-4 text-sm">
      <!-- -- TERMINAR HOY -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7 seccion mx-2 ">
        <h2 class="font-bold mb-10">
          TERMINAR HOY <span class="font-normal ml-7">{{ todayTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
          @task-done="markAsDone" class="mb-3" v-for="todayTask in todayTasksList" :key="todayTask"
          :oportunityTask="todayTask" :users="users" />
        <div class="text-center" v-if="!todayTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

      <!-- -- TERMINAR ESTA SEMANA -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
        <h2 class="font-bold mb-10 first-letter ml-2">
          TERMINAR ESTA SEMANA <span class="font-normal ml-7">{{ thisWeekTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
          @task-done="markAsDone" class="mb-3" v-for="thisWeekTask in thisWeekTasksList" :key="thisWeekTask"
          :oportunityTask="thisWeekTask" :users="users" />
        <div class="text-center" v-if="!thisWeekTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

      <!-- -- ACTIVIDADES PROXIMAS -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4  seccion mx-2">
        <h2 class="font-bold mb-10 first-letter ml-2">
          ACTIVIDADES PROXIMAS <span class="font-normal ml-7">{{ nextTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
          @task-done="markAsDone" class="mb-3" v-for="nextTask in nextTasksList" :key="nextTask"
          :oportunityTask="nextTask" :users="users" />
        <div class="text-center" v-if="!nextTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

      <!-- -- TERMINADAS -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
        <h2 class="font-bold mb-10 first-letter ml-2">
          TERMINADAS <span class="font-normal ml-7">{{ finishedTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
          @task-done="markAsDone" class="mb-3" v-for="finishedTask in finishedTasksList" :key="finishedTask"
          :oportunityTask="finishedTask" :users="users" />
        <div class="text-center" v-if="!finishedTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

      <!-- -- ATRASADAS -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
        <h2 class="font-bold mb-10 first-letter ml-2">
          ATRASADAS <span class="font-normal ml-7">{{ lateTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
          @task-done="markAsDone" class="mb-3" v-for="lateTask in lateTasksList" :key="lateTask"
          :oportunityTask="lateTask" :users="users" />
        <div class="text-center" v-if="!lateTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

    </div>
    <!-- ------------- tab 2 atividades ends ------------ -->

    <!-- ------------ tab 3 seguimiento integral starts ------------- -->
    <div v-if="tabs == 3" class="w-11/12 mx-auto my-8">
      <div v-if="currentOportunity?.clientMonitores?.length" class="overflow-x-auto">
        <table class="lg:w-[80%] w-full mx-auto text-sm">
          <thead>
            <tr class="text-center">
              <th class="font-bold pb-5">
                Folio <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Tipo que interacciones <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Fecha <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Concepto <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Vededor <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="monitor in currentOportunity?.clientMonitores" :key="monitor" class="mb-4 hover:bg-[#dfdbdba8]">
              <td class="text-left py-2 px-2 rounded-l-full text-secondary">
                {{ monitor.folio }}
              </td>
              <td class="text-left py-2 px-2">
                <span class="py-1 px-4 rounded-full">{{ monitor.type }}</span>
              </td>
              <td class="text-left py-2 px-2">
                <span class="py-1 px-2 rounded-full">{{ monitor.date }}</span>
              </td>
              <td class="text-left py-2 px-2">
                {{ monitor.concept }}
              </td>
              <td class="text-left py-2 px-2 text-secondary">
                {{ monitor.seller.name }}
              </td>
              <td v-if="$page.props.auth.user.permissions.includes('Eliminar tareas de oportunidades')"
                class="text-left py-2 px-2 rounded-r-full">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537" title="¿Eliminar?"
                  @confirm="deleteClientMonitor(monitor)">
                  <template #reference>
                    <i class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
                  </template>
                </el-popconfirm>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else>
        <p class="text-sm text-center text-gray-400">No hay seguimiento en esta oportunidad</p>
      </div>
    </div>
    <!-- ------------ tab 3 seguimiento integral ends ------------- -->

    <!-- ------------ tab 4 Historial starts ------------- -->
    <div v-if="tabs == 4" class="w-11/12 mx-auto my-8">

    </div>
    <!-- ------------ tab 4 Historial ends ------------- -->

    <!-- ------------ tab 5 Ecuesta post venta starts ------------- -->
    <div v-if="tabs == 5" class="w-11/12 mx-auto my-8">
      <table v-if="currentOportunity?.survey" class="lg:w-[80%] w-full mx-auto text-sm">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-5">
              ID <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              P1 <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              P2 <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              P3 <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              P4 <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">Comentario</th>
          </tr>
        </thead>
        <tbody>
          <tr class="mb-4 hover:bg-[#dfdbdba8]">
            <td class="text-center py-2 px-2 rounded-l-full">
              {{ currentOportunity?.survey?.oportunity_id }}
            </td>
            <td class="text-center py-2 px-2">
              <span class="py-1 px-4 rounded-full">{{ currentOportunity?.survey?.p1 ? 'Sí' : ' No' }}</span>
            </td>
            <td class="text-center py-2 px-2">
              <span class="py-1 px-2 rounded-full">{{ currentOportunity?.survey?.p2 ? 'Sí' : ' No' }}</span>
            </td>
            <td class="text-center py-2 px-2">
              {{ currentOportunity?.survey?.p3 ? 'Sí' : ' No' }}
            </td>
            <td class="text-center py-2 px-2">
              {{ currentOportunity?.survey?.p4 ? 'Sí' : ' No' }}
            </td>
            <td class="text-center py-2 px-2 rounded-r-full">
              {{ currentOportunity?.survey?.p5 }}
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else>
        <p class="text-sm text-center text-gray-400">No se ha contestado la encuesta</p>
      </div>
    </div>
    <!-- ------------ tab 5 Ecuesta post venta ends ------------- -->

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

    <Modal :show="showLostOportunityModal" @close="showLostOportunityModal = false">
      <div class="mx-7 my-4 space-y-4 relative">
        <div>
          <label>Causa oportunidad perdida
            <el-tooltip content="Escribe la causa por la cual se PERDIÓ esta oportunidad" placement="top">
              <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
            </el-tooltip>
          </label>
          <textarea v-model="lost_oportunity_razon" required class="textarea mt-3"></textarea>

        </div>
        <div class="flex justify-end space-x-3 pt-5 pb-1">
          <PrimaryButton @click="updateStatus">Actualizar estatus</PrimaryButton>
        </div>
      </div>
    </Modal>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import OportunityTaskCard from "@/Components/MyComponents/OportunityTaskCard.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Modal from "@/Components/Modal.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    return {
      oportunitySelected: "",
      currentOportunity: null,
      tabs: 1,
      showConfirmModal: false,
      showLostOportunityModal: false,
      status: null,
      lost_oportunity_razon: null,
      todayTasksList: [],
      thisWeekTasksList: [],
      nextTasksList: [],
      finishedTasksList: [],
      lateTasksList: [],
      statuses: [
        {
          label: "Nueva",
          color: "text-[#9A9A9A]",
        },
        {
          label: "Pendiente",
          color: "text-[#F3FD85]",
        },
        {
          label: "En proceso",
          color: "text-[#FEDBBD]",
        },
        {
          label: "Pagado",
          color: "text-[#AFFDB2]",
        },
        {
          label: "Perdida",
          color: "text-[#F7B7FC]",
        },
      ],
    }
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    OportunityTaskCard,
    ConfirmationModal,
    CancelButton,
    Modal,
    Link,
    Tag,
  },
  props: {
    oportunity: Object,
    oportunities: Object,
    users: Array
  },
  methods: {
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    async updateStatus() {
      try {
        const response = await axios.put(route('oportunities.update-status', this.currentOportunity.id), {
          status: this.status,
          lost_oportunity_razon: this.lost_oportunity_razon
        });
        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Se ha actulizado el estatus de la oportunidad",
            type: "success",
          });
          this.showLostOportunityModal = false;
          if (this.lost_oportunity_razon) {
            this.currentOportunity.lost_oportunity_razon = this.lost_oportunity_razon;
            this.lost_oportunity_razon = null;
          } else {
            this.currentOportunity.lost_oportunity_razon = null;
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    getColorStatus() {
      if (this.status == "Nueva") {
        return "text-[#9A9A9A]";
      } else if (this.status == "Pendiente") {
        return "text-[#F3FD85]";
      } else if (this.status == "En proceso") {
        return "text-[#FEDBBD]";
      } else if (this.status == "Pagado") {
        return "text-[#AFFDB2]";
      } else if (this.status == "Perdida") {
        return "text-[#F7B7FC]";
      } else {
        return "text-transparent";
      }
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
    getStatusStyles() {
      if (this.currentOportunity?.status === 'Nueva') {
        return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
      } else if (this.currentOportunity?.status === 'Pendiente') {
        return 'text-[#C88C3C] bg-[#F3FD85]';
      } else if (this.currentOportunity?.status === 'En proceso') {
        return 'text-[#FD8827] bg-[#FEDBBD]';
      } else if (this.currentOportunity?.status === 'Pagado') {
        return 'text-[#37951F] bg-[#ADFEB5]';
      } else if (this.currentOportunity?.status === 'Perdida') {
        return 'text-[#9E0FA9] bg-[#F7B7FC]';
      }
    },
    deleteItem() {
      this.$inertia.delete(route('oportunities.destroy', this.oportunitySelected));
      this.$inertia.get(route('oportunities.index'));
    },
    async markAsDone(data) {
      try {
        const response = await axios.put(route('oportunity-tasks.mark-as-done', data));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Se ha marcado como terminada",
            type: "success",
          });

          this.currentOportunity.oportunityTasks.find(item => item.id === data).finished_at = new Date();
        }
      } catch (error) {
        console.log(error);
      }

    },
    async deleteTask(data) {
      try {
        const response = await axios.delete(route('oportunity-tasks.destroy', data));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Se ha eliminado correctamente",
            type: "success",
          });

          const index = this.currentOportunity.oportunityTasks.findIndex(item => item.id === data);

          if (index !== -1) {
            this.currentOportunity.oportunityTasks.splice(index, 1);
          }
        }
      } catch (error) {
        console.log(error);
      }

    },
    updateOportunityTask(task) {
      const index = this.currentOportunity.oportunityTasks.findIndex(item => item.id === task.id);

      if (index !== -1) {
        this.currentOportunity.oportunityTasks[index] = task;
      }
    },
    async deleteClientMonitor(monitor) {
      try {
        const response = await axios.delete(route('client-monitors.destroy', monitor.id));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Se ha eliminado correctamente",
            type: "success",
          });
          const index = this.currentOportunity.clientMonitores.findIndex(item => item.id === monitor.id);

          if (index !== -1) {
            this.currentOportunity.clientMonitores.splice(index, 1);
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    generateSurveyUrl() {
      alert('http://127.0.0.1:8000/surveys/create/' + this.currentOportunity.id);
      // console.log('http://127.0.0.1:8000/surveys/create/' + this.currentOportunity.id);
    },
  },
  watch: {
    oportunitySelected(newVal) {
      this.currentOportunity = this.oportunities.data.find((item) => item.id == newVal);
      this.oportunitySelected = this.currentOportunity?.id
    },
  },
  mounted() {
    this.oportunitySelected = this.oportunity.id;
    this.currentOportunity = this.oportunities.data.find(
      (item) => item.id == this.oportunitySelected
    );
    this.status = this.currentOportunity?.status;
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
    todayTasksList() {
      return this.todayTasksList = this.currentOportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Terminar hoy' && !oportunity.finished_at);
    },
    thisWeekTasksList() {
      return this.thisWeekTasksList = this.currentOportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Esta semana' && !oportunity.finished_at);
    },
    nextTasksList() {
      return this.nextTasksList = this.currentOportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Proximas' && !oportunity.finished_at);
    },
    finishedTasksList() {
      return this.finishedTasksList = this.currentOportunity.oportunityTasks.filter(oportunity => oportunity.finished_at);
    },
    lateTasksList() {
      return this.lateTasksList = this.currentOportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Atrasadas' && !oportunity.finished_at);
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
  flex: 0 0 25%;
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