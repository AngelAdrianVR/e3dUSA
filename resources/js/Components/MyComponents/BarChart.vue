<template>
    <div
        class="min-h-[220px] border border-gray3 rounded-[10px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>

        <div id="chart">
            <apexchart type="bar" height="180" :options="chartOptions" :series="series"></apexchart>
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
                colors: this.options.colors,
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: this.options.categories,
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
    props: {
        title: String,
        icon: {
            default: '',
            type: String
        },
        options: Object,
    }
}
</script>