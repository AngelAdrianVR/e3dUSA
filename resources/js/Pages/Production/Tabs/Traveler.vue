<template>
    <div>
        <ul>
            <li @click="openTemplate(item.id)" v-for="(item, index) in sale.catalogProductCompanySales" :key="index"
                class="hover:underline cursor-pointer">
                <template v-if="item.catalog_product_company.catalog_product.part_number.split('-')[1] == 'LL'">
                    {{ item.catalog_product_company.catalog_product.part_number }}
                </template>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    props: {
        sale: Object,
    },
    methods: {
        openTemplate(cpcs) {
            const url = route('productions.show-traveler-template', cpcs);

            window.open(url, '_blank');
        },
    },
    mounted() {

    }
}
</script>