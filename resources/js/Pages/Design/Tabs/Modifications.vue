<template>
    <div class="mx-4 my-6">
        <table v-if="design.modifications.length" class="border-separate border-spacing-x-8">
            <thead>
                <tr class="text-sm">
                    <th class="text-left ">#</th>
                    <th class="text-left ">Modificaciones</th>
                    <th class="text-left w-40">Fecha</th>
                    <th class="text-left w-64">Resultados</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(modification, index) in design.modifications" :key="index" class="text-[#373737] text-xs">
                    <td class="pb-2">
                        {{ index + 1 }}
                    </td>
                    <td @click="handleOpenModification(modification.id)" class="pb-2 hover:underline cursor-pointer">
                        {{ modification.modifications }}
                    </td>
                    <td class="pb-2 w-40">
                        {{ getDateFormtted(modification.created_at) }}
                    </td>
                    <td class="pb-2 w-80">
                        <template v-if="modification?.media?.length">
                            <p v-if="modification?.media?.length" v-for="file in modification.media" :key="file.id">
                                • <a class="hover:underline text-primary hover:text-secondary" :href="file.original_url"
                                    target="_blank">{{ file.file_name }}</a>
                            </p>
                        </template>
                        <p v-else> No se han subido archivos </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <el-empty v-else description="No hay modificaciones registradas para esta orden" />

        <DialogModal :show="showModificationsResultsModal" @close="showModificationsResultsModal = false">
            <template #title>
                <p>Resultados de modificaciones</p>
            </template>
            <template #content>
                <form @submit.prevent="storeModificationsResults" ref="myForm">
                    <div>
                        <p v-for="file in design.modifications.find(item => item.id == selectedModification)?.media"
                            :key="file.id">
                            - <a class="hover:underline text-primary hover:text-secondary" :href="file.original_url"
                                target="_blank">{{ file.file_name }}</a>
                        </p>
                        <div v-if="design.designer.id !== $page.props.auth.user.id" class="col-span-full">
                            <div>
                                <InputLabel value="Archivos de resultado*" />
                                <FileUploader @files-selected="handleFileSelection" :multiple="true" />
                                <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                                    PDF, SVG, PNG, JPG, GIF (MAX. 4 MB).
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <CancelButton @click="showModificationsResultsModal = false" :disabled="loading">
                    Cerrar
                </CancelButton>
                <PrimaryButton v-if="showModificationsResultsModal && design.designer.id == $page.props.auth.user.id"
                    @click="storeModificationsResults()" :disabled="loading">
                    Subir resultados
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
<script>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import FileUploader from '@/Components/MyComponents/FileUploader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import moment from 'moment';

export default {
    data() {
        return {
            showModificationsResultsModal: false,
            selectedModification: null,
            modificationsMedia: null,
            loading: false,
        }
    },
    props: {
        design: Object,
    },
    components: {
        DialogModal,
        PrimaryButton,
        CancelButton,
        InputLabel,
        InputError,
        FileUploader,
    },
    methods: {
        handleFileSelection(files) {
            this.modificationsMedia = files;
        },
        handleOpenModification(modificationId) {
            this.showModificationsResultsModal = true;
            this.selectedModification = modificationId;
        },
        getDateFormtted(dateTime) {
            if (!dateTime) return null;
            return moment(dateTime).format("DD MMM YYYY, hh:mmA");
        },
        async storeModificationsResults() {
            this.loading = true;
            try {
                const response = await axios.post(route('design-modifications.upload-results'), {
                    modification_id: this.selectedModification,
                    media: this.modificationsMedia
                }, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                if (response.status === 200) {
                    const index = this.design.modifications.findIndex(item => item.id == this.selectedModification);
                    this.design.modifications[index] = response.data.item;
                    this.showModificationsResultsModal = false;
                    this.$notify({
                        title: "Éxito",
                        message: "Archivos subidos",
                        type: "success",
                    });
                }
            } catch (error) {
                this.$notify({
                    title: "Error",
                    message: error.message,
                    type: "error",
                });
            } finally {
                this.loading = false;
            }
        },
    },
}
</script>