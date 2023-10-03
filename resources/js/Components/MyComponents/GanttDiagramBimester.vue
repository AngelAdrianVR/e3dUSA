<template>
  <table class="border border-[#9A9A9A] default w-full">
        <tr>
          <th class="border-y border-[#9A9A9A] text-left pl-7 py-3 font-thin relative w-1/4" scope="row">
            Proyecto <br />
            <strong class="text-lg font-bold">{{ currentProject?.project_name }}</strong>
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
            <div class="flex space-x-3 justify-center w-[95%] mx-auto">
              <p
                v-for="day in daysInMonth"
                :key="day"
                class="text-secondary relative"
              >
                {{ daysOfWeek[(day + startDayOfWeek - 2) % 7] }}
                <span class="absolute -bottom-3 -left-0 text-[11px] text-black">{{ day }}</span>
              </p>
            </div>
          </th>
          <th class="border border-[#9A9A9A] text-center font-thin">
          <strong class="text-base uppercase font-bold">{{ nextMonthName }}</strong>
           <div class="flex space-x-3 justify-center w-[95%] mx-auto">
              <p
                v-for="day in daysInNextMonth"
                :key="day"
                class="text-secondary relative"
              >
                {{ daysOfWeek[(day + startDayOfWeekNextMonth - 2) % 7] }}
                <span class="absolute -bottom-3 -left-0 text-[11px] text-black">{{ day }}</span>
              </p>
            </div>
        </th>
        </tr>

        <tr v-for="task in currentProject?.tasks" :key="task" v-show="taskMatchesFilters(task)">
          <th class="text-lg font-normal pl-7 py-2 border-y border-[#9A9A9A]">
            <div :class="task.priority.color_border" class="border-r-4">
              {{ task.title }}
              <p class="text-[#9A9A9A] text-sm">Depto. {{ task.department }}</p>
            </div>
          </th>
          <td class="border-x border-[#CCCCCC]">
            <div class="w-[93%] mx-auto">
              <el-tooltip :content="task.start_date + ' -- ' + task.end_date" placement="top">
              <div
                class="h-5 rounded-full shadow-md shadow-gray-400/100"
                :class="getStatusColor(task) + ' start-day-2'"
                 :style="{
                    width: (100 / daysInMonth) * taskDuration(task) + '%',
                    '--days-in-month': daysInMonth,
                    '--task-start-day': taskStartDay(task)
                  }"
              ></div>
          </el-tooltip>
            </div>
          </td>
        </tr>
      </table>
</template>

<script>
import Checkbox from "@/Components/Checkbox.vue";

export default {
 data(){

    return{
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
 components:{
    Checkbox,
 },
 props:{
    currentProject: Object,
    currentDate: Object,
 },
 methods:{
    getStatusColor(task){
      if (task.status == "Por hacer") {
        return "bg-[#9A9A9A]";
      } else if (task.status == "En curso") {
        return "bg-[#0355B5]";
      } else {
        return "bg-green-500";
      }
    },
     taskStartDay(task) {
    const startDate = new Date(task.start_date);
    const startDay = startDate.getDate();
    const currentMonthFirstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
    const dayDifference = Math.abs(startDate - currentMonthFirstDay) / (1000 * 60 * 60 * 24);
    return dayDifference; // Sumar 1 para que el primer día sea 1 en lugar de 0
  },
    taskDuration(task) {
    const startDate = new Date(task.start_date);
    const endDate = new Date(task.end_date);
    const duration = (endDate - startDate) / (24 * 60 * 60 * 1000); // Convertir a días
    // const offsetNextMonth = 0 //offset que agrega 2.5 dias cuando la tarea termina el siguiente mes y se muestre en el dia correcto.

    // Calcula la fecha del primer día del mes siguiente
    const nextMonthDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 1);

    if (endDate >= nextMonthDate) {
      return duration + 3.5; //si la tarea termina el mes siguiente del que comenzó se suma un offset de 2.5 dias para mostrarlo en el dia correspondiente.
    }

    return duration + 1 ; // Sumar 1 para incluir el día de inicio
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
    return months[this.currentDate.getMonth()];
  },
  daysInMonth() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth() + 1;
    return new Date(year, month, 0).getDate();
  },
  startDayOfWeek() {
    const year = this.currentDate.getFullYear();
    const month = this.currentDate.getMonth();
    return new Date(year, month, 1).getDay(); // 0 para domingo, 1 para lunes, etc.
  },
  nextMonthDate() {
      const currentYear = this.currentDate.getFullYear();
      const currentMonth = this.currentDate.getMonth();
      // Calcula la fecha del mes siguiente
      const nextMonth = new Date(currentYear, currentMonth + 1, 1);
      return nextMonth;
    },
    nextMonthName() {
      const months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
      ];
      return months[this.nextMonthDate.getMonth()];
    },
    daysInNextMonth() {
      return new Date(this.nextMonthDate.getFullYear(), this.nextMonthDate.getMonth() + 1, 0).getDate();
    },
   daysInNextMonth() {
    const nextYear = this.nextMonthDate.getFullYear();
    const nextMonth = this.nextMonthDate.getMonth() + 1;
    return new Date(nextYear, nextMonth, 0).getDate();
  },
  startDayOfWeekNextMonth() {
    const nextYear = this.nextMonthDate.getFullYear();
    const nextMonth = this.nextMonthDate.getMonth();
    return new Date(nextYear, nextMonth, 1).getDay(); // 0 para domingo, 1 para lunes, etc.
  },
},
mounted() {
    // Verificar si hay tareas en el proyecto y si la primera tarea tiene una fecha de inicio
    if (this.currentProject && this.currentProject.tasks.length > 0) {
      const firstTask = this.currentProject.tasks[0];
      if (firstTask && firstTask.start_date) {
        this.currentDate = new Date(firstTask.start_date);
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