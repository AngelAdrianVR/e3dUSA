<template>
    <div
        class="min-h-[220px] border border-gray3 rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>

        <div id="chart">
            <apexchart type="bar" height="350" :options="chartOptions" :series="series"></apexchart>
        </div>
    </div>
   
</template>

<script>
export default {
data(){
    return {
        series: [
            {
              name: 'Actual',
              data: [
                {
                  x: '2011',
                  y: 1292,
                  goals: [
                    {
                      name: 'Esperado',
                      value: 1400,
                      strokeHeight: 5,
                      strokeColor: '#775DD0'
                    }
                  ]
                },
                {
                  x: '2012',
                  y: 4432,
                  goals: [
                    {
                      name: 'Expected',
                      value: 5400,
                      strokeHeight: 5,
                      strokeColor: '#775DD0'
                    }
                  ]
                },
                {
                  x: '2018',
                  y: 6553,
                  goals: [
                    {
                      name: 'Expected',
                      value: 7300,
                      strokeHeight: 2,
                      strokeDashArray: 2,
                      strokeColor: '#775DD0'
                    }
                  ]
                }
              ]
            }
          ],
          chartOptions: {
            chart: {
              height: 200,
              type: 'bar'
            },
            plotOptions: {
              bar: {
                columnWidth: '60%'
              }
            },
            colors: ['#00E396'],
            dataLabels: {
              enabled: false
            },
            legend: {
              show: true,
              showForSingleSeries: true,
              customLegendItems: ['Actual', 'Esperado'],
              markers: {
                fillColors: ['#00E396', '#775DD0']
              }
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
