<template>
    <div class="dark:text-white">
        <AppLayout title="Departamento de producción">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Tus órdenes de producción asignadas</h2>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p class="text-amber-500"><i class="fa-solid fa-circle mr-1"></i>Producción sin iniciar</p>
                <p class="text-red-500"><i class="fa-solid fa-circle mr-1"></i>Falta de materia prima</p>
                <p class="text-blue-500"><i class="fa-solid fa-circle mr-1"></i>Producción en proceso</p>
                <p class="text-green-500"><i class="fa-solid fa-circle mr-1"></i>Producción terminada</p>
            </div>

            <!-- Filtro por propietario -->
            <div class="w-1/4 flex flex-col">
                <label class="text-sm ml-2 mb-1">Filtro por propietario</label>
                <el-select @change="fetchItemsFiltered" v-model="filter" class="!w-full"
                    placeholder="Selecciona una opción">
                    <el-option v-for="item in options" :key="item" :label="item" :value="item" />
                </el-select>
            </div>

            <!-- tabla -->
            <div class="relative overflow-hidden min-h-[60vh]">
                <NotificationCenter module="production" />
                <div class="lg:w-5/6 mx-auto mt-6">
                    <div class="flex justify-between">
                        <!-- pagination -->
                        <div>
                            <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                                :total="productions.data.length" />
                        </div>
                    </div>
                    <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                        @selection-change="handleSelectionChange" ref="multipleTableRef"
                        :row-class-name="tableRowClassName">
                        <el-table-column type="selection" width="55" />
                        <el-table-column prop="p_folio" label="Folio" />
                        <el-table-column prop="user.name" label="Vendedor" />
                        <el-table-column prop="company_branch.name" label="Cliente" />
                        <el-table-column prop="created_at" label="Creada el" />
                        <el-table-column prop="status['label']" label="Estatus" />
                        <el-table-column prop="productions.length" label="N° Operadores" />
                        <el-table-column align="right" fixed="right" width="190">
                            <template #header>
                                <div class="flex space-x-2">
                                    <TextInput v-model="inputSearch" type="search" @keyup.enter="handleSearch" class="w-full text-gray-600"
                                        placeholder="Buscar" />
                                    <el-button @click="handleSearch" type="primary" plain class="mb-3"><i
                                            class="fa-solid fa-magnifying-glass"></i></el-button>
                                </div>
                            </template>
                            <template #default="scope">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </span>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'show-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                Ver</el-dropdown-item>
                                            <el-dropdown-item :command="'edit-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
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
            options: ['Mis órdenes', 'Todas las órdenes'], //opciones de filtro
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        Link,
        TextInput,
        NotificationCenter,
    },
    props: {
        productions: Array
    },
    methods: {
        handleSearch() {
            this.search = this.inputSearch;
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
                    productions: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.productions.data.forEach((production, index) => {
                        if (this.$refs.multipleTableRef.value.includes(production)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.productions.data.splice(index, 1);
                    }

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.message,
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
            if (row.status['label'] == 'Producción sin iniciar') {
                return 'cursor-pointer text-amber-500';
            } else if (row.status['label'] == 'Producción en proceso') {
                return 'cursor-pointer text-blue-500';
            } else if (row.status['label'] == 'Producción terminada') {
                return 'cursor-pointer text-green-500';
            } else if (row.status['label'] == 'Falta de materia prima') {
                return 'cursor-pointer text-red-500';
            }
            return 'cursor-pointer';
        },
        handleRowClick(row) {
            this.$inertia.get(route('productions.show', row));
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
                return this.productions.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.productions.data.filter(
                    (production) =>
                        production.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.status.label.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.p_folio.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.company_branch.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.p_folio.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
