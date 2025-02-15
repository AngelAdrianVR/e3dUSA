<template>
    <AppLayout title="Proveedores">
        <template #header>
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Proveedores</h2>
                </div>
                <div v-if="$page.props.auth.user.permissions.includes('Crear proveedores')"
                    class="flex space-x-2 items-center">
                    <SecondaryButton @click="showReportFilter = true"
                        class="border !border-secondary !text-secondary !bg-transparent">
                        Generar reporte
                    </SecondaryButton>
                    <Link :href="route('suppliers.create')">
                    <SecondaryButton class="size-9 flex items-center justify-center"><i class="fa-solid fa-plus"></i>
                    </SecondaryButton>
                    </Link>
                </div>
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
                <!-- buttons -->
                <div>
                    <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar proveedores')"
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
                @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="30" />
                <el-table-column prop="id" label="ID" width="45" />
                <el-table-column prop="name" label="Nombre" />
                <el-table-column prop="nickname" label="Alias" />
                <el-table-column prop="phone" label="Teléfono" />
                <el-table-column prop="address" label="Dirección" />
                <el-table-column prop="post_code" label="C.P." />
                <el-table-column prop="created_at" label="Agregado el" />
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
                                        v-if="$page.props.auth.user.permissions.includes('Editar proveedores')"
                                        :command="'edit-' + scope.row.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        Editar</el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="$page.props.auth.user.permissions.includes('Crear proveedores')"
                                        :command="'clone-' + scope.row.id">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                        </svg>
                                        Clonar</el-dropdown-item>
                                    <!-- <el-dropdown-item :command="'clone-' + scope.row.id"><i
                                                class="fa-solid fa-clone"></i>
                                            Clonar</el-dropdown-item> -->
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <DialogModal :show="showReportFilter" @close="showReportFilter = false" maxWidth="lg">
            <template #title>
                <h1 class="font-bold">Filtro de periodo para reporte</h1>
            </template>
            <template #content>
                <div>
                    <InputLabel value="Mes" />
                    <el-date-picker v-model="period" type="month" placeholder="Selecciona el mes" format="MMM, YYYY"
                        class="!w-full" value-format="YYYY-MM" />
                </div>
                <div class="mt-3">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold">Proveedor(es)</span>
                        <button v-if="suppliersFiltered.length == suppliers.length" @click="cleanFilter"
                            class="text-primary underline" type="button">
                            Limpiar selecciones
                        </button>
                        <button v-else @click="grantAllSuppliers" class="text-primary underline" type="button">
                            Seleccionar todos
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <label v-for="supplier in suppliers" :key="supplier.id" class="flex items-center">
                            <input type="checkbox" v-model="suppliersFiltered" :value="supplier.id"
                                class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent size-4" />
                            <span class="ml-2 text-xs">{{ supplier.name }}</span>
                        </label>
                    </div>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="openReport" :disabled="!period || !suppliersFiltered.length">Ver reporte
                </PrimaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import axios from 'axios';
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

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
            // modales
            showReportFilter: false,
            period: null,
            suppliersFiltered: [],
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        Link,
        TextInput,
        IndexSearchBar,
        DialogModal,
        InputLabel,
        PrimaryButton,
    },
    props: {
        suppliers: Array
    },
    methods: {
        cleanFilter() {
            this.suppliersFiltered = [];
        },
        grantAllSuppliers() {
            this.suppliersFiltered = this.suppliers
                .flat() // Aplanar los arrays de permisos por guard
                .map(supplier => supplier.id); // Obtener solo los IDs de los permisos
        },
        openReport() {
            const url = this.route('suppliers.rating-report', { p: this.period, s: this.suppliersFiltered });
            window.open(url, '_blank');
        },
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
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else {
                this.$inertia.get(route('suppliers.' + commandName, rowId));
            }
        },
        async clone(supplier_id) {
            try {
                const response = await axios.post(route('suppliers.clone', {
                    supplier_id: supplier_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.suppliers.unshift(response.data.newItem);

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
                const response = await axios.post(route('suppliers.massive-delete', {
                    suppliers: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.suppliers.forEach((supplier, index) => {
                        if (this.$refs.multipleTableRef.value.includes(supplier)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.suppliers.splice(index, 1);
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
            return this.suppliers.filter(
                (supplier) =>
                    !this.search ||
                    supplier.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    supplier.address.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>