<template>
    <div class="lg:min-h-[220px] min-h-[140px] bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:py-2 py-2 px-8 text-xs lg:text-sm">
        <div class="my-3 col-start-2 col-span-2">
            <h1 class="font-bold text-center">Mis tareas pendientes <i class="fa-solid fa-clipboard-check ml-2"></i></h1>
        </div>
        <div v-if="loading" class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-3 py-1">
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
            </div>
        </div>
        <div v-else class="h-2/3 overflow-y-scroll">
            <div v-if="tasks.length" class="h-full">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="text-xs">
                            <th class="text-start">Tarea</th>
                            <th class="text-center">Proiridad</th>
                            <th class="text-start">Fecha inicial</th>
                            <th class="text-start">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in tasks" :key="task.id" class="text-xs w-full">
                            <td class="w-1/2 text-start truncate">
                                <el-tooltip :content="task.title" placement="top">
                                    {{ task.title }}
                                </el-tooltip>
                            </td>
                            <td class="w-1/6 text-center">{{ task.priority }}</td>
                            <td class="w-1/6 text-start">{{ formatDate(task.start_date) }}</td>
                            <td class="w-1/6 text-start">{{ task.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="text-gray-400 text-center mt-8">No hay tareas pendientes</p>
            <!-- <div class="flex justify-end mx-6">
                <button class="text-primary text-xs">Ver detalles</button>
            </div> -->
        </div>
    </div>
</template>
<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            loading: true,
            tasks: [],
        }
    },
    components: {
    },
    methods: {
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMM', { locale: es }); // Formato personalizado
        },
        async fetchPendentTasks() {
            this.loading = true;
            try {
                const response = await axios.get(route('users.get-pendent-tasks'));

                if (response.status === 200) {
                    this.tasks = response.data.items;
                    this.loading = false;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this.fetchPendentTasks();
    }
}
</script>