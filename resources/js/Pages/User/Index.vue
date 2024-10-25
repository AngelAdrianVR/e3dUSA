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

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p><i class="fa-solid fa-circle mr-1 text-red-500"></i>Inactivo</p>
                <p><i class="fa-solid fa-circle mr-1 text-green-500"></i>Activo</p>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="users.length" />
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
                    <el-table-column prop="id" label="ID" width="80" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="is_active.string" label="Estatus">
                        <template #default="scope">
                            <div class="flex">
                                <p class="mr-2 mt-px text-[6px]"
                                    :class="scope.row.is_active.bool ? 'text-green-500' : 'text-red-500'">
                                    <i class="fa-solid fa-circle"></i>
                                </p>
                                <span>{{ scope.row.is_active.string }}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="employee_properties.job_position" label="Puesto" />
                    <el-table-column prop="employee_properties.join_date" label="Fecha ingreso" width="160" />
                    <el-table-column prop="disabled_at" label="Fecha de baja" width="160" />
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
                                        <el-dropdown-item :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item :command="'status-' + scope.row.id">
                                            <i class="text-xs pr-1" :class="scope.row.is_active.bool ? 'fa-solid fa-user-slash' :
                                                'fa-solid fa-user'"></i>
                                            {{ scope.row.is_active.bool ? 'Deshabilitar' :
                                                'Habilitar' }}</el-dropdown-item>
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
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            showInactivatingModal: false,
            userToInactivate: null,
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
        users: Object,
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
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
        },
        handleRowClick(row) {
            this.$inertia.get(route('users.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const userId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(userId);
            } else if (commandName == 'status') {
                const user = this.users.data.find(u => u.id == userId)
                if (user.is_active) {
                    this.showInactivatingModal = true;
                    this.userToInactivate = user;
                } else {
                    this.changeStatus(user);
                }
            } else {
                this.$inertia.get(route('users.' + commandName, userId));
            }
        },
        changeStatus(user) {
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
                return this.users.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.users.filter(
                    (user) =>
                        user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        user.email?.toLowerCase().includes(this.search.toLowerCase()) ||
                        user.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                        user.employee_properties?.job_position.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
