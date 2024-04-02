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

            <!-- Filtro -->
            <div class="w-44 lg:ml-32 ml-4 mt-2">
                <el-select @change="fetchItemsFiltered" v-model="filter" class="mt-2"
                    placeholder="Selecciona una opción">
                    <el-option v-for="item in options" :key="item" :label="item" :value="item" />
                </el-select>
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

                    <el-table :data="sales.data" @row-click="handleRowClick" max-height="670" style="width: 100%"
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
                        <el-table-column label="Utilidad" width="140">
                            <template #default="scope">
                                <div class="flex items-center space-x-2">
                                    <div class="flex flex-col space-y-1 items-center">
                                        <i class="fa-solid fa-flag-checkered"></i>
                                        <StarRating :rating="scope.row.profit / 100" />
                                    </div>
                                    <p class="flex-0 w-[80%]">{{ scope.row.profit }} %</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="user.name" label="Creado por" />
                        <el-table-column prop="created_at" label="Creado el" />
                        <el-table-column prop="company_branch.name" label="Cliente" />
                        <el-table-column prop="authorized_user_name" label="Autorizado por" />
                        <el-table-column label="Estatus">
                            <template #default="scope">
                                <div class="flex">
                                    <p class="mr-2" :class="getStatusColor(scope.row)">
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                    </p>
                                    <p class="flex-0 w-[80%]">{{ scope.row.status['label'] }}</p>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="promise_date" label="Fecha de entrega" width="180" />
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
                                            <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
            scope.row.user.id == $page.props.auth.user.id" :command="'edit-' + scope.row.id"><i
                                                    class="fa-solid fa-pen"></i>
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
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import StarRating from "@/Components/MyComponents/StarRating.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import Pagination from "@/Components/MyComponents/Pagination.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        return {
            disableMassiveActions: true,
            filter: 'Mis órdenes', //filtro
            options: ['Mis órdenes', 'Todas las órdenes'], //filtro
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
        Pagination,
        AppLayout,
        TextInput,
        Link,
        StarRating,
    },
    props: {
        sales: Object,
        company_branches: Array
    },
    methods: {
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
                        this.sales.data = response.data.items;
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
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
        fetchItemsFiltered() {
            this.$inertia.get(route('sales.fetch-filtered', this.filter));
        }
    },
};
</script>
