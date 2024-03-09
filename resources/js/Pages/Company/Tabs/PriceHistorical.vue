<template>
    <section class="p-7 w-full mx-auto my-4">
        <div class="flex justify-between items-center">
            <p class="text-secondary">Historial de precios</p>
        </div>
        <div v-if="company.catalogProducts?.length > 0" class="mt-5 mx-auto">
            <PriceHistoryTable :catalogProductsCompany="company.catalogProducts" />
        </div>
        <div v-else class="flex flex-col text-center justify-center">
            <p class="text-sm text-center text-gray-400">Este cliente no cuenta con productos registrados</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import PriceHistoryTable from "@/Components/MyComponents/Company/PriceHistoryTable.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        PriceHistoryTable,
    },
    methods: {
    },
}
</script>