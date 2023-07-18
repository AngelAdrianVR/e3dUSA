<template>
    <div
        class="lg:h-52 h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 grid grid-cols-3 text-xs lg:text-sm relative">
        <div class="my-3 col-start-2 col-span-2">
            <h3 class="text-center text-gray-700">Cumpleaños <i class="fa-solid fa-cake-candles ml-2"></i></h3>
        </div>
        <div v-if="contacts?.length" class="mb-28 px-2 overflow-y-scroll h-full w-full col-start-2 col-span-2">
            <p class="font-bold text-center">¡Felíz Cumpleaños te desea la familia Emblems 3D!</p>
            <ul class="text-xs overflow-y-scroll grid lg:grid-cols-2 grid-cols-1 gap-x-14 gap-y-3 lg:mt-6 mt-2">
                <li v-for="(contact, index) in contacts" :key="index" class="flex flex-col">
                    <strong>{{ contact.birthdate_day }} {{ months[contact.birthdate_month + 1] }}</strong>
                    <div class="flex space-x-2 items-center">
                        <i class="fa-regular fa-circle-user"></i>
                        <span>{{ contact.name }}</span>
                    </div>
                    <div class="flex space-x-2 items-center">
                        <i class="fa-regular fa-building"></i>
                        <span>{{ contact.contactable.name }}</span>
                    </div>
                    <div class="flex space-x-2 items-center">
                        <i class="fa-regular fa-envelope"></i>
                        <span>{{ contact.email }}</span>
                    </div>
                </li>
            </ul>
        </div>
        <img class="lg:h-60 h-44 absolute" src="@/../../public/images/globos.png">
        <p v-if="!contacts?.length" class="h-full w-full col-start-2 col-span-2 text-center text-primary">No hay cumpleaños
            hoy</p>
    </div>
</template>
<script>
export default {
    data() {
        return {
            months: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
            ],
        }
    },
    components: {
    },
    props: {
        contacts: Array,
    }
}
</script>