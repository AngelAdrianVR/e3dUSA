<template>
    <div>
        <div v-if="loading" class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
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
                    </Link>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p class="text-red-500"><i class="fa-solid fa-circle mr-1"></i>Esperando Autorización</p>
                <p class="text-gray-500"><i class="fa-solid fa-circle mr-1"></i>Autorizado. Sin orden de producción</p>
                <p class="text-amber-500"><i class="fa-solid fa-circle mr-1"></i>Producción sin iniciar</p>
                <p class="text-blue-500"><i class="fa-solid fa-circle mr-1"></i>Producción en proceso</p>
                <p class="text-green-500"><i class="fa-solid fa-circle mr-1"></i>Producción terminada</p>
            </div>
            <div class="relative overflow-hidden">
                <NotificationCenter module="sales" />
                <!-- tabla -->
                <div class="w-11/12 lg:w-5/6 mx-auto mt-6">
                    <div class="lg:flex justify-between items-center">
                        <!-- pagination -->
                        <div v-if="!search" class="overflow-auto mb-2">
                            <!-- <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                                :total="sales_count" /> -->
                            <Pagination :pagination="sales" class="py-2" />
                        </div>
                        <!-- buttons -->
                        <div>
                            <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar ordenes de venta')"
                                confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                                @confirm="deleteSelections">
                                <template #reference>
                                    <el-button type="danger" plain class="mb-3"
                                        :disabled="disableMassiveActions">Eliminar</el-button>
                                </template>
                            </el-popconfirm>
                        </div>
                        <div class="flex space-x-2 items-center mb-3">
                            <TextInput v-model="search" type="search" @keyup.enter="fetchMatches"
                                class="w-full text-gray-600 h-8" placeholder="Buscar" />
                            <PrimaryButton @click="fetchMatches" type="primary">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </PrimaryButton>
                        </div>
                    </div>
                    <el-table :data="sales.data" @row-click="handleRowClick" max-height="670" style="width: 100%"
                        @selection-change="handleSelectionChange" ref="multipleTableRef"
                        :row-class-name="tableRowClassName">
                        <el-table-column type="selection" width="45" />
                        <el-table-column prop="folio" label="folio" width="100" />
                        <el-table-column prop="user.name" label="Creado por" />
                        <el-table-column prop="created_at" label="Creado el" />
                        <el-table-column prop="company_branch.name" label="Cliente" />
                        <el-table-column prop="authorized_user_name" label="Autorizado por" />
                        <el-table-column prop="status['label']" label="Estatus" />
                        <el-table-column prop="promise_date" label="Fecha de entrega" />
                        <el-table-column align="right" fixed="right" width="40">
                            <!-- <template #header>
                                <div class="flex space-x-2">
                                    <TextInput v-model="search" type="search" @keyup.enter="fetchMatches"
                                        class="w-full text-gray-600" placeholder="Buscar" />
                                    <el-button @click="fetchMatches" type="primary" plain class="mb-3"><i
                                            class="fa-solid fa-magnifying-glass"></i></el-button>
                                </div>
                            </template> -->
                            <template #default="scope">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </span>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'show-' + scope.row.id"><i
                                                    class="fa-solid fa-eye"></i>
                                                Ver</el-dropdown-item>
                                            <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
                                                scope.row.user.id == $page.props.auth.user.id"
                                                :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                                Editar</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')"
                                                :command="'clone-' + scope.row.id"><i class="fa-solid fa-clone"></i>
                                                Clonar</el-dropdown-item>
                                            <el-dropdown-item :command="'print-' + scope.row.id">
                                                <i class="fa-solid fa-print"></i>
                                                Imprimir</el-dropdown-item>
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
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link } from "@inertiajs/vue3";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import Pagination from "@/Components/MyComponents/Pagination.vue";
import axios from 'axios';


export default {
    data() {
        return {
            disableMassiveActions: true,
            // inputSearch: '',
            search: '',
            loading: false,
            // pagination
            // itemsPerPage: 10,
            // start: 0,
            // end: 10,
        };

    },
    components: {
        AppLayout,
        SecondaryButton,
        PrimaryButton,
        Link,
        TextInput,
        NotificationCenter,
        Pagination,
    },
    props: {
        sales: Object,
        company_branches: Array
    },

    methods: {
        async fetchMatches() {
            this.loading = true;
            try {
                if (!this.search) {
                    this.$inertia.get(route('sales.index'));
                } else {
                    const response = await axios.get(route('sales.get-matches', { query: this.search }));

                    if (response.status === 200) {
                        this.sales.data = response.data.items;
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
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
                    this.sales.data.forEach((sale, index) => {
                        if (this.$refs.multipleTableRef.value.includes(sale)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar OV por índice
                    for (const index of deletedIndexes) {
                        this.sales.data.splice(index, 1);
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
            if (row.status['label'] == 'Esperando autorización') {
                return 'cursor-pointer text-red-500';
            } else if (row.status['label'] == 'Producción sin iniciar') {
                return 'cursor-pointer text-amber-500';
            } else if (row.status['label'] == 'Producción en proceso') {
                return 'cursor-pointer text-blue-500';
            } else if (row.status['label'] == 'Producción terminada') {
                return 'cursor-pointer text-green-500';
            } else if (row.status['label'] == 'Autorizado sin orden de producción') {
                return 'cursor-pointer text-gray-500';
            }
        },
        handleRowClick(row) {
            this.$inertia.get(route('sales.show', row));
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
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('sales.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.sales.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.sales.data.filter(
                    (sale) =>
                        sale.folio.toLowerCase().includes(this.search.toLowerCase()) ||
                        sale.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        sale.status.label.toLowerCase().includes(this.search.toLowerCase()) ||
                        sale.folio.toLowerCase().includes(this.search.toLowerCase()) ||
                        sale.company_branch.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
    },
};
</script>
