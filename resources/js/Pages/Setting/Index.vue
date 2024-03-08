<template>
    <div>
        <AppLayoutNoHeader title="Configuraciones">
            <div class="flex justify-between text-lg mx-14 mt-11">
                <span>Configuraciones</span>
            </div>
            <div class="flex justify-end mt-5 mx-14">
                <PrimaryButton v-if="$page.props.auth.user.permissions.includes('Crear configuraciones')"
                    @click="createSetting()" class="h-9 rounded-lg">
                    Crear
                </PrimaryButton>
            </div>
            <div class="mt-7 border-b-2">
                <!-- ------------------------Information panel tabs--------------------- -->
                <div class="border-2">
                    <!-- <div class="border-b-2 px-7 py-3 flex">
                        <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
                            "
                            class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base leading-none">
                            Roles
                        </p>
                        <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
                        <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
                            " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
                            Permisos
                        </p>
                        </div> -->
                    <!-- --------------------- Tab 1 settings starts------------------ -->
                    <div v-if="tabs == 1" class="px-7 py-7 text-sm">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left">
                                    <th class="font-normal pb-5"># <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                                    <th class="font-normal pb-5">Nombre <i class="fa-solid fa-arrow-down-long ml-3"></i>
                                    </th>
                                    <th class="font-normal pb-5">Valor <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                                    <th class="font-normal pb-5">Tipo <i class="fa-solid fa-arrow-down-long ml-3"></i>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(setting, index) in settings.data" :key="setting.id" class="mb-4">
                                    <td class="text-left pb-3">
                                        {{ setting.id }}
                                    </td>
                                    <td @click="editSetting(setting, index)" class="text-left pb-3">
                                        <span class="hover:underline cursor-pointer">{{ setting.label }}</span>
                                    </td>
                                    <td class="text-left pb-3"> {{ getOptionName(setting.options, setting.value) }}</td>
                                    <td class="text-left pb-3">{{ setting.type }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- --------------------- Tab 1 settings ends------------------ -->
                </div>
            </div>

            <!-- Settings modal -->
            <DialogModal :show="showSettingModal" @close="showSettingModal = false">
                <template #title>
                    <p v-if="editFlag">Configuración '{{ currentSetting.label }}'</p>
                    <p v-else>Crear nueva configuración</p>
                </template>
                <template #content>
                    <div>
                        <form @submit.prevent="editFlag ? updateSetting() : storeSetting()" ref="mySettingForm">
                            <div>
                                <IconInput v-model="settingForm.label"
                                    inputPlaceholder="Nombre de configuración *" inputType="text">
                                    <el-tooltip content="Nombre de configuración *" placement="top">
                                        A
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="settingForm.errors.label" />
                            </div>
                            <div>
                                <IconInput v-model="settingForm.key"
                                    inputPlaceholder="Clave de configuración *" inputType="text">
                                    <el-tooltip content="Clave de configuración *" placement="top">
                                        A
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="settingForm.errors.key" />
                            </div>
                            <div v-if="currentSetting?.options?.length">
                                <el-select v-model="settingForm.value" placeholder="Valor de configuración *"
                                    no-data-text="No hay valores">
                                    <el-option v-for="option in currentSetting.options" :key="option.value" :label="option.name"
                                        :value="option.value"></el-option>
                                </el-select>
                                <InputError :message="settingForm.errors.value" />
                            </div>
                            <div v-else>
                                <IconInput v-model="settingForm.value"
                                    inputPlaceholder="Valor de configuración *" inputType="text">
                                    <el-tooltip content="Valor de configuración *" placement="top">
                                        A
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="settingForm.errors.value" />
                            </div>
                            <div>
                                <IconInput v-model="settingForm.type"
                                    inputPlaceholder="Tipo de configuración *" inputType="text">
                                    <el-tooltip content="Tipo de configuración *" placement="top">
                                        A
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="settingForm.errors.type" />
                            </div>
                            <div class="mt-3">
                                <div class="text-xs text-secondary mb-3">
                                    Separar con guión medio (-) el nombre y el valor de las opciones. Ejemplo: <br>
                                    Automático-1. <br>
                                    Dejar vacío si las opciones válidas de esta configuración son valores abiertos.
                                </div>
                                <div class="flex space-x-2 mb-1">
                                    <IconInput v-model="newValidOption" inputPlaceholder="Ingresa una opción"
                                        inputType="text" class="w-full">
                                        <el-tooltip content="Opciones válidas para esta configuración" placement="top">
                                            <i class="fa-solid fa-palette"></i>
                                        </el-tooltip>
                                    </IconInput>
                                    <SecondaryButton @click="addValidOption" type="button">
                                        Agregar
                                        <i class="fa-solid fa-arrow-down ml-2"></i>
                                    </SecondaryButton>
                                </div>
                                <el-select v-model="settingForm.options" multiple clearable placeholder="Opciones"
                                    no-data-text="Agrega primero una caracteristica">
                                    <el-option v-for="option in options" :key="option" :label="option"
                                        :value="option"></el-option>
                                </el-select>
                            </div>
                        </form>
                    </div>
                </template>
                <template #footer>
                    <CancelButton @click="showSettingModal = false; settingForm.reset(); editFlag = false;"
                        :disabled="settingForm.processing">Cancelar</CancelButton>
                    <PrimaryButton @click="submitSettingForm" :disabled="settingForm.processing">{{ editFlag ? 'Actualizar'
                        :
                        'Crear' }}
                    </PrimaryButton>
                </template>
            </DialogModal>
     
        </AppLayoutNoHeader>
    </div>
</template>
    
<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";

export default {
    data() {
        const settingForm = useForm({
            label: null,
            key: null,
            value: null,
            type: null,
            options: [],
        });

        return {
            settingForm,
            currentSetting: null,
            showSettingModal: false,
            indexSettingEdit: null,
            editFlag: false,
            tabs: 1,

            newFeature: null,
            options: [],
        };
    },
    components: {
        AppLayoutNoHeader,
        PrimaryButton,
        SecondaryButton,
        CancelButton,
        DialogModal,
        IconInput,
        InputError
    },
    props: {
        settings: Object,
    },
    methods: {
        // settings
        getOptionName(options, value) {
            if (!options) return value;

            const option = options.find(opt => opt.value == value);

            return option ? option.name : value;
        },
        editSetting(setting, index) {
            if (this.$page.props.auth.user.permissions.includes('Editar configuraciones')) {
                this.currentSetting = setting;
                this.editFlag = true;
                this.indexSettingEdit = index;
                this.showSettingModal = true;
                this.settingForm.options = null;

                this.settingForm.label = setting.label;
                this.settingForm.key = setting.key;
                this.settingForm.value = setting.value;
                this.settingForm.type = setting.type;
                this.settingForm.options = setting.options?.map(item => `${item.name}-${item.value}`);
            }
        },
        createSetting() {
            this.currentSetting = null;
            this.showSettingModal = true;
            this.editFlag = false;
            this.settingForm.reset();
        },
        async updateSetting() {
            try {
                const response = await axios.put(route('settings.update', this.currentSetting), {
                    label: this.settingForm.label,
                    key: this.settingForm.key,
                    value: this.settingForm.value,
                    type: this.settingForm.type,
                    options: this.settingForm.options,
                });

                if (response.status === 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Configuración actualizada',
                        type: 'success'
                    });
                    this.settings.data[this.indexSettingEdit] = response.data.item;
                    this.settingForm.reset();
                    this.showSettingModal = false;
                }
            } catch (error) {
                this.$notify({
                    title: 'Error',
                    message: error.message,
                    type: 'error'
                });
            }
        },
        async storeSetting() {
            try {
                const response = await axios.post(route('settings.store'), {
                    label: this.settingForm.label,
                    key: this.settingForm.key,
                    value: this.settingForm.value,
                    type: this.settingForm.type,
                    options: this.settingForm.options,
                });

                if (response.status === 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Configuración creada',
                        type: 'success'
                    });
                    this.settings.data.push(response.data.item);
                    this.settingForm.reset();
                    this.showSettingModal = false;
                }
            } catch (error) {
                console.log(error);
                this.settingForm.errors = error.response.data.errors;
            }
        },
        submitSettingForm() {
            this.$refs.mySettingForm.dispatchEvent(new Event('submit', { cancelable: true }));
        },
        addValidOption() {
            if (this.newValidOption.trim() !== '') {
                this.settingForm.options.push(this.newValidOption);
                this.options.push(this.newValidOption);
                this.newValidOption = '';
            }
        },
    }
};
</script>
    