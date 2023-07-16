<template>
    <div class="h-52 bg-[#D9D9D9] rounded-xl pb-2 py-5 grid grid-cols-3 relative">
        <h3 class="text-center text-gray-700 my-3 col-start-2 col-span-2">Desempeño de Producción <i
                class="fa-solid fa-medal ml-2"></i></h3>
        <div class="mb-28 px-2 w-full col-start-2 col-span-2">
            <p class="text-sm text-center font-bold mb-3">¡Felicitaciones por tu excelente desempeño semanal!</p>
            <ul class="text-sm overflow-y-scroll ">
                <li v-for="(user, index) in users" :key="index">
                    <strong class="text-primary mr-4">{{ index + 1 }}</strong>
                    {{ user.name }}
                </li>
            </ul>
        </div>
        <img class="h-32 w-32 absolute top-12 left-5" src="@/../../public/images/star.png" alt="">
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