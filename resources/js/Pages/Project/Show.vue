<template>
  <AppLayoutNoHeader title="Detalles de proyecto">
    <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
      <span>Proyectos</span>
      <Link :href="route('projects.index')"
        class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
      <i class="fa-solid fa-xmark"></i>
      </Link>
    </div>

    <div class="flex justify-between mt-5 mx-2 lg:mx-14">
      <div class="md:w-1/3 mr-2">
        <el-select @change="$inertia.get(route('projects.show', selectedProject))" v-model="selectedProject" clearable
          filterable placeholder="Buscar proyecto" no-data-text="No hay proyectos registrados"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in projects" :key="item.id" :label="item.project_name" :value="item.id" />
        </el-select>
      </div>
      <div v-if="activeTab == '1'" class="flex space-x-2 w-full justify-end">
        <PrimaryButton @click="$inertia.get(route('projects.create'))" class="!rounded-[10px]">Nuevo proyecto
        </PrimaryButton>
        <button @click="$inertia.get(route('projects.edit', currentProject?.id ?? 1))"
          class="w-9 h-9 rounded-[10px] bg-[#D9D9D9]">
          <i class="fa-solid fa-pen text-sm"></i>
        </button>
      </div>
      <div v-if="(activeTab == '2' || activeTab == '3') && toBool(authUserPermissions[0])" class="flex space-x-2 w-full justify-end">
        <PrimaryButton @click="$inertia.get(route('tasks.create', { projectId: currentProject?.id ?? 1 }))"
          class="!rounded-[10px]">Nueva tarea
        </PrimaryButton>
      </div>
    </div>

    <!-- --------------project title--------------------------- -->
    <div class="text-center font-bold lg:text-lg mb-4 flex justify-center items-center mt-5 mx-2">
      <p>{{ currentProject?.project_name }}</p>
      <div class="flex items-center ml-5">
        <div v-for="(user, index) in currentProject?.users" :key="index">
          <el-tooltip :content="user.name" placement="top">
            <div v-if="index < 3" class="flex text-sm rounded-full w-8">
              <img class="h-7 w-7 rounded-full border border-[#cccccc] object-cover" :src="user.profile_photo_url"
                :alt="user.name" />
            </div>
          </el-tooltip>
        </div>
        <el-tooltip placement="top">
          <template #content>
            <li v-for="(user, index) in currentProject?.users.filter((item, index) => index >= 3)" :key="index"
              class="ml-2 text-xs">
              {{ user.name }}
            </li>
          </template>
          <span v-if="currentProject?.users.length > 3" class="ml-1 text-primary text-sm">
            +{{ (currentProject?.users.length - 3) }}
          </span>
        </el-tooltip>
      </div>
    </div>

    <el-tabs v-if="currentProject" v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
      <el-tab-pane label=" Información del proyecto" name="1">
        <General :project="currentProject" />
      </el-tab-pane>
      <el-tab-pane label="Tareas" name="2">
        <Tasks :project="currentProject" />
      </el-tab-pane>
      <el-tab-pane label="Cronograma" name="3">
        <Gantt :project="currentProject" />
      </el-tab-pane>
    </el-tabs>
    <!-- ------------- activeTab section starts ------------- -->
    <!-- <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
      <div class="flex">
        <p @click="activeTab = 1" :class="activeTab == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''"
          class="h-10 p-2 cursor-pointer ml-5 transition duration-300 ease-in-out text-xs md:text-base">
          Información del proyecto
        </p>
        <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
        <p @click="activeTab = 2" :class="activeTab == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''"
          class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
          Tareas
        </p>
        <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
        <p @click="activeTab = 3" :class="activeTab == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''"
          class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-xs md:text-base">
          Cronograma
        </p>
      </div>
    </div> -->
    <!-- ------------- activeTab section ends ------------- -->

    <!-- ------------- Tasks Starts 2 ------------- -->
    <!-- <div v-if="activeTab == 2" class="md:grid grid-cols-3 text-left p-4 text-sm">
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7">
        <h2 class="font-bold mb-10">
          POR HACER <span class="font-normal ml-7">{{ pendingTasksList?.length }}</span>
        </h2>
        <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="pendingTasksList"
          :animation="300" item-key="id" tag="ul" group="tasks" id="pendent"
          :class="(drag && !pendingTasksList?.length) ? 'h-40' : ''">
          <template #item="{ element: task }">
            <li>
              <ProjectTaskCard @delete-task="deleteProjectTask" @updated-status="updateTask($event)"
                :taskComponent="task" :users="currentProject?.users" :id="task.id" />
            </li>
          </template>
        </draggable>
        <div class="text-center" v-if="!pendingTasksList?.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-7">
        <h2 class="font-bold mb-10">
          EN CURSO <span class="font-normal ml-7">{{ inProgressTasksList?.length }}</span>
        </h2>
        <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="inProgressTasksList"
          :animation="300" item-key="id" tag="ul" group="tasks" id="process"
          :class="(drag && !inProgressTasksList?.length) ? 'h-40' : ''">
          <template #item="{ element: task }">
            <li>
              <ProjectTaskCard @delete-task="deleteProjectTask" @updated-status="updateTask($event)"
                :taskComponent="task" :users="currentProject?.users" />
            </li>
          </template>
        </draggable>
        <div class="text-center" v-if="!inProgressTasksList?.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

      <div class="h-auto lg:px-7">
        <h2 class="font-bold mb-10">
          TERMINADA <span class="font-normal ml-7">{{ finishedTasksList?.length }}</span>
        </h2>
        <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="finishedTasksList"
          :animation="300" item-key="id" tag="ul" group="tasks" id="finished"
          :class="(drag && !finishedTasksList?.length) ? 'h-40' : ''">
          <template #item="{ element: task }">
            <li>
              <ProjectTaskCard @delete-task="deleteProjectTask" @updated-status="updateTask($event)"
                :taskComponent="task" :users="currentProject?.users" />
            </li>
          </template>
        </draggable>
        <div class="text-center" v-if="!finishedTasksList?.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>
    </div> -->
    <!-- ------------- Tasks ends 2 ------------- -->

    <!-- ------------- Cronograma Starts 3 ------------- -->
    <!-- <div v-if="activeTab == 3" class="text-left text-sm items-center">
      <GanttDiagramMonth v-if="period === 'Mes'" :currentProject="currentProject" :currentDate="currentDate" />

      <GanttDiagramBimester v-if="period === 'Bimestre'" :currentProject="currentProject" :currentDate="currentDate" />
      <div class="text-right mr-9">
        <div class="border border-[#9A9A9A] rounded-md inline-flex justify-end mt-4">
          <p :class="period == 'Mes' ? 'bg-primary text-white rounded-sm' : 'border-[#9A9A9A]'
            " @click="period = 'Mes'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer">
            Mes
          </p>
          <p :class="period == 'Bimestre'
            ? 'bg-primary text-white rounded-sm'
            : 'border-[#9A9A9A]'
            " @click="period = 'Bimestre'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x border-transparent">
            Bimestre
          </p>
        </div>
      </div>
    </div> -->
    <!-- ------------- Cronograma ends 3 ------------- -->
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ProjectTaskCard from "@/Components/MyComponents/ProjectTaskCard.vue";
import GanttDiagramMonth from "@/Components/MyComponents/GanttDiagramMonth.vue";
import GanttDiagramBimester from "@/Components/MyComponents/GanttDiagramBimester.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Tag from "@/Components/MyComponents/Tag.vue";
import { Link } from "@inertiajs/vue3";
import draggable from 'vuedraggable';
import axios from 'axios';
import General from "./Tabs/General.vue";
import Tasks from "./Tabs/Tasks.vue";
import Gantt from "./Tabs/Gantt.vue";

export default {
  data() {
    return {
      selectedProyect: "",
      activeTab: '1',
      selectedProject: "",
      currentProject: null,
      // period: "Mes", //period of time in cronograma table tab 3
      // pendingTasksList: [],
      // inProgressTasksList: [],
      // finishedTasksList: [],
      // drag: false,
      // draggingTaskId: null,
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    ProjectTaskCard,
    Dropdown,
    DropdownLink,
    Checkbox,
    draggable,
    GanttDiagramMonth,
    GanttDiagramBimester,
    Modal,
    Link,
    Tag,
    General,
    Tasks,
    Gantt,
  },
  props: {
    projects: Object,
    project: Object,
    users: Array,
    defaultTab: Number,
  },
  computed: {
    authUserPermissions() {
      const permissions = this.currentProject?.users.find(item => item.id == this.$page.props.auth.user.id)?.pivot.permissions;
      if (permissions) {
        return JSON.parse(permissions);
      } else {
        return [0, 0, 0, 0, 0];
      }
    }
  },
  methods: {
    handleClickInTab(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    toBool(value) {
      if (value == 1 || value == true) return true;
      return false;
    },
    // handleStartDrag(evt) {
    //   this.draggingTaskId = evt.item.__draggable_context.element.id;
    //   this.drag = true;
    // },
    // handleAddDrag(evt) {
    //   let status = 'Terminada';
    //   if (evt.to.id === 'pendent') {
    //     status = 'Por hacer';
    //   } else if (evt.to.id === 'process') {
    //     status = 'En curso';
    //   }

    //   this.drag = false;
    //   this.updateTaskStatus(status);
    // },
    // async updateTaskStatus(status) {
    //   try {
    //     const response = await axios.put(route('tasks.update-status', this.draggingTaskId), { status: status });

    //     if (response.status === 200) {
    //       const taskIndex = this.currentProject.tasks.findIndex(item => item.id === this.draggingTaskId);
    //       this.currentProject.tasks[taskIndex].status = status;
    //       console.log(this.currentProject.tasks[taskIndex]);
    //     }
    //   } catch (error) {
    //     console.log(error);
    //   }
    // },
    // updateTask(task) {
    //   const taskIndex = this.currentProject.tasks.findIndex(
    //     (item) => item.id === task.id
    //   );

    //   if (taskIndex !== -1) {
    //     this.currentProject.tasks[taskIndex] = task;
    //   }

    //   this.updateTasksLists();
    // },
    // pendingTasks() {
    //   this.pendingTasksList = this.currentProject?.tasks.filter((task) => task.status === "Por hacer");
    // },
    // inProgressTasks() {
    //   this.inProgressTasksList = this.currentProject?.tasks.filter((task) => task.status === "En curso");
    // },
    // finishedTasks() {
    //   this.finishedTasksList = this.currentProject?.tasks.filter((task) => task.status === "Terminada");
    // },
    // updateTasksLists() {
    //   this.pendingTasks();
    //   this.inProgressTasks();
    //   this.finishedTasks();
    // },
    setTabInUrl() {
      // Obtener la URL actual
      const currentURL = new URL(window.location.href);
      // Extraer el valor de 'currentTab' de los parámetros de búsqueda
      const currentTabFromURL = currentURL.searchParams.get('currentTab');

      if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
      }
    },
    // async deleteProjectTask(data) {
    //   try {
    //     const response = await axios.delete(route('tasks.destroy', data));

    //     if (response.status === 200) {
    //       this.$notify({
    //         title: "Éxito",
    //         message: "Se ha eliminado correctamente",
    //         type: "success",
    //       });

    //       const index = this.currentProject.tasks.findIndex(item => item.id === data);

    //       if (index !== -1) {
    //         this.currentProject.tasks.splice(index, 1);
    //       }
    //       this.updateTasksLists();
    //     }
    //   } catch (error) {
    //     console.log(error);
    //   }

    // },
  },
  // watch: {
  //   selectedProject(newVal) {
  //     this.currentProject = this.projects.find((item) => item.id == newVal);
  //     this.uniqueUsers = [];
  //     this.updateTasksLists();

  //     // Verificar si hay tareas en el proyecto y si la primera tarea tiene una fecha de inicio
  //   if (this.currentProject && this.currentProject.tasks.length > 0) {
  //     const firstTask = this.currentProject.tasks[0];
  //     if (firstTask && firstTask.start_date) {
  //       this.currentDate = new Date(firstTask.start_date_raw);
  //     }
  //   }
  // },
  // },
  mounted() {
    this.selectedProject = this.project.data.id;
    this.currentProject = this.project.data;
    // this.uniqueUsers = [];
    // this.updateTasksLists();
    // if (this.currentProject && this.currentProject.tasks.length > 0) {
    //   const firstTask = this.currentProject.tasks[0];
    //   if (firstTask && firstTask.start_date) {
    //     this.currentDate = new Date(firstTask.start_date_raw);
    //   }
    // }
    // if (this.defaultTab != null) {
    //   this.activeTab = parseInt(this.defaultTab);
    // }
    this.setTabInUrl();
  },
};
</script>
