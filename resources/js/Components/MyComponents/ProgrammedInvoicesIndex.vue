<template>
    <div v-if="loading"
        class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
    </div>
    <div v-if="loading"
        class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
        <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
    </div>
    <main>

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
                <el-table :data="filteredItems" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="id" label="ID" width="70" sortable>
                        <template #default="scope">
                            <p>P-{{ scope.row.id }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="OV" width="80" :sortable="customSortBySaleId">
                        <template #default="scope">
                            <p @click.stop="$inertia.visit(route('sales.show', scope.row.sale_id))" class="text-blue-500 underline">{{ scope.row.sale_id }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="invoice_amount" label="Monto total de OV" width="150">
                        <template #default="scope">
                            <p>${{ scope.row.total_amount_sale?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="programed_by" label="Programado por" width="140" />
                    <el-table-column prop="reminder_date" label="Fecha programada" sortable>
                        <template #default="scope">
                            <div class="flex items-center space-x-2">
                                <el-tooltip v-if="checkScheduleDay(scope.row.reminder_date)" content="Programado para hoy" placement="top">
                                    <svg width="10" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.00835 7.56628C0.526638 7.44575 0.288067 7.47782 0.212745 7.80737L0.0198702 8.6512C-0.0683525 9.03705 0.140878 9.2066 0.574384 9.32626L8.53045 11.5202C8.89209 11.6199 9.3019 11.5839 9.37428 11.3273L9.63948 10.3871C9.76003 9.95968 9.49483 9.75715 9.13319 9.66379L1.22534 7.6223L1.00835 7.56628Z" fill="#F2A12F"/>
                                        <path d="M2.09315 3.94963C1.61632 5.30633 1.44933 6.12655 1.27344 7.66246L8.9643 9.61532C9.40276 8.11965 9.59296 7.28665 9.88046 5.80605C10.2409 3.94963 9.8418 2.85271 7.85528 1.73158C8.57856 -0.317471 6.38461 -0.703314 6.16762 1.41816C4.31987 1.28001 2.74563 2.09318 2.09315 3.94963Z" fill="#F3D21B"/>
                                        <path d="M5.61311 11.1583L3.34684 10.5315V11.2789C3.34684 12.3879 4.6339 12.7495 5.22736 11.7852L5.61311 11.1583Z" fill="#F3D21B"/>
                                    </svg>
                                </el-tooltip>
                                <p>{{ formatDate(scope.row.reminder_date) }}</p>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="reminder_time" label="Hora de recordatorio" />
                    <el-table-column prop="company_branch.name" label="Cliente" />
                    <el-table-column prop="number_of_invoice" label="No° de factura">
                        <template #default="scope">
                            <p>{{ scope.row.number_of_invoice }} de {{ scope.row.invoice_quantity }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="invoice_amount" label="Monto programado">
                        <template #default="scope">
                            <p>${{ scope.row.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
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
                                        <el-dropdown-item :command="'create|' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            Crear factura
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'reprogram|' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            Reprogramar factura
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

    <!-- -------------- reprogramación de factura Modal starts----------------------- -->
    <Modal :show="showReprogramModal" @close="showReprogramModal = false">
        <div class="p-5 relative">
            <h2 class="font-bold mb-5">Reprogramar factura {{ 'P-' + programmedInvoiceIdSelected }}</h2>
            <i @click="showReprogramModal = false"
            class="fa-solid fa-xmark cursor-pointer size-5 rounded-full border border-black flex items-center justify-center absolute right-3 top-3"></i>
            <form @submit.prevent="reprogramInvoice" class="md:grid grid-cols-2 gap-4">

                <div>
                    <InputLabel value="Fecha de recordatorio" />
                    <el-date-picker
                        v-model="form.reminder_date"
                        type="date"
                        placeholder="Selecciona la fecha de recordatorio"
                        :disabled-date="disabledPastDates"
                        class="!w-full"
                    />
                </div>
                <div>
                    <InputLabel value="Hora" />
                    <el-time-select
                        v-model="form.reminder_time"
                        start="00:00"
                        step="00:30"
                        end="23:59"
                        placeholder="Selecciona la hora de recordatorio"
                        format="hh:mm A"
                        class="!w-full"
                    />
                </div>
                <div class="mt-5 mx-3 md:text-right col-span-full">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Reprogramar factura
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    const form = useForm({
        reminder_date: null, // fecha de programación
        reminder_time: null, // hora de programación
    });

    return {
        form,
        programmedInvoiceIdSelected: null,
        disableMassiveActions: true,
        showReprogramModal: false,
        inputSearch: '',
        search: '',
        loading: false,
        programmedInvoices: [],
        pagination: null,
        filteredItems: [],
    }
},
components:{
    PaginationWithNoMeta,
    NotificationCenter,
    SecondaryButton,
    IndexSearchBar,
    PrimaryButton,
    InputLabel,
    InputError,
    Modal,
    Link
},
props:{
},
methods:{
    reprogramInvoice() {
        this.form.put(route('programmed-invoices.update', this.programmedInvoiceIdSelected), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "",
                    type: "success",
            });
            this.showReprogramModal = false;
            const selectedItem = this.filteredItems.find(pi => pi.id == this.programmedInvoiceIdSelected);
            selectedItem.reminder_date = this.form.reminder_date?.toISOString?.() || this.form.reminder_date;
            selectedItem.reminder_time = this.form.reminder_time;
            this.form.reset();
            }
        })
    },
    disabledPastDates(date) {
        const today = new Date();
        today.setHours(0, 0, 0, 0); // quitar hora para comparar solo fechas
        return date < today;
    },
    formatDateTime(dateTimeString) {
        if (!dateTimeString) return '';
        return format(parseISO(dateTimeString), 'dd MMM yyy, hh:mm a', { locale: es });
    },
    formatDate(dateTimeString) {
        if (!dateTimeString) return '';
        return format(parseISO(dateTimeString), 'dd MMM yyy', { locale: es });
    },
    handleRowClick(row) {
        return
    },
    handleCommand(command) {
        const commandName = command.split('|')[0];
        const rowId = command.split('|')[1];

        if (commandName === 'reprogram') {
            this.programmedInvoiceIdSelected = rowId;
            this.form.reminder_date = this.filteredItems.find(pi => pi.id == rowId)?.reminder_date;
            this.form.reminder_time = this.filteredItems.find(pi => pi.id == rowId)?.reminder_time;
            this.showReprogramModal = true;
        } else if ( commandName === 'create' ) {
            const selectedItem = this.filteredItems.find(pi => pi.id == rowId);
            this.$inertia.visit(route('invoices.create', { 
                sale_id: selectedItem.sale_id, 
                invoice_amount: selectedItem.amount,
                invoice_quantity: selectedItem.invoice_quantity,
                total_amount_sale: selectedItem.total_amount_sale,
                } ));
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
    checkScheduleDay(date) {
        if (!date) return false;

        const today = new Date();
        const targetDate = new Date(date);

        // Comparar solo el año, mes y día
        return (
        today.getFullYear() === targetDate.getFullYear() &&
        today.getMonth() === targetDate.getMonth() &&
        today.getDate() === targetDate.getDate()
        );
    },
    tableRowClassName({ row, rowIndex }) {
        return 'cursor-default text-xs';
    },
    handleSelectionChange(val) {
        this.$refs.multipleTableRef.value = val;

        if (!this.$refs.multipleTableRef.value.length) {
            this.disableMassiveActions = true;
        } else {
            this.disableMassiveActions = false;
        }
    },
    customSortBySaleId(a, b) {
        return String(a.sale_id).localeCompare(String(b.sale_id));
    },
    async deleteSelections() {
        try {
            const response = await axios.post(route('programmed-invoices.massive-delete', {
                programmedInvoices: this.$refs.multipleTableRef.value
            }));

            if (response.status == 200) {
                this.$notify({
                    title: 'Éxito',
                    message: response.data.message,
                    type: 'success'
                });

                // update list of invoices
                let deletedIndexes = [];
                this.filteredItems.forEach((programmedInvoice, index) => {
                    if (this.$refs.multipleTableRef.value.includes(programmedInvoice)) {
                        deletedIndexes.push(index);
                    }
                });

                // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                deletedIndexes.sort((a, b) => b - a);

                // Eliminar cotizaciones por índice
                for (const index of deletedIndexes) {
                    this.filteredItems.splice(index, 1);
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
    async handleSearch(search) {
        this.search = search;
        this.loading = true;
        try {
            if (!this.search) {
                this.filteredItems = this.programmedInvoices.data;
                this.pagination = this.programmedInvoices;
            } else {
                const response = await axios.get(route('programmed-invoices.get-matches', { query: this.search }));

                if (response.status === 200) {
                    this.filteredItems = response.data.items;
                }
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    },
    async fetchProgrammedInvoices() {
        this.loading = true;
        try {
            const response = await axios.get(route('programmed-invoices.get-all'));

            if ( response.status === 200 ) {
                this.programmedInvoices = response.data.programmed_invoices;
                this.pagination = this.programmedInvoices;
                this.filteredItems = this.programmedInvoices.data;
            }

        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    }
},
mounted() {
    this.fetchProgrammedInvoices();
}
}
</script>
