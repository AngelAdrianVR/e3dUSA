<template>
    <div class="lg:h-52 h-44 bg-[#D9D9D9] dark:bg-[#202020] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm grid grid-cols-3 relative transition-all ease-linear duration-500">
        <div class="my-3 col-start-2 col-span-2 h-full">
            <h3 class="text-center text-gray-700 dark:text-white">Agregados Recientemente <i class="fa-solid fa-user-plus ml-2"></i></h3>
        </div>
        <div v-if="users?.length" class="mb-28 lg:px-2 overflow-y-scroll h-full w-full col-start-2 col-span-2">
            <p class="font-bold text-center">¡Bienvenid@ a nuestro equipo Emblems 3D!</p>
            <ul class="text-xs grid lg:grid-cols-2 grid-cols-1 gap-x-14 gap-y-3 lg:mt-6 mt-2">
                <li v-for="(user, index) in users" :key="index" class="flex flex-col">
                    <div class="flex space-x-2 items-center">
                        <i class="fa-regular fa-circle-user"></i>
                        <span>{{ user.name }}</span>
                    </div>
                    <div class="flex space-x-2 items-center">
                        <i class="fa-solid fa-user-tie"></i>
                        <span>{{ user.employee_properties.job_position }}</span>
                    </div>
                    <div class="flex space-x-2 items-center">
                        <i class="fa-regular fa-envelope"></i>
                        <span>{{ user.email }}</span>
                    </div>
                </li>
            </ul>
        </div>
        <img class="lg:h-24 h-16 w-auto absolute top-12 left-5" src="@/../../public/images/manos.png">
        <p v-if="!users?.length" class="h-full w-full col-start-2 col-span-2 text-center text-primary">No hay agregados recientemente</p>
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