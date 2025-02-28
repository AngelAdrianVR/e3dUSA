<template>
  <Head v-if="toPrint" :title="'Nominas ' + payroll?.week" />
  <div v-if="!loading" :class="toPrint ? 'h-[50vh]' : null" class="grid grid-cols-4 md:grid-cols-3 lg:py-6 lg:px-10 lg:mb-0 text-[10px] md:text-sm">
    <div class="bg-transparent rounded-lg lg:mx-auto"
      :class="dontShowDetails ? 'col-span-full' : 'col-span-3 md:col-span-2 mr-auto w-5/6'">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <i class="fa-solid fa-circle-user mr-3 text-xl"></i>
          <p>{{ user.name }}</p>
        </div>
        <p class="text-xs">{{ getRemainingHoursWeekly() }} para completar semana</p>
      </div>
      <div class="overflow-x-auto shadow-md mt-3 rounded-lg">
        <table class="items-center w-full bg-transparent">
          <thead class="text-primary bg-[#e6e6e6] px-3">
            <tr class="rounded-tl-lg rounded-tr-lg">
              <th class="rounded-tl-lg">Día</th>
              <th>T. autorizado</th>
              <th>Entrada</th>
              <th>Salida</th>
              <th>T. a reponer</th>
              <th class="rounded-tr-lg py-px lg:py-2">Hrs x dia</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(attendance, index) in processedAttendances" :key="attendance.id">
              <tr v-if="!attendance.incident" class="text-gray-600 text-center border-b border-[#a9a9a9]">
                <th class="py-px lg:py-2 text-left text-sm">
                  <span class="ml-3 text-xs">
                    {{ attendance.date?.formatted }}
                  </span>
                </th>
                <td class="px-6 text-xs py-px lg:py-2">
                  <span v-if="attendance.additional_time?.authorized_at">
                    {{ formattedTimeToHoursAndMinutes(attendance.additional_time.time_requested) }}
                  </span>
                  <i v-else class="fa-solid fa-minus"></i>
                </td>
                <td class="px-6 text-xs py-px lg:py-2">
                  <p class="bg-transparent text-sm" :class="{
                    'text-amber-500': attendance.late > 0 && attendance.late < 15,
                    'text-red-600': attendance.late >= 15,
                  }">
                    {{ attendance.check_in }}
                    <i v-if="!attendance.late" class="fa-solid fa-face-smile text-green-600 ml-1"></i>
                    <i v-else-if="attendance.late < 15" class="fa-solid fa-face-meh ml-1"></i>
                    <i v-else class="fa-solid fa-face-sad-tear ml-1"></i>
                  </p>
                </td>
                <td class="px-6 text-xs py-px lg:py-2">
                  <p class="bg-transparent text-sm">{{ attendance.check_out }}</p>
                </td>
                <td v-if="attendance.total_break_time" class="px-6 text-xs py-px lg:py-2 w-32">
                  <el-tooltip placement="right">
                    <template #content>
                      <p class="text-yellow-500">Resumen de pausas</p>
                      <ol>
                        <li v-for="(pausa, index2) in attendance.pausas" :key="index2">
                          <span class="text-yellow-500">{{ index2 + 1 }}.</span> De {{ formatTimeTo12Hour(pausa.start) }}
                          a
                          {{ formatTimeTo12Hour(pausa.finish) ??
                            'Sin reanudar' }}
                        </li>
                      </ol>
                      <p class="leading-3 text-[10px]">
                        <span class="text-yellow-400">{{ attendance.total_break_time }}</span> es el tiempo que tienes
                        que reponer<br>
                        después de tu hora de salida. El tiempo <br>
                        de comida no lo tienes que reponer, <span class="text-yellow-400">
                          ({{ user.employee_properties.work_days[getValueByIndex(index)]?.break }} minutos)</span><br>
                        ya está restado del tiempo total que pausaste. <br>
                      </p>
                    </template>
                    <p>{{ attendance.total_break_time }} <i class="fa-solid fa-circle-info"></i></p>
                  </el-tooltip>
                </td>
                <td v-else class="px-6 text-xs py-px lg:py-2 w-32">
                  <i class="fa-solid fa-minus"></i>
                </td>
                <td v-if="attendance.total_worked_time" class="px-6 text-xs py-px lg:py-2 w-32">
                  {{ attendance.total_worked_time.formatted }}
                  <span v-if="attendance.extras_enabled" class="text-green-500"> +{{ attendance.extras.formatted }}
                    extras</span>
                </td>
              </tr>
              <tr v-else class="text-gray-600 text-center border-b border-[#a9a9a9]">
                <th class="py-1 text-left mt-3 text-sm">
                  <span class="ml-3 text-xs"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 py-2" colspan="5">
                  <p class="text-xs lg:text-sm rounded-xl py-px lg:py-2"
                    :class="'bg-' + attendance.incident.additionals.color + '-100 text-' + attendance.incident.additionals.color + '-600'">
                    {{ attendance.incident.name }}</p>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
    <div v-if="!dontShowDetails" class="mr-5">
      <p class="mb-3">
        <strong class="mr-3"> Semana {{ payroll?.week }} </strong>
        {{ payroll?.start_date }} - {{ payroll?.end_date }}
      </p>
      <div class="flex flex-col mr-5">
        <p class="grid grid-cols-3 gap-x-1">
          <span>Avance semana</span>
          <span class="text-center">{{ getWeekProcessInPercentage() }}</span>
          <span></span>
        </p>
        <p class="grid grid-cols-3 gap-x-1">
          <span>Días trabajados</span>
          <span class="text-center">{{ getWorkedDays().length }}</span>
          <span>${{ getWorkedDaysSalary().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
        </p>
        <p v-if="getIllness().length" class="grid grid-cols-3 gap-x-1">
          <span>Incapacidades</span>
          <span class="text-center">{{ getIllness().length }}</span>
          <span>${{ getIllnessSalary().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
        </p>
        <p class="grid grid-cols-3 gap-x-1">
          <span>T. adicional autorizado</span>
          <span class="text-center">{{ formattedTotalAdditionalTime() }}</span>
          <span class=""></span>
        </p>
        <p v-if="getHolidays().length" class="grid grid-cols-3 gap-x-1">
          <span>Días Feriados</span>
          <span class="text-center">{{ getHolidays().length }}</span>
          <!-- <span>${{ (getUserWorkDayByDate(getHolidays()[0].date.estandard).salary).toLocaleString('en-US', {
            minimumFractionDigits: 2
          }) }}</span> -->
        </p>
        <p v-if="getVacations().length" class="grid grid-cols-3 gap-x-1">
          <span>Vacaciones</span>
          <span class="text-center">{{ getVacations().length }}</span>
          <span>${{ getVacationsAmount().toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
        </p>
        <p v-if="getVacations().length" class="grid grid-cols-3 gap-x-1">
          <span>Prima vacacional</span>
          <span class="text-center"></span>
          <span>${{ (getVacationsAmount() * 0.25).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
        </p>
        <p v-for="(bonus, index) in bonuses" :key="index" class="grid grid-cols-3 gap-x-1">
          <span>{{ bonus.name }}</span>
          <span class="text-center"></span>
          <span class="leading-tight">
            ${{ bonus.amount.number_format }}
            <p v-if="bonus.additionals" class="text-red-600 text-[7px]">{{ bonus.additionals }}</p>
          </span>
        </p>
        <p v-for="(discount, index) in discounts" :key="index" class="grid grid-cols-3 gap-x-1 text-red-500">
          <span>{{ discount.name }}</span>
          <span class="text-center"></span>
          <span>-${{ discount.amount.number_format }}</span>
        </p>
        <p v-if="extras" class="grid grid-cols-3 gap-x-1">
          <span>Horas extras</span>
          <span class="text-center">{{ extras.formatted }}</span>
          <span>${{ extras.amount.number_format }}</span>
        </p>
        <p v-if="bonusForExtras" class="grid grid-cols-3 gap-x-1">
          <span>Bono por extras solicitados</span>
          <span class="text-center"></span>
          <span>${{ bonusForExtras }}</span>
        </p>
        <p v-if="extras" class="grid grid-cols-3 gap-x-1">
          <span>Total</span>
          <span class="text-center"></span>
          <span>${{ getTotal().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
        </p> <br>
        <p class="flex space-x-2">
        <p>Firma</p>
        <div class="border-b-2 border-black w-48"></div>
        </p>
      </div>
    </div>
  </div>
  <div v-else class="flex justify-center items-center pt-10">
    <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
  </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import moment from 'moment';

export default {
  data() {
    return {
      loading: true,
      processedAttendances: [],
      payroll: null,
      bonuses: null,
      discounts: null,
      extras: null,
      bonusForExtras: null,
      additionalTimes: null,
      totalAdditionalTime: 0,
    }
  },
  props: {
    user: Object,
    payrollId: Number,
    dontShowDetails: {
      type: Boolean,
      default: false,
    },
    toPrint: {
      type: Boolean,
      default: false,
    }
  },
  components: {
    PrimaryButton,
    CancelButton,
    Dropdown,
    DropdownLink,
    Head,
  },
  methods: {
    fetchData() {
      this.getAttendances();
      this.getPayroll();
      this.getBonuses();
      this.getDiscounts();
      this.getExtras();
      this.getExtrasRequested();
      this.getAuthorizedAdditionalTimes();
    },
    getValueByIndex(index) {
      const mapping = [5, 6, 0, 1, 2, 3, 4];
      
      // Asegúrar que el índice esté dentro del rango del arreglo
      if (index >= 0 && index < mapping.length) {
        return mapping[index];
      }
    },
    getDayOfWeek(date) {
      const fechaMoment = moment(date);

      return fechaMoment.day();
    },
    formatTimeTo12Hour(time) {
      if (time === null) return null;
      const formatted = moment(time, 'HH:mm:ss').format('h:mma');
      return formatted;
    },
    async getAttendances() {
      this.loading = true;
      try {
        const response = await axios.post(route('payrolls.processed-attendances'), {
          user_id: this.user.id,
          payroll_id: this.payrollId
        });

        if (response.status === 200) {
          this.processedAttendances = response.data.items;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async getPayroll() {
      try {
        const response = await axios.post(route('payrolls.get-payroll'), {
          payroll_id: this.payrollId
        });

        if (response.status === 200) {
          this.payroll = response.data.item;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async getBonuses() {
      try {
        const response = await axios.post(route('payrolls.get-bonuses'), {
          payroll_id: this.payrollId,
          user_id: this.user.id
        });

        if (response.status === 200) {
          this.bonuses = response.data.item;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async getDiscounts() {
      try {
        const response = await axios.post(route('payrolls.get-discounts'), {
          payroll_id: this.payrollId,
          user_id: this.user.id
        });

        if (response.status === 200) {
          this.discounts = response.data.item;
        }
      } catch (error) {
        console.log(error);
      }
    },
    async getExtras() {
      try {
        const response = await axios.post(route('payrolls.get-extras'), {
          payroll_id: this.payrollId,
          user_id: this.user.id
        });

        if (response.status === 200) {
          this.extras = response.data.item;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    async getExtrasRequested() {
      try {
        const response = await axios.post(route('payrolls.get-extras-requests'), {
          payroll_id: this.payrollId,
          user_id: this.user.id
        });

        if (response.status === 200) {
          this.bonusForExtras = response.data.item;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    async getAuthorizedAdditionalTimes() {
      try {
        const response = await axios.post(route('payrolls.get-additional-time'), {
          payroll_id: this.payrollId,
          user_id: this.user.id
        });
        if (response.status === 200) {
          this.additionalTimes = response.data.items;
          this.getTotalAditionalTimeInHours();
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    getVacations() {
      return this.processedAttendances.filter(item => item.incident?.id === 3);
    },
    getWorkedDays() {
      return this.processedAttendances.filter(item => item.incident?.id == 2 || item.incident == null);
    },
    getHolidays() {
      return this.processedAttendances.filter(item => item.incident?.id < 0);
    },
    getIllness() {
      return this.processedAttendances.filter(item => item.incident?.id == 8);
    },
    getWorkedDaysSalary() {
      // total_worked_time ya toma en cuenta si existe solicitud de tiempo adicional
      let totalSalary =
        this.getWorkedDays().reduce(
          (accum, object) => accum + object.total_worked_time?.hours * object.additionals.salary.hour, 0
        );

      return totalSalary;
    },
    getIllnessSalary() {
      let totalSalary = 0;

      this.getIllness().forEach(element => {
        totalSalary += this.getUserWorkDayByDate(element.date.estandard).salary * 0.6;
      });

      return totalSalary;
    },
    formattedTotalAdditionalTime() {
      const hours = parseInt(this.totalAdditionalTime); // Calcular las horas
      const minutes = Math.round((this.totalAdditionalTime - hours) * 60); // Calcular los minutos
      return `${hours}h ${minutes}m`; // Formatear como "Xh Ym"
    },
    formattedTimeToHoursAndMinutes(time) {
      const hours = time.split(':')[0];
      const minutes = time.split(':')[1];

      return `${parseInt(hours)}h ${parseInt(minutes)}m`;
    },
    getRemainingHoursWeekly() {
      const totalWeekHours = this.getWorkedDays().reduce((accum, object) => accum + object.total_worked_time?.hours, 0);
      const remainingHours = this.user.employee_properties.hours_per_week - totalWeekHours;

      const hours = parseInt(remainingHours);
      const minutes = Math.round((remainingHours - hours) * 60);

      return hours + 'h ' + minutes + 'm';
    },
    getWeekProcessInPercentage() {
      let totalWeekHours = this.getWorkedDays().reduce((accum, object) => accum + object.total_worked_time?.hours, 0);

      // sumar sueldo por dias feriados
      this.getHolidays().forEach(element => {
        totalWeekHours += this.getUserWorkDayByDate(element.date.estandard).hours;
      });

      // sumar sueldo por incapacidades (60%)
      this.getIllness().forEach(element => {
        totalWeekHours += this.getUserWorkDayByDate(element.date.estandard).hours * 0.6;
      });

      const percentage = (totalWeekHours * 100) / this.user.employee_properties.hours_per_week;

      return Math.round(percentage) + '%';
    },
    getVacationsAmount() {
      let totalPay = 0;
      // sumar sueldo por vacaciones
      this.getVacations().forEach(element => {
        totalPay += this.getUserWorkDayByDate(element.date.estandard).salary;
      });

      return totalPay;
    },
    getUserWorkDayByDate(date) {
      const dateMoment = moment(date, 'DD-MM-YYYY');
      // Obtener el día de la semana (0 para domingo, 1 para lunes, ..., 6 para sábado)
      const dayOfWeek = dateMoment.day();
      return this.user.employee_properties.work_days[dayOfWeek];
    },
    getTotal() {
      let holyDays = 0;
      let vacations = 0;
      let illness = 0;

      // sumar sueldo por dias feriados
      this.getHolidays().forEach(element => {
        holyDays += this.getUserWorkDayByDate(element.date.estandard).salary;
      });

      // sumar sueldo por vacaciones
      this.getVacations().forEach(element => {
        vacations += this.getUserWorkDayByDate(element.date.estandard).salary * 1.25;
      });

      // sumar sueldo por incapacidades
      this.getIllness().forEach(element => {
        illness += this.getUserWorkDayByDate(element.date.estandard).salary * 0.6;
      });

      const workedDays = this.getWorkedDaysSalary();
      const bonuses = this.bonuses?.reduce((accumulator, bonus) => {
        return accumulator + bonus.amount.raw;
      }, 0) ?? 0;

      const discounts = this.discounts?.reduce((accumulator, discount) => {
        return accumulator + discount.amount.raw;
      }, 0) ?? 0;

      const total = vacations
        + workedDays
        + holyDays
        + bonuses
        + illness
        - discounts
        + this.extras.amount.raw
        + this.bonusForExtras;

      return total;
    },
    getTotalAditionalTimeInHours() {
      this.additionalTimes?.forEach(element => {
        const time = element.time_requested;
        const hours = time.split(':')[0];
        const minutes = time.split(':')[1];

        this.totalAdditionalTime += parseFloat(hours) + parseFloat(minutes / 60);
      });
    },
  },
  watch: {
    payrollId(newPayrollId) {
      this.fetchData();
    },
  },
  mounted() {
    this.fetchData();
  }
}
</script>