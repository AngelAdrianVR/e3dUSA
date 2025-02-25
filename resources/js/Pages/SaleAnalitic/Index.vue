<template>
  <AppLayoutNoHeader title="Inicio CRM">
    <div class="md:mx-9 md:my-1 m-1 dark:text-white">
      <div class="flex justify-between mb-2">
        <label class="text-lg font-bold">Inicio</label>
      </div>

      <div class="text-center">
        <el-radio-group @change="resetValues()" v-model="type">
          <el-radio-button label="Producto de catálogo" value="Producto de catálogo" />
          <el-radio-button label="Materia prima" value="Materia prima" />
          <el-radio-button label="Buscar producto" value="Buscar producto" />
        </el-radio-group>
      </div>

      <!-- analisis de ventas -->
      <h2 class="text-primary lg:text-lg text-sm lg:mt-8 mt-6 font-bold">Análisis de ventas</h2>
      <div class="flex justify-between space-x-7 mt-4 lg:w-1/2">
        <div class="flex items-center space-x-4 w-2/3 lg:w-1/2">
          <!-- selector de familia -->
          <el-select v-if="type != 'Buscar producto'" @change="fetchProductSalesTop" class="lg:w-1/2"
            v-model="familySelected" filterable placeholder="Seleccione la familia"
            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="family in families" :key="family" :label="family.label" :value="family.code" />
          </el-select>
          <!-- selector de producto de catalogo individual -->
          <el-select v-else @change="fetchCatalogProductSales" class="lg:w-1/2" v-model="catalogProductSelected"
            filterable placeholder="Seleccione el producto" no-data-text="No hay opciones registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in catalog_products" :key="item" :label="item.name" :value="item.part_number" />
          </el-select>
        </div>

        <div class="flex items-center space-x-4 w-2/3 lg:w-1/2">
          <el-select @change="handleFetchMethod" class="lg:w-1/2" v-model="range"
            placeholder="Seleccione el rango de tiempo" no-data-text="No hay opciones registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="range in ranges" :key="range" :label="range" :value="range" />
          </el-select>
        </div>
      </div>
      <h2 class="text-center text-primary mt-7">Top 20 en ventas</h2>
      <!-- Tabla de productos vendidos -->
      <div v-if="!loading" class="w-10/12 h-[301px] overflow-auto mx-auto mt-3 mb-2 rounded-md dark:text-white">
        <table v-if="topProducts" class="w-full h-full mx-auto text-sm">
          <thead>
            <tr class="text-center border-b border-primary">
              <th class="font-bold pb-3 pl-2 text-left">
                N° parte
              </th>
              <th class="font-bold pb-3 text-left">
                Producto
              </th>
              <th v-if="type != 'Materia prima'" class="font-bold pb-3 text-left">
                Precio ant.
              </th>
              <th class="font-bold pb-3 text-left">
                Precio act.
              </th>
              <th v-if="type != 'Materia prima'" class="font-bold pb-3 text-left">
                Cliente
              </th>
              <th class="font-bold pb-3 text-left">
                Cantidad vendida
              </th>
              <th v-if="type != 'Materia prima'" class="font-bold pb-3 text-left">
                Total venta
              </th>
              <th v-if="type == 'Materia prima'" class="font-bold pb-3 text-left">
                Inversión total
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in topProducts" :key="product"
              @click="fetchProductInfo(product, type == 'Materia prima' ? 'sale-analitics.fetch-raw-material-info' : 'sale-analitics.fetch-product-info')"
              class="mb-4 hover:text-primary cursor-pointer">
              <td class="py-2 pl-2 rounded-l-full">
                {{ product.part_number }}
              </td>
              <td class="py-2">
                <p :title="product.name" class="truncate w-40">{{ product.name }}</p>
              </td>
              <td v-if="type != 'Materia prima'" class="py-2">
                ${{ product.old_price ?? '-' }}
              </td>
              <td class="py-2">
                ${{ product.new_price }}
              </td>
              <td v-if="type != 'Materia prima'" class="py-2">
                <p :title="product.client" class="truncate w-40">{{ product.client ?? '-' }}</p>
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
        <el-empty v-else description="No hay información para mostrar" />
      </div>
      <!-- Estado de carga -->
      <LoadingLogo v-else />

      <!-- Analisis de producto -->
      <div v-if="productSelected !== null" class="rounded-lg px-4 py-3 mx-9 mt-10 text-sm">
        <div class="w-full" v-if="!loadingCharts">
          <div class="flex items-center space-x-9 font-bold">
            <p>{{ productSelected.part_number }}</p>
            <p>{{ productSelected.name }}</p>
          </div>
          <div class="lg:flex space-x-4 mt-5">
            <figure class="rounded-md w-1/6 h-28">
              <img :src="productSelected.media?.original_url" class="rounded-md object-contain w-full h-28">
            </figure>
            <div class="lg:grid grid-cols-2 gap-4 text-center w-full">
              <LinealChart :options="productAmountSalesMonth"
                :title="'Ventas por mes ( ' + productSelected.quantity_sales.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' Piezas totales )'" />
              <LinealChart :options="productMoneySalesMonth"
                :title="'Monto por mes ( Monto total $' + productSelected.total_money.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' MXN )'" />
              <BarChart v-if="type == 'Producto de catálogo'" :options="barChartOptions"
                title="Ventas año en curso vs anterior" />
            </div>
          </div>
        </div>
        <!-- Estado de carga -->
        <LoadingLogo v-else />
      </div>

      <!-- Estadisticas -->
      <h2 class="text-primary lg:text-lg text-sm lg:mt-20 mt-6 font-bold">Estadísticas</h2>
      <el-date-picker @change="fetchEstatisticsData" v-model="estatisticsMonth" type="month" placeholder="Elige el mes y año" format="MM-YYYY"
        value-format="YYYY-MM-D" class="mt-2" />
      <div>
        <LoadingLogo v-if="loadingEstatistics" />
        <div v-else class="lg:grid grid-cols-1 gap-10 mt-4 space-y-4 lg:space-y-0">
          <PieChart v-for="(item, index) in pieChartOptions" :key="index" :options="item.data" :title="item.title"
            :icon="item.icon" />
        </div>
      </div>
    </div>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import InputLabel from "@/Components/InputLabel.vue";
import LinealChart from "@/Components/MyComponents/LinealChart.vue";
import ColumWithMakersChart from "@/Components/MyComponents/ColumWithMakersChart.vue";
import BarChart from "@/Components/MyComponents/BarChart.vue";
// import Loading from "@/Components/MyComponents/Loading.vue";
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";
import axios from 'axios';
import PieChart from '@/Components/MyComponents/PieChart.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
  data() {
    return {
      type: 'Producto de catálogo',
      estatisticsMonth: format(new Date(), 'yyyy-MM-dd'),
      sales: [],
      loading: false,
      loadingCharts: false,
      loadingEstatistics: false,
      topProducts: null,
      familySelected: null,
      productSelected: null,
      catalogProductSelected: null, //producto de catalogo seleccionado en "buscar producto" para ver info solo de ese producto.
      productAmountSalesMonth: {},
      barChartOptions: {},
      test: null,
      range: 'Total',
      ranges: [
        'Mensual',
        'Bimestral',
        'Trimestral',
        'Quatrimestre',
        'Semestre',
        'Total',
      ],
      families: [
        {
          label: 'Llaveros',
          code: 'C-LL',
        },
        {
          label: 'Emblemas',
          code: 'C-EM',
        },
        {
          label: 'Portaplacas',
          code: 'C-PP',
        },
        {
          label: 'Porta documentos',
          code: 'C-PD',
        },
        {
          label: 'Manta de develación',
          code: 'C-MT',
        },
        {
          label: 'Parasoles',
          code: 'C-PS',
        },
        {
          label: 'Funda llavero',
          code: 'C-FLL',
        },
        {
          label: 'Funda llavero aluminio',
          code: 'C-FA',
        },
        {
          label: 'Tapetes',
          code: 'C-TP',
        },
        {
          label: 'Placas estireno',
          code: 'C-PE',
        },
      ],
      pieChartOptions: [],
    }
  },
  components: {
    AppLayoutNoHeader,
    ColumWithMakersChart,
    LoadingLogo,
    LinealChart,
    InputLabel,
    PieChart,
    BarChart,
    // Loading,
  },
  props: {
    catalog_products: Array,
    meet_ways: Array,
  },
  methods: {
    setPieChartOptions() {
      this.meet_ways[0]['concept'] = 'No especificado';
      this.pieChartOptions = [
        {
          title: 'Ventas de emblemas ' + this.formatEstatisticsMonth(),
          icon: '',
          data: {
            unit: ' $MXN',
            colors: ['#31CB23', '#D47914', '#D90537', '#888888', '#0355B5', '#0397B5', '#A41314'],
            labels: this.getSalesByFamily['EM'].map(item => item[0]),
            series: this.getSalesByFamily['EM'].map(item => item[1]),
          }
        },
        {
          title: 'Ventas de llaveros ' + this.formatEstatisticsMonth(),
          icon: '',
          data: {
            unit: ' $MXN',
            colors: ['#31CB23', '#D47914', '#D90537', '#888888', '#0355B5', '#0397B5', '#A41314'],
            labels: this.getSalesByFamily['LL'].map(item => item[0]),
            series: this.getSalesByFamily['LL'].map(item => item[1]),
          }
        },
        {
          title: 'Ventas de porta placas abs y metal ' + this.formatEstatisticsMonth(),
          icon: '',
          data: {
            unit: ' $MXN',
            colors: ['#31CB23', '#D47914', '#D90537', '#888888', '#0355B5', '#0397B5', '#A41314'],
            labels: this.getSalesByFamily['PP'].map(item => item[0]),
            series: this.getSalesByFamily['PP'].map(item => item[1]),
          }
        },
        {
          title: 'Ventas de estireno ' + this.formatEstatisticsMonth(),
          icon: '',
          data: {
            unit: ' $MXN',
            colors: ['#31CB23', '#D47914', '#D90537', '#888888', '#0355B5', '#0397B5', '#A41314'],
            labels: this.getSalesByFamily['PE'].map(item => item[0]),
            series: this.getSalesByFamily['PE'].map(item => item[1]),
          }
        },
        {
          title: 'Ventas de tapetes ' + this.formatEstatisticsMonth(),
          icon: '',
          data: {
            unit: ' $MXN',
            colors: ['#31CB23', '#D47914', '#D90537', '#888888', '#0355B5', '#0397B5', '#A41314'],
            labels: this.getSalesByFamily['TP'].map(item => item[0]),
            series: this.getSalesByFamily['TP'].map(item => item[1]),
          }
        },
        {
          title: 'Cómo nos conocieron los clientes',
          icon: '<i class="fa-solid fa-user-check ml-2"></i>',
          data: {
            unit: ' clientes',
            colors: ['#31CB23', '#D47914', '#D90537', '#888888', '#0355B5', '#0397B5', '#A41314'],
            labels: this.meet_ways.map(item => item['concept']),
            series: this.meet_ways.map(item => item['total']),
          }
        },
      ];
    },
    formatEstatisticsMonth() {
      // Formatea el mes actual en español
      return format(new Date(this.estatisticsMonth), 'MMMM, Y', { locale: es });
    },
    handleFetchMethod() {
      if (this.type == 'Buscar producto') {
        this.fetchCatalogProductSales()
      } else {
        this.fetchProductSalesTop();
      }
    },
    async fetchEstatisticsData() {
      this.loadingEstatistics = true;
      try {
        const response = await axios.get(route('sale-analitics.get-estatistics-data', this.estatisticsMonth));

        if (response.status === 200) {
          this.sales = response.data.items;
          this.setPieChartOptions();
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loadingEstatistics = false;
      }
    },
    async fetchProductSalesTop() {
      this.productSelected = null;
      this.loading = true;
      try {
        const response = await axios.get(route('sale-analitics.fetch-top-products', [this.familySelected, this.range, this.type]));

        if (response.status === 200) {
          this.topProducts = response.data.items;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    async fetchCatalogProductSales() {
      this.productSelected = null;
      this.loading = true;
      try {
        const response = await axios.get(route('sale-analitics.fetch-catalog-product-sales', [this.catalogProductSelected, this.range]));

        if (response.status === 200) {
          this.topProducts = response.data.items;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
    async fetchProductInfo(product, routeName) {
      this.productSelected = product;
      this.loadingCharts = true;
      try {
        const response = await axios.get(route(routeName, this.productSelected.part_number));

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

          this.barChartOptions = {
            colors: ['#D90537', '#0355B5'],
            categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            series: [{
              name: 'Año anterior',
              data: Object.values(response.data.yearSales.lastYearSales).map(item => (item / 1000).toFixed(2)),
            },
            {
              name: 'Año en curso',
              data: Object.values(response.data.yearSales.currentYearSales).map(item => (item / 1000).toFixed(2)),
            }],
          }
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loadingCharts = false;
      }
    },
    resetValues() {
      this.topProducts = null;
      this.familySelected = null;
      this.productSelected = null;
      this.catalogProductSelected = null;
      this.range = 'Total';
    }
  },
  computed: {
    getSalesByFamily() {
      const resultArrays = {
        'PP': [],
        'EM': [],
        'LL': [],
        'PE': [],
        'TP': []
      };

      // Filtrar las ventas que cumplen con los criterios
      const filteredSales = this.sales.filter(sale => {
        return (
          sale.catalog_product_company_sales &&
          sale.catalog_product_company_sales.length > 0 &&
          sale.company_branch &&
          sale.company_branch.name
        );
      });

      // Agrupar las ventas por company_branch.name
      const salesByBranch = filteredSales.reduce((acc, sale) => {
        const branchName = sale.company_branch.name;

        if (!acc[branchName]) {
          acc[branchName] = [];
        }

        acc[branchName].push(sale);
        return acc;
      }, {});

      // Crear los arrays resultantes
      Object.keys(salesByBranch).forEach(branchName => {
        const sales = salesByBranch[branchName];

        const countByFamily = sales.reduce((count, sale) => {
          sale.catalog_product_company_sales.forEach(productSale => {
            const family = productSale.catalog_product_company?.catalog_product.part_number.split('-')[1];

            if (family in resultArrays) {
              // convertir a pesos aquellas ventas en dolares ($17.2 en promedio)
              const factor = productSale.catalog_product_company.new_currency == '$MXN' ? 1 : 17.2;
              count[family] = (count[family] || 0) + productSale.quantity * productSale.catalog_product_company.new_price * factor;
            }
          });

          return count;
        }, {});

        // Excluir los elementos con total de ventas igual a cero
        if (countByFamily['PP'] !== undefined && countByFamily['PP'] !== 0) {
          resultArrays['PP'].push([branchName, countByFamily['PP']]);
        }
        if (countByFamily['EM'] !== undefined && countByFamily['EM'] !== 0) {
          resultArrays['EM'].push([branchName, countByFamily['EM']]);
        }
        if (countByFamily['LL'] !== undefined && countByFamily['LL'] !== 0) {
          resultArrays['LL'].push([branchName, countByFamily['LL']]);
        }
        if (countByFamily['PE'] !== undefined && countByFamily['PE'] !== 0) {
          resultArrays['PE'].push([branchName, countByFamily['PE']]);
        }
        if (countByFamily['TP'] !== undefined && countByFamily['TP'] !== 0) {
          resultArrays['TP'].push([branchName, countByFamily['TP']]);
        }
      });

      return resultArrays;
    },
  },
  async mounted() {
    await this.fetchEstatisticsData();
    this.setPieChartOptions();
  },
};
</script>
