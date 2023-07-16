<template>
    <div class="h-52 bg-[#D9D9D9] rounded-xl pb-2 py-5 grid grid-cols-3 relative">
        <div class="my-3 col-start-2 col-span-2 h-full">
            <h3 class="text-center text-gray-700">
                Aniversarios
                <i class="fa-solid fa-champagne-glasses ml-2"></i>
            </h3>
        </div>
        <div class="mb-28 px-2 overflow-y-scroll h-4/6 w-full col-start-2 col-span-2">
            <p class="text-sm font-bold">
                ¡Gracias por un año más de compromiso en la familia Emblems 3D!
            </p>
        </div>
        <div class="px-2 h-4/6 w-full absolute top-12 left-5">
            <div class="relative">
                <img class="h-32 w-32 z-20" src="@/../../public/images/aniversario.png">
                <strong class="text-6xl z-10 absolute left-[49px] top-[18px]">{{ years }}</strong>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    components: {
    },
    props: {
        years: Number,
    }
}
</script>
