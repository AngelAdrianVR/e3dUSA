<template>
    <div>
        <AppLayout title="Subir diseño exclusivo">
            <template #header>
                <div class="flex justify-between">
                    <Back />
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Subir diseño</h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="store" class="relative overflow-x-hidden">
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg px-9 py-5 shadow-md">
                    <div class="mt-1">
                        <p class="text-sm text-center text-secondary">
                            <label class="text-sm mb-1 ml-1 text-black">Cliente: </label>
                            {{ company.business_name }}</p>
                    </div>
                    <div class="mt-1">
                        <label class="text-sm mb-1 ml-1">Nombre del diseño *</label>
                        <input v-model="form.name" class="input" type="text"
                            placeholder="Escribe el nombre para reconocerlo">
                        <InputError :message="form.errors.name" />
                    </div>
                    <div>
                        <div class="mt-1">
                            <label class="text-sm mb-1 ml-1">Tipo de diseño *</label>
                            <el-select v-model="form.type" placeholder="Tipo *">
                                <el-option v-for="item in design_types" :key="item.id" :label="item.name" :value="item.name" />
                            </el-select>
                        </div>
                        <InputError :message="form.errors.type" />
                    </div>
                    <div class="mt-1">
                        <label class="text-sm mb-1 ml-1">Descripción</label>
                        <textarea v-model="form.description" rows="4" class="textarea" autocomplete="off"
                            placeholder="Descipción opcional"></textarea>
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="ml-2 mt-2 col-span-full flex">
                        <FileUploader @files-selected="this.form.media = $event" :multiple="false" />
                    </div>
                    <InputError :message="form.errors.media" />
                    <!-- buttons -->
                    <div class="md:text-right">
                        <PrimaryButton :disabled="form.processing">
                            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                            Subir
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Back from "@/Components/MyComponents/Back.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from "axios";

export default {
    data() {
        const form = useForm({
            name: null,
            type: null,
            description: null,
            company_id: this.company.id,
            media: [],
        });
        return {
            form,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        FileUploader,
        InputError,
        CancelButton,
        Back,
        Link
    },
    props: {
        company: Object,
        design_types: Array,
    },
    methods: {
        store() {
            this.form.post(route('exclusive-designs.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Archivo guardado',
                        type: 'success'
                    });

                    this.form.reset();
                }
            });
        },
    },
    mounted() {
    }
};
</script>
