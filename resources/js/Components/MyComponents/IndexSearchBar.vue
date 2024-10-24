<template>
    <div class="flex items-center mb-3">
        <input v-model="inputSearch" type="search" @keyup.enter="$emit('search', inputSearch)"
            class="border-primary focus:border-primary bg-transparent focus:ring-0 rounded-l-md h-7 text-gray-600 dark:text-white shadow-sm shadow-gray-400 placeholder:text-sm text-sm"
            placeholder="Buscar" />
        <PrimaryButton @click="$emit('search', inputSearch)" class="!rounded-l-none !h-7 " type="primary">
            <i class="fa-solid fa-magnifying-glass"></i>
        </PrimaryButton>
    </div>
</template>
<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    data() {
        return {
            inputSearch: null,
        }
    },
    emits: ['search'],
    components: {
        PrimaryButton,
    }
}
</script>