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
          <el-select v-model="selectedProyect" clearable filterable placeholder="Buscar proyecto"
            no-data-text="No hay proyectos registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in projects.data" :key="item.id" :label="item.project_name" :value="item.id" />
          </el-select>
        </div>
        <div>
          <PrimaryButton @click="$inertia.get(route('projects.create'))">Agregar tarea</PrimaryButton>
        </div>
      </div>

<!-- --------------project title--------------------------- -->
      <p class="text-center font-bold text-lg mb-4">
        {{ project.project_name }}
      </p>


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


    <!-- ------------- Informacion del proyecto Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-3 text-left p-4  text-sm items-center">
      <!-- -- Por hacer -- -->
        <div class="border-r border-[#9A9A9A] h-56 pr-7">
            <h2 class="font-bold mb-10">POR HACER <span class="font-normal ml-7">{{ '6' }}</span></h2>
            <ProjectTaskCard v-for="task in pedingTasks" :key="task" :taskComponent="task" />
        </div>

        <!-- -- En curso -- -->
        <div class="border-r border-[#9A9A9A] h-56 px-7">
            <h2 class="font-bold mb-10">EN CURSO <span class="font-normal ml-7">{{ '3' }}</span></h2>
            <ProjectTaskCard v-for="task in inProgressTasks" :key="task" :taskComponent="task" />
        </div>

        <!-- -- Terminado -- -->
        <div class="h-56 px-7">
            <h2 class="font-bold mb-10">TERMINADO <span class="font-normal ml-7">{{ '1' }}</span></h2>
            <ProjectTaskCard v-for="task in finishedTasks" :key="task" :taskComponent="task" />
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

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
    return this.project.data.tasks.filter(task => task.status === 'Por hacer');
  },
  inProgressTasks() {
    return this.project.data.tasks.filter(task => task.status === 'En curso');
  },
  finishedTasks() {
    return this.project.data.tasks.filter(task => task.status === 'Terminada');
  },
},

}
</script>
