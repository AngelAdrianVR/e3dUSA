<template>
    <div class="lg:min-h-52 min-h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm relative">
        <h1 class="font-bold text-center">Ventas de {{ currentMonth }}</h1>

        <div id="chart">
            <apexchart type="pie" width="335" :options="chartOptions" :series="series"></apexchart>
        </div>
        <div class="flex justify-end mx-6 absolute bottom-3 right-5">
            <button class="text-primary text-xs">Ver detalles</button>
        </div>
    </div>
</template>

<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            currentMonth: null,
            series: [44, 55, 13, 43, 22, 8, 7],
            chartOptions: {
                chart: {
                    width: 300,
                    type: 'pie',
                },
                labels: ['Porta placas', 'Tapetes', 'Emblemas', 'Llaveros', 'Parasoles', 'Termos', 'Perfumes'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            },
        };
    },
    methods: {

    },
    mounted() {
        // Obtiene la fecha actual
        const currentDate = new Date();
        // Formatea el mes actual en español
        this.currentMonth = format(currentDate, 'MMMM', { locale: es });
    },
}
</script>