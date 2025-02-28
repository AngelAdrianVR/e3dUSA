<template>
<main class="overflow-auto pb-2 ">
  <table class="border border-[#9A9A9A] default w-[1800px]">
    <tr>
      <th class="border-y border-[#9A9A9A] text-left pl-7 py-3 font-thin relative w-1/4" scope="row">
        Proyecto <br />
        <p :title="currentProject?.project_name" class="text-base font-bold truncate w-4/5">{{ currentProject?.project_name }}</p>
        <i @click="showDepartmentFilter = !showDepartmentFilter"
          class="fa-solid fa-ellipsis text-primary absolute bottom-4 right-4 cursor-pointer hover:bg-[#dfdede] rounded-full p-2"></i>
        <div v-if="showDepartmentFilter" class="absolute right-4 top-[60px] bg-[#D9D9D9] rounded-md px-4 py-2">
          <label class="flex items-center">
            <Checkbox v-model:checked="productionCheck" class="bg-transparent" />
            <span class="ml-2 text-sm text-[#9A9A9A]">Producción</span>
          </label>
          <label class="flex items-center">
            <Checkbox v-model:checked="designCheck" class="bg-transparent" />
            <span class="ml-2 text-sm text-[#9A9A9A]">Diseño</span>
          </label>
          <label class="flex items-center">
            <Checkbox v-model:checked="salesCheck" class="bg-transparent" />
            <span class="ml-2 text-sm text-[#9A9A9A]">Ventas</span>
          </label>
          <label class="flex items-center">
            <Checkbox v-model:checked="marketingCheck" class="bg-transparent" />
            <span class="ml-2 text-sm text-[#9A9A9A]">Marketing</span>
          </label>
        </div>
      </th>
      <th class="border border-[#9A9A9A] text-center font-thin">
        <strong class="text-base uppercase font-bold tex">{{ monthName }}</strong><br />
        <div class="flex space-x-8 justify-center w-[95%] mx-4">
          <p v-for="day in daysInMonth" :key="day" class="text-secondary relative pb-1">
            {{ daysOfWeek[(day + startDayOfWeek - 2) % 7] }}
            <span class="absolute -bottom-3 -left-0 text-[9px] text-black dark:text-white">{{ day }}</span>
          </p>
        </div>
      </th>
    </tr>

    <tr v-for="task in currentProject?.tasks" :key="task" v-show="taskMatchesFilters(task)">
      <th class="font-normal pl-7 py-2 border-y border-[#9A9A9A]">
        <div :class="task.priority.color_border" class="border-r-4 flex items-center px-2">
          <div>
            <p class="w-[300px] truncate text-sm" :title="task.title">{{ task.title }}</p>
            <p class="text-[#9A9A9A] text-xs">Depto. {{ task.department }}</p>
          </div>

            <div class="flex items-center ml-2">
              <div v-for="(user, index) in task.participants" :key="index">
                <el-tooltip :content="user.name" placement="top">
                  <div v-if="index < 3" class="flex text-sm rounded-full w-8">
                    <img class="h-7 w-7 rounded-full border border-[#cccccc] object-cover" :src="user.profile_photo_url" :alt="user.name" />
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
                <span v-if="task.participants?.length > 3" class="ml-1 text-primary text-sm">
                  +{{ (task.participants.length - 3) }}
                </span>
              </el-tooltip>
          </div>
        </div>
      </th>
      <td class="border-x border-[#CCCCCC] overflow-x-hidden">
        <div class="w-[93%] mx-auto">
          <el-tooltip :content="task.start_date + ' - ' + task.end_date" placement="top">
            <template #content>
              <div>
                <p>Periodo esperado:</p>
                <p>{{ task.start_date + ' - ' + task.end_date }}</p>
                <p :class="{'text-sky-400': task.status === 'En curso',
                    'text-emerald-400': task.status === 'Terminada'
                }">
                  • Estatus: {{ task.status }}
                </p>
              </div>
            </template>
            <div class="h-5 rounded-full shadow-md shadow-gray-400/100 relative" :class="getStatusColor(task) + ' start-day-2'"
              :style="{
                width: (90 / daysInMonth) * taskDuration(task) + '%',
                '--days-in-month': daysInMonth,
                '--task-start-day': taskStartDay(task)
              }">
              <i v-if="task.status === 'Terminada'" class="fa-solid fa-check absolute left-[2px] top-[1px] text-green-500 rounded-full px-[2px] py-[1px] mt-[1px] bg-white"></i>
            </div>
          </el-tooltip>
        </div>
      </td>
    </tr>
  </table>
</main>
  <p v-if="this.currentDate == undefined">No hay tareas en este proyecto</p>
</template>

<script>
import Checkbox from "@/Components/Checkbox.vue";

export default {
  data() {

    return {
      productionCheck: true,
      designCheck: true,
      salesCheck: true,
      marketingCheck: true,
      showDepartmentFilter: false,
      daysOfWeek: ['L', 'M', 'I', 'J', 'V', 'S', 'D'],
      currentDate: this.currentDate, // La fecha actual
      cellWidth: 100 / this.daysInMonth, // Suponiendo que el ancho total de la columna del mes es 100%
    }
  },
  components: {
    Checkbox,
  },
  props: {
    currentProject: Object,
    // currentDate: Object,
  },
  methods: {
    getStatusColor(task) {
      if (task.status == "Por hacer") {
        return "bg-[#9A9A9A]";
      } else if (task.status == "En curso") {
        return "bg-[#0355B5]";
      } else {
        return "bg-green-500";
      }
    },
    taskStartDay(task) {
      const startDate = new Date(task.start_date_raw);
      const startDay = startDate.getDate();
      const currentMonthFirstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
      const dayDifference = Math.abs(startDate - currentMonthFirstDay) / (1000 * 60 * 60 * 24);
      return dayDifference; // Sumar 1 para que el primer día sea 1 en lugar de 0
    },
    taskDuration(task) {
      const startDate = new Date(task.start_date_raw);
      const endDate = new Date(task.end_date_raw);
      const duration = (endDate - startDate) / (24 * 60 * 60 * 1000); // Convertir a días
      return duration + 1; // Sumar 1 para incluir el día de inicio
    },
    getCurrentMonthName() {
      const months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
      ];
      const currentDate = new Date();
      return months[currentDate.getMonth()];
    },
    taskMatchesFilters(task) {
      // Verifica si la tarea cumple con al menos uno de los criterios de filtro
      return (
        (this.productionCheck && task.department === "Produccion") ||
        (this.designCheck && task.department === "Diseño") ||
        (this.salesCheck && task.department === "Ventas") ||
        (this.marketingCheck && task.department === "Marketing")
      );
    },
  },
  computed: {
    monthName() {
      const months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
      ];
      return months[this.currentDate?.getMonth()];
    },
    daysInMonth() {
      const year = this.currentDate?.getFullYear();
      const month = this.currentDate?.getMonth() + 1;
      return new Date(year, month, 0).getDate();
    },
    startDayOfWeek() {
      const year = this.currentDate?.getFullYear();
      const month = this.currentDate?.getMonth();
      return new Date(year, month, 1).getDay(); // 0 para domingo, 1 para lunes, etc.
    },
  },
  created() {
    // Verificar si hay tareas en el proyecto y si la primera tarea tiene una fecha de inicio
    if (this.currentProject && this.currentProject?.tasks?.length > 0) {
      const firstTask = this.currentProject.tasks[0];
      if (firstTask && firstTask.start_date_raw) {
        this.currentDate = new Date(firstTask.start_date_raw);
      }
    }
  },
}
</script>

<style scoped>
/* Calcula la distancia de margen para posicionar la barra en el dia correspondiente */
.start-day-2 {
  margin-left: calc((100% / var(--days-in-month)) * var(--task-start-day));
}
</style>