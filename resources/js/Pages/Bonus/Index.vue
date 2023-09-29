<template>
    <div>
        <AppLayout title="Gestión de bonos">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Gestión de bonos</h2>
                    </div>
                    <div>
                        <SecondaryButton @click="editFlag = false; showModal = true;">+ Nuevo</SecondaryButton>
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
                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
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
                    <el-table-column prop="id" label="ID" width="50" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="description" label="Descripción" />
                    <el-table-column prop="full_time" label="$ Tiempo completo" />
                    <el-table-column prop="half_time" label="$ Medio tiempo" />
                    <el-table-column align="right" fixed="right" width="120">
                        <template #header>
                            <TextInput v-model="search" type="search" class="w-full text-gray-600" placeholder="Buscar" />
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <!-- tabla -->

            <!-- -------------- Modal starts----------------------- -->
            <DialogModal :show="showModal" @close="showModal = false">
                <template #title>
                    <p v-if="editFlag">Editar Bono "{{ itemClicked.name }}"</p>
                    <p v-else>Crear Bono</p>
                </template>
                <template #content>
                    <form ref="myForm" @submit.prevent="editFlag ? update() : store()">
                        <div class="flex justify-end">
                            <el-switch v-model="form.is_active" inline-prompt size="large"
                                style="--el-switch-on-color: #0355B5; --el-switch-off-color: #CCCCCC"
                                active-text="Activo" inactive-text="Inactivo" />
                        </div>
                        <div>
                            <IconInput v-model="form.name" inputPlaceholder="Nombre de bono *" inputType="text">
                                <el-tooltip content="Nombre de bono *" placement="top">
                                    A
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="flex col-span-full mt-2">
                            <el-tooltip content="Descripción" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                    ...
                                </span>
                            </el-tooltip>
                            <textarea v-model="form.description" class="textarea" autocomplete="off"
                                placeholder="Descripción"></textarea>
                            <InputError :message="form.errors.description" />
                        </div>
                        <div class="grid grid-cols-2 gap-1 mt-3">
                            <div>
                                <IconInput v-model="form.full_time" inputPlaceholder="Monto para turno completo *"
                                    inputType="number">
                                    <el-tooltip content="Monto para turno completo *" placement="top">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="form.errors.full_time" />
                            </div>
                            <div>
                                <IconInput v-model="form.half_time" inputPlaceholder="Monto para medio turno *"
                                    inputType="number">
                                    <el-tooltip content="Monto para medio turno *" placement="top">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="form.errors.half_time" />
                            </div>
                        </div>
                    </form>
                </template>
                <template #footer>
                    <CancelButton @click="showModal = false; form.reset(); editFlag = false;" :disabled="form.processing">
                        Cancelar</CancelButton>
                    <PrimaryButton @click="submitForm" :disabled="form.processing">{{ editFlag ? 'Actualizar' : 'Crear' }}
                    </PrimaryButton>
                </template>
            </DialogModal>
            <!-- --------------------------- Modal ends ------------------------------------ -->

        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            name: null,
            description: null,
            full_time: null,
            half_time: null,
            is_active: true,
        });

        return {
            form,
            disableMassiveActions: true,
            search: '',
            // pagination
            itemsPerPage: 10,
            start: 0,
            end: 10,
            showModal: false,
            editFlag: false,
            itemClicked: null,
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        PrimaryButton,
        CancelButton,
        Link,
        TextInput,
        DialogModal,
        InputError,
        IconInput
    },
    props: {
        bonuses: Object
    },
    methods: {
        update() {
            this.form.put(route('bonuses.update', this.itemClicked), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Bono actualizado',
                        type: 'success'
                    });
                    this.form.reset();
                    this.showModal = false;
                }
            });
        },
        store() {
            this.form.post(route('bonuses.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Bono creado',
                        type: 'success'
                    });
                    this.form.reset();
                    this.showModal = false;
                }
            });
        },
        submitForm() {
            this.$refs.myForm.dispatchEvent(new Event('submit', { cancelable: true }));
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
            let classes = 'cursor-pointer';

            if (row.is_active !== 1) {
                classes += ' text-red-600';
            }

            return classes;
        },
        handleRowClick(row) {
            this.itemClicked = row;
            this.editFlag = true;
            this.showModal = true;

            this.form.name = row.name;
            this.form.description = row.description;
            this.form.full_time = row.full_time;
            this.form.half_time = row.half_time;
            this.form.is_active = Boolean(row.is_active);
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
