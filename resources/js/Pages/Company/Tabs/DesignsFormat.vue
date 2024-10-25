<template>
    <section class="p-7 w-full mx-auto my-4 dark:text-white">
        <div class="flex justify-between items-center">
            <p class="text-secondary">Formatos de autorización de diseño</p>
            <PrimaryButton @click="$inertia.get(route('design-authorizations.create', { company_id: company.id }))"
                class="self-start">Agregar formato</PrimaryButton>
        </div>
        <div v-if="company.company_branches?.some(branch => branch.designAuthorizations?.length > 0)" class="mt-5 mx-auto">
            <DesignAuthorizationTable :designAuthorizations="getAllDesignAuthorizations()" />
        </div>
        <div v-else class="flex flex-col text-center justify-center">
            <p class="text-sm text-center text-gray-400">No hay Formatos de autorización de diseño de este cliente</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import DesignAuthorizationTable from "@/Components/MyComponents/DesignAuthorizationTable.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        DesignAuthorizationTable,
        PrimaryButton,
    },
    methods: {
        getAllDesignAuthorizations() {
            // Recopila todas las ventas de todos los company_branches
            const designAuthorizations = [];
            if (this.company && this.company.company_branches) {
                this.company.company_branches.forEach(branch => {
                    if (branch.designAuthorizations) {
                        designAuthorizations.push(...branch.designAuthorizations);
                    }
                });
            }
            return designAuthorizations;
        },
    },
}
</script>