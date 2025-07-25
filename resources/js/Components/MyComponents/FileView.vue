<template>
    <div @click="openFile"
        class="flex space-x-2 border border-gray-400 dark:border-grayD9 rounded-md p-2 cursor-pointer hover:border-primary">
        <figure class="h-8 w-1/5">
            <img class="object-contain" :src="getFileTypeIcon()">
        </figure>
        <div :class="deletable ? 'w-3/5' : 'w-4/5'">
            <p :title="file.file_name" class="font-bold text-xs lg:text-sm truncate">{{ file.file_name }}</p>
            <p class="text-[10px] lg:text-xs text-gray99">{{ (file.size / 1000).toFixed(2) }}KB</p>
        </div>
        <el-popconfirm v-if="deletable" confirm-button-text="Si" cancel-button-text="No" icon-color="#6F6E72"
            :title="'¿Desea eliminar el elemento seleccionado?'" @confirm="deleteFile()">
            <template #reference>
                <button @click.stop="" type="button" class="text-primary px-1 hover:scale-125">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </button>
            </template>
        </el-popconfirm>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {

        }
    },
    props: {
        file: Object,
        deletable: {
            type: Boolean,
            default: false
        },
        url: {
            type: String,
            default: null
        }
    },
    emits: ['delete-file'],
    methods: {
        getFileTypeIcon() {
            // Asocia extensiones de archivo a iconos
            const fileExtension = this.file.file_name?.split('.').pop().toLowerCase();

            switch (fileExtension) {
                case 'pdf':
                    return '/images/pdf.png';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return '/images/image.png';
                case 'mp4':
                case 'avi':
                case 'mkv':
                case 'mov':
                    return '/images/video.png';
                default:
                    return '/images/doc.png';
            }
        },
        openFile() {
            let fileUrl = this.file.original_url;

            // sobreescribir la url por defecto
            if (this.url) {
                fileUrl = this.url;
            }

            // Verificamos si la URL está presente antes de intentar abrir una nueva pestaña
            if (fileUrl) {
                // Abrir el archivo en una nueva pestaña
                window.open(fileUrl, '_blank');
            } else {
                console.error('La URL del archivo no está definida.');
                this.$notify({
                    title: "Error de servidor",
                    message: "No se pudo abrir el archivo. Probablemente no exista o está dañado",
                    type: "error",
                });
            }
        },
        async deleteFile() {
            try {
                const response = await axios.delete(route('media.delete-file', this.file.id));
                if (response.status === 200) {
                    this.$notify({
                        title: "Archivo eliminado",
                        message: "",
                        type: "success",
                    });
                    this.$emit('delete-file', this.file.id);
                }
            } catch (error) {
                console.log(error);
            }
        }
    }
}
</script>
