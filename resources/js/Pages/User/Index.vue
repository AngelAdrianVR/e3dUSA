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
                            :total="users.length" />
                    </div>

                </div>

                <el-table :data="users" max-height="450" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column prop="id" label="ID" width="45" />
                    <el-table-column prop="name" label="Nombre" width="200" />
                    <el-table-column prop="is_active" label="Estatus" width="120" />
                    <el-table-column prop="name" label="Puesto" width="130" />
                    <el-table-column prop="created_at" label="Fecha ingreso" width="160" />
                    <el-table-column align="right" fixed="right" width="200">
                        <template #header>
                            <TextInput v-model="search" type="search" class="w-full text-gray-600" placeholder="Buscar" />
                        </template>
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <span class="el-dropdown-link mr-3">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </span>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id"><i class="fa-solid fa-eye"></i>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item :command="'edit-' + scope.row.id"><i class="fa-solid fa-user-slash"></i>
                                            Deshabilitar</el-dropdown-item>
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
        users: Array
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
            if (row.is_active === 1) {
                return 'text-green-600';
            }else{
                return 'text-red-600';
            }

            return '';
        },

        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('users.' + commandName, rowId));
            }
        },
    },
    computed: {
        // filteredTableData() {
        //     if (!this.search) {
        //         return this.machines.data.filter((item, index) => index >= this.start && index < this.end);
        //     } else {
        //         return this.machines.data.filter(
        //             (machine) =>
        //                 machine.name.toLowerCase().includes(this.search.toLowerCase()) ||
        //                 machine.serial_number.toLowerCase().includes(this.search.toLowerCase()) ||
        //                 machine.supplier.toLowerCase().includes(this.search.toLowerCase())
        //         )
        //     }
        // }
    },
};
</script>
