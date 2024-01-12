<template>
  <div v-if="pageLoading" class="absolute z-10 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
  </div>
  <div v-if="pageLoading"
    class="absolute z-20 top-1/2 left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
    <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
  </div>
  <div class="bg-[#D9D9D9] rounded-lg lg:w-4/5 mx-auto py-6 px-10">
    <div>
      <div class="flex items-center justify-between">
        <span class="text-secondary">Incidencias</span>
        <el-tag v-if="additionalTime" type="success">Tiempo adicional autorizado {{ additionalTime.time_requested
        }}</el-tag>
      </div>
      <div class="flex justify-center items-center">
        <i class="fa-solid fa-circle-user mr-3 text-xl"></i>
        <p>{{ user.name }}</p>
      </div>
      <p class="text-center">{{ user.employee_properties.department }}</p>
      <div class="overflow-x-auto">
        <table class="items-center w-full bg-transparent mt-9">
          <thead class="text-primary border-b-2 border-[#9a9a9a] text-[16px]">
            <tr>
              <th>Día</th>
              <th>Entrada</th>
              <th>Salida</th>
              <th>Hrs pausadas</th>
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody v-if="!loading">
            <template v-for="(attendance, index) in processedAttendances" :key="attendance.id">
              <tr v-if="!attendance.incident" class="text-gray-600 text-center">
                <th class="py-2 text-left mt-3 text-sm">
                  <span class="ml-3 font-bold"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 text-xs py-2">
                  <el-tooltip v-if="attendance.late" :content="'Retardo de ' + attendance.late + ' minutos'"
                    placement="top">
                    <input v-model="attendance.check_in" type="time"
                      class="bg-transparent text-sm rounded-md border-gray-400 text-amber-600">
                  </el-tooltip>
                  <input v-else v-model="attendance.check_in" type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td class="px-6 text-xs py-2">
                  <input v-model="attendance.check_out" type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td @click="showPausesModal = true; currentPayrollUser = attendance" v-if="attendance.total_break_time"
                  class="px-6 text-xs py-2 w-32 cursor-pointer">
                  <el-tooltip placement="right">
                    <template #content>
                      <p class="text-yellow-500">Resumen de pausas</p>
                      <ol>
                        <li v-for="(pausa, index) in attendance.pausas" :key="index">
                          <span class="text-yellow-500">{{ index + 1 }}.</span> De {{ formatTimeTo12Hour(pausa.start) }} a
                          {{ formatTimeTo12Hour(pausa.finish) ??
                            'Sin reanudar' }}
                        </li>
                        <p class="leading-3 text-[10px]">
                          <span class="text-yellow-400">{{ attendance.total_break_time }}</span> es el tiempo que el colaborador
                          debe reponer<br>
                          después de su hora de salida. El tiempo <br>
                          de comida no lo tendrá que reponer, <span class="text-yellow-400">
                            ({{ user.employee_properties.work_days[getValueByIndex(index)]?.break }} minutos)</span><br>
                          ya está restado del tiempo total que pausó. <br>
                        </p>
                      </ol>
                    </template>
                    <p>{{ attendance.total_break_time }} <i class="fa-solid fa-circle-info"></i></p>
                  </el-tooltip>
                </td>
                <td v-else @click="showPausesModal = true; currentPayrollUser = attendance"
                  class="px-6 text-xs py-2 w-32 cursor-pointer">
                  <i class="fa-solid fa-minus"></i>
                </td>
                <td v-if="attendance.total_worked_time" class="px-6 text-xs py-2 w-32">
                  {{ attendance.total_worked_time.formatted }}
                  <span v-if="attendance.extras_enabled" class="text-green-500"> +{{ attendance.extras.formatted }}
                    extras</span>
                </td>
                <td v-else class="px-6 text-xs py-2 w-32">
                  <i class="fa-solid fa-minus"></i>
                </td>
                <td class="w-11">
                  <el-dropdown trigger="click" @command="handleCommand">
                    <span class="w-6 h-6 rounded-full hover:bg-[#CCCCCC] cursor-pointer flex items-center justify-center">
                      <i class="fa-solid fa-ellipsis-vertical text-primary"></i>
                    </span>
                    <template #dropdown>
                      <el-dropdown-menu>
                        <el-dropdown-item v-if="attendance.late" :command="'late-' + attendance.id">
                          Quitar retardo</el-dropdown-item>
                        <el-dropdown-item v-else :command="'late-' + attendance.id">
                          Poner retardo</el-dropdown-item>
                        <el-dropdown-item v-if="!attendance.extras_enabled" :command="'extras-' + attendance.id">
                          Activar extras dobles</el-dropdown-item>
                        <el-dropdown-item v-else :command="'extras-' + attendance.id">
                          Desactivar extras dobles</el-dropdown-item>
                        <template v-for="(item, index1) in justifications" :key="item.id">
                          <el-dropdown-item v-if="item.id !== 7" :command="item.id + '-' + attendance.id + '-' + index">
                            {{ item.name }}</el-dropdown-item>
                        </template>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </td>
              </tr>
              <tr v-else class="text-gray-600 text-center">
                <th class="py-2 text-left mt-3 text-sm">
                  <span class="ml-3 font-bold"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 py-2" colspan="2">
                  <p class="text-lg rounded-xl py-2"
                    :class="'bg-' + attendance.incident.additionals.color + '-100 text-' + attendance.incident.additionals.color + '-600'">
                    {{ attendance.incident.name }}</p>
                </td>
                <td class="px-6 text-xs py-2 w-32">
                  <i class="fa-solid fa-minus"></i>
                </td>
                <td class="px-6 text-xs py-2 w-32">
                  <i class="fa-solid fa-minus"></i>
                </td>
                <td class="w-11">
                  <el-dropdown trigger="click" @command="handleCommand">
                    <span class="w-6 h-6 rounded-full hover:bg-[#CCCCCC] cursor-pointer flex items-center justify-center">
                      <i class="fa-solid fa-ellipsis-vertical text-primary"></i>
                    </span>
                    <template #dropdown>
                      <el-dropdown-menu>
                        <el-dropdown-item :command="'attendance-' + attendance.id + '-' + index">
                          Poner asistencia</el-dropdown-item>
                        <template v-for="(item, index1) in justifications" :key="item.id">
                          <el-dropdown-item v-if="item.id !== attendance.incident.id && item.id !== 7"
                            :command="item.id + '-' + attendance.id + '-' + index">
                            {{ item.name }}</el-dropdown-item>
                        </template>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
        <div v-if="loading" class="flex justify-center items-center pt-10">
          <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
        </div>
      </div>
      <div class="mt-6 text-right">
        <CancelButton @click="this.$emit('closeIncidentTable')">Cancelar</CancelButton>
        <PrimaryButton @click="updateAttendances" class="ml-3">Guardar</PrimaryButton>
      </div>
    </div>
  </div>
  <DialogModal :show="showPausesModal" @close="showPausesModal = false">
    <template #title>
      <h1>Gestor de pausas</h1>
    </template>
    <template #content>
      <div v-for="(pausa, index) in currentPayrollUser.pausas" :key="index" class="grid grid-cols-2 gap-3">
        <IconInput v-model="pausa.start" inputPlaceholder="Hora de inicio *" inputType="time">
          <el-tooltip content="Hora de inicio de la pausa" placement="top">
            <i class="fa-regular fa-circle-play"></i>
          </el-tooltip>
        </IconInput>
        <IconInput v-model="pausa.finish" inputPlaceholder="Hora de término *" inputType="time">
          <el-tooltip content="Hora de término de la pausa" placement="top">
            <i class="fa-regular fa-circle-stop"></i>
          </el-tooltip>
        </IconInput>
      </div>
      <div v-if="!currentPayrollUser?.pausas?.length" class="grid grid-cols-2 gap-3 pt-6">
        <IconInput v-model="newPausa.start" inputPlaceholder="Hora de inicio *" inputType="time">
          <el-tooltip content="Hora de inicio de la pausa" placement="top">
            <i class="fa-regular fa-circle-play"></i>
          </el-tooltip>
        </IconInput>
        <IconInput v-model="newPausa.finish" inputPlaceholder="Hora de término *" inputType="time">
          <el-tooltip content="Hora de término de la pausa" placement="top">
            <i class="fa-regular fa-circle-stop"></i>
          </el-tooltip>
        </IconInput>
      </div>
    </template>
    <template #footer>
      <CancelButton @click="showPausesModal = false">Cerrar</CancelButton>
      <PrimaryButton @click="updatePausas()">Guardar cambios</PrimaryButton>
    </template>
  </DialogModal>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import DialogModal from "@/Components/DialogModal.vue";
import moment from "moment";
import axios from "axios";

export default {
  data() {
    return {
      processedAttendances: [],
      loading: false,
      pageLoading: false,
      additionalTime: null,
      showPausesModal: false,
      currentPayrollUser: null,
      newPausa: {
        start: null,
        finish: null,
      },
    }
  },
  emits: ['closeIncidentTable'],
  props: {
    justifications: Array,
    user: Number,
    payrollId: Number,
  },
  components: {
    PrimaryButton,
    CancelButton,
    Dropdown,
    DropdownLink,
    DialogModal,
    IconInput,
  },
  watch: {
    user: {
      immediate: true,
      handler() {
        this.getAttendances();
      }
    }
  },
  methods: {
    getValueByIndex(index) {
      const mapping = [5, 6, 0, 1, 2, 3, 4];
      
      // Asegúrar que el índice esté dentro del rango del arreglo
      if (index >= 0 && index < mapping.length) {
        return mapping[index];
      }
    },
    async updatePausas() {
      try {
        let pausas = this.currentPayrollUser.pausas;
        if (this.newPausa.start) {
          pausas = [this.newPausa];
        }

        const response = await axios.put(route('users.update-pausas', this.currentPayrollUser.id), { pausas: pausas });

        if (response.status === 200) {
          this.showPausesModal = false;
          this.currentPayrollUser = null;
          this.getAttendances();
          this.$notify({
            title: 'Éxito',
            message: 'Pausa(s) actualizada(s)',
            type: 'success'
          });

          // reset new pausa
          this.newPausa.start = null;
          this.newPausa.finish = null;
        }
      } catch (error) {
        console.log(error);
      }
    },
    formatTimeTo12Hour(time) {
      if (time === null) return null;
      const formatted = moment(time, 'HH:mm:ss').format('h:mma');
      return formatted;
    },
    handleCommand(command) {
      const commandName = command.split('-')[0];
      const rowId = command.split('-')[1];

      if (commandName == 'late') {
        this.handleLate(rowId);
      } else if (commandName == 'extras') {
        this.handleExtras(rowId);
      } else if (commandName == 'attendance') {
        const index = command.split('-')[2];
        const date = this.processedAttendances[index].date.estandard;
        this.handleAttendance(rowId, date);
      } else {
        const index = command.split('-')[2];
        const date = this.processedAttendances[index].date.estandard;
        this.handleIncidents(rowId, commandName, date);
      }
    },
    async getAttendances(loadingState = true) {
      try {
        this.loading = loadingState;
        const response = await axios.post(route('payrolls.processed-attendances'), {
          user_id: this.user.id,
          payroll_id: this.payrollId
        });

        if (response.status === 200) {
          this.processedAttendances = response.data.items;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    async handleLate(payrollUserId) {
      try {
        this.pageLoading = true;
        const response = await axios.post(route('payrolls.handle-late'), {
          payroll_user_id: payrollUserId,
        });

        if (response.status === 200) {
          console.log(response.data.late);
          this.processedAttendances.find(item => item.id == payrollUserId).late = response.data.late;
          this.$notify({
            title: 'Éxito',
            message: 'Retardo cambiado',
            type: 'success'
          });
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.pageLoading = false;
      }
    },
    async handleExtras(payrollUserId) {
      try {
        this.pageLoading = true;
        const response = await axios.post(route('payrolls.handle-extras'), {
          payroll_user_id: payrollUserId,
        });

        if (response.status === 200) {
          let oldItem = this.processedAttendances.find(item => item.id == payrollUserId);
          oldItem.extras_enabled = response.data.extras_enabled
          oldItem.extras = response.data.extras;
          this.$notify({
            title: 'Éxito',
            message: 'Extras cambiado',
            type: 'success'
          });
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.pageLoading = false;
      }
    },
    async handleIncidents(payrollUserId, incidentId, date) {
      try {
        this.pageLoading = true;
        const response = await axios.post(route('payrolls.handle-incidents'), {
          payroll_user_id: payrollUserId,
          incident_id: incidentId,
          date: date,
          payroll_id: this.payrollId,
          user_id: this.user.id,
        });

        if (response.status === 200) {
          const index = this.processedAttendances.findIndex(item => item.date.estandard == date);
          this.processedAttendances[index] = response.data.item;
          this.$notify({
            title: response.data.title,
            message: response.data.message,
            type: response.data.type
          });
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.pageLoading = false;
      }
    },
    async handleAttendance(payrollUserId, date) {
      try {
        this.pageLoading = true;
        const response = await axios.post(route('payrolls.handle-attendance'), {
          payroll_user_id: payrollUserId,
          date: date,
          payroll_id: this.payrollId,
          user_id: this.user.id,
        });

        if (response.status === 200) {
          const index = this.processedAttendances.findIndex(item => item.id == payrollUserId);
          this.processedAttendances[index].incident = response.data.item.incident;
          this.$notify({
            title: 'Éxito',
            message: 'Asistencia registrada',
            type: 'success'
          });
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.pageLoading = false;
      }
    },
    async updateAttendances() {
      try {
        this.pageLoading = true;
        const response = await axios.post(route('payrolls.update-attendances'), {
          attendances: this.processedAttendances,
          user_id: this.user.id
        });

        if (response.status === 200) {
          this.getAttendances(false);
          this.$notify({
            title: 'Éxito',
            message: response.data.message,
            type: 'success'
          });
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.pageLoading = false;
      }
    },
    async getAuthorizedAdditionalTime() {
      try {
        const response = await axios.post(route('payrolls.get-additional-time'), {
          payroll_id: this.payrollId,
          user_id: this.user.id
        });
        if (response.status === 200) {
          this.additionalTime = response.data.item;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.getAuthorizedAdditionalTime();
  },
}
</script>