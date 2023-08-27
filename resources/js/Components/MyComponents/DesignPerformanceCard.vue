<template>
    <div class="lg:h-56 h-52 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 grid grid-cols-3 relative text-xs lg:text-sm">
        <h3 class="text-center text-gray-700 my-3 col-start-2 col-span-2">Desempeño de Diseño <i
                class="fa-solid fa-medal ml-2"></i></h3>
        <div v-if="users?.length" class="mb-28 px-2 w-full h-full col-start-2 col-span-2">
            <p class="text-center font-bold mb-3">¡Felicitaciones por tu excelente desempeño semanal!</p>
            <div class="text-xs col-span-2">
                <div class=" w-full" v-for="(user, index) in users" :key="index">
                    <p><strong class="text-primary mr-4">{{ index + 1 }}</strong>{{ user }} <div class="h-2 bg-green-500 rounded-full mb-2"></div></p>
                </div>
            </div>
        </div>
        <img class="lg:h-32 h-14 absolute top-12 left-5" src="@/../../public/images/star.png">
        <p v-if="!users?.length" class="h-full w-full col-start-2 col-span-2 text-center text-primary">No hay usuarios para mostrar</p>
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