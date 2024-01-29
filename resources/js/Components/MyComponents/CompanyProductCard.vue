<template>
  <div class="rounded-xl bg-[#cccccc] px-7 py-3 relative">
    <p class="text-center font-bold mt-4">
      {{ company_product.name }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{
      company_product.part_number
    }}</span>
    <el-tooltip content="Número de parte" placement="top">
      <i
        class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"
      ></i>
    </el-tooltip>
    <figure class="w-full my-3 rounded-[10px]">
        <!-- <el-image class="border" style="height: 100%; border-radius: 10px;"
          :src="company_product.media[0]?.original_url"
          fit="contain">
          <template #error>
            <div class="flex justify-center items-center text-[#ababab]">
              <i class="fa-solid fa-image text-6xl"></i>
            </div>
          </template>
        </el-image> -->
        <img class="object-contain h-40 rounded-md" :src="company_product.media[0]?.original_url" alt="">
      </figure>

      <div>
        <p class="text-primary text-left">Caracteristicas</p>
        <li v-for="(feature, index) in company_product.features"
          :key="index" class="text-gray-800 list-disc">{{ feature }}</li>
      </div>

    <div class="bg-[#d9d9d9] rounded-lg p-2 grid grid-cols-2 my-3">
      <span class="text-sm">Precio Anterior:</span>
      <span class="text-secondary text-sm"
        >{{ company_product.pivot.old_price }}
        {{ company_product.pivot.old_currency }}</span
      >
      <span class="text-sm">Establecido:</span>
      <span class="text-secondary text-sm mb-3">{{
        company_product.pivot.old_date
      }}</span>

      <span class="text-sm">Precio Actual:</span>
      <span class="text-secondary text-sm"
        >{{ company_product.pivot.new_price }}
        {{ company_product.pivot.new_currency }}</span
      >
      <span class="text-sm">Establecido:</span>
      <span class="text-secondary text-sm">{{ company_product.pivot.new_date }}</span>
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