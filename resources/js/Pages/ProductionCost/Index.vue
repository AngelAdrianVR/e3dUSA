<template>
    <div>
        <AppLayout title="Costos de producción">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Costos de producción</h2>
                    </div>
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear costos de produccion')"
                        :href="route('production-costs.create')">
                    <SecondaryButton>+ Nuevo</SecondaryButton>
                    </Link>
                </div>
            </template>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="production_costs.data.length" />
                    </div>
                    <!-- buttons -->
                    <div>
                        <el-popconfirm
                            v-if="$page.props.auth.user.permissions.includes('Eliminar costos de produccion')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                            @confirm="deleteSelections">
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
                    <el-table-column prop="id" label="ID" width="80" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="time" label="Tiempo/u" />
                    <el-table-column prop="cost.format" label="Costo" />
                    <el-table-column prop="description" label="Descripción" />
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Editar costos de produccion')"
                                            :command="'edit-' + scope.row.id">
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
            <!-- tabla -->
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        return {
            disableMassiveActions: true,
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
        IndexSearchBar,
    },
    props: {
        production_costs: Array
    },
    methods: {
        handleSearch(search) {
            this.search = search;
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
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
                const response = await axios.post(route('production-costs.massive-delete', {
                    production_costs: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of companies
                    let deletedIndexes = [];
                    this.production_costs.data.forEach((production_cost, index) => {
                        if (this.$refs.multipleTableRef.value.includes(production_cost)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar clientes por índice
                    for (const index of deletedIndexes) {
                        this.production_costs.data.splice(index, 1);
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
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else {
                this.$inertia.get(route('production-costs.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.production_costs.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.production_costs.data.filter(
                    (production_cost) =>
                        production_cost.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        production_cost.description.toLowerCase().includes(this.search.toLowerCase()) ||
                        production_cost.cost['format'].toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
