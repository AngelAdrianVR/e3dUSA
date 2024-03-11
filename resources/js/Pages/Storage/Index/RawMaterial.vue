<template>
    <div>
        <AppLayout title="Almacén materia prima">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Almacén de materia prima</h2>
                    </div>
                    <div>
                        <Link v-if="$page.props.auth.user.permissions.includes('Crear materia prima')"
                            :href="route('raw-materials.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p class="text-red-600"><i class="fa-solid fa-arrow-down mr-1"></i>Stok debajo de lo permitido</p>
                <p class="text-yellow-500"><i class="fa-solid fa-arrow-up mr-1"></i>Stock sobre lo permitido</p>
                <p class="text-green-600"><i class="fa-regular fa-circle-check mr-1"></i>Stock efectivo</p>
            </div>

            <div v-if="$page.props.auth.user.permissions.includes('Ver costo de almacen de materia prima')"
                class="text-center mt-3 hidden md:block">
                <el-tag size="small" class="mt-3" style="font-size: 16px;" type="success">Costo total en almacén de
                    materia prima:
                    ${{ totalRawMaterialMoney.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} MXN</el-tag>
            </div>
            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between flex-wrap">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="raw_materials.data.length" />
                    </div>
                    <!-- buttons -->
                    <div class="mb-3">
                        <el-popconfirm
                            v-if="$page.props.auth.user.permissions.includes('Generar reportes materia prima')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                            @confirm="generatePdf">
                            <template #reference>
                                <el-button type="primary" plain>Reporte PDF</el-button>
                            </template>
                        </el-popconfirm>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar materia prima')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain :disabled="disableMassiveActions">Eliminar</el-button>
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
                    <el-table-column prop="storageable.name" label="Nombre" />
                    <el-table-column prop="storageable.part_number" label="N° parte" />
                    <el-table-column prop="location" label="Ubicación" />
                    <el-table-column prop="storageable.min_quantity" label="Min. Stock">
                        <template #default="scope">
                            <span>{{ scope.row.storageable.min_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="storageable.max_quantity" label="Max. Stock">
                        <template #default="scope">
                            <span>{{ scope.row.storageable.max_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
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
                                            v-if="$page.props.auth.user.permissions.includes('Editar materia prima')"
                                            @click="$inertia.get(route('raw-materials.edit', scope.row.storageable))"
                                            :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')"
                                            :command="'turn-' + scope.row.id"><i
                                                class="fa-solid fa-arrows-turn-to-dots"></i>
                                            Convertir a producto de catalogo
                                        </el-dropdown-item>
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
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import TextInput from '@/Components/TextInput.vue';
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
        raw_materials: Array,
        totalRawMaterialMoney: Number
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
            const rowId = parseInt(command.split('-')[1]);

            if (commandName == 'turn') {
                this.turnIntoCatalogProduct(rowId);
            } else {
                this.$inertia.get(route('storages.' + commandName, rowId));
            }
        },
        async turnIntoCatalogProduct(rawMaterialId) {
            try {
                const response = await axios.post(route('raw-materials.turn-into-catalog-product', {
                    raw_material_id: rawMaterialId
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: response.data.title,
                        message: response.data.message,
                        type: response.data.type
                    });

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
        generatePdf() {
            window.open(route('pdf.raw-material-actual-stock'), '_blank');
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('raw-materials.massive-delete', {
                    raw_materials: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.raw_materials.data.forEach((raw_material, index) => {
                        if (this.$refs.multipleTableRef.value.includes(raw_material)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.raw_materials.data.splice(index, 1);
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
            this.$inertia.get(route('storages.show', row));
        },
        edit(index, raw_material) {
            this.$inertia.get(route('raw-materials.edit', raw_material.storageable));
        }
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.raw_materials.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.raw_materials.data.filter(
                    (raw_material) =>
                        !this.search ||
                        raw_material.storageable?.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        raw_material.location.toLowerCase().includes(this.search.toLowerCase()) ||
                        raw_material.quantity.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                        raw_material.storageable?.part_number.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
    },
};
</script>
