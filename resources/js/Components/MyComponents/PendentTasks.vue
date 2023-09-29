<template>
    <div class="min-h-[220px] bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:py-2 py-2 px-8 text-xs lg:text-sm">
        <div class="my-3 col-start-2 col-span-2">
            <h1 class="font-bold text-center">Mis tareas pendientes <i class="fa-solid fa-clipboard-check ml-2"></i></h1>
        </div>
        <div v-if="tasks.length" class="h-full">
            <div class="h-2/3 overflow-y-scroll">
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
                            <td class="w-1/6 text-start">{{ task.start_date }}</td>
                            <td class="w-1/6 text-start">{{ task.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- <div class="flex justify-end mx-6">
                <button class="text-primary text-xs">Ver detalles</button>
            </div> -->
        </div>
        <p v-else class="text-gray-400 text-center mt-8">No hay tareas pendientes</p>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    components: {
    },
    props: {
        tasks: Array,
    }
}
</script>