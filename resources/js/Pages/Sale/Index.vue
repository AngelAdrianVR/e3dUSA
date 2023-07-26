<template>
    <div>
        <AppLayout title="Órdenes de venta">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Órdenes de venta</h2>
                    </div>
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')" :href="route('sales.create')">
                    <SecondaryButton>+ Nuevo</SecondaryButton>
                    </Link>
                </div>
            </template>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="sales.data.length" />
                    </div>

                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar ordenes de venta')" confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                            title="¿Continuar?" @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="450" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="folio" label="folio" width="100" />
                    <el-table-column prop="user.name" label="Creado por" />
                    <el-table-column prop="created_at" label="Creado el" />
                    <el-table-column prop="company_branch.name" label="Cliente" />
                    <el-table-column prop="authorized_user_name" label="Autorizado por" />
                    <el-table-column prop="status['label']" label="Estatus" />
                    <el-table-column align="right" fixed="right" width="120">
                        <template #header>
                            <TextInput v-model="search" type="search" class="w-full text-gray-600" placeholder="Buscar" />
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
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
                                                            scope.row.user.id == $page.props.auth.user.id" :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')" :command="'clone-' + scope.row.id"><i
                                                class="fa-solid fa-clone"></i>
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
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link } from "@inertiajs/vue3";
import axios from 'axios';


export default {
    data() {
        return {
            disableMassiveActions: true,
            search: '',
            // pagination
            itemsPerPage: 25,
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
        sales: Object,
        company_branches: Array
    },

    methods: {
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        async deleteSelections() {

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
                const response = await axios.post(route('sales.massive-delete', {
                    sales: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of sales
                    let deletedIndexes = [];
                    this.sales.data.forEach((sale, index) => {
                        if (this.$refs.multipleTableRef.value.includes(sale)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar OV por índice
                    for (const index of deletedIndexes) {
                        this.sales.data.splice(index, 1);
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
            return 'cursor-pointer';
        },
        handleRowClick(row) {
            this.$inertia.get(route('sales.show', row));
        },
        async clone(sale_id) {
            try {
                const response = await axios.post(route('sales.clone', {
                    sale_id: sale_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.sales.data.unshift(response.data.newItem);

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
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('sales.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.sales.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.sales.data.filter(
                    (sale) =>
                        // sale.id.toLowerCase().includes(this.search.toLowerCase()) ||
                        sale.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        sale.company_branch.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
    },
};
</script>
