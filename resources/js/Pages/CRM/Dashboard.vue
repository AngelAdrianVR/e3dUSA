<template>
    <AppLayoutNoHeader title="Inicio">
        <div class="px-9 pt-3 pb-8 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- customers -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Clientes</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-6 lg:space-y-0">
                <CustomerDates />
                <BirthdateCardCustomer :contacts="customers_birthdays" />
            </div>

            <!-- Estadistics -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Estadísticas</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4 space-y-6 lg:space-y-0">
                <PieChart :title="'Ventas de ' + currentMonth" :colors="['#9A9A9A', '#0355B5', '#45E142',]"
                    :labels="['Porta placas', 'Tapetes', 'Emblemas', 'Llaveros', 'Parasoles', 'Termos', 'Perfumes']" />
                <BarChart />
            </div>

            <!-- sales -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Seguimiento de ventas</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <FunnelChart />
                <RecentSales
                    :sales="[{ close_date: '24 ago 2023', customer_name: 'BOSH', total_sold: '$19,458.5', seller: 'Evelin Montero' }]" />
            </div>

            <!-- performance -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Desempeño</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <GroupedBarChar />
            </div>

        </div>
        <DialogModal :show="false" @close="">
            <template #title>

            </template>
            <template #content>

            </template>
            <template #footer>
                <CancelButton @click="">
                    Cerrar
                </CancelButton>
            </template>
        </DialogModal>
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

import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            currentMonth: null,
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
        // Obtiene la fecha actual
        const currentDate = new Date();
        // Formatea el mes actual en español
        this.currentMonth = format(currentDate, 'MMMM', { locale: es });
    },
}
</script>
