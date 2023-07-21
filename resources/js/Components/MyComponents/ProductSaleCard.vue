<template>
  <div class="rounded-xl bg-[#cccccc] px-7 py-3 relative text-xs">
    <!-- selection circle -->
    <div @click="handleSelection" class="w-5 h-5 border-2 rounded-full absolute top-3 left-3 cursor-pointer flex items-center justify-center" :class="selected ? 'border-[#d90537]' : 'border-black' ">
      <div class="w-3 h-3 rounded-full" :class="selected ? 'bg-primary' : null "></div>
    </div>

    <!-- card -->
    <p class="text-center font-bold mt-4 text-sm">
      {{ catalog_product_company_sale.catalog_product_company.catalog_product.name }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{
      catalog_product_company_sale.catalog_product_company.catalog_product.part_number
    }}</span>
    <el-tooltip content="Número de parte" placement="top">
      <i
        class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"></i>
    </el-tooltip>

    <div class="grid grid-cols-2 gap-x-4">
      <figure class="bg-[#D9D9D9] w-full h-28 my-3 rounded-[10px]">
        <el-image style="height: 100%; border-radius: 10px;" :src="catalog_product_company_sale.catalog_product_company.catalog_product.media[0]?.original_url" fit="contain">
          <template #error>
            <div class="flex justify-center items-center text-[#ababab]">
              <i class="fa-solid fa-image text-6xl"></i>
            </div>
          </template>
        </el-image>
      </figure>
      <div>
        <p class="text-primary text-left">Caracteristicas</p>
        <li v-for="(feature, index) in catalog_product_company_sale.catalog_product_company.catalog_product.features" :key="index" class="text-gray-800 list-disc">{{ feature }}</li>
      </div>
    </div>

    <div class="border-b-2 border-[#9a9a9a] pb-1">
      <p class="text-primary ">Unidades ordenadas: <span class="ml-4 text-black">{{ catalog_product_company_sale.quantity }}</span></p>
    </div>

    <div class="border-b-2 border-[#9a9a9a] pb-1 mt-2">
      <p class="text-primary ">Operadores asignados:</p>
      <p v-for="production in productions" :key="production.id" class="mt-1">-{{ production.operator.name }}</p>
    </div>

    <div class="bg-[#d9d9d9] rounded-lg p-2 grid grid-cols-2 my-3">
      <span class="">Precio Anterior:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company.old_price }}
        {{ catalog_product_company_sale.catalog_product_company.old_currency }}</span>
      <span class="">Establecido:</span>
      <span class="text-secondary  mb-3">{{
        catalog_product_company_sale.catalog_product_company.old_date
      }}</span>

      <span class="">Precio Anterior:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company.new_price }}
        {{ catalog_product_company_sale.catalog_product_company.new_currency }}</span>
      <span class="">Establecido:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company.new_date }}</span>
    </div>

    <div>
      <span class="text-primary">Status:</span>
      <p class="">{{ catalog_product_company_sale }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selected: false,
    };
  },
  emits: ['selected'],
  props: {
    catalog_product_company_sale: Object,
    productions: Array,
  },
  components: {},
  methods: {
    handleSelection() {
      this.selected = !this.selected;
      this.$emit('selected', this.selected);
    },
  }
};
</script>