<template>
    <section class="p-7 w-full mx-auto my-4">
        <div v-if="company.projects?.length">
            <ProjectTable :projects="company.projects" />
        </div>
        <div v-else class="flex flex-col text-center justify-center">
            <p class="text-sm text-center text-gray-400">No hay proyectos para mostrar</p>
            <i class="fa-regular fa-folder-open text-9xl mt-16 text-gray-300"></i>
        </div>
    </section>
</template>
<script>
import ProjectTable from "@/Components/MyComponents/ProjectTable.vue";

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    components: {
        ProjectTable,
    },
    methods: {
    },
}
</script>