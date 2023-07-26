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
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="text-center mt-3">
                    <el-tag class="mt-3" style="font-size: 20px;" type="success">Costo total en almacén de Producto terminado: ${{totalFinishedProductMoney.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}} MXN</el-tag>
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
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="450" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="storageable.name" label="Nombre" width="250" />
                    <el-table-column prop="storageable.part_number" label="N° parte" />
                    <el-table-column prop="location" label="Ubicación" />
                    <el-table-column prop="storageable.min_quantity" label="Min. Stock" />
                    <el-table-column prop="storageable.max_quantity" label="Max. Stock" />
                    <el-table-column prop="quantity" label="Stock" />
                    <el-table-column align="right" fixed="right" width="120">
                        <template #header>
                            <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
                        </template>
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </span>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id"><i class="fa-solid fa-eye"></i>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Editar insumos')"
                                             @click="$inertia.get(route('storages.finished-products.edit', scope.row.id))">
                                             <i class="fa-solid fa-pen"></i>
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
import { Link } from "@inertiajs/vue3";
import axios from 'axios';


export default {
    data() {
        return {
            disableMassiveActions: true,
            search: '',
            // pagination
            itemsPerPage: 25,
            start: 0,
            end: 10,
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        Link,
        TextInput,
    },
    props: {
        finished_products: Array,
        totalFinishedProductMoney: Number
    },
    methods: {
        tableRowClassName({ row, rowIndex }) {
            if (row.quantity <= row.storageable.min_quantity) {
                return 'text-red-600 cursor-pointer';
            }
            else if (row.quantity >= row.storageable.max_quantity) {
                return 'text-amber-600 cursor-pointer';
            } else {
                return 'cursor-pointer';
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
                    finished_product.storageable.part_number.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
