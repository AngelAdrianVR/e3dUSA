<template>
    <div>
        <div v-if="loading"
            class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
        </div>
        <div v-if="loading"
            class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
            <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
        </div>
        <AppLayout title="Órdenes de venta">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Órdenes de venta</h2>
                    </div>
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')"
                        :href="route('sales.create')">
                    <SecondaryButton>+ Nuevo</SecondaryButton>
                    <!-- <p class="text-sm text-primary w-48" v-if="!$page.props.auth.user.permissions.includes('Crear ordenes de venta sin cotizacion')">Para crear una ov es necesario convertirla desde una cotización</p> -->
                    </Link>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2 dark:text-white">
                <p><i class="fa-solid fa-circle mr-1 text-red-500"></i>Esperando Autorización</p>
                <p><i class="fa-solid fa-circle mr-1 text-gray-500"></i>Autorizado. Sin orden de producción</p>
                <p><i class="fa-solid fa-circle mr-1 text-amber-500"></i>Producción sin iniciar</p>
                <p><i class="fa-solid fa-circle mr-1 text-blue-500"></i>Producción en proceso</p>
                <p><i class="fa-solid fa-circle mr-1 text-green-500"></i>Producción terminada</p>
                <p><i class="fa-solid fa-circle mr-1 text-teal-300"></i>Enviado</p>
            </div>

            <!-- Filtro -->
            <div class="flex items-center space-x-5 lg:mx-28 mx-4 mt-5 dark:text-white">
                <div class="w-1/4 flex flex-col">
                    <label class="text-sm ml-2 mb-1">Filtro por propietario</label>
                    <el-select @change="fetchItemsFiltered" v-model="filter" class="!w-full"
                        placeholder="Selecciona una opción">
                        <el-option v-for="item in options" :key="item" :label="item" :value="item" />
                    </el-select>
                </div>
                <div class="w-1/3 flex flex-col">
                    <label class="text-sm ml-2 mb-1">Filtro por estatus</label>
                    <el-cascader @change="applyStatusFilter()" v-model="statusfilter" :options="cascaderOptions"
                        :props="cascaderProps" collapse-tags class="!w-full" />
                </div>
            </div>

            <div class="relative overflow-hidden min-h-[60vh]">
                <NotificationCenter module="sales" />
                <!-- tabla -->
                <div class="w-11/12 lg:w-5/6 mx-auto mt-6">
                    <div class="lg:flex justify-between items-center">
                        <!-- pagination -->
                        <div v-if="!search" class="overflow-auto mb-2">
                            <Pagination :pagination="sales" class="py-2" />
                        </div>
                        <!-- buttons -->
                        <div>
                            <el-popconfirm
                                v-if="$page.props.auth.user.permissions.includes('Eliminar ordenes de venta')"
                                confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                title="¿Continuar?" @confirm="deleteSelections">
                                <template #reference>
                                    <el-button type="danger" plain class="mb-3"
                                        :disabled="disableMassiveActions">Eliminar</el-button>
                                </template>
                            </el-popconfirm>
                        </div>
                        <!-- buscador -->
                        <IndexSearchBar @search="fetchMatches" />
                    </div>

                    <el-table :data="filteredSales" @row-click="handleRowClick" max-height="670" style="width: 100%"
                        @selection-change="handleSelectionChange" ref="multipleTableRef"
                        :row-class-name="tableRowClassName">
                        <el-table-column type="selection" width="30" />
                        <el-table-column label="folio" width="100">
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
                                    <p class="flex-0 w-[80%]">{{ scope.row.folio }}</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column v-if="$page.props.auth.user.permissions.includes('Ver utilidades')"
                            label="Utilidad" width="140">
                            <template #default="scope">
                                <SaleProfit :profit="scope.row.profit" />
                            </template>
                        </el-table-column>
                        <el-table-column prop="user.name" label="Creado por" />
                        <el-table-column prop="created_at" label="Creado el" />
                        <el-table-column prop="company_branch.name" label="Cliente" />
                        <el-table-column prop="authorized_user_name" label="Autorizado por" />
                        <el-table-column prop="authorized_at" label="Autorizado el" />
                        <el-table-column label="Estatus">
                            <template #default="scope">
                                <div class="flex">
                                    <p class="mr-2" :class="getStatusColor(scope.row)">
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                    </p>
                                    <p class="flex-0 w-[80%]">{{ scope.row.raw_status }}</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="promise_date" label="Fecha de entrega" width="180" />
                        <el-table-column label="Cotización">
                            <template #default="scope">
                                <div>
                                    <p v-if="scope.row.quote_id" @click.stop="handleShowQuote(scope.row.quote_id)"
                                        class="text-blue-400 hover:underline">COT-{{ String(scope.row.quote_id).padStart('0', 4) }}</p>
                                    <p v-else>N/A</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="Factura">
                            <template #default="scope">
                                <div>
                                    <p v-if="scope.row.invoices?.length" @click.stop="handleShowInvoice(scope.row.invoices[0].id)"
                                        class="text-blue-400 hover:underline">{{ scope.row.invoices[0].folio }}</p>
                                    <p v-else>Sin factura</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column align="right">
                            <template #default="scope">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <button @click.stop
                                        class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'show-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                Ver</el-dropdown-item>
                                            <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
                                                scope.row.user.id == $page.props.auth.user.id"
                                                :command="'edit-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                                Editar</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')"
                                                :command="'clone-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                                </svg>
                                                Clonar</el-dropdown-item>
                                            <el-dropdown-item :command="'print-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                                                </svg>
                                                Imprimir</el-dropdown-item>
                                                <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Crear facturas') && !scope.row.invoices?.length" :command="'create-' + scope.row.id">
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
            <!-- tabla -->
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import SaleProfit from "@/Components/MyComponents/SaleProfit.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import Pagination from "@/Components/MyComponents/Pagination.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        return {
            disableMassiveActions: true,
            // filtro
            cascaderProps: { multiple: true },
            filter: 'Mis órdenes',
            statusfilter: [["Esperando autorización"], ["Autorizado sin orden de producción"], ["Producción sin iniciar"], ["Producción en proceso"]],
            cascaderOptions: [
                {
                    value: 'Esperando autorización',
                    label: 'Esperando autorización',
                },
                {
                    value: 'Autorizado sin orden de producción',
                    label: 'Autorizado sin orden de producción',
                },
                {
                    value: 'Producción sin iniciar',
                    label: 'Producción sin iniciar',
                },
                {
                    value: 'Producción en proceso',
                    label: 'Producción en proceso',
                },
                {
                    value: 'Producción terminada',
                    label: 'Producción terminada',
                },
            ],
            options: ['Mis órdenes', 'Todas las órdenes'],
            filteredSales: [],
            // inputSearch: '',
            search: '',
            loading: false,
            pagination: null,
            // pagination
            // itemsPerPage: 10,
            // start: 0,
            // end: 10,
        };

    },
    components: {
        NotificationCenter,
        SecondaryButton,
        IndexSearchBar,
        PrimaryButton,
        SaleProfit,
        Pagination,
        AppLayout,
        Link,
    },
    props: {
        sales: Object,
        company_branches: Array
    },
    methods: {
        handleShowQuote(quoteId) {
            const url = this.route('quotes.show', quoteId);
            window.open(url, '_blank');
        },
        handleShowInvoice(invoiceId) {
            const url = this.route('invoices.show', invoiceId);
            window.open(url, '_blank');
        },
        applyStatusFilter() {
            // Convertir el array de arrays a un array plano de strings
            const flatSelectedStatuses = this.statusfilter.flat();

            // Filtrar las ventas basadas en los estados seleccionados
            this.filteredSales = this.sales.data.filter(sale =>
                flatSelectedStatuses.includes(sale.status.label)
            );
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        getStatusColor(row) {
            if (row.raw_status == 'Esperando autorización') {
                return 'text-red-500';
            } else if (row.raw_status == 'Producción sin iniciar') {
                return 'text-amber-500';
            } else if (row.raw_status == 'Producción en proceso') {
                return 'text-blue-500';
            } else if (row.raw_status == 'Producción terminada') {
                return 'text-green-500';
            } else if (row.raw_status == 'Autorizado. Sin orden de producción') {
                return 'text-gray-500';
            } else if (row.raw_status == 'Enviado') {
                return 'text-teal-300';
            }
        },
        handleRowClick(row) {
            this.$inertia.get(route('sales.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else if (commandName == 'create') {
                this.$inertia.visit(route('invoices.create', { sale_id: rowId }));
            } else {
                this.$inertia.get(route('sales.' + commandName, rowId));
            }
        },
        fetchItemsFiltered() {
            this.$inertia.get(route('sales.fetch-filtered', this.filter));
        },
        removeLeftZeros() {
            // Verificar si el valor ingresado es un número
            const isNumber = /^\d+$/.test(this.search);

            if (isNumber) {
                // Eliminar ceros a la izquierda
                return parseInt(this.search, 10).toString();
            }

            return this.search;
        },
        async fetchMatches(search) {
            this.search = search;
            this.loading = true;
            try {
                if (!this.search) {
                    this.$inertia.get(route('sales.index'));
                } else {
                    const processed = this.removeLeftZeros();
                    const response = await axios.get(route('sales.get-matches', { query: processed }));

                    if (response.status === 200) {
                        this.filteredSales = response.data.items;
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async clone(sale_id) {
            try {
                const response = await axios.post(route('sales.clone', {
                    sale_id: sale_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.sales.data.unshift(response.data.newItem);

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }
            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
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
        async deleteSelections() {
            try {
                const response = await axios.post(route('sales.massive-delete', {
                    sales: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of sales
                    let deletedIndexes = [];
                    this.filteredSales.forEach((sale, index) => {
                        if (this.$refs.multipleTableRef.value.includes(sale)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.filteredSales.splice(index, 1);
                    }

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
    },
    mounted() {
        this.applyStatusFilter();
    },
};
</script>
