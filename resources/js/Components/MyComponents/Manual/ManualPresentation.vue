<template>
    <a :href="manual.media.find(item => item.collection_name == 'default')?.original_url" target="_blank" class="grid grid-cols-4 gap-x-6 border-t border-gray1 py-5 cursor-pointer hover:bg-[#ededed] px-2">
        <figure class="rounded-[3px] bg-[#d9d9d9] h-44 flex items-center justify-center relative">
            <img v-if="manual.type == 'Manual'" src="@/../../public/images/pdf.png" class="h-full object-cover">
            <img v-else :src="manual.media.find(item => item.collection_name == 'cover')?.original_url">
            <div class="absolute inset-0 rounded-[3px] bg-gray-700 opacity-80 flex items-center justify-center text-white">
                <svg v-if="manual.type == 'Manual'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <i v-else class="fa-regular fa-circle-play text-xl"></i>
            </div>
        </figure>
        <div class="col-span-3 flex flex-col">
            <header class="flex items-center justify-between mb-2">
                <h1 class="font-bold">{{ manual.title }}</h1>
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                    class="flex items-center space-x-2 text-sm rounded-full">
                    <img class="h-8 w-8 rounded-full object-cover border" :src="manual.user.profile_photo_url"
                        :alt="manual.user.name" />
                    <span class="text-gray1">{{ manual.user.name }}</span>
                </div>
            </header>
            <p>{{ manual.description }}</p>
            <small class="text-gray1 mt-auto">{{ formatDate(manual.created_at) }}</small>
        </div>
    </a>
</template>
<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    methods: {
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMMM, Y', { locale: es }); // Formato personalizado
        },
    },
    props: {
        manual: Object,
    }
}
</script>