<template>
    <section class="lg:grid grid-cols-2 gap-8 md:mt-12 md:px-14 dark:text-white">
        <CompanyBranchCard :company_branches="company.company_branches" />
    </section>
</template>
<script>
import CompanyBranchCard from "@/Components/MyComponents/CompanyBranchCard.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        CompanyBranchCard,
    },
    methods: {
    },
}
</script>