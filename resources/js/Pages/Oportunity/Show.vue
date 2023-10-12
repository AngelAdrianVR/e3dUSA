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
            <el-select v-model="oportunitySelected" clearable filterable
              placeholder="Buscar oportunidades" no-data-text="No hay oportuindades registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in oportunities.data" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>

          <div class="flex items-center space-x-2">
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar oportunidades') && tabs == 1" content="Editar oportunidad" placement="top">
              <Link :href="route('oportunities.edit', oportunitySelected)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <el-tooltip
            v-if="$page.props.auth.user.permissions.includes('Crear oportunidades') && tabs == 1"
            content="Crear oportunidad"
            placement="top"
          >
            <Link :href="route('oportunities.create')">
              <PrimaryButton class="rounded-md">Nueva oportunidad</PrimaryButton>
            </Link>
          </el-tooltip>
            <el-tooltip
            v-if="$page.props.auth.user.permissions.includes('Crear tareas de oportunidades') && tabs == 2"
            content="Crear actividad en la oportunidad"
            placement="top"
          >
            <Link :href="route('oportunity-tasks.create', oportunitySelected)">
              <PrimaryButton class="rounded-md">Nueva actividad</PrimaryButton>
            </Link>
          </el-tooltip>
            <el-tooltip
            v-if="tabs == 3"
            content="Enviar un correo a prospecto"
            placement="top"
          >
            <Link :href="route('oportunity-tasks.create', oportunitySelected)">
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
                <DropdownLink v-if="tabs == 3">
                  Registrar cita
                </DropdownLink>
                <DropdownLink v-if="tabs == 3">
                  Registrar pago
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
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Info de oportunidad
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Actividades
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Seguimiento integral
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 4" :class="tabs == 4 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Historial
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
          <span>{{ currentOportunity?.description }}</span>
          <span class="text-gray-500 my-2">Creado por</span>
          <span>{{ currentOportunity?.user?.name }}</span>
          <span class="text-gray-500 my-2">Estatus</span>
          <p :class="getStatusStyles()" class="rounded-full px-2 py-1 w-1/2 text-center">{{ currentOportunity?.status }}</p>
          <span class="text-gray-500 my-2">Probabilidad</span>
          <span>{{ currentOportunity?.probability }}%</span>
          <span class="text-gray-500 my-2">Fecha de inicio</span>
          <span>{{ currentOportunity?.start_date }}</span>
          <span class="text-gray-500 my-2">Fecha estimada de cierre</span>
          <span>{{ currentOportunity?.estimated_finish_date }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Usuarios</p>

          <span v-for="asignedName in uniqueAsignedNames" :key="asignedName" class="text-gray-500">{{ asignedName }}</span>
          <span>{{ currentOportunity?.company_branch }}</span>

          <p class="text-secondary col-span-2 mt-7">Documentos adjuntos</p>

          <span class="text-gray-500 my-2">Nombre</span>
          <span>{{ currentOportunity?.contact?.name }}</span>

          <div class="flex items-center justify-end space-x-2 col-span-2 mr-4">
            <el-tooltip content="Agendar reunión" placement="top">
              <i class="fa-regular fa-calendar text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
            </el-tooltip>
            <el-tooltip content="Registrar pago" placement="top">
              <i class="fa-solid fa-money-bill text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
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
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7 seccion">
        <h2 class="font-bold mb-10">
          TERMINAR HOY <span class="font-normal ml-7">{{ todayTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask" @task-done="markAsDone" class="mb-3" v-for="todayTask in todayTasksList" :key="todayTask" :oportunityTask="todayTask" :users="users" />
        <div class="text-center" v-if="!todayTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>
      
      <!-- -- TERMINAR ESTA SEMANA -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion">
        <h2 class="font-bold mb-10 first-letter ml-2">
          TERMINAR ESTA SEMANA <span class="font-normal ml-7">{{ thisWeekTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask" @task-done="markAsDone" class="mb-3" v-for="thisWeekTask in thisWeekTasksList" :key="thisWeekTask" :oportunityTask="thisWeekTask" :users="users" />
        <div class="text-center" v-if="!thisWeekTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>
      
      <!-- -- ACTIVIDADES PROXIMAS -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4  seccion">
        <h2 class="font-bold mb-10 first-letter ml-2">
          ACTIVIDADES PROXIMAS <span class="font-normal ml-7">{{ nextTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask" @task-done="markAsDone" class="mb-3" v-for="nextTask in nextTasksList" :key="nextTask" :oportunityTask="nextTask" :users="users" />
        <div class="text-center" v-if="!nextTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>
      
      <!-- -- TERMINADAS -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion">
        <h2 class="font-bold mb-10 first-letter ml-2">
          TERMINADAS <span class="font-normal ml-7">{{ finishedTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask" @task-done="markAsDone" class="mb-3" v-for="finishedTask in finishedTasksList" :key="finishedTask" :oportunityTask="finishedTask" :users="users" />
        <div class="text-center" v-if="!finishedTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>
      
      <!-- -- ATRASADAS -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion">
        <h2 class="font-bold mb-10 first-letter ml-2">
          ATRASADAS <span class="font-normal ml-7">{{ lateTasksList.length }}</span>
        </h2>
        <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask" @task-done="markAsDone" class="mb-3" v-for="lateTask in lateTasksList" :key="lateTask" :oportunityTask="lateTask" :users="users" />
        <div class="text-center" v-if="!lateTasksList.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

    </div>
      <!-- ------------- tab 2 atividades ends ------------ -->

      <!-- ------------ tab 3 seguimiento integral starts ------------- -->
      <div v-if="tabs == 3" class="w-11/12 mx-auto my-16">
      <table class="lg:w-[80%] w-full mx-auto">
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
          <tr v-for="monitor in currentOportunity?.clientMonitores" :key="monitor"
            class="mb-4 hover:bg-[#dfdbdba8]"
          >
            <td class="text-left py-2 px-2 rounded-l-full text-secondary">
              {{ monitor.folio }}
            </td>
            <td class="text-left py-2 px-2">
              <span
                class="py-1 px-4 rounded-full"
                >{{ monitor.type }}</span
              >
            </td>
            <td class="text-left py-2 px-2">
              <span
                class="py-1 px-2 rounded-full"
                >{{ monitor.date }}</span
              >
            </td>
            <td class="text-left py-2 px-2">
              {{ monitor.concept }}
            </td>
            <td class="text-left py-2 px-2 text-secondary">
              {{ monitor.seller.name }}
            </td>
            <td
              v-if="$page.props.auth.user.permissions.includes('Eliminar tareas de oportunidades')"
              class="text-left py-2 px-2 rounded-r-full"
            >
              <el-popconfirm
                confirm-button-text="Si"
                cancel-button-text="No"
                icon-color="#D90537"
                title="¿Eliminar?"
                @confirm="deleteClientMonitor(monitor)"
              >
                <template #reference>
                  <i class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
                </template>
              </el-popconfirm>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
      <!-- ------------ tab 3 seguimiento integral ends ------------- -->

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
import OportunityTaskCard from "@/Components/MyComponents/OportunityTaskCard.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
 data(){
    return{
    oportunitySelected: "",
    currentOportunity: null,
    tabs: 1,
    showConfirmModal: false,
    todayTasksList: [],
    thisWeekTasksList: [],
    nextTasksList: [],
    finishedTasksList: [],
    lateTasksList: [],
    }
 },
 components:{
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    OportunityTaskCard,
    ConfirmationModal,
    CancelButton,
    Link,
 },
 props:{
    oportunity: Object,
    oportunities: Object,
    users: Array
 },
 methods:{
    getStatusStyles(){
        if (this.currentOportunity?.status === 'Nueva') {
            return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
        } else if (this.currentOportunity?.status === 'Pendiente') {
             return 'text-[#C88C3C] bg-[#F3FD85]';
        } else if (this.currentOportunity?.status === 'En progreso') {
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
   updateOportunityTask(data) {
          const index = this.currentOportunity.oportunityTasks.findIndex(item => item.id === data);

          if (index !== -1) {
            this.currentOportunity.oportunityTasks[index] = response.data.item;
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
  },
  computed: {
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
  overflow-x: scroll; /* Permite el desplazamiento horizontal */
  white-space: nowrap; /* Evita el salto de línea de las secciones */
}

.seccion {
  flex: 0 0 25%; /* Establece el ancho de cada sección al 25% */
}

  .contenedor::-webkit-scrollbar {
    width: 4px; /* Ancho de la barra de desplazamiento */
  }

  .contenedor::-webkit-scrollbar-thumb {
    background-color: #ccc; /* Color de la barra de desplazamiento */
    border-radius: 5px; /* Radio de los bordes de la barra de desplazamiento */
  }
</style>