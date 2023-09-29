<template>
    <AppLayoutNoHeader title="Inicio">
        <div class="px-9 pt-3 pb-8 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- Estadistics -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Proyectos</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-6 lg:space-y-0">
                <StackedColumn100Chart :options="projecsProgressChartOptions" title="Progreso de proyectos"
                    icon="<i class='fa-regular fa-flag ml-2'></i>" />
                <PieChart :options="taskStatusChartOptions" title="Estado de las Tareas"
                    icon='<i class="fa-solid fa-file-lines ml-2"></i>' />
                <DonutChart :options="projectGroupsChartOptions" title="Información de grupos de proyectos"
                    icon='<i class="fa-solid fa-circle-nodes ml-2"></i>' />
            </div>

            <!-- performance -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Desempeño</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <GroupedBarChar />
            </div>

        </div>
    </AppLayoutNoHeader>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import BirthdateCardCustomer from '@/Components/MyComponents/BirthdateCardCustomer.vue';
import CustomerDates from '@/Components/MyComponents/CustomerDates.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import AppLayoutNoHeader from '@/Layouts/AppLayoutNoHeader.vue';
import PieChart from '@/Components/MyComponents/PieChart.vue';
import DonutChart from '@/Components/MyComponents/DonutChart.vue';
import BarChart from '@/Components/MyComponents/BarChart.vue';
import StackedColumn100Chart from '@/Components/MyComponents/StackedColumn100Chart.vue';
import FunnelChart from '@/Components/MyComponents/FunnelChart.vue';
import RecentSales from '@/Components/MyComponents/RecentSales.vue';
import GroupedBarChar from '@/Components/MyComponents/GroupedBarChar.vue';

export default {
    data() {
        return {
            taskStatusChartOptions: {
                colors: ['#9A9A9A', '#0355B5', '#45E142',],
                labels: ['Sin iniciar', 'En curso', 'Terminado'],
                series: [44, 55, 13],
            },
            projecsProgressChartOptions: {
                colors: ['#9A9A9A', '#0355B5', '#45E142',],
                categories: ['Dalton Honda', 'Grupo Plascencia', 'Grupo Plascencia', 'BMW placas', 'BMW placas'],
                series: [{
                    name: 'Por hacer',
                    data: [5, 10, 3, 7, 2]
                }, {
                    name: 'En curso',
                    data: [3, 3, 2, 8, 1]
                }, {
                    name: 'Terminado',
                    data: [1, 7, 5, 5, 2]
                }],
            },
            projectGroupsChartOptions: {
                colors: ['#F2C940', '#E98A20', '#F9A547', '#FB4872', '#D90537'],
                labels: ['Producción', 'Ventas', 'Diseño', 'Compras', 'Proyectos internos'],
                series: [44, 19, 22, 41, 12],
            },
        }
    },
    props: {
    },
    components: {
        AppLayoutNoHeader,
        BirthdateCardCustomer,
        DialogModal,
        CancelButton,
        CustomerDates,
        PieChart,
        DonutChart,
        BarChart,
        StackedColumn100Chart,
        FunnelChart,
        RecentSales,
        GroupedBarChar,
    },
    methods: {

    },
    mounted() {

    }
}
</script>