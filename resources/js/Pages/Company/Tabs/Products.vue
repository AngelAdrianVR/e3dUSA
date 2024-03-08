<template>
    <section class="p-7">
        <p class="text-secondary">Productos registrados</p>
        <div class="grid lg:grid-cols-4 md:grid-cols-2 mt-7 gap-10">
            <CompanyProductCard v-for="company_product in company.catalogProducts" :key="company_product.id"
                :company_product="company_product" />
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