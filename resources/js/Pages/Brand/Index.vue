<template>
    <AppLayout title="Gestión de marcas">
        <template #header>
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Gestión de marcas</h2>
                </div>
                <Link :href="route('brands.create')">
                <SecondaryButton>+ Nuevo</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="relative overflow-hidden min-h-[60vh]">
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="brands.length" />
                    </div>

                    <!-- buttons -->
                    <!-- <div>
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                            title="¿Continuar?" @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div> -->
                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>

                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <!-- <el-table-column type="selection" width="30" /> -->
                    <el-table-column prop="id" label="ID" width="80" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column label="Es de lujo">
                        <template #default="scope">
                            <svg v-if="scope.row.is_luxury" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="size-5 text-green-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-5 text-red-700">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </template>
                    </el-table-column>
                    <el-table-column
                        v-if="$page.props.auth.user.permissions.includes('Editar marcas') && $page.props.auth.user.permissions.includes('Eliminar marcas')"
                        align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Editar marcas')"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Eliminar marcas')"
                                            :command="'delete-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            Eliminar
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </AppLayout>
    <ConfirmationModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
        <template #title>
            <h1>Eliminar marca "{{ itemToDelete?.name }}"</h1>
        </template>
        <template #content>
            <p>Si eliminar esta marca debe indicar qué otra marca será asignada a todos los productos que la contienen
            </P>
            <div class="mt-4">
                <InputLabel value="Marca a reemplazar*" />
                <el-select v-model="form.brand" filterable clearable placeholder="Selecciona"
                    no-data-text="No hay unidades de medida registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in brands" :key="item.id" :label="item.name" :value="item.name" :disabled="itemToDelete?.id == item.id" />
                </el-select>
            </div>
        </template>
        <template #footer>
            <div class="flex items-center space-x-1 justify-end">
                <PrimaryButton class="ml-2" @click="deleteItem"
                :disabled="form.processing || !form.brand">Eliminar</PrimaryButton>
                <CancelButton @click="showDeleteConfirm = false; form.reset()" :disabled="form.processing">Cancelar
                </CancelButton>
            </div>
        </template>
    </ConfirmationModal>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
    data() {
        const form = useForm({
            brand: null,
        });

        return {
            form,
            disableMassiveActions: true,
            showDeleteConfirm: false,
            itemToDelete: null,
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
        PrimaryButton,
        CancelButton,
        Link,
        IndexSearchBar,
        ConfirmationModal,
        InputLabel,
    },
    props: {
        brands: Array,
    },
    methods: {
        handleSearch(search) {
            this.search = search;
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
        deleteItem() {
            this.form.delete(route('brands.destroy', this.itemToDelete.id), {
                preserveScroll: true,
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Marca eliminada correctamente',
                        type: 'success'
                    });
                    this.showDeleteConfirm = false;
                    this.form.reset();
                },
                onError: () => {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: 'No se pudo eliminar la marca',
                        type: 'error'
                    });
                }
            });
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('brands.massive-delete', {
                    brands: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of companies
                    let deletedIndexes = [];
                    this.brands.forEach((brand, index) => {
                        if (this.$refs.multipleTableRef.value.includes(brand)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar clientes por índice
                    for (const index of deletedIndexes) {
                        this.brands.splice(index, 1);
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
            this.$inertia.get(route('brands.edit', row));
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName === 'edit') {
                this.$inertia.get(route('brands.edit', rowId));
            } else if (commandName === 'delete') {
                this.showDeleteConfirm = true;
                this.itemToDelete = this.brands.find(b => b.id == rowId);
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.brands.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.brands.filter(
                    (brand) =>
                        brand.name.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
}
</script>
