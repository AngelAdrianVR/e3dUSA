<script setup>
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="px-6 py-4">
            <div class="flex justify-end">
                <button class="flex items-center justify-center size-5 rounded-full hover:bg-gray-400/50 hover:text-primary cursor-pointer">
                    <i @click="close"
                        class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
            <div class="text-lg font-bold text-center text-gray-900 dark:text-white">
                <slot name="title" />
            </div>

            <div class="mt-1 text-sm text-gray-600 dark:text-white">
                <slot name="content" />
            </div>
        </div>

        <div class="flex flex-row justify-end space-x-2 px-6 py-3 text-right bg-gray-400/40">
            <slot name="footer" />
        </div>
    </Modal>
</template>
