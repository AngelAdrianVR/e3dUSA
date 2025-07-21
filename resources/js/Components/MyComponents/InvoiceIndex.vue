<template>
    <div v-if="loading"
        class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
    </div>
    <div v-if="loading"
        class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
        <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
    </div>
    <main>
        <div class="flex space-x-6 items-center justify-center text-[13px] mt-2">
            <p class="flex flex-col items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#08688B]"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>Emitida</p>
            <p class="flex flex-col items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#B8B30E]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>Pendiente de pago</p>
            <p class="flex flex-col items-center justify-center"><svg width="21" height="21" viewBox="0 0 24 24" class="text-[#C4620C]" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.30078 19.5173C2.8856 19.5168 3.47013 19.5249 4.05421 19.5415M19.5508 19.5173V20.5223C19.5508 21.2763 18.8248 21.8163 18.0978 21.6183C14.7336 20.7044 11.2996 20.0859 7.83503 19.7682M3.80078 5.2673V6.0173C3.80078 6.21621 3.72176 6.40698 3.58111 6.54763C3.44046 6.68828 3.24969 6.7673 3.05078 6.7673H2.30078M2.30078 6.7673V6.3923C2.30078 5.7713 2.80478 5.2673 3.42578 5.2673C8.74133 5.2673 9.35662 5.2673 14.6722 5.2673M2.30078 6.7673V15.7673M20.3008 5.2673V6.0173C20.3008 6.4313 20.6368 6.7673 21.0508 6.7673H21.8008M20.3008 5.2673H20.6758C21.2968 5.2673 21.8008 5.7713 21.8008 6.3923V16.1423C21.8008 16.7633 21.2968 17.2673 20.6758 17.2673H20.3008M20.3008 5.2673C19.6254 5.2673 19.5241 5.2673 18.8487 5.2673M2.30078 15.7673V16.1423C2.30078 16.4407 2.41931 16.7268 2.63029 16.9378C2.84126 17.1488 3.12741 17.2673 3.42578 17.2673H3.80078M2.30078 15.7673H3.05078C3.24969 15.7673 3.44046 15.8463 3.58111 15.987C3.72176 16.1276 3.80078 16.3184 3.80078 16.5173V17.2673M20.3008 17.2673V16.5173C20.3008 16.3184 20.3798 16.1276 20.5205 15.987C20.6611 15.8463 20.8519 15.7673 21.0508 15.7673H21.8008M20.3008 17.2673C20.3008 17.2673 14.9222 17.2673 9.92946 17.2673M3.80078 17.2673C3.80078 17.2673 4.41681 17.2673 5.69804 17.2673M12.0508 14.2673C12.8464 14.2673 13.6095 13.9512 14.1721 13.3886C14.7347 12.826 15.0508 12.0629 15.0508 11.2673C15.0508 11.0133 15.0186 10.7626 14.9564 10.5207M12.0508 8.2673C11.2551 8.2673 10.4921 8.58337 9.92946 9.14598C9.36685 9.70859 9.05078 10.4716 9.05078 11.2673C9.05078 11.694 9.14168 12.1113 9.31274 12.4933M19.0679 2.41113L4.43777 21.6183M18.0508 11.2673H18.0588V11.2753H18.0508V11.2673ZM6.05078 11.2673H6.05878V11.2753H6.05078V11.2673Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Parcialmente pagada</p>
            <p class="flex flex-col items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-green-500"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" /></svg>Pagada</p>
            <p class="flex flex-col items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>Cancelada</p>
        </div>

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
                    <div>
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                            title="¿Continuar?" @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>
                <el-table :data="filteredInvoices" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column label="folio" width="100">
                        <template #default="scope">
                            <div class="flex">
                                <p class="mr-2">
                                    <el-tooltip :content="scope.row.status"
                                        placement="top">
                                        <span v-html="statusIcon(scope.row.status)"></span>
                                    </el-tooltip>
                                </p>
                                <p class="flex-0 w-[80%]">{{ scope.row.folio }}</p>
                                <!-- <p class="flex-0 w-[80%]">{{ String(scope.row.id).padStart(4, '0') }}</p> -->
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="created_by" label="Creado por" />
                    <el-table-column prop="created_at" label="Creado el">
                        <template #default="scope">
                            <p>{{ formatDateTime(scope.row.created_at) }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="invoice_quantity" label="No. factura">
                        <template #default="scope">
                            <p>{{ scope.row.number_of_invoice }} de {{ scope.row.invoice_quantity }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="sale_id" label="Orden de venta">
                        <template #default="scope">
                            <p @click.stop="$inertia.visit(route('sales.show', scope.row.sale_id))" class="text-blue-500 underline">{{ scope.row.sale_id }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="company_branch.name" label="Sucursal" />
                    <el-table-column prop="complements.length" label="Complementos">
                        <template #default="scope">
                            <p>{{ scope.row.complements?.length ?? '0' }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="invoice_amount" label="Monto de factura">
                        <template #default="scope">
                            <p>${{ scope.row.invoice_amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '$0.00' }}</p>
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
                                        <el-dropdown-item :command="'show|' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item :command="'edit|' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                            <div v-if="scope.row.status !== 'Cancelada' && scope.row.status !== 'Pagada'">
                                                <p class="text-gray-400 text-xs text-center px-3 my-1">Cambiar estatus</p>
                                                <el-dropdown-item
                                                    :command="'changeStatus|' + scope.row.id + '|Emitida'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                    </svg>
                                                    <span class="text-xs">Emitida</span>
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="'changeStatus|' + scope.row.id + '|Pendiente de pago'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                    <span class="text-xs">Pendiente de pago</span>
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="'changeStatus|' + scope.row.id + '|Parcialmente pagada'">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" class="size-[14px] mr-2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2.30078 19.5173C2.8856 19.5168 3.47013 19.5249 4.05421 19.5415M19.5508 19.5173V20.5223C19.5508 21.2763 18.8248 21.8163 18.0978 21.6183C14.7336 20.7044 11.2996 20.0859 7.83503 19.7682M3.80078 5.2673V6.0173C3.80078 6.21621 3.72176 6.40698 3.58111 6.54763C3.44046 6.68828 3.24969 6.7673 3.05078 6.7673H2.30078M2.30078 6.7673V6.3923C2.30078 5.7713 2.80478 5.2673 3.42578 5.2673C8.74133 5.2673 9.35662 5.2673 14.6722 5.2673M2.30078 6.7673V15.7673M20.3008 5.2673V6.0173C20.3008 6.4313 20.6368 6.7673 21.0508 6.7673H21.8008M20.3008 5.2673H20.6758C21.2968 5.2673 21.8008 5.7713 21.8008 6.3923V16.1423C21.8008 16.7633 21.2968 17.2673 20.6758 17.2673H20.3008M20.3008 5.2673C19.6254 5.2673 19.5241 5.2673 18.8487 5.2673M2.30078 15.7673V16.1423C2.30078 16.4407 2.41931 16.7268 2.63029 16.9378C2.84126 17.1488 3.12741 17.2673 3.42578 17.2673H3.80078M2.30078 15.7673H3.05078C3.24969 15.7673 3.44046 15.8463 3.58111 15.987C3.72176 16.1276 3.80078 16.3184 3.80078 16.5173V17.2673M20.3008 17.2673V16.5173C20.3008 16.3184 20.3798 16.1276 20.5205 15.987C20.6611 15.8463 20.8519 15.7673 21.0508 15.7673H21.8008M20.3008 17.2673C20.3008 17.2673 14.9222 17.2673 9.92946 17.2673M3.80078 17.2673C3.80078 17.2673 4.41681 17.2673 5.69804 17.2673M12.0508 14.2673C12.8464 14.2673 13.6095 13.9512 14.1721 13.3886C14.7347 12.826 15.0508 12.0629 15.0508 11.2673C15.0508 11.0133 15.0186 10.7626 14.9564 10.5207M12.0508 8.2673C11.2551 8.2673 10.4921 8.58337 9.92946 9.14598C9.36685 9.70859 9.05078 10.4716 9.05078 11.2673C9.05078 11.694 9.14168 12.1113 9.31274 12.4933M19.0679 2.41113L4.43777 21.6183M18.0508 11.2673H18.0588V11.2753H18.0508V11.2673ZM6.05078 11.2673H6.05878V11.2753H6.05078V11.2673Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <span class="text-xs">Parcialmente pagada</span>
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="'changeStatus|' + scope.row.id + '|Pagada'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                                                    </svg>
                                                    <span class="text-xs">Pagada</span>
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                    :command="'changeStatus|' + scope.row.id + '|Cancelada'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[14px] mr-2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                    <span class="text-xs">Cancelada</span>
                                                </el-dropdown-item>
                                            </div>
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
        pagination: this.invoices,
        filteredInvoices: [...this.invoices.data],
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
    invoices: Object
},
methods:{
    formatDateTime(dateTimeString) {
        if (!dateTimeString) return '';
        return format(parseISO(dateTimeString), 'dd MMM yyy, hh:mm a', { locale: es });
    },
    handleRowClick(row) {
        this.$inertia.get(route('invoices.show', row.id));
    },
    handleCommand(command) {
        const commandName = command.split('|')[0];
        const rowId = command.split('|')[1];
        const newStatus = command.split('|')[2];

        if (commandName === 'changeStatus') {
            this.changeStatus(newStatus, rowId);
        } else {
            this.$inertia.get(route('invoices.' + commandName, rowId));
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
    async changeStatus(status, invoiceId) {
        try {
            const response = await axios.post(route('invoices.change-status', invoiceId), { status });
            if (response.status === 200) {
                const invoiceIndex = this.filteredInvoices.findIndex(r => r.id == invoiceId);

                if (invoiceIndex !== -1) {
                    this.filteredInvoices[invoiceIndex].status = status;
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha cambiado el estatus',
                        type: 'success',
                    });
                }
            }
        } catch (error) {
            console.log(error);
        }
    },
    async deleteSelections() {
        try {
            const response = await axios.post(route('invoices.massive-delete', {
                invoices: this.$refs.multipleTableRef.value
            }));

            if (response.status == 200) {
                this.$notify({
                    title: 'Éxito',
                    message: response.data.message,
                    type: 'success'
                });

                // update list of invoices
                let deletedIndexes = [];
                this.filteredInvoices.forEach((invoice, index) => {
                    if (this.$refs.multipleTableRef.value.includes(invoice)) {
                        deletedIndexes.push(index);
                    }
                });

                // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                deletedIndexes.sort((a, b) => b - a);

                // Eliminar cotizaciones por índice
                for (const index of deletedIndexes) {
                    this.filteredInvoices.splice(index, 1);
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
    tableRowClassName({ row, rowIndex }) {
        return 'cursor-pointer text-xs';
    },
    statusIcon(status) {
        if ( status == 'Emitida' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#08688B]"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>'
        } else if ( status == 'Pendiente de pago' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#B8B30E]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
        } else if ( status == 'Parcialmente pagada' ) {
            return '<svg width="24" height="24" viewBox="0 0 24 24" class="text-[#C4620C]" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.30078 19.5173C2.8856 19.5168 3.47013 19.5249 4.05421 19.5415M19.5508 19.5173V20.5223C19.5508 21.2763 18.8248 21.8163 18.0978 21.6183C14.7336 20.7044 11.2996 20.0859 7.83503 19.7682M3.80078 5.2673V6.0173C3.80078 6.21621 3.72176 6.40698 3.58111 6.54763C3.44046 6.68828 3.24969 6.7673 3.05078 6.7673H2.30078M2.30078 6.7673V6.3923C2.30078 5.7713 2.80478 5.2673 3.42578 5.2673C8.74133 5.2673 9.35662 5.2673 14.6722 5.2673M2.30078 6.7673V15.7673M20.3008 5.2673V6.0173C20.3008 6.4313 20.6368 6.7673 21.0508 6.7673H21.8008M20.3008 5.2673H20.6758C21.2968 5.2673 21.8008 5.7713 21.8008 6.3923V16.1423C21.8008 16.7633 21.2968 17.2673 20.6758 17.2673H20.3008M20.3008 5.2673C19.6254 5.2673 19.5241 5.2673 18.8487 5.2673M2.30078 15.7673V16.1423C2.30078 16.4407 2.41931 16.7268 2.63029 16.9378C2.84126 17.1488 3.12741 17.2673 3.42578 17.2673H3.80078M2.30078 15.7673H3.05078C3.24969 15.7673 3.44046 15.8463 3.58111 15.987C3.72176 16.1276 3.80078 16.3184 3.80078 16.5173V17.2673M20.3008 17.2673V16.5173C20.3008 16.3184 20.3798 16.1276 20.5205 15.987C20.6611 15.8463 20.8519 15.7673 21.0508 15.7673H21.8008M20.3008 17.2673C20.3008 17.2673 14.9222 17.2673 9.92946 17.2673M3.80078 17.2673C3.80078 17.2673 4.41681 17.2673 5.69804 17.2673M12.0508 14.2673C12.8464 14.2673 13.6095 13.9512 14.1721 13.3886C14.7347 12.826 15.0508 12.0629 15.0508 11.2673C15.0508 11.0133 15.0186 10.7626 14.9564 10.5207M12.0508 8.2673C11.2551 8.2673 10.4921 8.58337 9.92946 9.14598C9.36685 9.70859 9.05078 10.4716 9.05078 11.2673C9.05078 11.694 9.14168 12.1113 9.31274 12.4933M19.0679 2.41113L4.43777 21.6183M18.0508 11.2673H18.0588V11.2753H18.0508V11.2673ZM6.05078 11.2673H6.05878V11.2753H6.05078V11.2673Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        } else if ( status == 'Pagada' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-green-500"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" /></svg>'
        } else if ( status == 'Cancelada' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
        }
    },
    async handleSearch(search) {
        this.search = search;
        this.loading = true;
        try {
            if (!this.search) {
                this.filteredInvoices = this.invoices.data;
                this.pagination = this.invoices; //cuando se busca con texto vacio s emuestran todas las porduccoines y la paginacion es de todas las producciones
            } else {
                const response = await axios.post(route('invoices.get-matches', { query: this.search }));

                if (response.status === 200) {
                    this.filteredInvoices = response.data.items;
                    console.log(this.filteredInvoices);
                    // this.pagination = response.data.items; //se cambia la paginacion a los encontrados
                }
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    },
}
}
</script>
