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
                <p class="text-red-600"><i class="fa-solid fa-circle mr-1"></i>Stok debajo de lo permitido</p>
                <p class="text-amber-600"><i class="fa-solid fa-circle mr-1"></i>Stock sobre lo permitido</p>
            </div>

            <div v-if="$page.props.auth.user.permissions.includes('Ver costo de almacen de materia prima')"
                class="text-center mt-3 hidden md:block">
                <el-tag class="mt-3" style="font-size: 20px;" type="success">Costo total en almacén de materia prima:
                    ${{ totalRawMaterialMoney.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} MXN</el-tag>
            </div>
            <div v-if="$page.props.auth.user.permissions.includes('Ver costo de almacen de materia prima')"
                class="text-center mt-3 md:hidden block">
                <el-tag class="mt-3" style="font-size: 17px;" type="success">Total Almacén materia prima:
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
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Generar reportes materia prima')"
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
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="storageable.name" label="Nombre" />
                    <el-table-column prop="storageable.part_number" label="N° parte" />
                    <el-table-column prop="location" label="Ubicación" />
                    <el-table-column prop="storageable.min_quantity" label="Min. Stock" />
                    <el-table-column prop="storageable.max_quantity" label="Max. Stock" />
                    <el-table-column prop="quantity" label="Stock" />
                    <el-table-column align="right" fixed="right" width="190">
                        <template #header>
                            <div class="flex space-x-2">
                                <TextInput v-model="inputSearch" @keyup.enter="handleSearch" type="search" class="w-full text-gray-600"
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
                                        <el-dropdown-item :command="'show-' + scope.row.id"><i class="fa-solid fa-eye"></i>
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
    },
    props: {
        raw_materials: Array,
        totalRawMaterialMoney: Number
    },
    methods: {
        handleSearch() {
            this.search = this.inputSearch;
        },
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
                        raw_material.storageable.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        raw_material.storageable.part_number.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
    },
};
</script>
