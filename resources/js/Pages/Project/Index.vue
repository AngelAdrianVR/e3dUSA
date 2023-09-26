<template>
  <AppLayoutNoHeader title="Gestor de proyectos |">
    <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Proyectos</span>
    </div>

    <div class="flex justify-between mt-5 mx-14">
        <div class="flex items-center space-x-2">
              <input  @keyup.enter="handleSearch" v-model="inputSearch" type="search" class="input"
                  placeholder="Buscar" />
              <SecondaryButton @click="handleSearch" type="submit" class=""><i
                      class="fa-solid fa-magnifying-glass"></i></SecondaryButton>
          </div>
        <div>
          <PrimaryButton @click="$inertia.get(route('projects.create'))">Nuevo proyecto</PrimaryButton>
        </div>
      </div>

    <div class="lg:px-16 px-4 py-7 text-sm overflow-x-scroll">
      <table class="lg:w-[80%] w-full mx-auto">
            <thead>
            <tr class="text-left">
                <th class="font-bold pb-5">Nombre del proyecto <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-bold pb-5">Estatus <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-bold pb-5">Tareas <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-bold pb-5">Fecha de inicio <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-bold pb-5">Fecha final <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th class="font-bold pb-5">Completa <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(project, index) in filteredTableData" :key="project.id" class="mb-4 cursor-pointer hover:bg-[#acababa8]" @click="$inertia.get(route('projects.show', project.id))">
                <td class="text-left py-2 px-2">
                {{ project.project_name }}
                </td>
                <td class="text-left py-2 px-2">
                <span class="">{{ project.status }}</span>
                </td>
                <td class="text-left py-2 flex space-x-1 items-center px-2">
                  <p class="text-xs">{{ project.tasks.filter(task => task.status === 'Terminada').length }}</p>
                  <div class="relative bg-[#D9D9D9] rounded-full h-5 w-24">
                    <div :class="( project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 == 100 ? 'rounded-full' : 'rounded-l-full'" class="absolute top-0 left-0 bg-secondary h-5" :style="{ width: ( project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 + '%' }"></div>
                     <p class="text-sm font-bold absolute top-0 right-8 text-white">{{ Math.round((project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100) }}%</p> 
                  </div>
                  <p class="text-xs">{{ project.tasks.length }}</p>
                </td>
                <td class="text-left py-2 px-2">
                {{ project.start_date }}
                </td>
                <td class="text-left py-2 px-2">
                {{ project.limit_date }}
                </td>
                <td class="text-left py-2 px-2">
                {{ project.finished_at }}
                </td>
                <td class="text-left py-2 px-2">
                </td>
            </tr>
            </tbody>
        </table>
</div>

  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

export default {
    data(){
        return{
            search: '',
            inputSearch: '',
        }
    },
    components:{
        AppLayoutNoHeader,
        PrimaryButton,
        SecondaryButton
    },
    props:{
      projects: Array
    },
    methods:{
      handleSearch() {
            this.search = this.inputSearch;
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.projects.data;
            } else {
                return this.projects.data.filter(
                    (project) =>
                        project.project_name.toLowerCase().includes(this.search.toLowerCase()) ||
                        project.status.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
}
</script>
