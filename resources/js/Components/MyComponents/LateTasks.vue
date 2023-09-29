<template>
    <div class="min-h-[220px] bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:py-2 py-2 px-8 text-xs lg:text-sm">
        <div class="my-3 col-start-2 col-span-2">
            <h1 class="font-bold text-center">Tareas atrasadas <i class="fa-solid fa-clock-rotate-left ml-2"></i></h1>
        </div>
        <div v-if="tasks.length" class="h-full">
            <div class="h-2/3 overflow-y-scroll">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="text-xs">
                            <th class="text-start">Usuario(s)</th>
                            <th class="text-start">Tarea</th>
                            <th class="text-start">Proyecto</th>
                            <th class="text-start">Atrasado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in tasks" :key="task.id" class="text-xs w-full">
                            <td class="w-1/6 text-start">
                                <div class="flex items-center">
                                    <el-tooltip :content="task.participants[0].name" placement="top">
                                        <figure class="w-6 h-6 rounded-full">
                                            <img :src="task.participants[0].profile_photo_url" class="w-full rounded-full">
                                        </figure>
                                    </el-tooltip>
                                    <el-tooltip placement="top">
                                        <template #content>
                                            <li v-for="(participant, index) in task.participants.filter((item, index) => index !== 0)"
                                                :key="index" class="ml-2 text-xs">
                                                {{ participant.name }}
                                            </li>
                                        </template>
                                        <span v-if="task.participants.length > 1" class="ml-1 text-primary text-xs">
                                            +{{ (task.participants.length - 1) }}
                                        </span>
                                    </el-tooltip>
                                </div>
                            </td>
                            <td class="w-1/2 text-start truncate pr-4">
                                <el-tooltip :content="task.title" placement="top">
                                    {{ task.title }}
                                </el-tooltip>
                            </td>
                            <td class="w-1/6 text-start truncate">{{ task.project.project_name }}</td>
                            <td class="w-1/6 text-start">{{ task.late_days }} día(s)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- <div class="flex justify-end mx-6">
                <button class="text-primary text-xs">Ver detalles</button>
            </div> -->
        </div>
        <p v-else class="text-gray-400 text-center mt-8">No hay tareas atrasadas</p>
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