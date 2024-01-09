<template>
  <table v-if="topProducts" class="w-full mx-auto text-sm">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-3 pl-2 text-left">
              N° parte
            </th>
            <th class="font-bold pb-3 text-left">
              Producto
            </th>
            <th class="font-bold pb-3 text-left">
              Precio ant.
            </th>
            <th class="font-bold pb-3 text-left">
              Precio act.
            </th>
            <th class="font-bold pb-3 text-left">
              Cliente
            </th>
            <th class="font-bold pb-3 text-left">
              Cantidad vendida
            </th>
            <th class="font-bold pb-3 text-left">
              Total venta
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="product in products"
            :key="product"
            @click="productSelected = product"
            class="mb-4 hover:bg-[#dfdbdba8] cursor-pointer"
          >
            <td class="py-2 pl-2 rounded-l-full">
              {{ product.part_number }}
            </td>
            <td class="py-2">
              <p :title="product.name" class="truncate w-40">{{ product.name }}</p>
            </td>
            <td class="py-2">
              ${{ product.old_price }}
            </td>
            <td class="py-2">
              ${{ product.new_price }}
            </td>
            <td class="py-2">
              <p :title="product.client" class="truncate w-40">{{ product.client }}</p>
            </td>
            <td class="py-2">
              {{ product.quantity_sales?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
            </td>
            <td class="py-2 rounded-r-full">
              ${{ product.total_money?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else>
        <p class="text-sm text-center text-gray-400">No hay Ventas registradas</p>
      </div>
</template>

<script>
export default {
data(){
    return {

    }
},
components:{

},
props:{
products: Array,
},
methods:{

},
};
</script>