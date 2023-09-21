<template>
    <div class="lg:h-52 h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:py-2 py-2 px-8 text-xs lg:text-sm">
        <div class="my-3 col-start-2 col-span-2">
            <h3 class="text-center text-gray-700">Citas próximas <i class="fa-regular fa-calendar-check ml-2"></i></h3>
        </div>
        <div v-if="dates.length" class="h-full">
            <div class="h-2/3 overflow-y-scroll">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="text-xs">
                            <th>Contacto</th>
                            <th class="text-start">Fecha</th>
                            <th class="text-start">Hora</th>
                            <th class="text-start">Cita</th>
                            <th class="text-start">Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="date in dates" :key="date.id" class="text-xs w-full">
                            <td class="w-1/5 py-1"><i class="fa-regular fa-user mr-2"></i> {{ date.contact }}</td>
                            <td class="w-1/5">{{ date.date }}</td>
                            <td class="w-1/5">{{ date.time }}</td>
                            <td class="w-1/5">{{ date.type }}</td>
                            <td class="w-1/5 truncate">{{ date.reason }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mx-6">
                <button class="text-primary text-xs">Ver todas</button>
            </div>
        </div>
        <p v-else class="text-gray-400 text-center mt-8">No hay citas proximas registradas</p>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    components: {
    },
    props: {
        dates: Array,
    }
}
</script>