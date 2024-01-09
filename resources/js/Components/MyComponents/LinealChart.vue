<template>
    <div
        class="min-h-[220px] border border-gray3 rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>

        <div id="chart">
            <apexchart type="line" height="250" :options="chartOptions" :series="series"></apexchart>
        </div>
    </div>
   
</template>

<script>
export default {
data(){
    return {
        series: this.options.series,
          chartOptions: {
            chart: {
              height: 250,
              type: 'line',
              zoom: {
                enabled: false
              }
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              curve: 'straight'
            },
            title: {
            //   text: 'Ventas mensuales',
              align: 'left'
            },
            grid: {
              row: {
                colors: this.options.colors,
                opacity: 0.5
              },
            },
            xaxis: {
              categories: this.options.categories,
            }
          },
    }
},
props:{
    title: String,
    icon: {
        default: '',
        type: String
    },
    options: Object,
},
methods:{

}
}
</script>
