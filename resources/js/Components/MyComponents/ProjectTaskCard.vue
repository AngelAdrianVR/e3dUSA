<template>
  <div :class="taskComponent.priority.color_border" class="shadow-md shadow-gray-400/100 h-36 rounded-r-md border-l-4 py-2 px-3 cursor-grab active:cursor-grabbing my-3">
            <!-- ------------ top ------------------ -->
<el-tooltip :content="'Prioridad: ' + taskComponent.priority.label" placement="top">
                <div class="flex justify-between items-center">
                    <div class="hover:bg-[#dfdede] cursor-pointer rounded-full px-2">
                        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                        <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                    </div>
                    <div class="flex">
                      <p v-if="taskComponent.is_paused" class="mr-4 rounded-full text-orange-600 bg-orange-200 px-2">{{ 'Pausado' }}</p>
                      <p class="mr-5">{{ taskComponent.created_at }}</p>
                    </div>
                </div>
</el-tooltip>
            <!-- ------------ body -------------------------- -->
                <div class="flex items-center justify-between p-3">
                    <p class="text-lg">{{ taskComponent.title }}</p>
                    <i class="fa-solid fa-paperclip cursor-pointer hover:bg-[#dfdede] rounded-full p-2"></i>
                </div>

            <!-- ----------------- footer --------------- -->
                <footer class="p-3 border-t border-[#9A9A9A] relative">
                    <div class="flex justify-between items-center px-3">
                      <div class="flex items-end text-[#9A9A9A]">
                        <i class="fa-regular fa-comments text-lg cursor-pointer hover:bg-[#dfdede] rounded-full py-1 px-2"></i>
                       <p class="text-xs ml-1">{{ '3' }} </p>
                       <p class="text-sm ml-3">| {{ 'Dpto. Diseño' }} </p>
                      </div>
                      <div class="flex items-end absolute bottom-3 right-0 cursor-default">
                        <p class="text-primary mr-2">+2</p>
                        <el-tooltip content="Nombre de la persona" placement="bottom">
                            <div class="rounded-full w-10 h-10 border border-[#9A9A9A]  bg-gray-100 "></div>
                        </el-tooltip>
                      </div>
                    </div>
                </footer>
            </div>
</template>

<script>
export default {
data(){
    return {

    }
},
components:{

},
props:{
    taskComponent: Object
},
methods:{

},

}
</script>
