<template>
    <section class="p-7 dark:text-white">
        <p class="text-secondary text-lg">Productos que se le vende al cliente actualmente ({{ company.catalogProducts?.length }})</p>
        <div v-if="company.catalogProducts?.length" class="grid lg:grid-cols-4 md:grid-cols-2 mt-7 gap-10">
            <CompanyProductCard v-for="company_product in company.catalogProducts" :key="company_product.id"
                :company_product="company_product" />
        </div>
        <p class="text-secondary mt-8 text-lg">Productos sugeridos ({{ company.suggested_products?.length }})</p>
        <div v-if="company.suggested_products?.length" class="grid lg:grid-cols-4 md:grid-cols-2 mt-7 gap-10">
            <SuggestedProductCard v-for="suggested in suggestedProducts" :key="suggested"
                :suggested="suggested" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
            <p class="text-sm text-center text-gray-400">No hay productos registrados en este cliente</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import CompanyProductCard from "@/Components/MyComponents/CompanyProductCard.vue";
import SuggestedProductCard from "@/Components/MyComponents/SuggestedProductCard.vue";
import axios from "axios";

export default {
    data() {
        return {
            suggestedProducts: [],
        }
    },
    props: {
        company: Object,
    },
    components: {
        CompanyProductCard,
        SuggestedProductCard,
    },
    methods: {
        async fetchSuggestedProducts() {
            try {
                const response = await axios.post(route('catalog-products.get-by-ids'), {ids: this.company.suggested_products});

                if (response.status === 200) {
                    this.suggestedProducts = response.data.items;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this. fetchSuggestedProducts();
    }
}
</script>