<template>
    <AppLayout title="Editar tutorial / manual">
        <template #header>
            <div class="flex justify-between">
                <Back />
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Editar tutorial / manual</h2>
                </div>
            </div>
        </template>

        <!-- Form -->
        <form @submit.prevent="update">
            <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9  p-4 shadow-md space-y-4 mx-3">
                <h1 class="font-bold text-lg text-center">Edición de tutoriales y manuales</h1>
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
                <div v-if="loading">
                    <i class="fa-solid fa-spinner fa-spin text-primary mr-2"></i> cargando archivo(s)...
                </div>
                <div v-show="!loading">
                    <div v-if="form.type == 'Tutorial'" class="mt-2">
                        <InputLabel value="Imagen de portada*" class="ml-2" />
                        <FileUploader ref="cover" @files-selected="form.cover = $event[0]; updateMedia = true;"
                            :multiple="false" acceptedFormat="imagen" />
                    </div>
                    <div class="mt-2">
                        <InputLabel :value="form.type == 'Manual' ? 'Manual*' : 'Video*'" class="ml-2" />
                        <FileUploader ref="media" @files-selected="form.media = $event[0]; updateMedia = true;"
                            :multiple="false" :acceptedFormat="form.type == 'Manual' ? 'pdf' : 'Video'" />
                        <InputError :message="form.errors.media" />
                    </div>
                </div>
                <div class="mt-9 mx-3 md:text-right">
                    <PrimaryButton :disabled="form.processing">
                        Guardar cambios
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
            title: this.manual.title,
            description: this.manual.description,
            type: this.manual.type,
            media: null,
            cover: null,
        });
        return {
            form,
            updateMedia: false,
            loading: false,
        }
    },
    props: {
        manual: Object,
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
        update() {
            if (this.updateMedia) {
                this.form.post(route("manuals.update-with-media", this.manual), {
                    method: '_put',
                    onSuccess: () => {
                        this.$notify({
                            title: "Éxito",
                            message: "Se ha editado el tutorial / manual",
                            type: "success",
                        });
                    },
                });
            } else {
                this.form.media = null;
                this.form.cover = null;
                this.form.put(route("manuals.update", this.manual), {
                    onSuccess: () => {
                        this.$notify({
                            title: "Éxito",
                            message: "Se ha editado el tutorial / manual",
                            type: "success",
                        });
                    },
                });
            }
        },
        async createFileFromUrl(url, fileName, mimeType) {
            return await fetch(url)
                .then(response => response.blob())
                .then(blob => new File([blob], fileName, { type: mimeType }))
        },
        async addMedia() {
            this.loading = true;
            // agregar media al formulario
            const media = this.manual.media.find(item => item.collection_name == 'default');
            const fileMedia = await this.createFileFromUrl(media.original_url, media.file_name, media.mime_type);
            this.form.media = fileMedia;
            this.$refs.media.selectedFiles.push(fileMedia);
            //  agregar la portada
            if (this.manual.type !== 'Manual') {
                const cover = this.manual.media.find(item => item.collection_name == 'cover');
                const fileCover = await this.createFileFromUrl(cover.original_url, cover.file_name, cover.mime_type);
                this.form.cover = fileCover;
                this.$refs.cover.selectedFiles.push(fileCover);
            }
            this.loading = false;
        }
    },
    mounted() {
       this.addMedia();
    },
};
</script>
  