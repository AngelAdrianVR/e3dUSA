<template>
    <section class="p-7">
        <p class="text-secondary">Productos registrados</p>
        <div v-if="company.catalogProducts?.length" class="grid lg:grid-cols-4 md:grid-cols-2 mt-7 gap-10">
            <CompanyProductCard v-for="company_product in company.catalogProducts" :key="company_product.id"
                :company_product="company_product" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
            <p class="text-sm text-center text-gray-400">No hay productos registrados en este cliente</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import CompanyProductCard from "@/Components/MyComponents/CompanyProductCard.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        CompanyProductCard,
    },
    methods: {
    },
}
</script>