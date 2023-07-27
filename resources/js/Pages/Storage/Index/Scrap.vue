<template>
    <div>
        <AppLayout title="Almacén de scrap">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Almacén de scrap</h2>
                    </div>
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear scrap')"
                        :href="route('storages.scraps.create')">
                    <SecondaryButton>+ Nuevo</SecondaryButton>
                    </Link>
                </div>
            </template>

                <div v-if="$page.props.auth.user.permissions.includes('Ver costo de almacen de scrap')" class="text-center mt-3">
                    <el-tag class="mt-3" style="font-size: 20px;" type="danger">Scrap total: ${{totalScrapMoney.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}} MXN</el-tag>
                </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="scraps.length" />
                    </div>
                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar scrap')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                </div>
                <el-table :data="filteredTableData" max-height="450" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="storageable.name" label="Nombre" />
                    <el-table-column prop="storageable.part_number" label="N° parte" />
                    <el-table-column prop="location" label="Ubicación" />
                    <el-table-column prop="quantity" label="Cantidad" />
                    <el-table-column align="right" fixed="right" width="120">
                        <template #header>
                            <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
                        </template>
                        <!-- <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </span>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id"><i class="fa-solid fa-eye"></i>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar scrap')"
                                            :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template> -->
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
        scraps: Array,
        totalScrapMoney: Number,
    },
    methods: {
        tableRowClassName({ row, rowIndex }) {
            return 'text-red-600';
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
                const response = await axios.post(route('storages.scraps.massive-delete', {
                    scraps: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.scraps.forEach((scrap, index) => {
                        if (this.$refs.multipleTableRef.value.includes(scrap)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.scraps.splice(index, 1);
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

        // handleRowClick(row) {
        //     console.log(row);
        //     this.$inertia.get(route('storages.show', row));
        // },

        edit(index, scrap) {
            console.log(scrap);
            this.$inertia.get(route('storages.finished-products.edit', scrap.storageable));
        }
    },

    computed: {
        filteredTableData() {
            return this.scraps.filter(
                (scrap) =>
                    !this.search ||
                    scrap.storageable.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    scrap.storageable.part_number.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
