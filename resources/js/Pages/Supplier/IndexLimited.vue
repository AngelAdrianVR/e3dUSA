<template>
    <AppLayout title="Proveedores">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl leading-tight">Proveedores</h2>
            </div>
        </template>

        <!-- tabla -->
        <div class="lg:w-5/6 mx-auto mt-6">
            <div class="flex justify-between">
                <!-- pagination -->
                <div>
                    <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                        :total="suppliers.length" />
                </div>
                <!-- buscador -->
                <IndexSearchBar @search="handleSearch" />
            </div>
            <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                ref="multipleTableRef" :row-class-name="tableRowClassName">
                <el-table-column prop="id" label="ID" width="45" />
                <el-table-column prop="nickname" label="Alias" />
            </el-table>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";

export default {
    data() {
        return {
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
        IndexSearchBar,
    },
    props: {
        suppliers: Array
    },
    methods: {
        handleSearch(search) {
            this.search = search;
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        handleRowClick(row) {
            this.$inertia.get(route('suppliers.show', row));
        },
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },
    },
    computed: {
        filteredTableData() {
            return this.suppliers.filter(
                (supplier) =>
                    !this.search ||
                    supplier.nickname.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>