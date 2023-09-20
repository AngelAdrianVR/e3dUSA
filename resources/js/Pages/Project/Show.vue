<template>
  <AppLayoutNoHeader title="Detalles de proyecto |">
    <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Proyectos</span>
        <Link :href="route('projects.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
    </div>

    <div class="flex justify-between mt-5 mx-14">
        <div class="md:w-1/3 mr-2">
          <el-select v-model="selectedProject" clearable filterable placeholder="Buscar proyecto"
            no-data-text="No hay proyectos registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in projects.data" :key="item.id" :label="item.project_name" :value="item.id" />
          </el-select>
        </div>
        <div>
          <PrimaryButton @click="$inertia.get(route('projects.create'))">+ Agregar tarea</PrimaryButton>
        </div>
      </div>

<!-- --------------project title--------------------------- -->
      <div class="text-center font-bold text-lg mb-4 flex justify-center items-center">
        <p>{{currentProject?.project_name }}</p>
        <div class="flex items-center ml-24">
            <figure v-for="user in uniqueUsers.slice(0, maxUsersToShow)" :key="user.id">
          <el-tooltip :content="user.name" placement="top">
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                    class="flex text-sm items-center rounded-full">
                    <img class="h-12 w-12 rounded-full object-cover" :src="user.profile_photo_url"
                    :alt="user.name" />
                </div>
          </el-tooltip>
            </figure>
            <el-tooltip
              v-if="remainingUsersCount > 0"
              :content="userNames.join(', ')"
              placement="top"
            >
              <div
                class="rounded-full w-10 h-10 bg-[#D9D9D9] flex items-center justify-center text-primary text-sm cursor-default"
              >
                +{{ remainingUsersCount }}
              </div>
            </el-tooltip>
        </div>
      </div>


      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Información del proyecto
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Tareas
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Cronograma
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->


    <!-- ------------- Tasks Starts 2 ------------- -->
      <div v-if="tabs == 2" class="md:grid grid-cols-3 text-left p-4  text-sm items-center">
      <!-- -- Por hacer -- -->
        <div class="border-r border-[#9A9A9A] h-56 pr-7">
            <h2 class="font-bold mb-10">POR HACER <span class="font-normal ml-7">{{ pedingTasks.length }}</span></h2>
            <ProjectTaskCard v-for="task in pedingTasks" :key="task" :taskComponent="task" />
        </div>

        <!-- -- En curso -- -->
        <div class="border-r border-[#9A9A9A] h-56 px-7">
            <h2 class="font-bold mb-10">EN CURSO <span class="font-normal ml-7">{{ inProgressTasks.length }}</span></h2>
            <ProjectTaskCard v-for="task in inProgressTasks" :key="task" :taskComponent="task" />
        </div>

        <!-- -- Terminado -- -->
        <div class="h-56 px-7">
            <h2 class="font-bold mb-10">TERMINADO <span class="font-normal ml-7">{{ finishedTasks.length }}</span></h2>
            <ProjectTaskCard v-for="task in finishedTasks" :key="task" :taskComponent="task" />
        </div>
      </div>
      <!-- ------------- Tasks ends 2 ------------- -->
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ProjectTaskCard from "@/Components/MyComponents/ProjectTaskCard.vue";
import { Link } from "@inertiajs/vue3";

export default {
    data(){
        return{
            selectedProyect: "",
            pedingTasks: null,
            inProgressTasks: null,
            finishedTasks: null,
            tabs: 1,
            uniqueUsers: [],
            maxUsersToShow: 3 ,
            selectedProject: '',
            currentProject: null,
        }
    },
    components:{
        AppLayoutNoHeader,
        PrimaryButton,
        ProjectTaskCard,
        Link,
    },
    props:{
      projects: Object,
      project: Object
    },
    methods:{
      
    },
    computed: {
  pedingTasks() {
    return this.currentProject?.tasks.filter(task => task.status === 'Por hacer');
  },
  inProgressTasks() {
    return this.currentProject?.tasks.filter(task => task.status === 'En curso');
  },
  finishedTasks() {
    return this.currentProject?.tasks.filter(task => task.status === 'Terminada');
  },

  // Calcular la lista de usuarios únicos
    uniqueUsers() {
      const uniqueUsers = [];
      const userIds = new Set();

      if (this.currentProject) {

        // Recorrer las tareas del proyecto
      for (const task of this.currentProject?.tasks) {
        // Recorrer los usuarios de cada tarea
        for (const user of task.users) {
          // Verificar si el usuario ya ha sido agregado
          if (!userIds.has(user.id)) {
            // Agregar el usuario a la lista de usuarios únicos
            uniqueUsers.push(user);
            this.uniqueUsers.push(user);
            userIds.add(user.id);
          }
        }
      }

    } 
      return uniqueUsers;
  },
   // Calcular la cantidad de usuarios restantes
    remainingUsersCount() {
      return Math.max(0,this.uniqueUsers.length - this.maxUsersToShow);
    },

    // Calcular los nombres de los usuarios restantes
    userNames() {
      const remainingUsers = this.uniqueUsers.slice(this.maxUsersToShow);
      return remainingUsers.map((user) => user.name);
    },
},
watch: {
    selectedProject(newVal) {
      this.currentProject = this.projects.data.find(
        (item) => item.id == newVal
      );
      this.uniqueUsers = [];
    },
  },

mounted() {
    this.selectedProject = this.project.data.id;

  },

}
</script>
