<template>
    <div class="h-auto py-3 px-2 border bg-[#d9d9d9] rounded-lg">
        <template v-if="meetings.data.length">
            <div v-for="(meeting, index) in meetings.data" :key="meeting.id" class="grid grid-cols-3 px-3 py-2">
                <div>
                    <h2 class="font-bold ml-2 mb-3">{{ meeting.subject }}</h2>
                    <p class="text-sm"><i class="fa-regular fa-calendar mr-1"></i> {{ meeting.date }} </p>
                    <p class="text-sm"><i class="fa-regular fa-clock mr-1"></i> {{ meeting.start }} - {{ meeting.end }} </p>
                    <p class="text-sm"><i class="fa-solid fa-location-dot mr-1"></i> {{ meeting.location }} </p>
                </div>
                <div class="grid grid-cols-5 col-span-2 text-sm">
                    <p>Anfitrión:</p> <span class="col-span-4 truncate">{{ meeting.user.name }}</span>
                    <p>Participantes:</p> <span class="col-span-4 truncate">{{ meeting.users.map(objeto => objeto.name).join(', ') }}</span>
                    <p>Descripción:</p> <span class="col-span-4 truncate">{{ meeting.description }}</span>
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