<template>
  <AppLayoutNoHeader title="Calendario |">
    <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
      <span>Calendario</span>
      <Link
        :href="route('dashboard')"
        class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
      >
        <i class="fa-solid fa-xmark"></i>
      </Link>
    </div>

    <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11">
      <span class="text-primary text-sm cursor-pointer"
        >Mes <i class="fa-solid fa-angle-down text-xs ml-2"></i
      ></span>
      <div class="flex justify-between items-center w-1/5">
        <i @click="changeMonth(-1)"
          class="fa-solid fa-angle-left text-primary text-xs mr-5 cursor-pointer p-1"
        ></i>
        <i class="fa-solid fa-calendar-days text-primary text-sm mr-2"></i>
        <p class="text-[#cccccc]">|</p>
        <p class="text-sm ml-2">{{ currentMonth.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' }) }}</p>
        <i @click="changeMonth(1)"
          class="fa-solid fa-angle-right text-primary text-xs ml-5 cursor-pointer p-1"
        ></i>
      </div>
      <div class="flex space-x-2">
        <Dropdown align="right" width="48">
          <template #trigger>
            <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
              Mis tareas <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
            </button>
          </template>
          <template #content>
            <DropdownLink :href="route('machines.create')">
              Todas las tareas
            </DropdownLink>
          </template>
        </Dropdown>
      </div>
    </div>


      <!-- -------------- calendar section --------------- -->
      <section class="w-11/12 mx-auto mb-16">
    <table class="default w-full mt-12">
      <tr class="text-center">
        <th class="py-2 w-[14.28%] border border-[#9A9A9A]">DOM</th>
        <th class="py-2 w-[14.28%] border border-[#9A9A9A]">LUN</th>
        <th class="py-2 w-[14.28%] border border-[#9A9A9A]">MAR</th>
        <th class="py-2 w-[14.28%] border border-[#9A9A9A]">MIE</th>
        <th class="py-2 w-[14.28%] border border-[#9A9A9A]">JUE</th>
        <th class="py-2 w-[14.28%] border border-[#9A9A9A]">VIE</th>
        <th class="py-2 w-[14.28%] border border-[#9A9A9A]">SAB</th>
      </tr>
      <tr v-for="(week, index) in weeks" :key="index" class="text-sm text-right">
        <td v-for="day in week" :key="day" class="h-32 border border-[#9A9A9A] relative">
          <p style="z-index: 999;" class="absolute bottom-0 right-3">{{ day }}</p>
          <!-- Agregar línea para tareas y eventos -->
          <div v-for="task in tasks" :key="task.id">
            <div class="" v-if="isTaskDay(task, day)">
                <div @click="showInfoTask = !showInfoTask" :class="task.type === 'Tarea' ? 'bg-[#B9D9FE] border-[#0355B5] border-l-4 border' : 'bg-[#FDB9C9] border-[#D90537] border-l-4 border'" class="h-5 rounded-sm my-1 text-xs justify-center items-center cursor-pointer flex relative">
                    {{ task.title }}
                <div v-if="showInfoTask" style="z-index: 999;" class="absolute -bottom-36 w-56 h-32 bg-[#D9D9D9] rounded-md border"></div>
                </div>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </section>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
        currentMonth: new Date(),
        showInfoTask: false,
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    SecondaryButton,
    Link,
    Dropdown,
    DropdownLink,
  },
  props: {
    tasks: Array,
    // events: Array,
  },
  methods: {
    changeMonth(offset) {
      const newMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + offset, 1);
      this.currentMonth = newMonth;
    },
    isTaskDay(task, day) {
      const taskStartDate = new Date(task.start_date);
      const taskFinishDate = new Date(task.finish_date);
      const currentDay = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), day);
      return currentDay >= taskStartDate && currentDay <= taskFinishDate;
    },
  },
  computed: {
    weeks() {
      const firstDayOfMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), 1);
      const lastDayOfMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + 1, 0);
      const startDayOfWeek = firstDayOfMonth.getDay(); // 0 (Domingo) a 6 (Sábado)
      const totalDaysInMonth = lastDayOfMonth.getDate();
      const days = [];
      let currentWeek = [];

      // Llena los días previos al primer día del mes con celdas vacías
      for (let i = 0; i < startDayOfWeek; i++) {
        currentWeek.push('');
      }

      // Agrega los números de los días del mes
      for (let i = 1; i <= totalDaysInMonth; i++) {
        currentWeek.push(i);
        if (currentWeek.length === 7) {
          days.push([...currentWeek]);
          currentWeek = [];
        }
      }

      // Añade la última semana si es necesario
      if (currentWeek.length > 0) {
        days.push([...currentWeek]);
      }

      return days;
    },
  },
};
</script>

