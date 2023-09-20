<template>
    <div>
        <AppLayout title="Gestión de dias festivos">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Gestión de dias festivos</h2>
                    </div>
                    <div>
                        <SecondaryButton @click="editFlag = false; showModal = true;">+ Nuevo</SecondaryButton>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p class="text-gray-500"><i class="fa-solid fa-circle mr-1"></i>Activo</p>
                <p class="text-red-500"><i class="fa-solid fa-circle mr-1"></i>Inactivo</p>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="holidays.data.length" />
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
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="id" label="ID" width="50" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="date.formatted" label="Fecha" />
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
                                style="--el-switch-on-color: #0355B5; --el-switch-off-color: #CCCCCC" active-text="Activo"
                                inactive-text="Inactivo" />
                        </div>
                        <div>
                            <IconInput v-model="form.name" inputPlaceholder="Nombre de festividad *" inputType="text">
                                <el-tooltip content="Nombre de festividad *" placement="top">
                                    A
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="flex items-center mt-2">
                            <el-tooltip content="Método de pago" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                    <i class="fa-regular fa-calendar-days"></i>
                                </span>
                            </el-tooltip>
                            <el-select v-model="form.day" clearable placeholder="Dia">
                                <el-option v-for="item in 31" :key="item" :label="item" :value="item" />
                            </el-select>
                            <el-select v-model="form.month" clearable placeholder="Mes">
                                <el-option v-for="(item, index) in months" :key="index" :label="item.label" :value="item.value" />
                            </el-select>
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
            day: null,
            month: null,
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
            months: [
                {label: "Enero", value: "01"},
                {label: "Febrero", value: "02"},
                {label: "Marzo", value: "03"},
                {label: "Abril", value: "04"},
                {label: "Mayo", value: "05"},
                {label: "Junio", value: "06"},
                {label: "Julio", value: "07"},
                {label: "Agosto", value: "08"},
                {label: "Septiembre", value: "09"},
                {label: "Octubre", value: "10"},
                {label: "Noviembre", value: "11"},
                {label: "Diciembre", value: "12"},
            ],
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
        holidays: Object
    },
    methods: {
        update() {
            this.form.put(route('holidays.update', this.itemClicked), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Dia festivo actualizado',
                        type: 'success'
                    });
                    this.form.reset();
                    this.showModal = false;
                }
            });
        },
        store() {
            this.form.post(route('holidays.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Dia festivo creado',
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
                const response = await axios.post(route('holidays.massive-delete', {
                    holidays: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.holidays.data.forEach((holiday, index) => {
                        if (this.$refs.multipleTableRef.value.includes(holiday)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.holidays.data.splice(index, 1);
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
            this.form.day = row.date.string.split('-')[2];
            this.form.month = row.date.string.split('-')[1];
            this.form.is_active = Boolean(row.is_active);
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.holidays.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.holidays.data.filter(
                    (holiday) =>
                        holiday.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        holiday.date.toLowerCase().includes(this.search.toLowerCase()) ||
                        holiday.id.toString().toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
