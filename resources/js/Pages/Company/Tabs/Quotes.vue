<template>
    <section class="p-7 w-full mx-auto my-4">
        <div v-if="hasQuotes()" class="overflow-x-auto">
            <CompanyQuoteTable :quotes="getAllQuotes()" />
        </div>
        <div v-else class="flex flex-col text-center justify-center">
            <p class="text-sm text-center text-gray-400">No hay cotizaciones para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import CompanyQuoteTable from "@/Components/MyComponents/CompanyQuoteTable.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        CompanyQuoteTable,
    },
    methods: {
        hasQuotes() {
            const tieneCotizaciones = this.company.company_branches
                .map((branch) => branch.quotes.length > 0)
                .some((tieneCotizacionesEnBranch) => tieneCotizacionesEnBranch);

            return tieneCotizaciones; // Devolverá true si hay cotizaciones en al menos un company_branch
        },
        getAllQuotes() {
            // Recopila todas las ventas de todos los company_branches
            const quotes = [];
            if (this.company && this.company.company_branches) {
                this.company.company_branches.forEach(branch => {
                    if (branch.quotes) {
                        quotes.push(...branch.quotes);
                    }
                });
            }
            return quotes;
        },
    },
}
</script>