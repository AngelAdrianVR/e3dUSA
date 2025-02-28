<template>

    <Head title="Reporte de evaluaciones" />
    <!-- logo -->
    <div class="flex items-center justify-between mx-6 mt-5">
        <ApplicationLogo class="h-auto w-1/5 inline-block" />
        <SecondaryButton v-if="showAdditionalElements" @click="print()">Guardar PDF</SecondaryButton>
    </div>

    <!-- contenido -->
    <div class="text-xs px-6 mt-8 relative">
        <div class="bg-[#DADADA] opacity-70 size-12 rounded-full absolute top-7 left-1/2 -z-10"></div>
        <div class="bg-[#DADADA] opacity-70 size-40 rounded-full absolute top-32 left-[77%] -z-10"></div>
        <div class="bg-[#DADADA] opacity-70 size-32 rounded-full absolute top-7 left-[82%] -z-10"></div>
        <div class="bg-[#DADADA] opacity-70 size-16 rounded-full absolute top-7 left-10 -z-10"></div>
        <div class="bg-[#DADADA] opacity-70 size-8 rounded-full absolute top-10 left-28 -z-10"></div>
        <div class="bg-[#DADADA] opacity-70 size-30 rounded-full absolute top-36 right-2/3 -z-10"></div>
        <div class="bg-[#DADADA] opacity-70 size-8 rounded-full absolute top-36 right-1/2 -z-10"></div>
        <div class="bg-[#DADADA] opacity-70 size-8 rounded-full absolute top-40 right-[85%] -z-10"></div>
        <h1 class="font-bold text-xl mx-5 flex items-center justify-between">
            <span>Evaluación proveedores</span>
            <span class="text-[#373737] text-base">{{ period }}</span>
        </h1>
        <table class="w-full table-auto mt-4" v-if="Object.keys(data).length > 0">
            <thead>
                <tr class="text-left *:px-2 *:py-1">
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Alias</th>
                    <th>Ordenes evaluadas</th>
                    <th>Evaluación promedio</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in Object.values(data)" :key="index" class="*:px-2 *:py-1">
                   <td>{{ item.supplier_id }}</td>
                   <td>{{ item.supplier_name }}</td>
                   <td>{{ item.supplier_nickname }}</td>
                   <td>{{ item.total_purchases }}</td>
                   <td>{{ item.avg_points }}</td>
                </tr>
            </tbody>
        </table>
        <p v-else class="text-center text-lg mt-10">No hay información de compras de los proveedores seleccionados</p>
    </div>
</template>
<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head } from '@inertiajs/vue3';

export default {
    data() {
        return {
            showAdditionalElements: true,
        };
    },
    components: {
        ApplicationLogo,
        Head,
        SecondaryButton,
    },
    props: {
        data: Object,
        period: String,
    },
    methods: {
        handleBeforePrint() {
            this.showAdditionalElements = false;
        },
        handleAfterPrint() {
            this.showAdditionalElements = true;
        },
        print() {
            this.showAdditionalElements = false;
            this.$nextTick(() => {
                window.print();
            });
        },
    },
    mounted() {
        window.addEventListener('beforeprint', this.handleBeforePrint);
        window.addEventListener('afterprint', this.handleAfterPrint);
    },
    beforeDestroy() {
        window.removeEventListener('beforeprint', this.handleBeforePrint);
        window.removeEventListener('afterprint', this.handleAfterPrint);
    }
}
</script>