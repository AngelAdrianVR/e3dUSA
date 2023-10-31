<template>
    <div>
        <AppLayout title="Maquinaria">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Maquinaria</h2>
                    </div>
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear maquinas')"
                        :href="route('machines.create')">
                    <SecondaryButton>+ Nuevo</SecondaryButton>
                    </Link>
                </div>
            </template>

            <!-- tabla -->
            <div class="relative overflow-hidden">
                <NotificationCenter module="machine" />
                <div class="lg:w-5/6 mx-auto mt-6">
                    <div class="flex justify-between">
                        <!-- pagination -->
                        <div>
                            <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                                :total="machines.data.length" />
                        </div>

                        <!-- buttons -->
                        <div>
                            <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar maquinas')"
                                confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                                @confirm="deleteSelections">
                                <template #reference>
                                    <el-button type="danger" plain class="mb-3"
                                        :disabled="disableMassiveActions">Eliminar</el-button>
                                </template>
                            </el-popconfirm>
                        </div>
                    </div>

                    <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                        @selection-change="handleSelectionChange" ref="multipleTableRef"
                        :row-class-name="tableRowClassName">
                        <el-table-column type="selection" width="45" />
                        <el-table-column prop="id" label="ID" width="45" />
                        <el-table-column prop="name" label="Nombre" width="200" />
                        <el-table-column prop="serial_number" label="N° de serie" width="120" />
                        <el-table-column prop="supplier" label="Proveedor" width="130" />
                        <el-table-column prop="days_next_maintenance" label="Mantenimiento cada" width="160" />
                        <el-table-column prop="aquisition_date" label="Fecha de adquisición" />
                        <el-table-column prop="needs_maintenance" label="Necesita mantenimiento preventivo" />
                        <el-table-column align="right" fixed="right" width="190">
                            <template #header>
                                <div class="flex space-x-2">
                                    <TextInput v-model="inputSearch" type="search" @keyup.enter="handleSearch"
                                        class="w-full text-gray-600" placeholder="Buscar" />
                                    <el-button @click="handleSearch" type="primary" plain class="mb-3"><i
                                            class="fa-solid fa-magnifying-glass"></i></el-button>
                                </div>
                            </template>
                            <template #default="scope">
                                <el-dropdown trigger="click" @command="handleCommand">
                                    <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </span>
                                    <template #dropdown>
                                        <el-dropdown-menu>
                                            <el-dropdown-item :command="'show-' + scope.row.id"><i
                                                    class="fa-solid fa-eye"></i>
                                                Ver</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="$page.props.auth.user.permissions.includes('Editar maquinas')"
                                                :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
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
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
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
        NotificationCenter,
    },
    props: {
        machines: Object,
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

        async clone(company_id) {
            try {
                const response = await axios.post(route('companies.clone', {
                    company_id: company_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.companies.data.unshift(response.data.newItem);

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
                const response = await axios.post(route('machines.massive-delete', {
                    machines: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of companies
                    let deletedIndexes = [];
                    this.machines.data.forEach((machine, index) => {
                        if (this.$refs.multipleTableRef.value.includes(machine)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar clientes por índice
                    for (const index of deletedIndexes) {
                        this.machines.data.splice(index, 1);
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
            this.$inertia.get(route('machines.show', row));
        },

        tableRowClassName({ row, rowIndex }) {

            return 'cursor-pointer';
        },

        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else {
                this.$inertia.get(route('machines.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.machines.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.machines.data.filter(
                    (machine) =>
                        machine.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        machine.serial_number.toLowerCase().includes(this.search.toLowerCase()) ||
                        machine.supplier.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
