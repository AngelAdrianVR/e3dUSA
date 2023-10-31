<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-transparent border-2 border-[#D90537] rounded-full font-semibold text-xs text-black tracking-widest focus:bg-transparent active:bg-transparent focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-200 disabled:opacity-25 disabled:cursor-not-allowed">
        <slot />
    </button>
</template>
