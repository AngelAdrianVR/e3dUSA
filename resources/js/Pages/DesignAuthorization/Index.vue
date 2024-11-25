<template>
    <AppLayout title="Formato de autorización de diseño">
        <template #header>
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Formato de autorización de diseño</h2>
                </div>
                <Link :href="route('design-authorizations.create')">
                    <SecondaryButton>+ Nuevo</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="flex space-x-6 items-center justify-center text-xs mt-2 dark:text-white">
            <p><i class="fa-solid fa-circle mr-1 text-red-500"></i>Rechazado</p>
            <p><i class="fa-solid fa-circle mr-1 text-green-500"></i>Aceptado</p>
        </div>

        <!-- tabla -->
        <div class="relative overflow-hidden min-h-[60vh]">
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <!-- <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="design_authorizations.length" />
                    </div> -->

                    <!-- buttons -->
                    <div>
                        <el-popconfirm
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                            title="¿Continuar?" @confirm="deleteSelections">
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
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="id" label="ID" width="80" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="version" label="Version" width="80" />
                    <el-table-column prop="seller.name" label="Vendedor" />
                    <el-table-column prop="company_branch.name" label="Sucursal" />
                    <el-table-column label="Autorización interna">
                        <template #default="scope">
                            <p class="flex">{{ formatDate(scope.row.authorized_at) ?? '-' }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Respuesta del cliente">
                        <template #default="scope">
                            <p v-if="scope.row.responded_at" class="flex items-center space-x-2">
                                <i class="fa-solid fa-circle mr-1 text-[7px]" :class="scope.row.design_accepted ? 'text-green-500' : 'text-red-500'"></i>
                                <span>{{ scope.row.design_accepted ? 'Aceptado' : 'Rechazado' }}</span>
                            </p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Razón de rechazo">
                        <template #default="scope">
                            <p class="flex items-center space-x-2">
                                {{ scope.row.rejected_razon ?? '-' }}
                            </p>
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
                                        <el-dropdown-item :command="'show-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="!scope.row.responded_at"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
        <!-- tabla -->
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
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
components:{
    AppLayout,
    SecondaryButton,
    IndexSearchBar,
    Link
},
props: {
    design_authorizations: Array,
},
methods: {
    formatDate(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMM yyyy', { locale: es }); // Formato personalizado
        }
    },
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
    async deleteSelections() {
        try {
            const response = await axios.post(route('design_authorizations.massive-delete', {
                design_authorizations: this.$refs.multipleTableRef.value
            }));

            if (response.status == 200) {
                this.$notify({
                    title: 'Éxito',
                    message: response.data.message,
                    type: 'success'
                });

                // update list of companies
                let deletedIndexes = [];
                this.design_authorizations.forEach((box, index) => {
                    if (this.$refs.multipleTableRef.value.includes(box)) {
                        deletedIndexes.push(index);
                    }
                });

                // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                deletedIndexes.sort((a, b) => b - a);

                // Eliminar clientes por índice
                for (const index of deletedIndexes) {
                    this.design_authorizations.splice(index, 1);
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
        this.$inertia.get(route('design-authorizations.show', row));
    },
    tableRowClassName({ row, rowIndex }) {
        return 'cursor-pointer text-xs';
    },
    handleCommand(command) {
        const commandName = command.split('-')[0];
        const rowId = command.split('-')[1];

        // if (commandName == 'clone') {
        //     this.clone(rowId);
        // } else {
            this.$inertia.get(route('design-authorizations.' + commandName, rowId));
        // }
    },
},
computed: {
    filteredTableData() {
        if (!this.search) {
            return this.design_authorizations.filter((item, index) => index >= this.start && index < this.end);
        } else {
            return this.design_authorizations.filter(
                (design_authorization) =>
                    design_authorization.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                    design_authorization.company_branch.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    design_authorization.name.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    }
},
}
</script>
