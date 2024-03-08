<template>
    <section class="p-7 w-full mx-auto my-4">
        <div class="flex justify-between items-center">
            <p class="text-secondary">Diseños exclusivos</p>
            <PrimaryButton v-if="$page.props.auth.user.permissions.includes('Crear diseños exclusivos')"
                @click="$inertia.get(route('exclusive-designs.create'), { company_id: company.id })"
                class="self-start">
                Agregar formato
            </PrimaryButton>
        </div>
        <div class="mt-5 mx-auto">
            <ExclusiveDesignTable :companyId="company.id" />
        </div>
    </section>
</template>
<script>
import ExclusiveDesignTable from "@/Components/MyComponents/ExclusiveDesignTable.vue";
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
        ExclusiveDesignTable,
        PrimaryButton,
    },
    methods: {
    },
}
</script>