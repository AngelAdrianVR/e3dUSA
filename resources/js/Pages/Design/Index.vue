<template>
    <div class="dark:text-white">
        <AppLayout title="Departamento de diseño">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Órdenes de diseño</h2>
                    </div>
                    <div>
                        <Link :href="route('designs.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p><i class="fa-solid fa-circle mr-1 text-amber-500"></i>Esperando autorización</p>
                <p><i class="fa-solid fa-circle mr-1 text-amber-700"></i>Autorizado. Sin iniciar</p>
                <p><i class="fa-solid fa-circle mr-1 text-secondary"></i>En proceso</p>
                <p><i class="fa-solid fa-circle mr-1 text-green-500"></i>Terminado</p>
            </div>

            <!-- Filtro -->
            <!-- <div class="w-44 lg:ml-32 ml-4 mt-2">
                <el-select @change="fetchItemsFiltered" v-model="filter" class="mt-2" clearable
                    filterable placeholder="Selecciona una opción">
                    <el-option v-for="item in options" :key="item" :label="item"
                        :value="item" />
                </el-select>
            </div> -->

            <!-- tabla -->
            <div class="relative overflow-hidden min-h-[60vh]">
                <NotificationCenter module="design" />
                <div class="lg:w-5/6 mx-auto mt-6 relative">
                    <div class="flex justify-between">
                        <!-- pagination -->
                        <div>
                            <el-pagination v-if="!search" @current-change="handlePagination" layout="prev, pager, next"
                                :total="designs.length" />
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
                        <el-table-column prop="id" label="ID" width="80" />
                        <el-table-column prop="user.name" label="Solicitante" />
                        <!-- <el-table-column prop="name" label="Diseño" /> -->
                        <el-table-column label="Diseño" width="210">
                            <template v-slot="scope">
                                <el-tooltip v-if="scope.row.has_priority" content="Prioridad alta" placement="top">
                                    <span><i class="fa-solid fa-triangle-exclamation text-primary mr-1"></i>{{
                                        scope.row.name }}</span>
                                </el-tooltip>
                                <span v-else>{{ scope.row.name }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column prop="design_type.name" label="Clasificación" />
                        <el-table-column prop="designer.name" label="Diseñador(a)" />
                        <el-table-column prop="created_at" label="Solicitado el" />
                        <el-table-column prop="status[label]" label="Estatus">
                            <template #default="scope">
                                <div class="flex">
                                    <p class="mr-2 mt-px text-[6px]" :class="getStatusColor(scope.row)">
                                        <i class="fa-solid fa-circle"></i>
                                    </p>
                                    <span>{{ scope.row.status['label'] }}</span>
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
                                            <el-dropdown-item :command="'show-' + scope.row.id"><i
                                                    class="fa-solid fa-eye"></i>
                                                Ver</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="(scope.row.status['label'] != 'Terminado' && scope.row.user.id == $page.props.auth.user.id) ||
                                                    ($page.props.auth.user.permissions.includes('Ordenes de diseño todas') && scope.row.status['label'] != 'Terminado')"
                                                :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                                Editar</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="$page.props.auth.user.permissions.includes('Crear formatos de autorizacion') && scope.row.status['label'] == 'Terminado'"
                                                :command="'af-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                                                </svg>
                                                Crear formato de autorización
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import axios from 'axios';

export default {
    data() {
        return {
            filter: 'Mis órdenes', //filtro
            options: ['Mis órdenes', 'Todas las órdenes'], //filtro
            // localDesigns: this.designs.data,
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
        NotificationCenter,
        SecondaryButton,
        IndexSearchBar,
        AppLayout,
        TextInput,
        Link
    },
    props: {
        designs: Object
    },
    methods: {
        getStatusColor(row) {
            if (row.status['label'] == 'Esperando Autorización') {
                return 'cursor-pointer text-amber-500';
            } else if (row.status['label'] == 'Autorizado. Sin iniciar') {
                return 'cursor-pointer text-amber-700';
            } else if (row.status['label'] == 'En proceso') {
                return 'cursor-pointer text-[#0355B5]';
            } else if (row.status['label'] == 'Terminado') {
                return 'cursor-pointer text-green-500';
            }
        },
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
                const response = await axios.post(route('designs.massive-delete', {
                    designs: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.designs.forEach((design, index) => {
                        if (this.$refs.multipleTableRef.value.includes(design)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.designs.splice(index, 1);
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
        handleRowClick(row) {
            this.$inertia.get(route('designs.show', row.id));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('designs.' + commandName, rowId));
            }
        },
        //obtiene las ventas filtradas (mias y todas)
        async fetchItemsFiltered() {
            this.loading = true;
            try {
                const response = await axios.get(route('designs.fetch-filtered', this.filter));
                if (response.status === 200) {
                    this.designs = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        }
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.designs.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.designs.filter(
                    (design) =>
                        design.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                        design.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        design.status.label.toLowerCase().includes(this.search.toLowerCase()) ||
                        design.designer.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        design.user.name.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
