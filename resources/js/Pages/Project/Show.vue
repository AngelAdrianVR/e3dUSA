<template>
  <AppLayoutNoHeader title="Detalles de proyecto |">
    <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Proyectos</span>
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

<!-- ---------- Task card component --------- -->
            <div class="shadow-md shadow-gray-400/100 h-36 rounded-r-md border-l-4 border-red-600 py-2 px-3 cursor-grab active:cursor-grabbing">
                <div class="flex justify-between items-center">
                    <div>
                        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                    </div>
                    <p class="mr-5">{{ '24/02/2023' }}</p>
                </div>

                <div class="p-3">
                    <p class="text-lg">{{ 'Fabricación de portaplacas' }}</p>
                </div>

                <footer class="p-3 border-t border-[#9A9A9A] mt-1 relative">
                    <div class="flex justify-between items-center px-3">
                        <i class="fa-regular fa-comments text-[#9A9A9A] text-lg cursor-pointer"></i>
                        <div class="rounded-full w-12 h-12 border border-[#9A9A9A] absolute bottom-3 bg-gray-100 right-0"></div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- -- En curso -- -->
        <div class="border-r border-[#9A9A9A] h-56 px-7">
            <h2 class="font-bold">EN CURSO <span class="font-normal ml-7">{{ '3' }}</span></h2>
        </div>

        <!-- -- Terminado -- -->
        <div class="h-56 px-7">
            <h2 class="font-bold">TERMINADO <span class="font-normal ml-7">{{ '1' }}</span></h2>
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    data(){
        return{
            selectedProyect: "",
            tabs: 1,
        }
    },
    components:{
        AppLayoutNoHeader,
        PrimaryButton
    },
    props:{
      projects: Object,
      project: Object
    },
    methods:{
      
    },
}
</script>
