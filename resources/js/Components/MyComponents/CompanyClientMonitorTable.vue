<template>
<div class="overflow-x-auto">
    <table class="w-full mx-auto">
        <thead>
          <tr class="text-left">
            <th class="font-bold pb-5">
              Folio <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
            </th>
            <th class="font-bold pb-5">
              Tipo de interacción <i class="fa-solid fa-arrow-down-long ml-3 px-14 lg:px-2"></i>
            </th>
            <th class="font-bold pb-5">
              Fecha <i class="fa-solid fa-arrow-down-long ml-3 px-5  lg:px-2"></i>
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
          <tr v-for="monitor in client_monitors" :key="monitor"
            class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
            @click="showMonitorType(monitor)"
          >
            <td class="text-left text-secondary py-2 px-2 rounded-l-full">
              {{ monitor.folio }}
            </td>
            <td class="text-left py-2 px-2 ">
              {{ monitor.type }}
            </td>
            <td class="text-left py-2 px-2">
              <span
                class="py-1 px-2 rounded-full"
                >{{ monitor.date }}</span
              >
            </td>
            <td class="text-left py-2 px-2">
              <p :title="monitor.concept" class="w-36 truncate">{{ monitor.concept }}</p>
            </td>
            <td class="text-left py-2 px-2 rounded-r-full">
              {{ monitor.seller?.name }}
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
showMonitorType(monitor) {
      if (monitor.type == 'Correo') {
        this.$inertia.get(route('email-monitors.show', monitor.emailMonitor?.id));
      } else if (monitor.type == 'Pago') {
        this.$inertia.get(route('payment-monitors.show', monitor.paymentMonitor?.id));
      } else if (monitor.type == 'Reunión') {
        this.$inertia.get(route('meeting-monitors.show', monitor.mettingMonitor?.id));
      } else if (monitor.type == 'WhatsApp') {
        this.$inertia.get(route('whatsapp-monitors.show', monitor.whatsappMonitor?.id));
      }
    },
}
}
</script>

<style>

</style>