<template>
    <AppLayout title="Factuaración">
        <main class="p-2 md:py-4 md:px-6 dark:text-white">
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Facturas</h2>
                </div>
                <div>
                    <!-- <Link :href="route('invoices.report')"> -->
                        <SecondaryButton @click="showReportModal = true" class="!bg-[#838383]">Generar reporte</SecondaryButton>
                    <!-- </Link> -->
                </div>
            </div>

            <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
                <el-tab-pane label="Facturas" name="1">
                    <InvoiceIndex :invoices="invoices" />
                </el-tab-pane>
                <el-tab-pane label="OV terminadas S/Factura" name="2">
                    <SalesNoInvoiceIndex />
                </el-tab-pane>
                <el-tab-pane label="Facturas programadas" name="3">
                    <ProgrammedInvoicesIndex />
                </el-tab-pane>
            </el-tabs>
        </main>

        <!-- -------------- Report Modal starts----------------------- -->
        <Modal :show="showReportModal" @close="showReportModal = false">
            <div class="p-5 relative">
                <h2 class="font-bold mb-1">Generar reporte</h2>
                <p class="my-3">Filtra la información para visualizar las facturas</p>
                <i @click="showReportModal = false"
                    class="fa-solid fa-xmark cursor-pointer size-5 rounded-full flex items-center justify-center absolute right-3 top-3"></i>

                <section class="md:grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel value="Tipo de registro*" />
                        <el-cascader
                            class="!w-full"
                            v-model="types"
                            :options="options"
                            :props="cascaderProps"
                            collapse-tags
                            collapse-tags-tooltip
                            clearable
                        />
                    </div>

                    <div>
                        <InputLabel value="Cliente*" />
                        <el-select
                            v-model="company_branches_ids"
                            multiple
                            collapse-tags
                            collapse-tags-tooltip
                            filterable
                            placeholder="Selecciona"
                            >
                            <el-option
                                v-for="item in company_branches"
                                :key="item.value"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                    </div>
                    <div class="mt-5 mx-3 md:text-right col-span-full">
                        <PrimaryButton @click="generateReport">
                            Generar reporte
                        </PrimaryButton>
                    </div>
                </section>   
            </div>
        </Modal>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import InvoiceIndex from "@/Components/MyComponents/InvoiceIndex.vue";
import SalesNoInvoiceIndex from "@/Components/MyComponents/SalesNoInvoiceIndex.vue";
import ProgrammedInvoicesIndex from "@/Components/MyComponents/ProgrammedInvoicesIndex.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    return {
        showReportModal: false,
        activeTab: '1',
        cascaderProps: {multiple: true},
        types: [],
        company_branches_ids: [],
        options: [
            {
                value: 'Todas',
                label: 'Todas',
            },
            {
                value: 'Facturas registradas',
                label: 'Facturas registradas',
                children: [
                    {
                        value: 'Pendiente de pago',
                        label: 'Pendiente de pago',
                    },
                    {
                        value: 'Parcialmente pagada',
                        label: 'Parcialmente pagada',
                    },
                    {
                        value: 'Pagada',
                        label: 'Pagada',
                    },
                    {
                        value: 'Cancelada',
                        label: 'Cancelada',
                    },
                ],
            },
            {
                value: 'Facturas programadas',
                label: 'Facturas programadas',
            },
            {
                value: 'OV terminadas sin facturas',
                label: 'OV terminadas sin facturas',
            },
        ]
    }
},
components: {
    ProgrammedInvoicesIndex,
    PaginationWithNoMeta,
    SalesNoInvoiceIndex,
    SecondaryButton,
    PrimaryButton,
    InvoiceIndex,
    InputLabel,
    AppLayout,
    Modal,
    Link
},
props: {
invoices: Object,
company_branches: Array
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
    generateReport() {
        const data = {
            types: this.types,
            company_branches_ids: this.company_branches_ids,
        }

        this.$inertia.post(route('invoices.report'), data)
    }
},
mounted() {
    this.setTabInUrl();
},
}
</script>
