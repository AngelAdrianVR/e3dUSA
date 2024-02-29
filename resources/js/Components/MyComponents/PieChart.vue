<template>
    <div
        class="lg:min-h-52 min-h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>

        <div v-if="options.series.length" id="chart">
            <apexchart type="pie" width="700" :options="chartOptions" :series="series"></apexchart>
        </div>
        <p v-else class="text-xs text-center mt-10">No hay datos para  mostrar</p>
    </div>
</template>

<script>

export default {
    data() {
        return {
            series: this.options.series,
            chartOptions: {
                chart: {
                    width: 700,
                    type: 'pie',
                },
                colors: this.options.colors,
                labels: this.options.labels,
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'right'
                        }
                    }
                }]
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
}
</script>