<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" type="submit" class="block w-full px-4 py-1 dark:text-white text-left text-sm leading-4 hover:bg-primary rounded-lg hover:text-white focus:outline-none focus:opacity-75 transition duration-150 ease-in-out">
            <slot />
        </button>

        <a v-else-if="as =='a'" :href="href" class="block px-4 py-1 text-sm leading-4 dark:text-white hover:bg-primary rounded-lg hover:text-white focus:outline-none focus:opacity-75 transition duration-150 ease-in-out">
            <slot />
        </a>

        <Link v-else :href="href" class="block px-4 py-1 text-sm leading-4 dark:text-white hover:bg-primary rounded-lg hover:text-white focus:outline-none focus:opacity-75 transition duration-150 ease-in-out">
            <slot />
        </Link>
    </div>
</template>
