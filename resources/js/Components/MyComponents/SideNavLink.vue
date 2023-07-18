<script setup>

import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    dropdown: Boolean,

    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-[#D9D9D9]', 'px-2'],
    },
});

let open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        '48': 'w-48',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left';
    }

    if (props.align === 'right') {
        return 'origin-top-right';
    }

    return 'origin-top-right';
});

const classes = computed(() => {
    return props.active
        ? 'flex flex-col items-center py-3 px-2 bg-[#CCCCCC] font-medium text-[10px] w-full text-[#D90537] rounded-lg relative'
        : 'flex flex-col items-center text-[10px] font-medium py-3 px-2 hover:bg-[#CCCCCC] focus:bg-[#CCCCCC] outline-none w-full cursor-pointer relative';
});
</script>

<template>
    <div v-if="props.dropdown">
        <div>
            <div @click="open = !open" :class="classes">
                <slot name="trigger" />
                <!-- <i class="fa-solid fa-angle-right absolute mt-2 right-2 text-gray-600" :class="props.active ? 'text-[#D90537]' : ''"></i> -->
            </div>

            <!-- Full Screen Dropdown Overlay -->
            <div v-show="open" class="fixed inset-0 z-40" @click="open = false" />

            <transition enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95">
                <div v-show="open" class="absolute z-50 rounded-tr-xl rounded-br-xl origin-top-right -right-[192px] translate-y-[-41px]" :class="[widthClass, alignmentClasses]"
                    style="display: none;" @click="open = false">
                    <div class="rounded-tr-xl rounded-br-xl" :class="contentClasses">
                        <slot name="content" />
                    </div>
                </div>
            </transition>
        </div>
    </div>

    <Link v-else :href="href" :class="classes">
    <slot name="trigger" />
    </Link>
</template>
