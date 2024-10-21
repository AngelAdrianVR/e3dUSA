<template>
    <div class="md:grid grid-cols-3 text-left p-4 text-sm">
        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7">
            <h2 class="font-bold mb-10">
                POR HACER <span class="font-normal ml-7">{{ pendingTasksList.length }}</span>
            </h2>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="pendingTasksList"
                :animation="300" item-key="id" tag="ul" group="tasks" id="pendent"
                :class="(drag && !pendingTasksList.length) ? 'h-40' : ''">
                <template #item="{ element: task }">
                    <li>
                        <ProjectTaskCard @delete-task="deleteProjectTask" @updated-status="updateTask($event)"
                            :taskComponent="task" :users="project.users" :id="task.id" />
                    </li>
                </template>
            </draggable>
            <div class="text-center" v-if="!pendingTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>

        <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:px-7">
            <h2 class="font-bold mb-10">
                EN CURSO <span class="font-normal ml-7">{{ inProgressTasksList.length }}</span>
            </h2>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="inProgressTasksList"
                :animation="300" item-key="id" tag="ul" group="tasks" id="process"
                :class="(drag && !inProgressTasksList.length) ? 'h-40' : ''">
                <template #item="{ element: task }">
                    <li>
                        <ProjectTaskCard @delete-task="deleteProjectTask" @updated-status="updateTask($event)"
                            :taskComponent="task" :users="project.users" />
                    </li>
                </template>
            </draggable>
            <div class="text-center" v-if="!inProgressTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>

        <div class="h-auto lg:px-7">
            <h2 class="font-bold mb-10">
                TERMINADA <span class="font-normal ml-7">{{ finishedTasksList.length }}</span>
            </h2>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="finishedTasksList"
                :animation="300" item-key="id" tag="ul" group="tasks" id="finished"
                :class="(drag && !finishedTasksList.length) ? 'h-40' : ''">
                <template #item="{ element: task }">
                    <li>
                        <ProjectTaskCard @delete-task="deleteProjectTask" @updated-status="updateTask($event)"
                            :taskComponent="task" :users="project.users" />
                    </li>
                </template>
            </draggable>
            <div class="text-center" v-if="!finishedTasksList.length">
                <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
                <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
            </div>
        </div>
    </div>
</template>
<script>
import ProjectTaskCard from '@/Components/MyComponents/ProjectTaskCard.vue';
import draggable from 'vuedraggable';

export default {
    data() {
        return {
            pendingTasksList: [],
            inProgressTasksList: [],
            finishedTasksList: [],
            drag: false,
            draggingTaskId: null,
        }
    },
    props: {
        project: Object,
    },
    components: {
        ProjectTaskCard,
        draggable,
    },
    methods: {
        inProgressTasks() {
            this.inProgressTasksList = this.project.tasks.filter((task) => task.status === "En curso");
        },
        finishedTasks() {
            this.finishedTasksList = this.project.tasks.filter((task) => task.status === "Terminada");
        },
        updateTasksLists() {
            this.pendingTasks();
            this.inProgressTasks();
            this.finishedTasks();
        },
        pendingTasks() {
            this.pendingTasksList = this.project.tasks.filter((task) => task.status === "Por hacer");
        },
        handleStartDrag(evt) {
            this.draggingTaskId = evt.item.__draggable_context.element.id;
            this.drag = true;
        },
        handleAddDrag(evt) {
            let status = 'Terminada';
            if (evt.to.id === 'pendent') {
                status = 'Por hacer';
            } else if (evt.to.id === 'process') {
                status = 'En curso';
            }

            this.drag = false;
            this.updateTaskStatus(status);
        },
        updateTask(task) {
            const taskIndex = this.project.tasks.findIndex(
                (item) => item.id === task.id
            );

            if (taskIndex !== -1) {
                this.project.tasks[taskIndex] = task;
            }

            this.updateTasksLists();
        },
        async updateTaskStatus(status) {
            try {
                const response = await axios.put(route('tasks.update-status', this.draggingTaskId), { status: status });

                if (response.status === 200) {
                    const taskIndex = this.project.tasks.findIndex(item => item.id === this.draggingTaskId);
                    this.project.tasks[taskIndex].status = status;
                    console.log(this.project.tasks[taskIndex]);
                }
            } catch (error) {
                console.log(error);
            }
        },
        async deleteProjectTask(data) {
            try {
                const response = await axios.delete(route('tasks.destroy', data));

                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha eliminado correctamente",
                        type: "success",
                    });

                    const index = this.project.tasks.findIndex(item => item.id === data);

                    if (index !== -1) {
                        this.project.tasks.splice(index, 1);
                    }
                    this.updateTasksLists();
                }
            } catch (error) {
                console.log(error);
            }

        },
    },
    mounted() {
        this.updateTasksLists();
        if (this.project && this.project.tasks.length > 0) {
            const firstTask = this.project.tasks[0];
            if (firstTask && firstTask.start_date) {
                this.currentDate = new Date(firstTask.start_date_raw);
            }
        }
    }
}
</script>