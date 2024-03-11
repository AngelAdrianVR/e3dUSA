<template>
    <div>
        <AppLayout title="Almacén de seguimiento de muestras">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Almacén de seguimiento de muestras</h2>
                    </div>
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear seguimiento de muestras')"
                        :href="route('storages.samples.create')">
                    <SecondaryButton>Agregar producto a seguimiento de muestras</SecondaryButton>
                    </Link>
                </div>
            </template>

            <div v-if="$page.props.auth.user.permissions.includes('Ver costo de almacen de seguimiento de muestras')"
                class="text-center mt-3">
                <el-tag class="mt-3" size="small" style="font-size: 16px;" type="danger">Muestras total:
                    ${{ totalSamplesMoney.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} MXN</el-tag>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div class="mb-2">
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="sample_products.length" />
                    </div>
                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar seguimiento de muestras')"
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
                    class="cursor-pointer" @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="storageable.name" label="Nombre" />
                    <el-table-column prop="storageable.part_number" label="N° parte" />
                    <el-table-column prop="location" label="Ubicación" />
                    <el-table-column prop="quantity" label="Cantidad">
                        <template #default="scope">
                            <span>
                                {{ scope.row.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
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
        sample_products: Array,
        totalSamplesMoney: Number,
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
        handleRowClick(row) {
            this.$inertia.get(route('storages.show', row));
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('storages.samples.massive-delete', {
                    samples: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of samples
                    let deletedIndexes = [];
                    this.sample_products.forEach((sample, index) => {
                        if (this.$refs.multipleTableRef.value.includes(sample)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar samples por índice
                    for (const index of deletedIndexes) {
                        this.sample_products.splice(index, 1);
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
    },
    computed: {
        filteredTableData() {
            return this.sample_products.filter(
                (sample) =>
                    !this.search ||
                    sample.storageable.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    sample.storageable.part_number.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
