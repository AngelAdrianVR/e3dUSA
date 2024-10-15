<template>
    <div class="w-full">
        <input type="file" ref="fileInput" style="display: none" @change="handleFileInputChange" :multiple="multiple"
            :accept="getAcceptedFormat()" />
        <button type="button" @click="openFileBrowser" class="w-full px-3 py-[2px] rounded-md bg-[#CCCCCC]">
            <p class="flex items-center justify-between text-sm text-[#9A9A9A]">
                <span v-if="selectedFiles.length"class="text-[#373737]">
                    {{ selectedFiles.length }} {{ selectedFiles.length == 1 ? 'archivo seleccionado' : 'archivos seleccionados' }}
                </span>
                <span v-else>Clic para seleccionar {{ multiple ? 'los archivos' : 'el archivo' }}</span>
                <span class="text-primary w-10 h-7 flex items-center justify-center border-2 border-primary rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4"
                        stroke="currentColor" class="size-[22px]">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                    </svg>
                </span>
            </p>
        </button>

        <div v-if="selectedFiles.length">
            <ul class="lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5 text-sm mt-2" :class="multiple ? 'grid' : null">
                <li v-for="(file, index) in selectedFiles" :key="index" class="flex items-center justify-between px-2">
                    <p class="flex items-center">
                        <i :class="getFileTypeIcon(file.name)"></i>
                        <span class="ml-2">{{ file.name }}</span>
                    </p>
                    <button type="button" @click="removeFile(index)"><i class="fa-solid fa-xmark text-xs"></i></button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedFiles: [],
        };
    },
    props: {
        multiple: {
            type: Boolean,
            default: true
        },
        acceptedFormat: {
            type: String,
            default: 'Todo'
        },
    },
    emits: ['files-selected'],
    methods: {
        getAcceptedFormat() {
            const format = this.acceptedFormat.toLowerCase();
            if (format === 'video') return 'video/*';
            else if (format === 'pdf') return 'application/pdf';
            else if (format === 'imagen') return 'image/*';
            else '*/*';
        },
        openFileBrowser() {
            // Simula el clic en el input file al hacer clic en el botón personalizado
            this.$refs.fileInput.click();
        },
        handleFileInputChange(event) {
            // Agrega los archivos seleccionados a la lista existente
            const newSelectedFiles = Array.from(event.target.files);
            if (this.multiple) {
                this.selectedFiles = [...this.selectedFiles, ...newSelectedFiles];
            } else {
                this.selectedFiles = [...newSelectedFiles];
            }
            this.$emit('files-selected', this.selectedFiles);
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1); // Elimina el archivo de la lista por su índice
            this.$emit('files-selected', this.selectedFiles); // Emitir la lista actualizada después de la eliminación
        },
        getFileTypeIcon(fileName) {
            // Asocia extensiones de archivo a iconos
            const fileExtension = fileName?.split('.').pop().toLowerCase();
            switch (fileExtension) {
                case 'pdf':
                    return 'fa-regular fa-file-pdf text-red-700';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'fa-regular fa-image text-blue-300';
                case 'mp4':
                case 'avi':
                case 'mkv':
                case 'mov':
                    return 'fa-regular fa-file-video text-sky-400';
                default:
                    return 'fa-regular fa-file-lines';
            }
        },
    },
};
</script>