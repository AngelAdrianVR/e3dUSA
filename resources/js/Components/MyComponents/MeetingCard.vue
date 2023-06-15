<template>
    <div class="w-full md:w-5/6 h-auto py-3 px-2 border border-black rounded-lg">
            <div v-if="meeting" class="grid grid-cols-3 px-3 py-2">
            <h2 class="font-bold ml-2">{{ meeting.title }}</h2>
                <div class="col-start-1">
                    <p class="text-gray-500 text-sm"><i class="fa-regular fa-calendar mr-1"></i> {{ meeting.date }} </p> 
                    <p class="text-gray-500 text-sm"><i class="fa-regular fa-clock mr-1"></i> {{ meeting.hour }} </p> 
                    <p class="text-gray-500 text-sm"><i class="fa-solid fa-location-dot mr-1"></i> {{ meeting.location }} </p> 
                </div>
                <div class="col-start-2 col-span-2">
                    <p class="text-gray-500 text-sm">Anfitrión: <span class="ml-2">{{ meeting.host }}</span></p> 
                    <p class="text-gray-500 text-sm">Participantes: <span class="ml-2">{{ meeting.participants }}</span></p> 
                    <p class="text-gray-500 text-sm">Descripción: <span class="ml-2">{{ meeting.description }}</span></p> 
                </div>
            </div>

            <div v-else>
                <p class="text-center text-sm">No hay reuniones próximas</p>
            </div>
    </div>
</template>

<script>
export default {
    data(){
        return{

        }
    },
    props:{
        meeting: Object,
    }
}
</script>