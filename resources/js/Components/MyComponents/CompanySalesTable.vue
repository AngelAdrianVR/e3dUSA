<template>
  <div class="w-11/12 overflow-x-auto text-sm">
    <table class="w-full mx-auto">
      <thead>
        <tr class="text-center">
          <th class="font-bold pb-5"> Folio </th>
          <th class="font-bold pb-5"> Vendedor </th>
          <!-- <th class="font-bold pb-5">
              Productos
            </th> -->
          <th class="font-bold pb-5"> Estatus </th>
          <th class="font-bold pb-5"> Creado el </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="sale in company_sales" :key="sale" class="mb-4 hover:bg-[#d9d9d9]">
          <td class="text-center text-secondary py-2 px-2 rounded-l-full hover:underline">
            <a :href="'/sales/' + sale.id"> {{ sale.folio }} </a>
          </td>
          <td class="text-center text-secondary py-2 px-2 hover:underline">
            <a :href="'/users/' + sale.user?.id"> {{ sale.user?.name }} </a>
          </td>
          <!-- <td class="text-center py-2 px-2">
              <span
                class="py-1 px-4 rounded-full"
                >{{ sale.products ? sale.products + 'Producto(s)' : '--' }}</span
              >
            </td> -->
          <td class="text-center py-2 px-2">
            <span class="py-1 px-4 rounded-full" :class="getStatusColor(sale)">{{ sale.status.label }}</span>
          </td>
          <td class="text-center py-2 px-2 rounded-r-full">
            {{ sale.created_at }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {

    }
  },
  components: {
  },
  props: {
    company_sales: Array,
  },
  methods: {
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
