<template>
    <div class="h-auto py-3 px-2 border bg-[#d9d9d9] rounded-[20px] lg:rounded-lg">
        <template v-if="meetings.data.length">
            <div v-for="(meeting, index) in meetings.data" :key="meeting.id" class="lg:grid grid-cols-3 px-3 py-2 border-b border-[#a9a9a9]">
                <div>
                    <h2 class="font-bold ml-2 mb-3">{{ meeting.subject }}</h2>
                    <p class="text-sm"><i class="fa-regular fa-calendar mr-1"></i> {{ meeting.date }} </p>
                    <p class="text-sm"><i class="fa-regular fa-clock mr-1"></i> {{ meeting.start.format }} - {{ meeting.end.format }} </p>
                    <p class="text-sm"><i class="fa-solid fa-location-dot mr-1"></i> {{ meeting.location }} </p>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-5 border-t-2 border-[#cccccc] pt-2 mt-2 lg:border-none col-span-2 text-sm">
                    <p>Anfitrión:</p> <span class="lg:col-span-4 truncate">{{ meeting.user.name }}</span>
                    <p>Participantes:</p> <span class="lg:col-span-4 truncate">{{ meeting.participants?.map(objeto => objeto.name).join(', ') }}</span>
                    <p>Descripción:</p> <span class="lg:col-span-4 truncate">{{ meeting.description }}</span>
                </div>
            </div>
        </template>

        <div v-else>
            <p class="text-center text-sm">No hay reuniones próximas</p>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    props: {
        meetings: {
            type: Object,
            default: { data: [] }
        },
    }
}
</script>