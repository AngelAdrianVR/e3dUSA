<template>
    <div class="bg-secondary-gray rounded-lg lg:w-2/3 mx-auto p-6">
        <div class="">
            <span class="text-secondary">Incidencias</span>
            <div class="flex justify-center items-center">
                <i class="fa-solid fa-circle-user mr-3 text-xl"></i>
                <p>{{ 'Samuel Antonio Espinoza Cruz' }}</p>
            </div>
            <p class="text-center">{{'Producción'}}</p>
            <p class="text-center mt-2"><strong class="mr-4">Nómina semanal {{ 'payroll.data.week' }}</strong>{{ 'payroll.data.start_date' }} | {{ 'payroll.data.end_date' }}</p>
            
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
                <th
                  class="p-2 text-left flex items-center">
                  <span class="ml-3 font-bold"> {{ 'attendance.day' }} </span>
                </th>
                <td class="px-6  text-xs p-2">
                  <input type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td class="px-6  text-xs p-2">
                  <input type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td class="px-6  text-xs p-2">
                  <input type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td class="px-6  text-xs p-2">
                  <input type="time"
                    class="bg-transparent text-sm rounded-md border-gray-400">
                </td>
                <td class="px-6  text-xs p-2">
                <i class="fa-solid fa-minus"></i>
                </td>
                <td class="px-6  text-xs p-2">
                <i class="fa-solid fa-minus"></i>
                </td>
              </tr>
            </tbody>
          </table>
            
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

export default {
    data(){
        return{

        }
    },
    props:{

    },
    components:{
        PrimaryButton,
        CancelButton,
    },
    methods:{

    },
}
</script>