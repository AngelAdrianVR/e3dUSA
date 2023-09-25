<template>
    <div class="lg:min-h-52 min-h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">Embudo de ventas</h1>

        <div id="chart">
            <apexchart type="bar" height="150" :options="chartOptions" :series="series"></apexchart>
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
                    name: "Cantidad",
                    data: [10, 7, 5, 3],
                },
            ],
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 350,
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        horizontal: true,
                        barHeight: '75%',
                        isFunnel: true,
                        distributed: true,
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opt) {
                        return val;
                    },
                    dropShadow: {
                        enabled: false,
                    },
                    style: {
                        fontSize: '11px',
                        colors: ["#ffffff"]
                    }
                },
                colors: [
                    '#31CB23',
                    '#D47914',
                    '#888888',
                    '#D90537',
                ],
                xaxis: {
                    categories: [
                        'Prospectos',
                        'Propuesta',
                        'Cotización ',
                        'Ventas cerradas',
                    ],
                },
                legend: {
                    show: true,
                },
            },

        };
    },
    methods: {

    },
}
</script>