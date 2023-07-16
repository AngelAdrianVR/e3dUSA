<template>
    <AppLayoutNoHeader title="Gestión de dias festivos">
        <div class="px-14 pt-8">
            <h1>Inicio</h1>

            <div class="flex justify-between items-center mt-14">
                <h2 class="text-primary text-xl">Reuniones</h2>
                <thirthButton>Registrar reunion</thirthButton>
            </div>
            <MeetingCard :meetings="meetings" class="mt-4" />

            <!-- operative -->
            <h2 class="text-primary text-xl mt-16">Operativo</h2>
            <div class="lg:grid grid-cols-4 gap-7 mt-4">
                <DashboardCard v-for="(quickCard, index) in quickCards" :key="index" :title="quickCard.title"
                    :counter="quickCard.counter" :icon="quickCard.icon" :href="quickCard.url" />
            </div>

            <!-- Collaborators -->
            <h2 class="text-primary text-xl mt-16">Colaboradores</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <ProductionPerformanceCard :users="[{id:1, name:'Miguel Osvaldo vr'},{id:2, name:'Angel Adrian vr'},{id:3, name:'Diego Gollaz J'}]"/>
                <BirthdateCard />
                <RecentlyAddedCard />
                <InformationCard :years="3" />
            </div>

             <!-- customers -->
             <h2 class="text-primary text-xl mt-16">Clientes</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <BirthdateCard />
            </div>
        </div>
    </AppLayoutNoHeader>
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import MeetingCard from '@/Components/MyComponents/MeetingCard.vue';
import DashboardCard from '@/Components/MyComponents/DashboardCard.vue';
import ProductionPerformanceCard from '@/Components/MyComponents/ProductionPerformanceCard.vue';
import BirthdateCard from '@/Components/MyComponents/BirthdateCard.vue';
import RecentlyAddedCard from '@/Components/MyComponents/RecentlyAddedCard.vue';
import InformationCard from '@/Components/MyComponents/InformationCard.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayoutNoHeader from '@/Layouts/AppLayoutNoHeader.vue';

export default {
    data() {
        return {
            quickCards: [
                {
                    title: 'Cotizaciones por autorizar',
                    counter: 0,
                    icon: 'fa-solid fa-file-invoice',
                    url: route('quotes.index')
                },
                {
                    title: 'Órdenes de venta por autorizar',
                    counter: 1,
                    icon: 'fa-solid fa-clipboard',
                    url: route('sales.index')
                },
                {
                    title: 'Órdenes de compra por autorizar',
                    counter: 0,
                    icon: 'fa-solid fa-cart-shopping',
                    url: route('purchases.index')
                },
                {
                    title: 'Órden de diseño por autorizar',
                    counter: 0,
                    icon: 'fa-solid fa-pen-ruler',
                    url: route('designs.index')
                },
                {
                    title: 'Productos con existencia baja',
                    counter: 4,
                    icon: 'fa-solid fa-arrows-down-to-line',
                    url: route('raw-materials.index')
                },
                {
                    title: 'Órdenes de producción sin iniciar',
                    counter: 0,
                    icon: 'fa-solid fa-spinner',
                    url: route('dashboard')
                },
                {
                    title: 'Órdenes de diseño por iniciar',
                    counter: 10,
                    icon: 'fa-solid fa-compass-drafting',
                    url: route('dashboard')
                },
                {
                    title: 'Venta sin órden de producción',
                    counter: 0,
                    icon: 'fa-solid fa-list-check',
                    url: route('sales.index')
                },
                {
                    title: 'Horas adicionales por autorizar',
                    counter: 3,
                    icon: 'fa-solid fa-users',
                    url: route('admin-additional-times.index')
                },
            ],
        }
    },
    props: {
        meetings: Object,
    },
    components: {
        ThirthButton,
        ApplicationLogo,
        MeetingCard,
        DashboardCard,
        PrimaryButton,
        SecondaryButton,
        ProductionPerformanceCard,
        InformationCard,
        RecentlyAddedCard,
        BirthdateCard,
        AppLayoutNoHeader,
    }
}
</script>
