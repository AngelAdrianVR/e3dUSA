<template>
    <div v-if="loading"
        class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
    </div>
    <div v-if="loading"
        class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
        <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
    </div>
    <main>
        <section>
            <div class="flex justify-end">
                <Link :href="route('invoices.create')">
                    <SecondaryButton>Crear factura</SecondaryButton>
                </Link>
            </div>
        </section>

        <!-- tabla -->
        <div class="relative overflow-hidden min-h-[60vh]">
            <!-- <NotificationCenter module="production" /> -->
            <div class="lg:w-[90%] mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div v-if="!search" class="overflow-auto mb-2">
                        <PaginationWithNoMeta :pagination="pagination" class="py-2" />
                    </div>

                    <!-- buttons -->
                    <!-- <div>
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                            title="¿Continuar?" @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div> -->
                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>
                <el-table :data="filteredSales" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column label="folio" width="120" sortable>
                        <template #default="scope">
                            <div class="flex">
                                <p class="mr-2">
                                    <el-tooltip v-if="scope.row.is_sale_production" content="Orden de venta"
                                        placement="top">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-4 mt-1 text-purple-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                        </svg>
                                    </el-tooltip>
                                    <el-tooltip v-else content="Orden de stock" placement="top">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="size-4 mt-1 text-rose-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                        </svg>
                                    </el-tooltip>
                                </p>
                                <p class="flex-0 w-[80%]">OV-{{ scope.row.id }}</p>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="user.name" label="Creado por" />
                    <el-table-column prop="authorized_user_name" label="Autorizado por" />
                    <el-table-column prop="authorized_at" label="Autorizado el">
                        <template #default="scope">
                            <p>{{ formatDate(scope.row.authorized_at) }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="company_branch.name" label="Cliente/Sucursal" />
                    <!-- <el-table-column prop="invoice_amount" label="Monto de la OV">
                        <template #default="scope">
                            <p>${{ '0.00' }}</p>
                        </template>
                    </el-table-column> -->
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show|' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item :command="'create|' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            Crear factura
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </main>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    return {
        disableMassiveActions: true,
        inputSearch: '',
        search: '',
        loading: false,
        sales: [],
        pagination: null,
        filteredSales: [],
    }
},
components:{
    PaginationWithNoMeta,
    NotificationCenter,
    SecondaryButton,
    IndexSearchBar,
    Link
},
props:{
},
methods:{
    formatDateTime(dateTimeString) {
        if (!dateTimeString) return '';
        return format(parseISO(dateTimeString), 'dd MMM yyy, hh:mm a', { locale: es });
    },
    formatDate(dateTimeString) {
        if (!dateTimeString) return '';
        return format(parseISO(dateTimeString), 'dd MMM yyy', { locale: es });
    },
    handleRowClick(row) {
        this.$inertia.get(route('sales.show', row.id));
    },
    handleCommand(command) {
        const commandName = command.split('|')[0];
        const rowId = command.split('|')[1];

        if (commandName === 'create') {
            this.$inertia.visit(route('invoices.create', { sale_id: rowId }));
        } else {
            this.$inertia.get(route('sales.' + commandName, rowId));
        }
    },
    handleSelectionChange(val) {
        this.$refs.multipleTableRef.value = val;

        if (!this.$refs.multipleTableRef.value.length) {
            this.disableMassiveActions = true;
        } else {
            this.disableMassiveActions = false;
        }
    },
    tableRowClassName({ row, rowIndex }) {
        return 'cursor-pointer text-xs';
    },
    async handleSearch(search) {
        this.search = search;
        this.loading = true;
        try {
            if (!this.search) {
                this.filteredSales = this.sales.data;
                this.pagination = this.sales; //cuando se busca con texto vacio s emuestran todas las porduccoines y la paginacion es de todas las producciones
            } else {
                const response = await axios.get(route('sales.get-matches-no-invoices', { query: this.search }));

                if (response.status === 200) {
                    this.filteredSales = response.data.sales;
                }
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    },
    async fetchSalesWithNoInvoices() {
        this.loading = true;
        try {
            const response = await axios.get(route('sales.get-no-invoices'));

            if ( response.status === 200 ) {
                this.sales = response.data.sales;
                this.pagination = this.sales;
                this.filteredSales = this.sales.data;
            }

        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    }
},
mounted() {
    this.fetchSalesWithNoInvoices();
}
}
</script>
