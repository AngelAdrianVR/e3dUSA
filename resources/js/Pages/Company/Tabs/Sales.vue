<template>
    <section class="p-7 w-full mx-auto my-4 dark:text-white">
        <div v-if="company.company_branches?.some(branch => branch.sales?.length > 0)">
            <CompanySalesTable :company_sales="getAllSales()" />
        </div>
        <div v-else class="flex flex-col text-center justify-center">
            <p class="text-sm text-center text-gray-400">No hay ordenes de venta de este cliente</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import CompanySalesTable from "@/Components/MyComponents/CompanySalesTable.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        CompanySalesTable,
    },
    methods: {
        getAllSales() {
            // Recopila todas las ventas de todos los company_branches
            const sales = [];
            if (this.company && this.company.company_branches) {
                this.company.company_branches.forEach(branch => {
                    if (branch.sales) {
                        sales.push(...branch.sales);
                    }
                });
            }
            return sales;
        },
    },
}
</script>