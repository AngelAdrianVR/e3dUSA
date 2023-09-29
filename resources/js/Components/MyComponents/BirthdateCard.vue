<template>
    <div class="lg:h-52 h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 grid grid-cols-3 text-xs lg:text-sm relative">
        <div class="my-3 col-start-2 col-span-2">
            <h3 class="text-center text-gray-700">Cumpleaños <i class="fa-solid fa-cake-candles ml-2"></i></h3>
        </div>
        <div v-if="users?.length" class="mb-28 overflow-y-scroll h-full w-full col-start-2 col-span-2">
            <p class="font-bold text-center">¡Felíz Cumpleaños te desea la familia Emblems 3D!</p>
            <ul class="grid lg:grid-cols-2 grid-cols-1 gap-x-12 gap-y-3 lg:mt-6 mt-2">
                <li v-for="(user, index) in users" :key="index" class="flex flex-col items-center text-[11px] leading-normal">
                    <i class="fa-regular fa-circle-user"></i>
                    <strong>{{ user.employee_properties.birthdate.formatted }}</strong>
                    <p>{{ user.name }}</p>
                    <p>{{ user.employee_properties.job_position }}</p>
                </li>
            </ul>
        </div>
        <img class="lg:h-60 h-44 absolute" src="@/../../public/images/globos.png">
        <p v-if="!users?.length" class="h-full w-full col-start-2 col-span-2 text-center text-primary">No hay cumpleaños hoy</p>
    </div>
</template>
<script>
export default {
    components: {
    },
    props: {
        users: Array,
    }
}
</script>