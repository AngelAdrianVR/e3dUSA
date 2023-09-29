<template>
    <div class="lg:h-52 h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 text-xs lg:text-sm grid grid-cols-3 relative">
        <div class="my-3 col-start-2 col-span-2 h-full">
            <h3 class="text-center text-gray-700">
                Aniversarios
                <i class="fa-solid fa-champagne-glasses ml-2"></i>
            </h3>
        </div>
        <div v-if="users?.length" class="mb-28 px-2 overflow-y-scroll h-full w-full col-start-2 col-span-2">
            <p class="font-bold text-center">¡Gracias por un año más de compromiso en la familia Emblems 3D!</p>
            <ul class="overflow-y-scroll grid lg:grid-cols-2 grid-cols-1 gap-x-14 gap-y-3 lg:mt-6 mt-2 text-xs">
                <li v-for="(user, index) in users" :key="index" class="flex flex-col items-center">
                    <i class="fa-regular fa-circle-user"></i>
                    <p>{{ user.name }}</p>
                    <p>{{ user.employee_properties.job_position }}</p>
                </li>
            </ul>
        </div>
        <div class="px-2 h-4/6 absolute lg:top-12 lg:left-5 top-14 left-1">
            <div class="relative">
                <img class="lg:h-32 lg:w-32 h-16 z-20" src="@/../../public/images/aniversario.png">
                <strong v-if="users[0]?.years" class="lg:text-6xl text-4xl z-10 absolute lg:left-[49px] lg:top-[18px] left-[46px] top-1">{{ users[0].years }}</strong>
            </div>
        </div>
        <p v-if="!users?.length" class="h-full w-full col-start-2 col-span-2 text-center text-primary">No hay
            aniversarios hoy</p>
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
