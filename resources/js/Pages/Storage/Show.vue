<template>
    <div>
        <AppLayoutNoHeader title="Almacén">
            <div class="flex justify-between text-lg mx-14 mt-11">
                <span>Almacén</span>
                <Link :href="route('storages.raw-materials.index')"
                    class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
                </Link>
            </div>
            <div class="flex justify-between mt-5 mx-14">
                <div class="w-1/3">
                    <el-select v-model="selectedStorage" clearable filterable placeholder="Buscar producto"
                        no-data-text="No hay productos en el almacén" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in storages" :key="item.id" :label="item.storageable.name"
                            :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <el-tooltip v-if="$page.props.auth.user.permissions.includes('Crear entradas')"
                        content="Dar entrada a almacén" placement="top">
                        <button @click="is_add = true; showDialogModal = true"
                            class="rounded-lg bg-green-600 text-white py-2 px-2 text-sm">Entrada</button>
                    </el-tooltip>

                    <el-tooltip v-if="$page.props.auth.user.permissions.includes('Crear salidas')"
                        content="Dar salida de almacén" placement="top">
                        <button @click="is_add = false; showDialogModal = true"
                            class="rounded-lg bg-primary text-white py-2 px-2 text-sm">Salida</button>
                    </el-tooltip>

                    <Dropdown align="right" width="48"
                        v-if="$page.props.auth.user.permissions.includes('Crear scrap') && $page.props.auth.user.permissions.includes('Eliminar materia prima')">
                        <template #trigger>
                            <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">Más <i
                                    class="fa-solid fa-chevron-down text-[11px] ml-2"></i></button>
                        </template>
                        <template #content>
                            <DropdownLink @click="console.log('No funciona')" as="button"
                                v-if="$page.props.auth.user.permissions.includes('Crear scrap')">
                                Mandar a scrap
                            </DropdownLink>
                            <DropdownLink @click="showConfirmModal = true" as="button"
                                v-if="$page.props.auth.user.permissions.includes('Eliminar materia prima')">
                                Eliminar
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
            <div class="lg:grid grid-cols-3 mt-12 border-b-2">
                <div class="px-14">
                    <h2 class="text-xl font-bold text-center mb-6">{{ currentStorage?.storageable.name }}</h2>
                    <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
                        class="w-full h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center">
                        <el-image style="height: 100%; " :src="currentStorage?.storageable?.media[0]?.original_url"
                            fit="fit">
                            <template #error>
                                <div class="flex justify-center items-center text-[#ababab]">
                                    <i class="fa-solid fa-image text-6xl"></i>
                                </div>
                            </template>
                        </el-image>
                        <div v-if="imageHovered" @click="openImage(currentStorage?.storageable?.media[0]?.original_url)"
                            class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
                            <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
                        </div>
                    </figure>
                    <div class="mt-8 ml-6 text-sm">
                        <div class="flex mb-2">
                            <p class="w-1/3 text-primary">Existencias</p>
                            <p>{{ currentStorage?.quantity ?? '0' }} {{ currentStorage?.storageable.measure_unit ?? '' }}
                            </p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-primary">Ubicación</p>
                            <p>{{ currentStorage?.location ?? '--' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 border-2">
                    <div class="border-b-2 px-7 py-3">
                        Información general
                    </div>
                    <div class="px-7 py-7 text-sm">
                        <div class="flex space-x-2 mb-6">
                            <p class="w-1/3 text-[#9A9A9A]">Tipo:</p>
                            <p>{{ currentStorage?.type }}</p>
                        </div>
                        <div class="flex space-x-2 mb-6">
                            <p class="w-1/3 text-[#9A9A9A]">Fecha de Alta</p>
                            <p>{{ currentStorage?.created_at.split('T')[0] }}</p>
                        </div>
                        <div class="flex mb-2 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
                            <p>{{ currentStorage?.id }}</p>
                        </div>
                        <div class="flex mb-6 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Características</p>
                            <p>{{ currentStorage?.storageable?.features?.join(', ') }}</p>
                        </div>
                        <div class="flex mb-2 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
                            <p>{{ currentStorage?.storageable.part_number }}</p>
                        </div>
                        <div class="flex mb-6 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
                            <p>{{ currentStorage?.storageable.measure_unit }}</p>
                        </div>
                        <div class="flex mb-6 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Costo de aquisición</p>
                            <p class="text-[#4FC03D]">{{ currentStorage?.storageable.cost }}</p>
                        </div>
                        <div class="flex mb-2 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad miníma permitida en almacén</p>
                            <p>{{ currentStorage?.storageable.min_quantity }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad máxima permitida en almacén</p>
                            <p>{{ currentStorage?.storageable.max_quantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
                <template #title>
                    Eliminar producto de Almacén
                </template>
                <template #content>
                    Continuar con la eliminación?
                </template>
                <template #footer>
                    <div class="">
                        <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
                        <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
                    </div>
                </template>
            </ConfirmationModal>

            <!-- -------------- Dialog Modal starts----------------------- -->
            <DialogModal :show="showDialogModal" @close="showDialogModal = false; is_add = null">
                <template #title>
                    <p>Ingresa la cantidad</p>
                </template>
                <template #content>
                    <form ref="myForm" @submit.prevent="is_add ? addStorage() : subStorage()">
                        <div>
                            <IconInput v-model="form.quantity" inputPlaceholder="Cantidad" inputType="number">
                                <el-tooltip content="Cantidad" placement="top">
                                    123
                                </el-tooltip>
                            </IconInput>
                            <p v-if="errorMessage" class="text-red-600 text-xs">{{ errorMessage }}</p>
                        </div>
                    </form>
                </template>
                <template #footer>
                    <CancelButton @click="showDialogModal = false; form.reset(); is_add = null" :disabled="form.processing">
                        Cancelar</CancelButton>
                    <PrimaryButton @click="submitForm" :disabled="form.processing">{{ is_add ? 'Dar entrada' : 'Dar salida'
                    }}
                    </PrimaryButton>
                </template>
            </DialogModal>
            <!-- --------------------------- Dialog Modal ends ------------------------------------ -->
        </AppLayoutNoHeader>
    </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import InputError from "@/Components/InputError.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            quantity: null,
        });
        return {
            form,
            selectedStorage: '',
            currentStorage: null,
            imageHovered: false,
            showConfirmModal: false,
            showDialogModal: false,
            is_add: null,
            errorMessage: null,
        };
    },
    components: {
        AppLayoutNoHeader,
        PrimaryButton,
        CancelButton,
        Link,
        DropdownLink,
        Dropdown,
        ConfirmationModal,
        DialogModal,
        IconInput,
        InputError
    },
    props: {
        storage: Object,
        storages: Array,
    },
    methods: {
        showOverlay() {
            this.imageHovered = true;
        },
        hideOverlay() {
            this.imageHovered = false;
        },
        openImage(url) {
            window.open(url, '_blank');
        },

        async deleteItem() {
            try {
                const response = await axios.delete(route('storages.destroy', this.currentStorage?.id));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    const index = this.storages.findIndex(item => item.id === this.currentStorage.id);
                    if (index !== -1) {
                        this.storages.splice(index, 1);
                        this.selectedStorage = '';
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
            } finally {
                this.showConfirmModal = false;
            }
        },

        submitForm() {
            this.$refs.myForm.dispatchEvent(new Event('submit', { cancelable: true }));
        },

        async addStorage() {
            try {
                const response = await axios.post(route('storages.add', this.selectedStorage), {
                    quantity: this.form.quantity
                });
                if (response.status === 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Entrada exitosa',
                        type: 'success'
                    });
                    this.form.reset();
                    this.showDialogModal = false;
                    this.is_add = null;
                    this.currentStorage.quantity = response.data.item.quantity;
                    this.errorMessage = null;
                }
            } catch (error) {
                if (error.response.status === 422) {
                    console.log(error);
                    this.errorMessage = error.response.data.message;

                    this.$notify({
                        title: 'Error',
                        message: 'Formulario incompleto o formato invalido',
                        type: 'error'
                    });
                } else {
                    this.$notify({
                        title: 'Error',
                        message: error.message,
                        type: 'error'
                    });
                }
            }
        },
        async subStorage() {
            try {
                const response = await axios.post(route('storages.sub', this.selectedStorage), {
                    quantity: this.form.quantity
                });
                if (response.status === 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Entrada exitosa',
                        type: 'success'
                    });
                    this.form.reset();
                    this.showDialogModal = false;
                    this.is_add = null;
                    this.currentStorage.quantity = response.data.item.quantity;
                    this.errorMessage = null;
                }
            } catch (error) {
                if (error.response.status === 422) {
                    console.log(error);
                    this.errorMessage = error.response.data.message;

                    this.$notify({
                        title: 'Error',
                        message: 'Formulario incompleto o formato invalido',
                        type: 'error'
                    });
                } else {
                    this.$notify({
                        title: 'Error',
                        message: error.message,
                        type: 'error'
                    });
                }
            }
        },

    },
    watch: {
        selectedStorage(newVal) {
            this.currentStorage = this.storages.find(item => item.id == newVal);
        }
    },
    mounted() {
        this.selectedStorage = this.storage.id;
    }
};
</script>
