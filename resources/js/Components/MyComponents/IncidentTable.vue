<template>
  <div class="bg-[#D9D9D9] rounded-lg lg:w-4/5 mx-auto p-6">
    <div>
      <span class="text-secondary">Incidencias</span>
      <div class="flex justify-center items-center">
        <i class="fa-solid fa-circle-user mr-3 text-xl"></i>
        <p>{{ 'Samuel Antonio Espinoza Cruz' }}</p>
      </div>
      <p class="text-center">{{ 'Producción' }}</p>
      <p class="text-center mt-2"><strong class="mr-4">Nómina semanal {{ 'payroll.data.week' }}</strong>{{
        'payroll.data.start_date' }} | {{ 'payroll.data.end_date' }}</p>
      <div class="overflow-x-auto">
        <table class="items-center w-full bg-transparent mt-9">
          <thead class="text-primary border-b-2 border-[#9a9a9a] text-sm">
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
          <tbody>
            <tr class="text-gray-600 text-center">
              <th class="p-2 text-left flex items-center">
                <span class="ml-3 font-bold"> {{ 'attendance.day' }} </span>
              </th>
              <td class="px-6 text-xs p-2">
                <input type="time" class="bg-transparent text-sm rounded-md border-gray-400">
              </td>
              <td class="px-6 text-xs p-2">
                <input type="time" class="bg-transparent text-sm rounded-md border-gray-400">
              </td>
              <td class="px-6 text-xs p-2">
                <input type="time" class="bg-transparent text-sm rounded-md border-gray-400">
              </td>
              <td class="px-6 text-xs p-2">
                <input type="time" class="bg-transparent text-sm rounded-md border-gray-400">
              </td>
              <td class="px-6 text-xs p-2">
                <i class="fa-solid fa-minus"></i>
              </td>
              <td class="px-6 text-xs p-2">
                <i class="fa-solid fa-minus"></i>
              </td>
              <td class="w-11">
                <Dropdown align="right" width="60">
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
                </Dropdown>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-6 text-right">
        <PrimaryButton class="mr-3">Guardar</PrimaryButton>
        <CancelButton @click="this.$emit(closeIncidentTable)">Cancelar</CancelButton>
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

    }
  },
  props: {
    justifications: Array,
  },
  components: {
    PrimaryButton,
    CancelButton,
    Dropdown,
    DropdownLink,
  },
  methods: {

  },
}
</script>