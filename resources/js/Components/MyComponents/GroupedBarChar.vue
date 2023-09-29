<template>
    <div class="lg:min-h-52 min-h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">Ventas recientes <i class="fa-solid fa-bullseye ml-2"></i></h1>

        <div id="chart">
            <apexchart type="bar" height="200" :options="chartOptions" :series="series"></apexchart>
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
            series: [{
                name: 'Meta',
                data: [500, 500, 500]
            }, {
                name: 'Ventas',
                data: [125.9, 300, 209.57]
            }],
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 150
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return "$ " + val + "K"
                    },
                    offsetX: -6,
                    style: {
                        fontSize: '10px',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: false,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return "$ " + val + "K"
                        }
                    }
                },
                xaxis: {
                    categories: ['Edgar Sherman', 'Norberto Platas', 'Santiago'],
                },
            },
        };
    },
    methods: {

    },
}
</script>