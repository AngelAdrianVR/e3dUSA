<template>
  <AppLayoutNoHeader title="Calendario">
    <div class="relative overflow-hidden">
      <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-2 dark:text-white">
        <span>Calendario de actividades</span>
        <Link :href="route('dashboard')"
          class="cursor-default w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
        <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>

      <div class="flex justify-between text-lg mx-2 lg:mx-14 mt-11 dark:text-white">
        <!-- <span class="text-primary text-sm cursor-pointer">Mes <i class="fa-solid fa-angle-down text-xs ml-2"></i></span> -->
        <span></span>
        <div class="flex justify-between items-center lg:w-1/5">
          <i @click="changeMonth(-1)" class="fa-solid fa-angle-left text-primary text-xs mr-5 cursor-default px-2 py-1 hover:bg-gray2 rounded-full"></i>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-primary mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
          </svg>
          <!-- <i class="fa-solid fa-calendar-days text-primary text-sm mr-2"></i> -->
          <p class="text-[#cccccc]">|</p>
          <p class="text-sm ml-2 uppercase">{{ currentMonth.toLocaleDateString('es-ES', {
            month: 'long', year: 'numeric'
          })
          }}</p>
          <i @click="changeMonth(1)" class="fa-solid fa-angle-right text-primary text-xs ml-5 cursor-default px-2 py-1 hover:bg-gray2 rounded-full"></i>
        </div>
        <div class="flex space-x-2">
          <Link :href="route('calendars.create')">
          <PrimaryButton>+ Agendar</PrimaryButton>
          </Link>
        </div>
      </div>
      <div class="mx-14">
        <button @click="showPendentInvitationsModal = true" v-if="pendent_invitations.length"
          class="bg-[#FEDBBD] text-[#FD8827] px-2 py-1 rounded-[5px] text-sm">Invitaciones pendientes <span
            class="ml-2">({{
              pendent_invitations.length }})</span></button>
      </div>
      <NotificationCenter module="calendar" />
      <!-- -------------- calendar section --------------- -->
      <section @click="selectedDay = null" class="w-11/12 mx-auto mb-24 min-h-screen">
        <table class="w-full mt-12">
          <tr class="text-center *:py-2 *:w-[14.28%] *:border *:border-[#9A9A9A] *:bg-gray2 *:text-secondary">
            <th>DOM</th>
            <th>LUN</th>
            <th>MAR</th>
            <th>MIE</th>
            <th>JUE</th>
            <th>VIE</th>
            <th>SAB</th>
          </tr>
          <tr v-for="(week, index) in weeks" :key="index" class="text-sm text-right">
            <td v-for="day in week" :key="day" class="h-32 pb-4 border border-[#9A9A9A] relative">
              <p 
                :class="{
                  'bg-red-500 text-white': day === today,
                  'bg-gray-500 text-white': day !== today
                }" 
                class="absolute bottom-0 right-0 rounded-full size-6 flex items-center justify-center text-center">
                <span>{{ day }}</span>
              </p>
              <!-- Agregar línea para tareas y eventos -->
              <div v-for="task in tasks.data" :key="task.id">
                <div class="mt-1" v-if="isTaskDay(task, day)">
                  <div @click.stop="selectedTask = task; selectedDay = day"
                    :class="task.status === 'Terminada' 
                    ? 'bg-[#D1FAE5] border-[#10B981] border-l-4 border' 
                    : task.type === 'Tarea' 
                        ? 'bg-[#bfdbfc] border-[#0355B5] border-l-4 border' 
                        : 'bg-[#FDB9C9] border-[#D90537] border-l-4 border'"
                    class="h-7 rounded-sm text-xs justify-center items-center cursor-pointer flex relative">

                    <p class="w-48 truncate">{{ task.title }}
                        <!-- <svg v-if="task.status === 'Terminada'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                          class="size-4 text-green-600 ml-3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg> -->

                       <!-- <i v-if="task.status === 'Terminada'" class="fa-solid fa-check ml-3 text-sm font-bold text-green-500"></i> -->
                       </p>

                    <div v-if="selectedTask === task && selectedDay == day" style="z-index: 999;"
                      class="px-1 pb-3 absolute -bottom-[185px] w-64 h-auto bg-[#D9D9D9] rounded-md border cursor-default">
                      <!-- --- Head --- -->
                      <div class="flex items-center justify-end">
                        <p :class="selectedTask.type === 'Tarea' ? 'border-[#0355B5] bg-[#B9D9FE]' : 'bg-[#FDB9C9] border-[#D90537]'"
                          class="border inline rounded-md py-[1px] px-[4px]">{{ selectedTask.type }}</p>
                        <i @click.stop="selectedTask = null; selectedDay = null"
                          class="fa-solid fa-xmark text-xs p-2 ml-4 cursor-default hover:text-red-500"></i>
                      </div>
                      <!-- --- Body --- -->
                      <div class="px-3">
                        <p class="font-bold text-left pb-[2px] pl-1">{{ selectedTask?.title }}</p>
                        <div class="grid grid-cols-2 border-y border-[#9A9A9A] p-1 text-left">
                          <p class="text-[#9A9A9A] text-xs">Hora inicio</p>
                          <p class="text-[#9A9A9A] text-xs">Hora termino</p>
                          <p class="text-xs mb-3">{{ selectedTask?.start_time ?? 'Todo el día' }}</p>
                          <p class="text-xs mb-3">{{ selectedTask?.end_time ?? 'Todo el día' }}</p>
                          <p class="text-[#9A9A9A] text-xs col-span-2">Descripción</p>
                          <p class="text-xs col-span-2">{{ selectedTask?.description ?? 'Sin descripción' }}</p>
                        </div>
                      </div>
                      <!-- --- Footer --- -->
                      <div class="px-4 pt-2 flex justify-between items-center">
                        <el-popconfirm v-if="selectedTask?.status === 'Pendiente'" confirm-button-text="Si"
                          cancel-button-text="No" icon-color="#0355B5" title="Tarea terminada?" @confirm="taskDone">
                          <template #reference>
                            <PrimaryButton class="text-xs h-5">Hecho</PrimaryButton>
                          </template>
                        </el-popconfirm>
                      
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537"
                          title="Eliminar tarea?" @confirm="deleteTask">
                          <template #reference>
                            <i class="fa-regular fa-trash-can text-primary cursor-pointer"></i>
                          </template>
                        </el-popconfirm>

                        <p v-if="selectedTask?.status === 'Terminada'" class="text-green-600 bg-green-200 px-2 py-1">Terminada</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </section>
    </div>

    <DialogModal :show="showPendentInvitationsModal" @close="showPendentInvitationsModal = false">
      <template #title>
        Invitaciones pendientes
      </template>
      <template #content>
        <div v-for="(meeting, index) in pendent_invitations_local" :key="index"
          class="lg:grid grid-cols-3 px-3 py-2 border-b border-[#a9a9a9]">
          <div>
            <h2 class="font-bold ml-2 mb-3">{{ meeting.title }}</h2>
            <p class="text-sm">Fecha: {{ formatDateDns(meeting.start_date) }} </p>
            <p v-if="!meeting.is_full_day" class="text-sm">Hora: {{ meeting.start_time }} - {{ meeting.end_time }} </p>
            <p class="text-sm">Ubicación: {{ meeting.location }} </p>
          </div>
          <div
            class="grid grid-cols-2 lg:grid-cols-5 border-t-2 border-[#cccccc] pt-2 mt-2 lg:border-none col-span-2 text-sm">
            <p>Anfitrión:</p> <span class="lg:col-span-4 truncate">{{ meeting.user?.name }}</span>
            <p>Descripción:</p> <span class="lg:col-span-4 truncate">{{ meeting.description }}</span>
            <p v-if="meeting.user.id !== $page.props.auth.user.id">Asistenca:</p>
            <span v-if="meeting.user.id !== $page.props.auth.user.id" class="lg:col-span-4 flex space-x-2">
              <div
                v-if="meeting.participants.find(item => item.user_id == $page.props.auth.user.id && item.status == 'Pendiente')">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                  @confirm="setAttendanceConfirmation('Confirmado', index)">
                  <template #reference>
                    <button
                      class="text-sm text-white bg-[#2CC513] rounded-[10px] py-px px-2 mr-3 disabled:opacity-25 disabled:cursor-not-allowed"
                      :disabled="loading">Aceptar</button>
                  </template>
                </el-popconfirm>
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                  @confirm="setAttendanceConfirmation('Rechazado', index)">
                  <template #reference>
                    <button
                      class="text-sm text-white bg-primary rounded-[10px] py-px px-2 mr-3 disabled:opacity-25 disabled:cursor-not-allowed"
                      :disabled="loading">Rechazar</button>
                  </template>
                </el-popconfirm>
                <i v-if="loading" class="fa-solid fa-spinner fa-spin text-sm text-primary"></i>
              </div>
            </span>
          </div>
        </div>
      </template>
      <template #footer>

      </template>
    </DialogModal>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import DialogModal from "@/Components/DialogModal.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import axios from 'axios';
import moment from 'moment';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      currentMonth: new Date(),
      selectedTask: null, // Variable para realizar un seguimiento de la tarea seleccionada
      selectedDay: null, // Seguinmiento del dia seleccionado
      showPendentInvitationsModal: false,
      pendent_invitations_local: this.pendent_invitations,
      today: new Date().getDate(), // Obtiene el día actual (número)
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    SecondaryButton,
    Link,
    Dropdown,
    DropdownLink,
    NotificationCenter,
    DialogModal,
  },
  props: {
    tasks: Object,
    pendent_invitations: Array,
  },
  methods: {
    async setAttendanceConfirmation(status, index) {
      this.loading = true;
      try {
        const response = await axios.put(route('calendars.set-attendance-confirmation', this.pendent_invitations[index].id), {
          status: status
        });

        if (response.status === 200) {
          this.pendent_invitations_local.splice(index, 1);

          if (response.data.item) {
            this.tasks.data.push(response.data.item);
          }

          this.$notify({
            title: 'Éxito',
            message: 'Se ha cambiado el status de la invitacion a ' + status,
            type: 'success'
          });
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      } finally {
        this.loading = false;
      }
    },
    formatDateDns(date) {
      const parsedDate = new Date(date);
      return format(parsedDate, "dd MMM, yyyy", { locale: es }); // Formato personalizado
    },
    async taskDone() {
      try {
        const response = await axios.put(route('calendars.task-done', this.selectedTask));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Tarea terminada",
            type: "success",
          });
          this.selectedTask.status = 'Terminada';
        }
      } catch (error) {
        console.log(error);
      }


    },
    deleteTask() {
      this.$inertia.delete(route('calendars.destroy', this.selectedTask));
      this.$notify({
        title: "Éxito",
        message: "Tarea eliminada",
        type: "success",
      });

      this.selectedTask = null;
    },
    changeMonth(offset) {
      const newMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + offset, 1);
      this.currentMonth = newMonth;
    },
    isTaskDay(task, day) {
      if (day) {
        const taskStartDate = new Date(task.start_date);
        const currentDay = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), day);

        // Convierte las fechas a objetos Moment
        const momentFecha1 = moment(taskStartDate);
        const momentFecha2 = moment(currentDay);

        return momentFecha1.isSame(momentFecha2);
      }
      return false;
    },
    getDurationTask() {
      // Convierte las fechas en objetos Date
      const startDate = new Date(this.selectedTask.start_date);
      const finishDate = new Date(this.selectedTask.finish_date);

      // Calcula la diferencia en milisegundos
      const diferenciaEnMilisegundos = finishDate - startDate;

      // Convierte la diferencia en días
      const duracionEnDias = diferenciaEnMilisegundos / (1000 * 60 * 60 * 24);

      return duracionEnDias;
    },
    formatDate(dateString) {
      if (dateString) {
        const date = new Date(dateString);
        const options = {
          hour: 'numeric',
          minute: 'numeric',
          hour12: true, // Habilitar el formato AM/PM
        };
        return date.toLocaleTimeString(undefined, options);
      }
      return ''; // Manejar el caso en el que la fecha sea nula
    }
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

