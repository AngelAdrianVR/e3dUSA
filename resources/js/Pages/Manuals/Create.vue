<template>
    <AppLayout title="Crear tutorial / manual">
        <template #header>
            <div class="flex justify-between">
                <Back />
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Crear tutorial / manual</h2>
                </div>
            </div>
        </template>

        <!-- Form -->
        <form @submit.prevent="store">
            <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg lg:p-9  p-4 shadow-md space-y-4 mx-3">
                <h1 class="font-bold text-lg text-center">Creación de tutoriales y manuales</h1>
                <div class="flex justify-center items-center space-x-12">
                    <div class="flex items-center">
                        <input v-model="form.type" value="Tutorial"
                            class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-black bg-transparent"
                            type="radio" name="type" />
                        <p class="ml-3">Tutorial</p>
                    </div>
                    <div class="flex items-center">
                        <input v-model="form.type" value="Manual"
                            class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-black bg-transparent"
                            type="radio" name="type" />
                        <p class="ml-3">Manual</p>
                    </div>
                    <InputError :message="form.errors.type" />
                </div>
                <div class="mt-2">
                    <InputLabel value="Título*" class="ml-2" />
                    <input v-model="form.title" type="text" class="input" placeholder="Escribe aquí el título" />
                    <InputError :message="form.errors.title" />
                </div>
                <div class="mt-2">
                    <InputLabel value="Objetivo o descripción*" class="ml-2" />
                    <textarea v-model="form.description" class="textarea" rows="5"
                        placeholder="Escribe el objetivo o una descripción breve para el tutorial"></textarea>
                    <InputError :message="form.errors.description" />
                </div>
                <div v-if="form.type == 'Tutorial'" class="mt-2">
                    <InputLabel value="Imagen de portada*" class="ml-2" />
                    <FileUploader @files-selected="this.form.cover = $event[0]" :multiple="false" acceptedFormat="imagen" />
                </div>
                <div class="mt-2">
                    <InputLabel :value="form.type == 'Manual' ? 'Manual*' : 'Video*'" class="ml-2" />
                    <FileUploader @files-selected="this.form.media = $event[0]" :multiple="false"
                        :acceptedFormat="form.type == 'Manual' ? 'pdf' : 'Video'" />
                    <InputError :message="form.errors.media" />
                </div>
                <div class="mt-9 mx-3 md:text-right">
                    <PrimaryButton :disabled="form.processing">
                        <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                        Guardar
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
  
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            title: null,
            description: null,
            type: 'Tutorial',
            media: null,
            cover: null,
        });
        return {
            form,
        }
    },
    components: {
        AppLayout,
        PrimaryButton,
        CancelButton,
        FileUploader,
        InputError,
        InputLabel,
        Back,
        Link
    },
    methods: {
        store() {
            this.form.post(route("manuals.store"), {
                onSuccess: () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha registrado el nuevo tutorial / manual",
                        type: "success",
                    });
                },
            });
        },
        // async getProduction() {
        //     try {
        //         const response = await axios.get(route('manuals.get-production', this.form.production_id));
        //         if (response.status === 200) {
        //             this.productionObj = response.data.item;

        //             // Limpiar el array antes de agregar nuevas instancias
        //             this.form.product_inspection = [];

        //             // Iterar sobre los productos y agregar instancias al array
        //             this.productionObj.catalog_product_company_sales.forEach((item) => {
        //                 this.form.product_inspection.push({
        //                     name: item.catalog_product_company?.catalog_product?.name,
        //                     status: null,
        //                     progress: null,
        //                     total_pieces: parseInt(item.quantity),
        //                     pieces: null,
        //                     stop_explanation: null,
        //                     problem_description: null,
        //                     corrective_actions: null,
        //                     notes: null,
        //                     // media: []
        //                 });
        //             });
        //         }

        //     } catch (error) {
        //         console.log(error);

        //         this.$notify({
        //             title: "Error de servidor",
        //             message: "No se encontró la orden de producción",
        //             type: "error",
        //         });
        //     }
        // },
    }
};
</script>
  