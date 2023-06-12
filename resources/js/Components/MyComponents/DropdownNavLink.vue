<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
});
</script>

<template>
    <div>
        <Link :href="href" class="block px-4 py-2 text-sm leading-5 text-gray-900 hover:bg-primary hover:text-white focus:outline-none focus:bg-primary transition duration-150 ease-in-out">
            <slot />
        </Link>
    </div>
</template>
