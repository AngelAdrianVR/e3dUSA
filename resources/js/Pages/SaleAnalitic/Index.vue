<template>
  <AppLayout title="Análisis de ventas">
    <div class="flex flex-col md:mx-9 md:my-1 space-y-3 m-1">
      <div class="flex justify-between mb-2">
        <label class="text-lg">Análisis de ventas</label>
      </div>

        <div class="flex justify-between space-x-7 lg:w-1/2">
          <div class="flex items-center space-x-4 w-2/3 lg:w-1/2">
              <el-select @change="fetchProductSalesTop" class="lg:w-1/2" v-model="familySelected" filterable
              placeholder="Seleccione la familia" no-data-text="No hay opciones registradas"
              no-match-text="No se encontraron coincidencias">
                  <el-option v-for="family in families" :key="family" :label="family.label" :value="family.code" />
              </el-select>
          </div>

          <div class="flex items-center space-x-4 w-2/3 lg:w-1/2">
              <el-select :disabled="!familySelected" @change="fetchProductSalesTop" class="lg:w-1/2" v-model="range"
              placeholder="Seleccione el rango de tiempo" no-data-text="No hay opciones registradas"
              no-match-text="No se encontraron coincidencias">
                  <el-option v-for="range in ranges" :key="range" :label="range" :value="range" />
              </el-select>
          </div>
        </div>
    </div>
    <h2 class="text-center text-primary mt-7">Top 20 en ventas</h2>

    <!-- Tabla de productos vendidos -->
    <div v-if="!loading" class="w-10/12 h-[301px] overflow-auto mx-auto mt-3 mb-2 rounded-md">
      <table v-if="topProducts" class="w-full h-full mx-auto text-sm">
        <thead>
          <tr class="text-center border-b border-primary">
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
            v-for="product in topProducts"
            :key="product"
            @click="fetchProductInfo(product)"
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
    </div>

    <!-- Estado de carga  -->
    <div v-else class="flex justify-center items-center pt-10">
      <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
    </div>

    <!-- Analisis de producto -->
      <div v-if="productSelected" class="rounded-lg px-4 py-3 mx-9 mt-10 text-sm">
        <div class="w-full" v-if="!loadingCharts">
          <div class="flex items-center space-x-9 font-bold">
            <p>{{ productSelected.part_number }}</p>
            <p>{{ productSelected.name }}</p>
          </div>
          <div class="lg:flex space-x-4 mt-5">
            <figure class="rounded-md">
                <img :src="productSelected.media?.original_url" class="rounded-md object-contain w-full h-28">
            </figure>
            <div class="lg:grid grid-cols-2 gap-4 text-center border w-[90%]">
              <LinealChart :options="productAmountSalesMonth" title="Ventas acumuladas por mes" />
              <!-- <ColumWithMakersChart :options="productMoneySalesMonth" title="Ventas vs Espectativas" />  -->
              <LinealChart :options="productMoneySalesMonth" title="Ventas en pesos mexicanos $MXN por mes" />
            </div>
          </div>
        </div>
         <!-- Estado de carga  -->
        <div v-else class="flex justify-center items-center pt-10">
          <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
        </div>
      </div>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import LinealChart from "@/Components/MyComponents/LinealChart.vue";
import ColumWithMakersChart from "@/Components/MyComponents/ColumWithMakersChart.vue";
import axios from 'axios';

export default {
data() {
    return {
        loading: false,
        loadingCharts: false,
        topProducts: null,
        familySelected: null,
        productSelected: null,
        productAmountSalesMonth: {},
        range: 'Total',
        ranges: [
          'Mensual',
          'Bimestral',
          'Total',
        ],
        families: [
            {
              label:'Llaveros',
              code:'C-LL',
            },
            {
              label:'Emblemas',
              code:'C-EM',
            },
            {
              label:'Portaplacas',
              code:'C-PP',
            },
            {
              label:'Porta documentos',
              code:'C-PD',
            },
            {
              label:'Manta de develación',
              code:'C-MT',
            },
            {
              label:'Parasoles',
              code:'C-PS',
            },
            {
              label:'Funda llavero',
              code:'C-FLL',
            },
            {
              label:'Funda llavero aluminio',
              code:'C-FA',
            },
            {
              label:'Tapetes',
              code:'C-TP',
            },
            {
              label:'Placas estireno',
              code:'C-PE',
            },
        ],   

    }
},
components:{
AppLayout,
ColumWithMakersChart,
LinealChart,
InputLabel

},
props:{

},
methods:{
    async fetchProductSalesTop() {
      this.loading = true;
        try {
          const response = await axios.get(route('sale-analitics.fetch-top-products', [this.familySelected, this.range]));

          if (response.status === 200) {
          this.topProducts = response.data.items;
        }
        } catch (error) {
          console.log(error);
        } finally {
          this.loading = false;
        }
    },
    async fetchProductInfo(product) {
      this.productSelected = product
      this.loadingCharts = true;
        try {
          const response = await axios.get(route('sale-analitics.fetch-product-info', this.productSelected.part_number));

          if (response.status === 200) {

          this.productAmountSalesMonth = {
            colors: ['#f3f3f3', 'transparent'],
            categories: response.data.items.map(sale => sale.month),
            series: [{
              name: 'Unidades vendidas',
              data: response.data.items.map(sale => sale.total_sales)
            }],
          };

          this.productMoneySalesMonth = {
            colors: ['#f3f3f3', 'transparent'],
            categories: response.data.items.map(sale => sale.month),
            series: [{
              name: 'Cantidad en pesos $MXN',
              data: response.data.items.map(sale => sale.money_sales)
            }],
          };

        }
        } catch (error) {
          console.log(error);
        } finally {
          this.loadingCharts = false;
        }
    }
}
};
</script>
