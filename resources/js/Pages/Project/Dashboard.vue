<template>
    <AppLayoutNoHeader title="Inicio">
        <div class="px-9 pt-3 pb-8 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- Estadistics -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Proyectos</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-6 lg:space-y-0">
                <PieChart />
                <BarChart />
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
import BarChart from '@/Components/MyComponents/BarChart.vue';
import FunnelChart from '@/Components/MyComponents/FunnelChart.vue';
import RecentSales from '@/Components/MyComponents/RecentSales.vue';
import GroupedBarChar from '@/Components/MyComponents/GroupedBarChar.vue';

export default {
    data() {
        return {
        }
    },
    props: {
        customers_birthdays: Array,
    },
    components: {
        AppLayoutNoHeader,
        BirthdateCardCustomer,
        DialogModal,
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
       
    }
}
</script>