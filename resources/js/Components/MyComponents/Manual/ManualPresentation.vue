<template>
    <article @click="openFile"
        class="lg:grid grid-cols-5 gap-x-6 border-t border-gray1 py-5 cursor-pointer hover:bg-[#ededed] px-2">
        <figure class="rounded-[10px] bg-[#d9d9d9] h-44 lg:h-32 flex items-center justify-center relative mb-3 lg:mb-0">
            <div v-if="loading"
                class="absolute inset-0 rounded-[10px] bg-gray-700 opacity-80 flex items-center justify-center text-white">
                <i class="fa-solid fa-spinner animate-spin text-xl"></i>
            </div>
            <div v-else
                class="absolute inset-0 rounded-[10px] bg-gray-700 opacity-80 flex items-center justify-center text-white">
                <svg v-if="manual.type == 'Manual'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <i v-else class="fa-regular fa-circle-play text-xl"></i>
            </div>
            <img v-if="manual.type == 'Manual'" src="@/../../public/images/pdf.png"
                class="h-full object-cover rounded-[10px]" @load="imageLoaded">
            <img v-else :src="manual.media.find(item => item.collection_name == 'cover')?.original_url"
                class="h-full object-cover rounded-[10px]" @load="imageLoaded">

        </figure>
        <div class="lg:col-span-4 flex flex-col">
            <header class="flex items-center justify-between mb-2">
                <h1 class="font-bold">{{ manual.title }}</h1>
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                    class="flex items-center space-x-2 text-sm rounded-full">
                    <img class="h-5 w-5 rounded-full object-cover border" :src="manual.user.profile_photo_url"
                        :alt="manual.user.name" />
                    <small class="text-gray1">{{ manual.user.name }}</small>
                </div>
            </header>
            <p>{{ manual.description }}</p>
            <footer class="flex justify-between mt-auto">
                <p class="flex items-center space-x-2 text-gray1">
                    <small class="">{{ formatDate(manual.created_at) }}</small>
                    <i class="fa-solid fa-circle text-[4px]"></i>
                    <small class="font-bold">{{ manual.type }}</small>
                </p>
                <div class="flex items-center space-x-6">
                    <button v-if="$page.props.auth.user.permissions.includes('Eliminar manuales')" @click.stop="showConfirmationModal = true" class="text-primary"><i class="fa-regular fa-trash-can"></i></button>
                    <ThirthButton v-if="$page.props.auth.user.permissions.includes('Editar manuales')" @click.stop="edit"
                        class="!rounded-[5px] !px-2 !py-1">Editar</ThirthButton>
                </div>
            </footer>
        </div>
    </article>
    <ConfirmationModal :show="showConfirmationModal" @close="showConfirmationModal = false">
        <template #title>
            Eliminar elemento
        </template>
        <template #content>
                Esta acción es permanente y no se podrá revertir. ¿Continuar con la eliminación?
        </template>
        <template #footer>
            <CancelButton @click="showConfirmationModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Si, eliminar</PrimaryButton>
        </template>
    </ConfirmationModal>
</template>
<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

export default {
    data() {
        return {
            loading: true,
            showConfirmationModal: false,
        };
    },
    methods: {
        imageLoaded() {
            this.loading = false;
        },
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMMM, Y', { locale: es }); // Formato personalizado
        },
        openFile() {
            const url = this.manual.media.find(item => item.collection_name == 'default')?.original_url;
            window.open(url, '_blank');
        },
        edit() {
            this.$inertia.get(route('manuals.edit', this.manual));
        },
        deleteItem() {
            this.$inertia.delete(route('manuals.destroy', this.manual));
        },
    },
    components: {
        ThirthButton,
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
    },
    props: {
        manual: Object,
    }
}
</script>