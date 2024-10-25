<template>
  <AppLayoutNoHeader title="Detalles de proyecto">
    <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
      <span>Proyectos</span>
      <Link :href="route('projects.index')"
        class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
      <i class="fa-solid fa-xmark"></i>
      </Link>
    </div>

    <div class="flex justify-between mt-5 mx-2 lg:mx-14">
      <div class="md:w-1/3 mr-2">
        <el-select @change="$inertia.get(route('projects.show', selectedProject))" v-model="selectedProject"
          filterable placeholder="Buscar proyecto" no-data-text="No hay proyectos registrados"
          no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in projects" :key="item.id" :label="item.project_name" :value="item.id" />
        </el-select>
      </div>
      <div v-if="activeTab == '1'" class="flex space-x-2 w-full justify-end">
        <PrimaryButton @click="$inertia.get(route('projects.create'))" class="!rounded-[10px]">Nuevo proyecto</PrimaryButton>
        <button @click="$inertia.get(route('projects.edit', currentProject?.id ?? 1))"
          class="size-9 flex items-center justify-center rounded-lg bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-5">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
          </svg>
        </button>
      </div>
      <div v-if="(activeTab == '2' || activeTab == '3') && toBool(authUserPermissions[0])" class="flex space-x-2 w-full justify-end">
        <PrimaryButton @click="$inertia.get(route('tasks.create', { projectId: currentProject?.id ?? 1 }))"
          class="!rounded-[10px]">Nueva tarea
        </PrimaryButton>
      </div>
    </div>

    <!-- --------------project title--------------------------- -->
    <div class="text-center font-bold lg:text-lg mb-4 flex justify-center items-center mt-5 mx-2 dark:text-white">
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
import draggable from 'vuedraggable';
import General from "./Tabs/General.vue";
import Tasks from "./Tabs/Tasks.vue";
import Gantt from "./Tabs/Gantt.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    return {
      selectedProyect: "",
      activeTab: '1',
      selectedProject: "",
      currentProject: null,
    };
  },
  components: {
    GanttDiagramBimester,
    GanttDiagramMonth,
    AppLayoutNoHeader,
    ProjectTaskCard,
    PrimaryButton,
    DropdownLink,
    draggable,
    Dropdown,
    Checkbox,
    General,
    Modal,
    Tasks,
    Gantt,
    Link,
    Tag,
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
    setTabInUrl() {
      // Obtener la URL actual
      const currentURL = new URL(window.location.href);
      // Extraer el valor de 'currentTab' de los parámetros de búsqueda
      const currentTabFromURL = currentURL.searchParams.get('currentTab');

      if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
      }
    },
  },
  mounted() {
    this.selectedProject = this.project.data.id;
    this.currentProject = this.project.data;
    this.setTabInUrl();
  },
};
</script>
