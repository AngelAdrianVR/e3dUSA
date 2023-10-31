<template>
    <AppLayoutNoHeader title="Inicio">
        <div class="px-9 pt-3 pb-8 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- customers -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Clientes</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <CustomerDates />
                <BirthdateCardCustomer :contacts="customers_birthdays" />
            </div>

            <!-- Estadistics -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Estadísticas</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <PieChart :options="monthSalesChartOptions" :title="'Ventas de ' + currentMonth"
                    icon='<i class="fa-solid fa-hand-holding-dollar ml-2"></i>' />
                <BarChart :options="yearComparisonChartOptions" title="Ventas año en curso vs anterior"
                    icon='<i class="fa-solid fa-scale-unbalanced-flip ml-2"></i>' />
            </div>

            <!-- sales -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Seguimiento de ventas</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <FunnelChart :options="funnelSalesChartOptions" title="Embudo de ventas"
                    icon='<i class="fa-solid fa-filter-circle-dollar ml-2"></i>' />
                <RecentSales
                    :sales="[{ close_date: '24 ago 2023', customer_name: 'BOSH', total_sold: '$19,458.5', seller: 'Evelin Montero' }]" />
            </div>

            <!-- performance -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6 font-bold">Desempeño</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-4 lg:space-y-0">
                <GroupedBarChar :options="saleGoalsChartOptions" title="Objetivo de ventas"
                    icon='<i class="fa-solid fa-bullseye ml-2"></i>' />
            </div>

        </div>
    </AppLayoutNoHeader>
</template>

<script>
import BirthdateCardCustomer from '@/Components/MyComponents/BirthdateCardCustomer.vue';
import CustomerDates from '@/Components/MyComponents/CustomerDates.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import AppLayoutNoHeader from '@/Layouts/AppLayoutNoHeader.vue';
import PieChart from '@/Components/MyComponents/PieChart.vue';
import BarChart from '@/Components/MyComponents/BarChart.vue';
import FunnelChart from '@/Components/MyComponents/FunnelChart.vue';
import RecentSales from '@/Components/MyComponents/RecentSales.vue';
import GroupedBarChar from '@/Components/MyComponents/GroupedBarChar.vue';

import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            currentMonth: null,
            monthSalesChartOptions: {
                colors: ['#31CB23', '#D47914', '#D90537', '#888888', '#0355B5', '#0397B5', '#A41314'],
                labels: ['Porta placas', 'Tapetes', 'Emblemas', 'Llaveros', 'Parasoles', 'Termos', 'Perfumes'],
                series: [44, 55, 13, 19, 6, 1, 1],
            },
            yearComparisonChartOptions: {
                colors: ['#9A9A9A', '#0355B5', '#45E142',],
                categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                series: [{
                    name: 'Año pasado',
                    data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 70, 90.5, 110.95]
                },
                {
                    name: 'Año en curso',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 0, 0, 0]
                }],
            },
            funnelSalesChartOptions: {
                colors: ['#31CB23', '#D47914', '#888888', '#D90537'],
                categories: ['Prospectos', 'Propuesta', 'Cotización ', 'Ventas cerradas'],
                series: [{
                    name: "Cantidad",
                    data: [10, 7, 5, 3],
                }],
            },
            saleGoalsChartOptions: {
                colors: ['#31CB23', '#D41614'],
                categories: ['Edgar Sherman', 'Norberto Platas', 'Santiago'],
                series: [{
                    name: 'Meta',
                    data: [500, 500, 500]
                }, {
                    name: 'Ventas',
                    data: [125.9, 300, 209.57]
                }],
                labelPrefix: '$ ',
                labelSufix: 'K',
            },
        }
    },
    props: {
        customers_birthdays: Array,
    },
    components: {
        AppLayoutNoHeader,
        BirthdateCardCustomer,
        CancelButton,
        CustomerDates,
        PieChart,
        BarChart,
        FunnelChart,
        RecentSales,
        GroupedBarChar,
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
