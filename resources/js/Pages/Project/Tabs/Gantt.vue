<template>
    <div class="text-left text-sm items-center">
        <!-- <GanttDiagramMonth v-if="period === 'Mes'" :currentProject="project" :currentDate="currentDate" />
        <GanttDiagramBimester v-if="period === 'Bimestre'" :currentProject="project"
            :currentDate="currentDate" />
        <div class="text-right mr-9">
            <div class="border border-[#9A9A9A] rounded-md inline-flex justify-end mt-4">
                <p :class="period == 'Mes' ? 'bg-primary text-white rounded-sm' : 'border-[#9A9A9A]'
                    " @click="period = 'Mes'" class="px-4 py-2 text-[#9A9A9A] cursor-pointer">
                    Mes
                </p>
                <p :class="period == 'Bimestre'
                    ? 'bg-primary text-white rounded-sm'
                    : 'border-[#9A9A9A]'
                    " @click="period = 'Bimestre'"
                    class="px-4 py-2 text-[#9A9A9A] cursor-pointer border-x border-transparent">
                    Bimestre
                </p>
            </div>
        </div> -->
    </div>
</template>
<script>
import GanttDiagramBimester from '@/Components/MyComponents/GanttDiagramBimester.vue';
import GanttDiagramMonth from '@/Components/MyComponents/GanttDiagramMonth.vue';

export default {
    data() {
        return {
            period: "Mes",
        }
    },
    props: {
        project: Object
    },
    components: {
        GanttDiagramMonth,
        GanttDiagramBimester,
    },
    methods: {

    },
    mounted() {

    }
}
</script>