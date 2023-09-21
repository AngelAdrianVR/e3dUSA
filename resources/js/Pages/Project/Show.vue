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
        <div class="flex space-x-2">
          <PrimaryButton v-if="$page.props.auth.user.permissions.includes('Crear tareas')"
           @click="$inertia.get(route('projects.create'))">+ Agregar tarea</PrimaryButton>
          <Dropdown align="right" width="48">
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                Mis tareas <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink :href="route('machines.create')">
                Agregar nueva máquina
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>

<!-- --------------project title--------------------------- -->
      <div class="text-center font-bold text-lg mb-4 flex justify-center items-center">
        <p>{{ currentProject?.project_name }}</p>
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


    <!-- ------------- Cronograma Starts 3 ------------- -->
      <div v-if="tabs == 3" class="text-left text-sm items-center">
        
      <table class="border border-[#9A9A9A] default w-full">
  <tr>
    <th class="border-y border-[#9A9A9A] text-left pl-7 py-3 font-thin relative" scope="row">Proyecto <br>
     <strong class="text-lg font-bold">{{ currentProject?.project_name }}</strong>
     <i class="fa-solid fa-ellipsis text-primary absolute bottom-4 right-4 cursor-pointer hover:bg-[#dfdede] rounded-full p-2"></i>
     </th>
    <th class="border border-[#9A9A9A] text-center font-thin">%</th>
    <th class="border border-[#9A9A9A] text-center font-thin"><strong class="text-base uppercase font-bold tex">Junio</strong><br>
     <div class="flex space-x-5 justify-center">
      <p class="text-secondary relative">L <span class="absolute -bottom-3 -left-[2px] text-xs text-black">01</span></p>
      <p class="text-secondary relative">M <span class="absolute -bottom-3 -left-[2px] text-xs text-black">02</span></p>
      <p class="text-secondary relative">I <span class="absolute -bottom-3 -left-[2px] text-xs text-black">03</span></p>
      <p class="text-secondary relative">J <span class="absolute -bottom-3 -left-[2px] text-xs text-black">04</span></p>
      <p class="text-secondary relative">V <span class="absolute -bottom-3 -left-[2px] text-xs text-black">05</span></p>
      <p class="text-secondary relative">S <span class="absolute -bottom-3 -left-[2px] text-xs text-black">06</span></p>
      <p class="text-secondary relative">D <span class="absolute -bottom-3 -left-[2px] text-xs text-black">07</span></p>
      </div>
     </th>
    <th class="border border-[#9A9A9A] text-center font-thin"><strong class="text-base uppercase font-bold tex">Julio</strong><br>
     <div class="flex space-x-5 justify-center">
      <p class="text-secondary relative">L <span class="absolute -bottom-3 -left-[2px] text-xs text-black">01</span></p>
      <p class="text-secondary relative">M <span class="absolute -bottom-3 -left-[2px] text-xs text-black">02</span></p>
      <p class="text-secondary relative">I <span class="absolute -bottom-3 -left-[2px] text-xs text-black">03</span></p>
      <p class="text-secondary relative">J <span class="absolute -bottom-3 -left-[2px] text-xs text-black">04</span></p>
      <p class="text-secondary relative">V <span class="absolute -bottom-3 -left-[2px] text-xs text-black">05</span></p>
      <p class="text-secondary relative">S <span class="absolute -bottom-3 -left-[2px] text-xs text-black">06</span></p>
      <p class="text-secondary relative">D <span class="absolute -bottom-3 -left-[2px] text-xs text-black">07</span></p>
      </div>
     </th>
    <th class="border border-[#9A9A9A] text-center font-thin"><strong class="text-base uppercase font-bold tex">Agosto</strong><br>
     <div class="flex space-x-5 justify-center">
      <p class="text-secondary relative">L <span class="absolute -bottom-3 -left-[2px] text-xs text-black">01</span></p>
      <p class="text-secondary relative">M <span class="absolute -bottom-3 -left-[2px] text-xs text-black">02</span></p>
      <p class="text-secondary relative">I <span class="absolute -bottom-3 -left-[2px] text-xs text-black">03</span></p>
      <p class="text-secondary relative">J <span class="absolute -bottom-3 -left-[2px] text-xs text-black">04</span></p>
      <p class="text-secondary relative">V <span class="absolute -bottom-3 -left-[2px] text-xs text-black">05</span></p>
      <p class="text-secondary relative">S <span class="absolute -bottom-3 -left-[2px] text-xs text-black">06</span></p>
      <p class="text-secondary relative">D <span class="absolute -bottom-3 -left-[2px] text-xs text-black">07</span></p>
      </div>
     </th>
    <th class="border border-[#9A9A9A] text-center font-thin"><strong class="text-base uppercase font-bold tex">Septiembre</strong><br>
     <div class="flex space-x-5 justify-center">
      <p class="text-secondary relative">L <span class="absolute -bottom-3 -left-[2px] text-xs text-black">01</span></p>
      <p class="text-secondary relative">M <span class="absolute -bottom-3 -left-[2px] text-xs text-black">02</span></p>
      <p class="text-secondary relative">I <span class="absolute -bottom-3 -left-[2px] text-xs text-black">03</span></p>
      <p class="text-secondary relative">J <span class="absolute -bottom-3 -left-[2px] text-xs text-black">04</span></p>
      <p class="text-secondary relative">V <span class="absolute -bottom-3 -left-[2px] text-xs text-black">05</span></p>
      <p class="text-secondary relative">S <span class="absolute -bottom-3 -left-[2px] text-xs text-black">06</span></p>
      <p class="text-secondary relative">D <span class="absolute -bottom-3 -left-[2px] text-xs text-black">07</span></p>
      </div>
     </th>
  </tr>

  <tr>
    <th class="text-lg font-normal pl-7 py-2 border-y border-[#9A9A9A]">Fabricación de portaplacas <br>
      <p class="text-[#9A9A9A] text-sm">Depto. Finanzas</p>
    </th>
    <td class="font-normal text-center border border-[#9A9A9A]">100</td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
  </tr>
  <tr>
    <th class="text-lg font-normal pl-7 py-2 border-y border-[#9A9A9A]">Grabado laser de emblema... <br>
      <p class="text-[#9A9A9A] text-sm">Depto. Producción</p>
    </th>
    <td class="font-normal text-center border border-[#9A9A9A]">50</td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
  </tr>
  <tr>
    <th class="text-lg font-normal pl-7 py-2 border-y border-[#9A9A9A]">Empaque y etiquetado de... <br>
      <p class="text-[#9A9A9A] text-sm">Depto. Ventas</p>
    </th>
    <td class="font-normal text-center border border-[#9A9A9A]">0</td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
  </tr>
  <tr>
    <th class="text-lg font-normal pl-7 py-2 border-y border-[#9A9A9A]">Enviar correo de confirmacion... <br>
      <p class="text-[#9A9A9A] text-sm">Depto. Ventas</p>
    </th>
    <td class="font-normal text-center border border-[#9A9A9A]">30</td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
  </tr>
  <tr>
    <th class="text-lg font-normal pl-7 py-2 border-y border-[#9A9A9A]">Empaque y etiquetado de... <br>
      <p class="text-[#9A9A9A] text-sm">Depto. Diseño</p>
    </th>
    <td class="font-normal text-center border border-[#9A9A9A]">60</td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
    <td class="border-x border-[#9A9A9A]"></td>
  </tr>
</table>

<div class="text-right mr-9">
  <div class="border border-[#9A9A9A] rounded-md inline-flex justify-end mt-4">
    <p :class="period == 'Hoy' ? 'bg-primary text-white rounded-sm' :'border-[#9A9A9A]' " @click="period = 'Hoy'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x border-transparent">Hoy</p>
    <p :class="period == 'Semana' ? 'bg-primary text-white rounded-sm' :'border-[#9A9A9A]'" @click="period = 'Semana'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x border-transparent">Semana</p>
    <p :class="period == 'Mes' ? 'bg-primary text-white rounded-sm' :'border-[#9A9A9A]'"  @click="period = 'Mes'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x">Mes</p>
    <p :class="period == 'Trimestre' ? 'bg-primary text-white rounded-sm' :'border-[#9A9A9A]'" @click="period = 'Trimestre'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x border-transparent">Trimestre</p>
  </div>  
</div>

      </div>
      <!-- ------------- Cronograma ends 3 ------------- -->
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ProjectTaskCard from "@/Components/MyComponents/ProjectTaskCard.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
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
            period: 'Hoy' //period of time in cronograma table tab 3
        }
    },
    components:{
        AppLayoutNoHeader,
        PrimaryButton,
        ProjectTaskCard,
        Link,
        Dropdown,
        DropdownLink,
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
