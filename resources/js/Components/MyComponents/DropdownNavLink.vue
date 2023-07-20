<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: String,
    as: String,
});
</script>

<template>
    <div>
        <Link :href="href" class="block rounded-lg px-2 py-[2px] text-xs leading-4 text-gray-900 hover:bg-primary hover:text-white focus:outline-none focus:bg-primary transition duration-150 ease-in-out">
            <slot />
        </Link>
    </div>
</template>
