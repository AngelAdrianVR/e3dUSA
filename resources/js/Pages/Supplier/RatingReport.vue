<template>

    <Head title="Reporte de evaluaciones" />
    <!-- logo -->
    <div class="flex items-center mx-6 mt-5">
        <ApplicationLogo class="h-auto w-1/5 inline-block" />
    </div>

    <!-- contenido -->
    <div class="text-xs px-6 mt-8">
        <h1 class="font-bold text-xl mx-5">Evaluación proveedores </h1>
        <table class="w-full table-fixed mt-4">
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
    </div>
</template>
<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head } from '@inertiajs/vue3';

export default {
    data() {
        return {

        };
    },
    components: {
        ApplicationLogo,
        Head,
    },
    props: {
        data: Object,
    },
    methods: {
        handleBeforePrint() {
            this.showAdditionalElements = false;
        },
        handleAfterPrint() {
            this.showAdditionalElements = true;
        },
        print() {
            window.print();
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