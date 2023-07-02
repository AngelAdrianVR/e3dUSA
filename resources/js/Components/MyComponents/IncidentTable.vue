<template>
  <div class="bg-[#D9D9D9] rounded-lg lg:w-4/5 mx-auto py-6 px-10">
    <div>
      <span class="text-secondary">Incidencias</span>
      <div class="flex justify-center items-center">
        <i class="fa-solid fa-circle-user mr-3 text-xl"></i>
        <p>{{ 'Samuel Antonio Espinoza Cruz' }}</p>
      </div>
      <p class="text-center">{{ 'Producción' }}</p>
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
            <tr v-for="(attendance, index) in processedAttendances" :key="attendance.id"
              class="text-gray-600 text-center">
              <th class="py-2 text-left mt-3 text-sm w-44">
                <span class="ml-3 font-bold"> {{ attendance.date?.formatted }} </span>
              </th>
              <td class="px-6 text-xs py-2">
                <input v-model="attendance.check_in" type="time"
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
                <el-dropdown trigger="click" @command="handleCommand">
                  <span class="w-6 h-6 rounded-full hover:bg-[#CCCCCC] cursor-pointer flex items-center justify-center">
                    <i class="fa-solid fa-ellipsis-vertical text-primary"></i>
                  </span>
                  <template #dropdown>
                    <el-dropdown-menu>
                      <el-dropdown-item :command="'late-' + attendance.id">
                        Poner retardo</el-dropdown-item>
                      <el-dropdown-item :command="'extra-' + attendance.id">
                        Extras dobles</el-dropdown-item>
                      <el-dropdown-item v-for="(item, index1) in justifications" :key="index1"
                        :command="index1 + '-' + attendance.id">
                        {{ item.name }}</el-dropdown-item>
                    </el-dropdown-menu>
                  </template>
                </el-dropdown>
                <!-- <Dropdown align="right" width="60">
                      <template #trigger>
                        <span class="block w-6 h-6 rounded-full hover:bg-[#CCCCCC] cursor-pointer">
                          <i class="fa-solid fa-ellipsis-vertical text-primary"></i>
                        </span>
                      </template>
                      <template #content>
                        <DropdownLink as="button">
                          Poner retardo
                        </DropdownLink>
                        <DropdownLink as="button">
                          Extras dobles
                        </DropdownLink>
                        <DropdownLink v-for="(item, index) in justifications" :key="index" as="button">
                          {{ item.name }}
                        </DropdownLink>
                      </template>
                    </Dropdown> -->
              </td>
            </tr>
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

      <!-- <div class="flex space-x-4 mt-9 border-b-2 border-[#9a9a9a]">
                <span class="text-primary">Día</span>
                <span class="text-primary">Entrada</span>
                <span class="text-primary">Inicio break</span>
                <span class="text-primary">Fin break</span>
                <span class="text-primary">Salida</span>
                <span class="text-primary">Hrs break</span>
                <span class="text-primary">Hrs total</span>
            </div> -->
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
    }
  },
  props: {
    justifications: Array,
    userId: Number,
    payrollId: Number,
  },
  components: {
    PrimaryButton,
    CancelButton,
    Dropdown,
    DropdownLink,
  },
  watch: {
    userId: {
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

      if (commandName == 'clone') {
        this.clone(rowId);
      } else if (commandName == 'make_so') {
        console.log('SO');
      } else {
        this.$inertia.get(route('catalog-products.' + commandName, rowId));
      }
    },
    async getAttendances() {
      try {
        this.loading = true;
        const response = await axios.post(route('payrolls.processed-attendances'), {
          user_id: this.userId,
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
    }
  },
}
</script>