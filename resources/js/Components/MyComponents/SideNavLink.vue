<script setup>

import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'flex flex-col items-center py-2 px-2 bg-[#CCCCCC] font-semibold text-sm leading-5 w-full'
        : 'flex flex-col items-center text-sm font-medium py-2 px-2 hover:bg-[#CCCCCC] w-full';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
