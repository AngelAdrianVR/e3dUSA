<template>
  <div class="lg:grid grid-cols-3 py-6 px-10">
    <div class="bg-[#f2f2f2] rounded-lg mx-auto col-span-2 w-5/6">
      <div class="flex items-center">
        <i class="fa-solid fa-circle-user mr-3 text-xl"></i>
        <p>{{ user.name }}</p>
      </div>
      <div class="overflow-x-auto shadow-md mt-3 rounded-lg">
        <table class="items-center w-full bg-transparent">
          <thead class="text-primary bg-[#e6e6e6] text-[12px] px-3">
            <tr class="rounded-tl-lg rounded-tr-lg">
              <th class="rounded-tl-lg">Día</th>
              <th>Entrada</th>
              <th>Salida</th>
              <th>Break</th>
              <th class="rounded-tr-lg py-2">Hrs x dia</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(attendance, index) in processedAttendances" :key="attendance.id">
              <tr v-if="!attendance.incident" class="text-gray-600 text-center border-b border-[#a9a9a9]">
                <th class="py-2 text-left mt-3 text-sm">
                  <span class="ml-3 font-bold"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 text-xs py-2">
                  <p class="bg-transparent text-sm" :class="attendance.late ? 'text-amber-600' : ''">{{ attendance.check_in }}</p>
                </td>
                <td class="px-6 text-xs py-2">
                  <p class="bg-transparent text-sm">{{ attendance.check_out }}</p>
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
              </tr>
              <tr v-else class="text-gray-600 text-center border-b border-[#a9a9a9]">
                <th class="py-1 text-left mt-3 text-sm">
                  <span class="ml-3 font-bold"> {{ attendance.date?.formatted }} </span>
                </th>
                <td class="px-6 py-2" colspan="4">
                  <p class="text-sm rounded-xl py-2"
                    :class="'bg-' + attendance.incident.additionals.color + '-100 text-' + attendance.incident.additionals.color + '-600'">
                    {{ attendance.incident.name }}</p>
                </td>
                <!-- <td class="px-6 text-xs py-2 w-32">
                    <i class="fa-solid fa-minus"></i>
                  </td>
                  <td class="px-6 text-xs py-2 w-32">
                    <i class="fa-solid fa-minus"></i>
                  </td> -->
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
    <div class="mr-auto">
      <p class="mb-3">
        <strong class="mr-3"> Semana {{ payroll?.week }} </strong> 
        {{ payroll?.start_date }} - {{ payroll?.end_date }}
      </p>
      <div class="flex flex-col space-y-2">
        <p class="flex justify-between">
          Días a pagar
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
        <p class="flex justify-between">
          Vacaciones
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
        <p class="flex justify-between">
          Asistencia
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
        <p class="flex justify-between">
          Días a pagar
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
        <p class="flex justify-between">
          Días a pagar
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
        <p class="flex justify-between">
          Días a pagar
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
        <p class="flex justify-between">
          Días a pagar
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
        <p class="flex justify-between">
          Días a pagar
          <span>{{ '2' }}</span>
          <span>{{ '$2,500.00' }}</span>
        </p>
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
      loading: false,
      pageLoading: false,
      processedAttendances: [],
      payroll: null,
    }
  },
  props: {
    user: Number,
    payrollId: Number,
  },
  components: {
    PrimaryButton,
    CancelButton,
    Dropdown,
    DropdownLink,
  },
  methods: {
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
    async getPayroll() {
      try {
        const response = await axios.post(route('payrolls.get-payroll'), {
          payroll_id: this.payrollId
        });

        if (response.status === 200) {
          this.payroll = response.data.item;
          console.log(this.payroll);
        }
      } catch (error) {
        console.log(error);
      } finally {
      }
    },
  },
  mounted() {
    this.getAttendances();
    this.getPayroll();
  }
}
</script>