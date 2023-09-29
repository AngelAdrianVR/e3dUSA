<template>
    <div class="min-h-[220px] bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">{{ title }} <span v-html="icon"></span></h1>

        <div id="chart">
            <apexchart type="bar" height="200" :options="chartOptions" :series="series"></apexchart>
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
                colors: this.options.colors,
                dataLabels: {
                    enabled: true,
                    formatter: (val) => {
                        return this.options.labelPrefix + val + this.options.labelSufix
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
                        formatter: (val) => {
                            return this.options.labelPrefix + val + this.options.labelSufix
                        }
                    }
                },
                xaxis: {
                    categories: this.options.categories,
                },
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