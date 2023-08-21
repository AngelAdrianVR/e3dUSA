<template>
  <div v-if="!loading" class="grid grid-cols-4 md:grid-cols-3 lg:py-6 lg:px-10 mb-6 lg:mb-0 text-[10px] md:text-sm">
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
              <th class="rounded-tl-lg md:w-48">Día</th>
              <th>Entrada</th>
              <th>Salida</th>
              <th>Break</th>
              <th class="rounded-tr-lg py-px lg:py-2">Hrs x dia</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(attendance, index) in processedAttendances" :key="attendance.id">
              <tr v-if="!attendance.incident" class="text-gray-600 text-center border-b border-[#a9a9a9]">
                <th class="py-px lg:py-2 text-left text-sm">
                  <span class="ml-3 font-bold text-xs"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 text-xs py-px lg:py-2">
                  <p class="bg-transparent text-sm" :class="{
                    'text-amber-500': attendance.late > 0 && attendance.late < 15,
                    'text-red-600': attendance.late >= 15,
                  }">
                    {{ attendance.check_in }}
                    <i v-if="!attendance.late" class="fa-solid fa-face-smile text-yellow-400 ml-1"></i>
                    <i v-else-if="attendance.late < 15" class="fa-solid fa-face-meh ml-1"></i>
                    <i v-else class="fa-solid fa-face-sad-tear ml-1"></i>
                  </p>
                </td>
                <td class="px-6 text-xs py-px lg:py-2">
                  <p class="bg-transparent text-sm">{{ attendance.check_out }}</p>
                </td>
                <td v-if="attendance.total_break_time" class="px-6 text-xs py-px lg:py-2 w-32">
                  {{ attendance.total_break_time }}
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
                  <span class="ml-3 font-bold"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 py-2" colspan="4">
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
          <span>Días trabajados</span>
          <span class="text-center">{{ getWorkedDays().length }}</span>
          <span>${{ getWorkedDaysSalary().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
        </p>
        <p class="grid grid-cols-3 gap-x-1">
          <span>Tiempo adicional</span>
          <span class="text-center">{{ additionalTime?.time_requested ?? '00:00' }}</span>
          <span class=""></span>
        </p>
        <p v-if="getHolidays().length" class="grid grid-cols-3 gap-x-1">
          <span>Días Feriados</span>
          <span class="text-center">{{ getHolidays().length }}</span>
          <span>${{ (user.employee_properties.salary.day * getHolidays().length).toLocaleString('en-US', {
            minimumFractionDigits: 2
          }) }}</span>
        </p>
        <p v-if="getVacations().length" class="grid grid-cols-3 gap-x-1">
          <span>Vacaciones</span>
          <span class="text-center">{{ getVacations().length }}</span>
          <span>${{ getVacations().length * user.employee_properties.salary.day }}</span>
        </p>
        <p v-if="getVacations().length" class="grid grid-cols-3 gap-x-1">
          <span>Prima vacacional</span>
          <span class="text-center"></span>
          <span>${{ getVacations().length * user.employee_properties.salary.day * 0.25 }}</span>
        </p>
        <p v-for="(bonus, index) in bonuses" :key="index" class="grid grid-cols-3 gap-x-1">
          <span>{{ bonus.name }}</span>
          <span class="text-center"></span>
          <span>${{ bonus.amount.number_format }}</span>
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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

export default {
  data() {
    return {
      loading: true,
      processedAttendances: [],
      payroll: null,
      bonuses: null,
      discounts: null,
      extras: null,
      additionalTime: null,
    }
  },
  props: {
    user: Number,
    payrollId: Number,
    dontShowDetails: {
      type: Boolean,
      default: false,
    }
  },
  components: {
    PrimaryButton,
    CancelButton,
    Dropdown,
    DropdownLink,
  },
  methods: {
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
    getVacations() {
      return this.processedAttendances.filter(item => item.incident?.id === 3);
    },
    getWorkedDays() {
      return this.processedAttendances.filter(item => item.incident?.id == 2 || item.incident == null);
    },
    getHolidays() {
      return this.processedAttendances.filter(item => item.incident?.id < 0);
    },
    getWorkedDaysSalary() {
      let totalWeekHours = this.getWorkedDays().reduce((accum, object) => accum + object.total_worked_time?.hours, 0);
      let totalWeekHoursAllowed = this.processedAttendances.find(item => item.check_in)?.additionals?.hours_per_week;

      if (this.additionalTime) {
        const time = this.additionalTime.time_requested;
        const hours = time.split(':')[0];
        const minutes = time.split(':')[1]

        totalWeekHoursAllowed += parseFloat(hours) + parseFloat(minutes / 60);
      }

      if (totalWeekHours > totalWeekHoursAllowed) {
        totalWeekHours = totalWeekHoursAllowed;
      }
      return totalWeekHours * this.user.employee_properties.salary.hour;
    },
    getRemainingHoursWeekly() {
      const totalWeekHours = this.getWorkedDays().reduce((accum, object) => accum + object.total_worked_time?.hours, 0);
      const remainingHours = this.user.employee_properties.hours_per_week - totalWeekHours;

      const hours = parseInt(remainingHours);
      const minutes = Math.round((remainingHours - hours) * 60);

      return hours + 'h ' + minutes + 'm';
    },
    getTotal() {
      const dayly_salary = this.processedAttendances.find(item => item.check_in)?.additionals?.salary.day;
      const vacations = this.getVacations().length * 1.25 * dayly_salary;
      const workedDays = this.getWorkedDaysSalary();
      const holyDays = dayly_salary * this.getHolidays().length;
      const bonuses = this.bonuses.reduce((accumulator, bonus) => {
        return accumulator + bonus.amount.raw;
      }, 0);

      const discounts = this.discounts.reduce((accumulator, discount) => {
        return accumulator + discount.amount.raw;
      }, 0);

      return vacations
        + workedDays
        + holyDays
        + bonuses
        - discounts
        + this.extras.amount.raw;
    }
  },
  mounted() {
    this.getAttendances();
    this.getPayroll();
    this.getBonuses();
    this.getDiscounts();
    this.getExtras();
    this.getAuthorizedAdditionalTime();
  }
}
</script>