<template>
    <div>
        <!-- Estado de carga -->
        <div v-if="loading"
            class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
        </div>
        <div v-if="loading"
            class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
            <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
        </div>

        <div
            v-if="loadingExport"
            class="fixed inset-0 bg-gray-900 bg-opacity-80 z-50 flex items-center justify-center"
            >
            <div class="text-center">
                <svg
                class="animate-spin size-14 text-blue-600 mx-auto"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-100"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"
                ></path>
                </svg>
                <p class="mt-4 text-gray-100">Generando archivo, por favor espera...</p>
            </div>
        </div>

        <AppLayout title="Catalogo de productos">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Catálogo de productos</h2>
                    </div>
                    <div v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')">
                        <Link :href="route('catalog-products.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <!-- tabla -->
            <div class="w-[95%] lg:w-5/6 mx-auto mt-6">
                <div class="lg:flex justify-between mb-2">
                    <!-- pagination -->
                        <div v-if="!search" class="overflow-auto mb-2">
                            <PaginationWithNoMeta :pagination="pagination" class="py-2" />
                        </div>
                    <!-- <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="catalog_products.length" />
                    </div> -->
                    <!-- buttons -->
                    <div class="flex items-center space-x-2">
                        <!-- <PrimaryButton @click="openReport">Reporte de precios</PrimaryButton> -->
                        <el-dropdown split-button type="primary" @click="openReport">
                            Reporte de precios
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item @click="exportToExcel">Exportar lista de productos en Excel</el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                        <div v-if="$page.props.auth.user.permissions.includes('Eliminar catalogo de productos')">
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                title="¿Continuar?" @confirm="deleteSelections">
                                <template #reference>
                                    <el-button type="danger" plain
                                        :disabled="disableMassiveActions">Eliminar</el-button>
                                </template>
                            </el-popconfirm>
                        </div>
                    </div>
                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column label="Imagen" width="100">
                        <template #default="scope">
                            <figure class="border rounded-md size-20 flex items-center justify-center">
                                <el-image
                                    @click.stop=""
                                    style="width: 100%; height: 100%; border-radius: 6px"
                                    :src="scope.row.media[0]?.original_url"
                                    :preview-src-list="[scope.row.media[0]?.original_url]"
                                    fit="contain"
                                    preview-teleported
                                    :hide-on-click-modal="true"
                                />
                            </figure>
                        </template>
                    </el-table-column>
                    <el-table-column prop="part_number" label="Num de parte" width="200" />
                    <el-table-column prop="name" label="Nombre" width="250" />
                    <el-table-column prop="cost.number_format" label="Costo $" width="150">
                        <template #default="scope">
                            <p>${{ scope.row.cost }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="description" label="Descripción" />
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Editar catalogo de productos')"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')"
                                            :command="'clone-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                            </svg>
                                            Clonar</el-dropdown-item>
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
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import axios from 'axios';

export default {
    data() {
        return {
            loading: false,
            loadingExport: false,
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            filteredTableData: [...this.catalog_products.data],
            // pagination
            pagination: this.catalog_products,
            // itemsPerPage: 10,
            // start: 0,
            // end: 10,
        };
    },
    components: {
        PaginationWithNoMeta,
        SecondaryButton,
        IndexSearchBar,
        PrimaryButton,
        AppLayout,
        TextInput,
        Link,
    },
    props: {
        catalog_products: Object
    },
    methods: {
        openReport() {
            window.open(route('catalog-products.prices-report'), '_blank');
        },
        exportToExcel() {
            this.loadingExport = true;

            axios({
                url: '/export-catalog-products',
                method: 'GET',
                responseType: 'blob',
            }).then(response => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'catalogo_precios.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }).catch(error => {
                console.error('Error al exportar:', error);
            }).finally(() => {
                this.loadingExport = false;
            });
        },
        procesarUrlImagenLocal(originalUrl) {
            // Reemplaza la parte inicial de la URL
            // const nuevaUrl = originalUrl.replace('https://https://intranetemblems3d.dtw.com.mx/', 'http://www.intranetemblems3d.dtw.com.mx');
            const nuevaUrl = originalUrl?.replace('http://localhost:8000', 'https://intranetemblems3d.dtw.com.mx/');  // para hacer pruebas en local
            return nuevaUrl;
        },
        async handleSearch(search) {
            this.search = search;
            this.loading = true;
            try {
                if (!this.search) {
                    this.filteredTableData = this.catalog_products.data;
                    this.pagination = this.catalog_products; //cuando se busca con texto vacio s emuestran todas las porduccoines y la paginacion es de todas las producciones
                } else {
                    const response = await axios.post(route('catalog-products.get-matches', { query: this.search }));

                    if (response.status === 200) {
                        this.filteredTableData = response.data.items;
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        // handleSearch(search) {
        //     this.search = search;
        // },
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        // handlePagination(val) {
        //     this.start = (val - 1) * this.itemsPerPage;
        //     this.end = val * this.itemsPerPage;
        // },
        async clone(catalog_product_id) {
            try {
                const response = await axios.post(route('catalog-products.clone', {
                    catalog_product_id: catalog_product_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.catalog_products.unshift(response.data.newItem);

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
        async deleteSelections() {
            try {
                const response = await axios.post(route('catalog-products.massive-delete', {
                    catalog_products: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.catalog_products.forEach((catalog_product, index) => {
                        if (this.$refs.multipleTableRef.value.includes(catalog_product)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.catalog_products.splice(index, 1);
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
        handleRowClick(row) {
            this.$inertia.get(route('catalog-products.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('catalog-products.' + commandName, rowId));
            }
        },

    },
    // computed: {
    //     filteredTableData() {
    //         if (!this.search) {
    //             return this.catalog_products.filter((item, index) => index >= this.start && index < this.end);
    //         } else {
    //             return this.catalog_products.filter(
    //                 (catalog_product) =>
    //                     catalog_product.name.toLowerCase().includes(this.search.toLowerCase()) ||
    //                     catalog_product.part_number.toLowerCase().includes(this.search.toLowerCase())
    //             )
    //         }
    //     }
    // },
    mounted() {
    }
};
</script>

<style>
.dark .el-pagination button {
    background-color: #333333;
    color: #cccccc;
}
</style>
