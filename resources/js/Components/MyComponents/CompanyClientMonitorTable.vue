<template>
<div class="overflow-x-auto">
    <table class="w-full mx-auto">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-5">
              Folio <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
            </th>
            <th class="font-bold pb-5">
              Tipo de interacción <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
            </th>
            <th class="font-bold pb-5">
              Fecha <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
            </th>
            <th class="font-bold pb-5">
              Concepto <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
            </th>
            <th class="font-bold pb-5">
              Vendedor <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client_monitor in client_monitors" :key="client_monitor"
            class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
            @click="$inertia.get(route('client-monitors.show', client_monitor.id))"
          >
            <td class="text-center text-secondary py-2 px-2 rounded-l-full">
              {{ client_monitor.folio }}
            </td>
            <td class="text-center py-2 px-2 ">
              {{ client_monitor.type }}
            </td>
            <td class="text-center py-2 px-2">
              <span
                class="py-1 px-4 rounded-full"
                >{{ client_monitor.date }}</span
              >
            </td>
            <td class="text-center py-2 px-2">
              <span
                class="py-1 px-2"
                >{{ client_monitor.concept}}</span
              >
            </td>
            <td class="text-center py-2 px-2 rounded-r-full">
              {{ client_monitor.seller?.name }}
            </td>
          </tr>
        </tbody>
      </table>
</div>
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
client_monitors: Array,
},
methods:{

}
}
</script>

<style>

</style>