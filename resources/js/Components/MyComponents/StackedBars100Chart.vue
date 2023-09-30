<template>
    <div
        class="lg:min-h-52 min-h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>

        <div id="chart">
            <apexchart type="bar" height="250" :options="chartOptions" :series="series"></apexchart>
        </div>
        <!-- <div class="flex justify-end mx-6 absolute bottom-3 right-5">
            <button class="text-primary text-xs">Ver detalles</button>
        </div> -->
    </div>
</template>

<script>
export default {
    data() {
        return {
            series: this.options.series,
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                    stackType: '100%'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                    },
                },
                // stroke: {
                //     width: 1,
                //     colors: ['#fff']
                // },
                colors: this.options.colors,
                xaxis: {
                    categories: this.options.categories,
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " tareas"
                        }
                    }
                },
                fill: {
                    opacity: 1

                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetX: 40
                }
            },
        };
    },
    props: {
        title: String,
        icon: {
            default: '',
            type: String
        },
        options: Object,
    },
    methods: {

    },
}
</script>