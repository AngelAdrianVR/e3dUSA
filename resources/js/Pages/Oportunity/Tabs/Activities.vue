<template>
    <div class="contenedor text-left p-4 text-sm">
        <!-- -- TERMINAR HOY -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7 seccion mx-2 ">
            <h2 class="font-bold mb-10">
                TERMINAR HOY <span class="font-normal ml-7">{{ todayTasksList.length }}</span>
            </h2>
            <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
                @task-done="markAsDone" class="mb-3" v-for="todayTask in todayTasksList" :key="todayTask"
                :oportunityTask="todayTask" :users="opportunity.users" />
            <div class="text-center" v-if="!todayTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>
        <!-- -- TERMINAR ESTA SEMANA -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
            <h2 class="font-bold mb-10 first-letter ml-2">
                TERMINAR ESTA SEMANA <span class="font-normal ml-7">{{ thisWeekTasksList.length }}</span>
            </h2>
            <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
                @task-done="markAsDone" class="mb-3" v-for="thisWeekTask in thisWeekTasksList" :key="thisWeekTask"
                :oportunityTask="thisWeekTask" :users="opportunity.users" />
            <div class="text-center" v-if="!thisWeekTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>
        <!-- -- ACTIVIDADES PROXIMAS -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4  seccion mx-2">
            <h2 class="font-bold mb-10 first-letter ml-2">
                ACTIVIDADES PROXIMAS <span class="font-normal ml-7">{{ nextTasksList.length }}</span>
            </h2>
            <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
                @task-done="markAsDone" class="mb-3" v-for="nextTask in nextTasksList" :key="nextTask"
                :oportunityTask="nextTask" :users="opportunity.users" />
            <div class="text-center" v-if="!nextTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>
        <!-- -- ATRASADAS -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
            <h2 class="font-bold mb-10 first-letter ml-2">
                ATRASADAS <span class="font-normal ml-7">{{ lateTasksList.length }}</span>
            </h2>
            <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
                @task-done="markAsDone" class="mb-3" v-for="lateTask in lateTasksList" :key="lateTask"
                :oportunityTask="lateTask" :users="opportunity.users" />
            <div class="text-center" v-if="!lateTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>
        <!-- -- TERMINADAS -- -->
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-4 seccion mx-2">
            <h2 class="font-bold mb-10 first-letter ml-2">
                TERMINADAS <span class="font-normal ml-7">{{ finishedTasksList.length }}</span>
            </h2>
            <OportunityTaskCard @updated-oportunityTask="updateOportunityTask" @delete-task="deleteTask"
                @task-done="markAsDone" class="mb-3" v-for="finishedTask in finishedTasksList" :key="finishedTask"
                :oportunityTask="finishedTask" :users="opportunity.users" />
            <div class="text-center" v-if="!finishedTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>
    </div>
</template>
<script>
import OportunityTaskCard from '@/Components/MyComponents/OportunityTaskCard.vue';

export default {
    data() {
        return {
            todayTasksList: [],
            thisWeekTasksList: [],
            nextTasksList: [],
            finishedTasksList: [],
            lateTasksList: [],
        }
    },
    props: {
        opportunity: Object,
    },
    components: {
        OportunityTaskCard,
    },
    computed: {
        todayTasksList() {
            return this.todayTasksList = this.opportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Terminar hoy' && !oportunity.finished_at);
        },
        thisWeekTasksList() {
            return this.thisWeekTasksList = this.opportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Esta semana' && !oportunity.finished_at);
        },
        nextTasksList() {
            return this.nextTasksList = this.opportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Proximas' && !oportunity.finished_at);
        },
        finishedTasksList() {
            return this.finishedTasksList = this.opportunity.oportunityTasks.filter(oportunity => oportunity.finished_at);
        },
        lateTasksList() {
            return this.lateTasksList = this.opportunity.oportunityTasks.filter(oportunity => oportunity.deadline_status === 'Atrasadas' && !oportunity.finished_at);
        },
    },
    methods: {
        async markAsDone(data) {
            try {
                const response = await axios.put(route('oportunity-tasks.mark-as-done', data));

                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha marcado como terminada",
                        type: "success",
                    });

                    this.opportunity.oportunityTasks.find(item => item.id === data).finished_at = new Date();
                }
            } catch (error) {
                console.log(error);
            }
        },
        async deleteTask(data) {
            try {
                const response = await axios.delete(route('oportunity-tasks.destroy', data));
                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha eliminado correctamente",
                        type: "success",
                    });

                    const index = this.opportunity.oportunityTasks.findIndex(item => item.id === data);
                    if (index !== -1) {
                        this.opportunity.oportunityTasks.splice(index, 1);
                    }
                }
            } catch (error) {
                console.log(error);
            }
        },
    },
}
</script>