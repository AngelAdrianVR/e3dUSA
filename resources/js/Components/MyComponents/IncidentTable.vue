<template>
  <div v-if="pageLoading" class="absolute left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
  </div>
  <div v-if="pageLoading"
    class="absolute top-1/2 left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
    <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
  </div>
  <div class="bg-[#D9D9D9] rounded-lg lg:w-4/5 mx-auto py-6 px-10">
    <div>
      <span class="text-secondary">Incidencias</span>
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
              <th>Inicio break</th>
              <th>Fin break</th>
              <th>Salida</th>
              <th>Hrs break</th>
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
                  <input v-model="attendance.start_break" type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td class="px-6 text-xs py-2">
                  <input v-model="attendance.end_break" type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td class="px-6 text-xs py-2">
                  <input v-model="attendance.check_out" type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td v-if="attendance.total_break_time" class="px-6 text-xs py-2 w-32">
                  {{ attendance.total_break_time }}
                </td>
                <td v-else class="px-6 text-xs py-2 w-32">
                  <i class="fa-solid fa-minus"></i>
                </td>
                <td v-if="attendance.total_worked_time" class="px-6 text-xs py-2 w-32">
                  {{ attendance.total_worked_time }}
                </td>
                <td v-else class="px-6 text-xs py-2 w-32">
                  <i class="fa-solid fa-minus"></i>
                </td>
                <td class="w-11">
                  <el-dropdown v-if="!pageLoading" trigger="click" @command="handleCommand">
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
                        <el-dropdown-item v-for="(item, index1) in justifications" :key="item.id"
                          :command="item.id + '-' + attendance.id">
                          {{ item.name }}</el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </td>
              </tr>
              <tr v-else class="text-gray-600 text-center">
                <th class="py-2 text-left mt-3 text-sm">
                  <span class="ml-3 font-bold"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 py-2" colspan="4">
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
                  <el-dropdown v-if="!pageLoading" trigger="click" @command="handleCommand">
                    <span class="w-6 h-6 rounded-full hover:bg-[#CCCCCC] cursor-pointer flex items-center justify-center">
                      <i class="fa-solid fa-ellipsis-vertical text-primary"></i>
                    </span>
                    <template #dropdown>
                      <el-dropdown-menu>
                        <el-dropdown-item :command="'attendance-' + attendance.id + '-' + index">
                          Poner asistencia</el-dropdown-item>
                        <template v-for="(item, index1) in justifications" :key="item.id">
                          <el-dropdown-item v-if="item.id !== attendance.incident.id"
                            :command="item.id + '-' + attendance.id">
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
        <CancelButton @click="this.$emit(closeIncidentTable)">Cancelar</CancelButton>
        <PrimaryButton class="ml-3">Guardar</PrimaryButton>
      </div>
    </div>
  </div>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

export default {
  data() {
    return {
      processedAttendances: [],
      loading: false,
      pageLoading: false,
    }
  },
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
        this.handleIncidents(rowId, commandName);
      }
    },
    async getAttendances() {
      try {
        this.loading = true;
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
          this.processedAttendances.find(item => item.id == payrollUserId).extras_enabled = response.data.extras_enabled;
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
    async handleIncidents(payrollUserId, incidentId) {
      try {
        this.pageLoading = true;
        const response = await axios.post(route('payrolls.handle-incidents'), {
          payroll_user_id: payrollUserId,
          incident_id: incidentId,
        });

        if (response.status === 200) {
          const index = this.processedAttendances.findIndex(item => item.id == payrollUserId);
          this.processedAttendances[index] = response.data.item;
          this.$notify({
            title: 'Éxito',
            message: 'Incidencia cambiada',
            type: 'success'
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
          this.processedAttendances[index] = response.data.item;
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
    }
  },
}
</script>