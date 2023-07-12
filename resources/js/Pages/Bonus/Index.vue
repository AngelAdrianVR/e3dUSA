<template>
    <div>
        <AppLayout title="Gestión de bonos">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Gestión de bonos</h2>
                    </div>
                    <div>
                        <Link :href="route('bonuses.create')">
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
                            :total="bonuses.data.length" />
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
                <el-table :data="filteredTableData" max-height="450" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="id" label="ID" width="50" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="description" label="Descripción" />
                    <el-table-column prop="full_time" label="$ Tiempo completo" />
                    <el-table-column prop="half_time" label="$ Medio tiempo" />
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
                                        <el-dropdown-item :command="'clone-' + scope.row.id"><i
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

            <!-- -------------- Modal starts----------------------- -->
            <Modal :show="showCreateModal" @close="showCreateModal = false">
                <form @submit.prevent="editFlag == true ? update() : store()">
                    <div class="p-3 relative">
                        <i @click="
                            showCreateModal = false;
                        form.reset();
                        editFlag = false;
                        " class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>
                        <div class="flex justify-start space-x-3 pt-5 pb-1">
                            <PrimaryButton>{{ editFlag == true ? 'Actualizar' : 'Enviar' }}</PrimaryButton>
                            <CancelButton @click="
                                createRequestModal = false;
                            form.reset();
                            editFlag = false
                                ">Cancelar</CancelButton>
                        </div>
                    </div>
                </form>
            </Modal>
            <!-- --------------------------- Modal ends ------------------------------------ -->

        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';


export default {
    data() {
        const form = useForm({
            name: null,
            description: null,
            full_time: null,
            half_time: null,
            is_active: null,
        });

        return {
            form,
            disableMassiveActions: true,
            search: '',
            // pagination
            itemsPerPage: 10,
            start: 0,
            end: 10,
            showCreateModal: false,
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        Link,
        TextInput,
        Modal,
    },
    props: {
        bonuses: Object
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
        async deleteSelections() {
            try {
                const response = await axios.post(route('bonuses.massive-delete', {
                    bonuses: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.bonuses.data.forEach((bonus, index) => {
                        if (this.$refs.multipleTableRef.value.includes(bonus)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.bonuses.data.splice(index, 1);
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
            if (row.is_active !== 1) {
                return 'text-red-600';
            }

            return '';
        },
        handleRowClick(row) {
            this.$inertia.get(route('bonuses.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('bonuses.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.bonuses.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.bonuses.data.filter(
                    (bonus) =>
                        bonus.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        bonus.description.toLowerCase().includes(this.search.toLowerCase()) ||
                        bonus.id.toString().toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
