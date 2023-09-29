<template>
    <div class="lg:min-h-52 min-h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">Ventas año en curso vs anterior</h1>

        <div id="chart">
            <apexchart type="bar" height="180" :options="chartOptions" :series="series"></apexchart>
        </div>
        <div class="flex justify-end mx-6 absolute bottom-3 right-5">
            <button class="text-primary text-xs">Ver detalles</button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            series: [
                {
                    name: 'Año pasado',
                    data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 70, 90.5, 110.95]
                },
                {
                    name: 'Año en curso',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 0, 0, 0]
                },
            ],
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 145
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                },
                yaxis: {
                    title: {
                        text: '$ (miles)'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "$ " + val + "K"
                        }
                    }
                }
            },

        };
    },
    methods: {

    },
}
</script>