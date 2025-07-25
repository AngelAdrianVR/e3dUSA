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
                <el-table :data="filteredItems" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="id" label="ID" width="80" sortable>
                        <template #default="scope">
                            <p>P-{{ scope.row.id }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="OV" width="120" sortable>
                        <template #default="scope">
                            <p @click.stop="$inertia.visit(route('sales.show', scope.row.sale_id))" class="text-blue-500 underline">{{ scope.row.sale_id }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="programed_by" label="Programado por" />
                    <el-table-column prop="reminder_date" label="Fecha programada">
                        <template #default="scope">
                            <p>{{ formatDate(scope.row.reminder_date) }}</p>
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
                                            Crear factura
                                        </el-dropdown-item>
                                        <el-dropdown-item :command="'reprogram|' + scope.row.id">
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
        return
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
        return 'cursor-default text-xs';
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
                    this.filteredItems = response.data.programmed_invoices;
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
