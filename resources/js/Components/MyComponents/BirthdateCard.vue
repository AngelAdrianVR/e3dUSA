<template>
    <div class="lg:h-64 h-44 bg-[#D9D9D9] dark:bg-[#202020] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 grid grid-cols-3 text-xs lg:text-sm relative transition-all ease-linear duration-500">
        <div class="my-3 col-start-2 col-span-2">
            <h3 class="text-center text-gray-700 dark:text-white">Cumpleaños de este mes <i class="fa-solid fa-cake-candles ml-2"></i></h3>
        </div>
        <div v-if="users?.length" class="mb-28 overflow-y-scroll h-full w-full col-start-2 col-span-2">
            <p class="font-bold text-center">¡Felíz Cumpleaños te desea la familia Emblems 3D!</p>
            <ul class="lg:mt-6 mt-2">
                <li v-for="(user, index) in users" :key="index" class="grid grid-cols-3 gap-3 text-[11px] leading-normal">
                    <div class="flex items-center">
                        <i class="fa-regular fa-circle-user mr-2"></i>
                        <p>{{ user.name }}</p>
                    </div>
                    <p>{{ user.employee_properties.department }}</p>
                    <strong>{{ user.employee_properties.birthdate.formatted }}</strong>
                </li>
            </ul>
        </div>
        <img class="lg:h-60 h-44 absolute" src="@/../../public/images/globos.png">
        <p v-if="!users?.length" class="h-full w-full col-start-2 col-span-2 text-center text-primary">No hay cumpleaños este mes</p>
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