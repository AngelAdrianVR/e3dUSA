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
                <el-tag class="mt-3" style="font-size: 20px;" type="danger">Muestras total:
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
                    <!-- <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar seguimiento de muestras')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div> -->
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    class="cursor-pointer" @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="storageable.name" label="Nombre" />
                    <el-table-column prop="storageable.part_number" label="N° parte" />
                    <el-table-column prop="location" label="Ubicación" />
                    <el-table-column prop="quantity" label="Cantidad" />
                    <el-table-column align="right" fixed="right" width="190">
                        <template #header>
                            <div class="flex space-x-2">
                                <TextInput v-model="inputSearch" type="search" @keyup.enter="handleSearch"
                                    class="w-full text-gray-600" placeholder="Buscar" />
                                <el-button @click="handleSearch" type="primary" plain class="mb-3"><i
                                        class="fa-solid fa-magnifying-glass"></i></el-button>
                            </div>
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
        sample_products: Array,
        totalSamplesMoney: Number,
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
        handleRowClick(row) {
            this.$inertia.get(route('storages.show', row));
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
