<template>
  <div class="rounded-xl bg-[#cccccc] px-7 py-3 relative">
    <p class="text-center font-bold mt-4">
      {{ raw_material.product }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{raw_material.part_number}}</span>
    <el-tooltip content="Número de parte" placement="top">
        <i class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"></i>
    </el-tooltip>
    <figure class="bg-[#D9D9D9] w-full h-48 my-3">
        fotografia
    </figure>
     
        <p class="text-primary text-left text-sm">Unidades ordenadas: <span class="text-gray-900"> {{raw_material.quantity + ' ' + raw_material.measure_unit}} </span> </p>
        <div class="border-b border-[#888888] mb-2"></div>

        <p class="text-primary text-left text-sm">Detalles</p>

     <div class="grid grid-cols-2">
     <p class="text-secondary col-span-2 text-sm">Características</p>
        <span v-for="feature in raw_material.features" :key="feature" class="text-[#9a9a9a] text-sm">{{ feature }}</span>
    </div>

    <div class="bg-[#d9d9d9] rounded-lg p-2 grid grid-cols-2 my-3">
        <span class="text-sm">Costo:</span>
        <span class="text-secondary text-sm mb-3">${{ raw_material.cost ?? '--' }}</span>
        <span class="text-sm">Unidad de medida:</span>
        <span class="text-secondary text-sm mb-3">{{ raw_material.measure_unit }}</span>
        <span class="text-sm">Descripción:</span>
        <span class="text-secondary text-sm">{{ raw_material.description ?? '--' }}</span>

    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    raw_material: Object,
  },
  components: {},
};
</script>