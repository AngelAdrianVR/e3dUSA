<template>
  <AppLayout title="Análisis de ventas">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
      <div class="flex justify-between mb-7">
        <label class="text-lg">Análisis de ventas</label>
      </div>

        <InputLabel value="Familia" class="ml-2" />
        <div  class="flex items-center space-x-4 w-2/3 lg:w-1/2">
            <el-select @change="getProductSalesTop" class="lg:w-1/2" v-model="familySelected" filterable
            placeholder="Seleccione" no-data-text="No hay opciones registradas"
            no-match-text="No se encontraron coincidencias">
                <el-option v-for="family in families" :key="family" :label="family" :value="family" />
            </el-select>
        </div>
    </div>

    <!-- Tabla de productos vendidos -->
    <div class="w-11/12 mx-8 my-16">
      <table v-if="topProducts" class="w-full mx-auto text-sm">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-3 pl-2 text-left">
              Lugar <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Producto <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Precio ant. <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Precio act. <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Cantidad vendida <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Total venta <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Cliente <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(product, index) in topProducts"
            :key="index"
            @click="productSelected = product"
            class="mb-4 hover:bg-[#dfdbdba8] cursor-pointer"
          >
            <td class="py-2 pl-2 rounded-l-full">
              {{ index + 1 }}
            </td>
            <td class="py-2">
              {{ product.name }}
            </td>
            <td class="py-2">
              {{ product.old_price }}
            </td>
            <td class="py-2">
              {{ product.new_price }}
            </td>
            <td class="py-2">
              {{ product.client }}
            </td>
            <td class="py-2">
              {{ product.quantity_sales }}
            </td>
            <td class="py-2">
              {{ product.total_money }}
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else>
        <p class="text-sm text-center text-gray-400">No hay Ventas registradas</p>
      </div>
    </div>
      
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import axios from 'axios';

export default {
data() {
    return {
        topProducts: null,
        familySelected: null,
        productSelected: null,
        families: [
            'Llaveros',
            'Emblemas',
            'Portaplacas',
            'Porta documentos',
            'Manta develación',
            'Parasoles',
            'Funda llavero',
            'Funda aluminio llavero',
            'Tapetes',
            'Placas de estireno',
        ],
    }
},
components:{
AppLayout,
InputLabel

},
props:{

},
methods:{
    getProductSalesTop() {
        console.log(this.familySelected);
    }
}
};
</script>
