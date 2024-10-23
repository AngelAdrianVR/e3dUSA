<template>
    <Link :href="href"
        class="hidden h-32 border-[#D90537] bg-[#D9D9D9] dark:bg-[#202020] bg-opacity-60 rounded-xl lg:grid grid-cols-3 hover:border-2 hover:scale-105 transition ease-in duration-300 cursor-pointer"
        :class="counter ? 'bg-opacity-100 text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'">
    <div class="flex flex-col justify-center items-center col-start-1 col-span-2 px-3">
        <p class="text-4xl font-bold">{{ counter }}</p>
        <p class="text-[16px] text-center">{{ title }}</p>
    </div>
    <div class="col-start-3 px-3 mx-auto my-auto text-5xl">
        <i :class="icon"></i>
    </div>
    </Link>

    <Link :href="href"
        class="lg:hidden h-32 border-[#D90537] bg-[#D9D9D9] bg-opacity-60 text-gray-400 rounded-[30px] flex flex-col justify-center items-center p-3 hover:border-2 hover:scale-105 transition ease-in duration-300 cursor-pointer"
        :class="counter ? 'bg-opacity-100 text-gray-900' : ''">
    <div class="flex justify-center space-x-3 items-center px-3">
        <p class="text-3xl font-bold">{{ counter }}</p>
        <div class="text-2xl">
            <i :class="icon"></i>
        </div>
    </div>
    <p class="text-sm text-center leading-normal">{{ title }}</p>
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    props: {
        title: String,
        counter: Number,
        icon: String,
        href: String,
    }
}
</script>
