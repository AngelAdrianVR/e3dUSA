<template>
    <section class="p-7 w-full mx-auto my-4 dark:text-white">
        <div v-if="company.clientMonitors?.length">
            <CompanyClientMonitorTable :client_monitors="company.clientMonitors" />
        </div>
        <div class="flex flex-col text-center justify-center" v-else>
            <p class="text-sm text-center text-gray-400">No hay Seguimiento para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import CompanyClientMonitorTable from "@/Components/MyComponents/CompanyClientMonitorTable.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        CompanyClientMonitorTable,
    },
    methods: {
    },
}
</script>