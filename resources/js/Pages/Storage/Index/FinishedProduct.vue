<template>
    <div>
        <AppLayout title="Almacén Producto terminado">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Almacén de producto terminado</h2>
                    </div>
                    <div>
                        <Link v-if="$page.props.auth.user.permissions.includes('Crear producto terminado')"
                            :href="route('storages.finished-products.create')">
                        <SecondaryButton>Guardar producto terminado</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2 dark:text-white">
                <p><i class="fa-solid fa-arrow-down mr-1 text-red-600"></i>Stok debajo de lo permitido</p>
                <p><i class="fa-solid fa-arrow-up mr-1 text-yellow-500"></i>Stock sobre lo permitido</p>
                <p><i class="fa-regular fa-circle-check mr-1 text-green-600"></i>Stock efectivo</p>
            </div>

            <div v-if="$page.props.auth.user.permissions.includes('Ver costo de almacen de producto terminado')"
                class="text-center mt-3">
                <el-tag class="mt-3" size="small" style="font-size: 16px;" type="success">Costo total en almacén de Producto
                    terminado:
                    ${{ totalFinishedProductMoney.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} MXN</el-tag>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="finished_products.length" />
                    </div>
                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar producto terminado')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                            title="Se regresarán los componentes a almacén de materia prima. ¿Continuar?"
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
                    <el-table-column prop="storageable.name" label="Nombre" width="250" />
                    <el-table-column prop="storageable.part_number" label="N° parte" />
                    <el-table-column prop="location" label="Ubicación" />
                    <el-table-column prop="storageable.min_quantity" label="Min. Stock">
                        <template #default="scope">
                            <span>{{ scope.row.storageable.min_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="storageable.max_quantity" label="Max. Stock">
                        <template #default="scope">
                            <span>{{ scope.row.storageable.max_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                                }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="quantity" label="Stock">
                        <template #default="scope">
                            <div class="flex">
                                <p class="mr-2 mt-px text-[10px]">
                                    <i :class="getStatusColor(scope.row)"></i>
                                </p>
                                <span>{{ scope.row.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column align="right" fixed="right" width="190">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Editar insumos')"
                                            @click="$inertia.get(route('storages.finished-products.edit', scope.row.id))">
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
        IndexSearchBar,
    },
    props: {
        finished_products: Array,
        totalFinishedProductMoney: Number
    },
    methods: {
        handleSearch(search) {
            this.search = search;
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        getStatusColor(row) {
            if (row.quantity <= row.storageable.min_quantity) {
                return 'text-red-600 fa-solid fa-arrow-down';
            }
            else if (row.quantity >= row.storageable.max_quantity) {
                return 'text-yellow-500 fa-solid fa-arrow-up';
            } else {
                return 'text-green-600 fa-regular fa-circle-check';
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
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('storages.' + commandName, rowId));
            }
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('storages.massive-delete', {
                    finished_products: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.finished_products.forEach((finished_product, index) => {
                        if (this.$refs.multipleTableRef.value.includes(finished_product)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.finished_products.splice(index, 1);
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
        handleRowClick(row) {
            console.log(row);
            this.$inertia.get(route('storages.show', row));
        },

        edit(index, finished_product) {
            console.log(finished_product);
            this.$inertia.get(route('storages.finished-products.edit', finished_product.storageable));
        },


    },

    computed: {
        filteredTableData() {
            return this.finished_products.filter(
                (finished_product) =>
                    !this.search ||
                    finished_product.storageable.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    finished_product.location.toLowerCase().includes(this.search.toLowerCase()) ||
                    finished_product.quantity.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                    finished_product.storageable.part_number.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
