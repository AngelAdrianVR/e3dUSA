<template>
    <div>
        <AppLayout title="Usuarios">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Usuarios</h2>
                    </div>
                    <div>
                        <Link :href="route('users.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
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
                            :total="users.data.length" />
                    </div>

                    <!-- buttons -->
                    <div>
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                            title="¿Continuar?" @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="450" style="width: 100%" @selection-change="handleSelectionChange"
                    ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column prop="id" label="ID" width="45" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="is_active.string" label="Estatus" />
                    <el-table-column prop="employee_properties.job_position" label="Puesto" />
                    <el-table-column prop="employee_properties.join_date" label="Fecha ingreso" width="160" />
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
                                        <el-dropdown-item :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item @click="changeStatus(scope.row)"><i
                                                class="fa-solid fa-user-slash"></i>
                                            {{scope.row.is_active.bool ? 'Deshabilitar' : 'Habilitar'}}</el-dropdown-item>
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import axios from 'axios';


export default {
    data() {


        return {
            disableMassiveActions: true,
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
        users: Object,
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
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },
        tableRowClassName({ row, rowIndex }) {
            if (row.is_active.bool) {
                return 'text-green-600 cursor-pointer';
            } else {
                return 'text-red-600 cursor-pointer';
            }
        },
        handleRowClick(row) {
            this.$inertia.get(route('users.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            }else {
                this.$inertia.get(route('users.' + commandName, rowId));
            }
        },
        changeStatus(user){

            this.$inertia.put(route("users.change-status", user)),
            
            this.$notify({
                title: "Éxito",
            message: "Se cambió el estatus del usuario",
            type: "success",
          });

        }
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.users.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.users.data.filter(
                    (user) =>
                        user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        user.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                        user.employee_properties?.job_position.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
