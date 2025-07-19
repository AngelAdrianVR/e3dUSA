<template>
    <AppLayout title="Factuaración">
        <main class="p-2 md:py-4 md:px-6 dark:text-white">
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Facturas</h2>
                </div>
                <div>
                    <!-- <Link :href="route('invoices.create')"> -->
                        <SecondaryButton disabled>Generar reporte</SecondaryButton>
                    <!-- </Link> -->
                </div>
            </div>

            <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
                <el-tab-pane label="Facturas" name="1">
                <InvoiceIndex :invoices="invoices" />
                </el-tab-pane>
                <el-tab-pane label="OV terminadas S/Factura" name="2">
                    OV terminadas S/Factura
                </el-tab-pane>
                <el-tab-pane label="Facturas programadas" name="3">
                    Facturas programadas
                </el-tab-pane>
            </el-tabs>

        </main>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import InvoiceIndex from "@/Components/MyComponents/InvoiceIndex.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    return {
        activeTab: '1',
    }
},
components: {
    PaginationWithNoMeta,
    SecondaryButton,
    InvoiceIndex,
    AppLayout,
    Link
},
props: {
invoices: Array
},
methods: {
    handleClickInTab(tab) {
        // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
        const currentURL = new URL(window.location.href);
        currentURL.searchParams.set('currentTab', tab.props.name);
        // Actualiza la URL
        window.history.replaceState({}, document.title, currentURL.href);
    },
    setTabInUrl() {
        // Obtener la URL actual
        const currentURL = new URL(window.location.href);
        // Extraer el valor de 'currentTab' de los parámetros de búsqueda
        const currentTabFromURL = currentURL.searchParams.get('currentTab');

        if (currentTabFromURL) {
            this.activeTab = currentTabFromURL;
        }
    },
},
mounted() {
    this.setTabInUrl();
},
}
</script>
