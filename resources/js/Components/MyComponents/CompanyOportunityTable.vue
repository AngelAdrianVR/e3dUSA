<template>
  <table class="lg:w-[80%] w-full mx-auto">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-5">
              Folio <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Oportunidades <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Fecha inicio <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Estimación de cierre <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Estatus <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-5">
              Cerrada el <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="oportunity in oportunities" :key="oportunity"
            class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
            @click="$inertia.get(route('oportunities.show', oportunity.id))"
          >
            <td class="text-center text-secondary py-2 px-2 rounded-l-full">
              {{ oportunity.folio }}
            </td>
            <td class="text-center py-2 px-2 ">
              {{ oportunity.name }}
            </td>
            <td class="text-center py-2 px-2">
              <span
                class="py-1 px-4 rounded-full"
                :class="getStatusStyles(oportunity)"
                >{{ oportunity.status }}</span
              >
            </td>
            <td class="text-center py-2 px-2">
              <span
                class="py-1 px-2"
                >{{ oportunity.created_at.isoFormat}}</span
              >
            </td>
            <td class="text-center py-2 px-2">
              {{ oportunity.estimated_finish_date }}
            </td>
            <td class="text-center py-2 px-2 rounded-r-full">
              {{ oportunity.finished_at ?? "--" }}
            </td>
          </tr>
        </tbody>
      </table>
</template>

<script>
export default {
data(){
    return{

    }
},
components:{

},
props:{
oportunities: Array,
},
methods:{
    getStatusStyles(oportunity){
        if (oportunity.status === 'Nueva') {
            return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
        } else if (oportunity.status === 'Pendiente') {
             return 'text-[#C88C3C] bg-[#F3FD85]';
        } else if (oportunity.status === 'En progreso') {
             return 'text-[#FD8827] bg-[#FEDBBD]';
        } else if (oportunity.status === 'Pagado') {
             return 'text-[#37951F] bg-[#ADFEB5]';
        } else if (oportunity.status === 'Perdida') {
             return 'text-[#9E0FA9] bg-[#F7B7FC]';
        }
    },
}
}
</script>

<style>

</style>