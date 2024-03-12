<template>
    <div>
        <AppLayout title="Departamento de producción">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Todas las órdenes de producción</h2>
                    </div>
                    <div>
                        <Link :href="route('productions.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p class="text-amber-500"><i class="fa-solid fa-circle mr-1"></i>Producción sin iniciar</p>
                <p class="text-red-500"><i class="fa-solid fa-circle mr-1"></i>Falta de materia prima</p>
                <p class="text-blue-500"><i class="fa-solid fa-circle mr-1"></i>Producción en proceso</p>
                <p class="text-green-500"><i class="fa-solid fa-circle mr-1"></i>Producción terminada</p>
            </div>
            <!-- tabla -->
            <div class="relative overflow-hidden min-h-[60vh]">
                <NotificationCenter module="production" />
                <div class="lg:w-[90%] mx-auto mt-6">
                    <div class="flex justify-between">
                        <!-- pagination -->
                        <div>
                            <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                                :total="productions.length" />
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
                    <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
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
                        <el-table-column prop="user.name" label="Vendedor" />
                        <el-table-column prop="company_branch.name" label="Cliente" />
                        <el-table-column prop="created_at" label="Creada el" />
                        <el-table-column label="Estatus">
                            <template #default="scope">
                                <div class="flex">
                                    <p class="mr-2" :class="getStatusColor(scope.row)">
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                    </p>
                                    <p class="flex">{{ scope.row.status['label'] }}</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="operators" label="Operadores" width="210" />
                        <el-table-column prop="production.percentage" label="% Avance" width="115" />
                        <el-table-column prop="promise_date" label="Fecha entrega" />
                        <el-table-column align="right">
                            <template #default="scope">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <button @click.stop
                                        class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'show-' + scope.row.id"><i
                                                    class="fa-solid fa-eye"></i>
                                                Ver</el-dropdown-item>
                                            <el-dropdown-item :command="'edit-' + scope.row.id"><i
                                                    class="fa-solid fa-pen"></i>
                                                Editar</el-dropdown-item>
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';


export default {
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            // pagination
            itemsPerPage: 10,
            start: 0,
            end: 10,
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        Link,
        TextInput,
        NotificationCenter,
        IndexSearchBar,
    },
    props: {
        productions: Array
    },
    methods: {
        handleSearch(search) {
            this.search = search;
        },
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('productions.massive-delete', {
                    sales: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of productions
                    let deletedIndexes = [];
                    this.productions.forEach((production, index) => {
                        if (this.$refs.multipleTableRef.value.includes(production)) {
                            deletedIndexes.push(index);
                        }
                    });

                    deletedIndexes.sort((a, b) => b - a);

                    for (const index of deletedIndexes) {
                        this.productions.splice(index, 1);
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
        getStatusColor(row) {
            if (row.status['label'] == 'Esperando autorización') {
                return 'text-red-500';
            } else if (row.status['label'] == 'Producción sin iniciar') {
                return 'text-amber-500';
            } else if (row.status['label'] == 'Producción en proceso') {
                return 'text-blue-500';
            } else if (row.status['label'] == 'Producción terminada') {
                return 'text-green-500';
            } else if (row.status['label'] == 'Autorizado sin orden de producción') {
                return 'text-gray-500';
            }
        },
        handleRowClick(row) {
            this.$inertia.get(route('productions.show', row.id));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('productions.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.productions.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.productions.filter(
                    (production) =>
                        production.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.status.label.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.company_branch.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.operators.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.folio.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
