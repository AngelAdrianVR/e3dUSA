<template>
  <div @click="$inertia.get(route('oportunities.show', oportunity.id))"
    class="border border-[#D9D9D9] text-left rounded-md py-2 px-7 shadow-md shadow-gray-400/100 h-24 relative cursor-pointer w-72 lg:w-auto"
  >
    <div class="flex items-center absolute top-2 left-2 cursor-move p-1">
      <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
      <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
    </div>
    <el-tooltip :content="'Prioridad: ' + oportunity.priority.label" placement="top">
        <i :class="oportunity.priority.color" class="fa-solid fa-circle text-[9px] absolute top-3 right-2 p-1"></i>
    </el-tooltip>
<p>{{ oportunity?.name }}</p>
    <p>{{ oportunity?.contact }}</p>
    <p>${{ oportunity?.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
    <div class="flex justify-between">
      <p class="text-gray-400">Actividades <span class="text-white rounded-full px-1 bg-secondary text-xs">{{ oportunity?.oportunityTasks?.length }}</span></p>
      <p>{{ oportunity?.created_at?.diffForHumans }}</p>
    </div>
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
    methods:{

    },
    props:{
        oportunity: Object
    },
};
</script>

<style></style>
