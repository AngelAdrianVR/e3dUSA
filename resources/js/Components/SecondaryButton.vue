<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-secondary border border-transparent rounded-full font-semibold text-xs text-gray-100 tracking-widest shadow-sm hover:bg-secondary-light focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-200">
        <slot />
    </button>
</template>
