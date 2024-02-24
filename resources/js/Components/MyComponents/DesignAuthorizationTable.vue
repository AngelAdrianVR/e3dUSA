<template>
<div class="w-8/12 overflow-x-auto">
    <table class="w-full">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-5">
              Nombre del documento
            </th>
            <th class="font-bold pb-5">
              Fecha
            </th>
            <th class="font-bold pb-5">
              Estatus
            </th>
            <th class="font-bold pb-5">
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="design in designAuthorizations" :key="design" class="border-b border-[#9A9A9A]">
            <td class="text-center py-2 px-2">
                <div class="flex items-center">
                    <img class="w-14 object-contain" src="@/../../public/images/pdf.png" alt="">
                    <p class="font-bold">{{ design.name }}</p>
                </div>
            </td>
            <td class="text-center text-secondary py-2 px-2 hover:underline">
                {{ design.authorized_at ?? '--' }}
            </td>
            <td class="text-center py-2 px-2">
              <div :class="design.status.color" class="flex items-center space-x-2"><p>{{ design.status.label }}</p><span v-html="design.status.icon"></span></div>
            </td>
            <td class="text-center py-2 px-2 rounded-r-full">
              <div class="flex items-center space-x-2">
                <i class="fa-solid fa-pencil text-xs"></i>
                <i class="fa-regular fa-trash-can text-xs"></i>
              </div>
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
designAuthorizations: Array,
},
methods:{ 
  getStatusColor(sale) {
      if (sale.status.label == "Esperando autorización") {
        return "bg-amber-400";
      } else if (sale.status.label == "Producción sin iniciar") {
        return "bg-gray-400";
      } else if (sale.status.label == "Producción en proceso") {
        return "bg-blue-400";
      } else if (sale.status.label == "Producción terminada") {
        return "bg-green-400";
      } else if (sale.status.label == "Autorizado sin orden de producción") {
        return "bg-amber-400";
      } else {
        return "bg-transparent";
      }
  }
}
}
</script>
