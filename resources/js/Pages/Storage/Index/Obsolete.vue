<template>
    <div>
        <AppLayout title="Almacén de producto obsoleto">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Almacén de producto obsoleto</h2>
                    </div>
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear producto obsoleto')"
                        :href="route('storages.obsolete.create')">
                    <SecondaryButton>Enviar producto a obsoleto</SecondaryButton>
                    </Link>
                </div>
            </template>

            <div v-if="$page.props.auth.user.permissions.includes('Ver costo de almacen de obsoleto')"
                class="text-center mt-3">
                <el-tag class="mt-3" size="small" style="font-size: 16px;" type="danger">Obsoleto total:
                    ${{ totalObsoleteMoney.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} MXN</el-tag>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div class="mb-2">
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="obsolete_products.length" />
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
        obsolete_products: Array,
        totalObsoleteMoney: Number,
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
    },
    computed: {
        filteredTableData() {
            return this.obsolete_products.filter(
                (obsolete) =>
                    !this.search ||
                    obsolete.storageable.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    obsolete.storageable.part_number.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
