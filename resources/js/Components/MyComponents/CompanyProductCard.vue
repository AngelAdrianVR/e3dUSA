<template>
  <div class="rounded-xl bg-[#cccccc] px-7 py-3 relative">
    <p class="text-center font-bold mt-4">
      {{ company_product.catalog_product.name }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{company_product.catalog_product.part_number}}</span>
    <el-tooltip content="Número de parte" placement="top">
        <i class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"></i>
    </el-tooltip>
    <figure class="bg-[#D9D9D9] w-full h-48 my-3">
        fotografia
    </figure>
     
        <p class="text-primary text-left">Detalles</p>

     <div class="grid grid-cols-2">
        <span class="text-[#9a9a9a]">Material:</span>
        <span class="text-gray-800">Metalico</span>

        <span class="text-[#9a9a9a]">Características:</span>
        <span class="text-gray-800">Uso rudo</span>
    </div>

    <div class="bg-[#d9d9d9] rounded-lg p-2 grid grid-cols-2 my-3">
        <span class="text-sm">Precio Anterior:</span>
        <span class="text-secondary text-sm">${{ company_product.old_price }} {{ company_product.old_currency }}</span>
        <span class="text-sm">Establecido:</span>
        <span class="text-secondary text-sm mb-3">{{ company_product.old_date }}</span>


        <span class="text-sm">Precio Anterior:</span>
        <span class="text-secondary text-sm">${{ company_product.new_price }} {{ company_product.new_currency }}</span>
        <span class="text-sm">Establecido:</span>
        <span class="text-secondary text-sm">{{ company_product.new_date }}</span>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    company_product: Object,
  },
  components: {},
};
</script>