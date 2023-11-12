<template>
<div class="overflow-x-auto">
    <table class="w-full mx-auto">
        <thead>
          <tr class="text-left">
            <th class="font-bold pb-5">Nombre del proyecto <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i></th>
            <th class="font-bold pb-5">Estatus <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i></th>
            <th class="font-bold pb-5">Tareas <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i></th>
            <th class="font-bold pb-5">Fecha de inicio <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i></th>
            <th class="font-bold pb-5">Fecha final <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i></th>
            <th class="font-bold pb-5">Completa <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects" :key="project.id"
            class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]" @click="$inertia.get(route('projects.show', project.id))">
            <td class="text-left py-2 px-2 rounded-l-full">
              <p :title="project.project_name" class="w-44 truncate">{{ project.project_name }}</p>
            </td>
            <td class="text-left py-2 px-2">
              <span
                :class="calculateProjectStatus(project.tasks)?.text_color + ' ' + calculateProjectStatus(project.tasks)?.bg"
                class="py-1 px-2 rounded-full">{{ calculateProjectStatus(project.tasks)?.label }}</span>
            </td>
            <td class="text-left py-2 flex space-x-1 items-center px-2">
              <p class="text-xs">{{ project.tasks.filter(task => task.status === 'Terminada').length }}</p>
              <div class="relative bg-[#D9D9D9] rounded-full h-5 w-24">
                <div
                  :class="(project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 == 100 ? 'rounded-full' : 'rounded-l-full'"
                  class="absolute top-0 left-0 bg-secondary h-5"
                  :style="{ width: (project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) * 100 + '%' }">
                </div>
                <p class="text-sm font-bold absolute top-0 right-8 text-white">{{ project.tasks.length != 0 ?
                  Math.round((project.tasks.filter(task => task.status === 'Terminada').length / project.tasks.length) *
                    100) : '0' }}%</p>
              </div>
              <p class="text-xs">{{ project.tasks.length }}</p>
            </td>
            <td class="text-left py-2 px-2">
              {{ project.start_date }}
            </td>
            <td class="text-left py-2 px-2">
              {{ project.limit_date }}
            </td>
            <td class="text-left py-2 px-2 rounded-r-full">
              {{ project.finished_at ?? '--' }}
            </td>
          </tr>
        </tbody>
      </table>
  </div>
</template>

<script>
export default {
  data(){
    return{
      
      }
},
components:{

},
props:{
projects: Array,
},
methods:{
    calculateProjectStatus(tasks) {
      const totalTasks = tasks.length;
      const completedTasks = tasks.filter(task => task.status === 'Terminada').length;
      const inProgressTasks = tasks.filter(task => task.status === 'En curso').length;

      if (totalTasks === 0) {
        return {
          label: 'Sin tareas',
          text_color: 'text-red-600',
          bg: 'bg-red-200',
        };
      }

      if (completedTasks === totalTasks) {
        return {
          label: 'Terminado',
          text_color: 'text-green-600',
          bg: 'bg-green-200',
        };
      } else if (inProgressTasks > 0 || completedTasks > 0) {
        return {
          label: 'En proceso',
          text_color: 'text-secondary',
          bg: 'bg-blue-200',
        };
      } else {
        return {
          label: 'Sin iniciar',
          text_color: 'text-orange-600',
          bg: 'bg-orange-200',
        };
      }
    },
}
}
</script>

<style>

</style>