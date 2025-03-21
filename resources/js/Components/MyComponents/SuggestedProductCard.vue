<template>
  <div class="rounded-xl bg-[#cccccc] dark:bg-[#202020] dark:text-white px-7 py-3 relative">
    <p class="text-center font-bold mt-4">
      {{ suggested.name }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{
      suggested.part_number
    }}</span>
    <el-tooltip content="Número de parte" placement="top">
      <i
        class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"></i>
    </el-tooltip>
    <!-- <figure class="w-full my-3 rounded-[10px]">
      <img class="object-contain h-40 rounded-md" :src="suggested.media[0]?.original_url" alt="">
    </figure> -->
    <div>
      <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
        class="bg-[#D9D9D9] dark:bg-[#333333] w-full  my-3 rounded-[10px] relative">
        <img class="object-contain h-40 mx-auto" :src="suggested.media[currentImage]?.original_url" alt="">
        <div v-if="imageHovered" @click="openImage(suggested.media[currentImage]?.original_url)"
          class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
          <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
        </div>
      </figure>
      <div v-if="suggested.media?.length > 1" class="my-3 flex items-center justify-center space-x-3">
        <i @click="currentImage = index" v-for="(image, index) in suggested.media?.length" :key="index"
          :class="index == currentImage ? 'text-black' : 'text-white'"
          class="fa-solid fa-circle text-[7px] cursor-pointer"></i>
      </div>
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
  data() {
    return {
      imageHovered: false, //imagen de tarjeta
      currentImage: 0, //imagen de tarjeta
    };
  },
  props: {
    suggested: Object,
  },
  components: {},
  computed: {
    priceChangePercentage() {
      const oldPrice = this.suggested.pivot?.old_price;
      const newPrice = this.suggested.pivot?.new_price;

      if (oldPrice && newPrice) {
        const percentageChange = ((newPrice - oldPrice) / oldPrice) * 100;
        return percentageChange.toFixed(2);
      }

      return null;
    },
    priceChangeClass() {
      if (this.priceChangePercentage > 0) return 'text-green-700';
      if (this.priceChangePercentage < 0) return 'text-red-700';
      return 'text-gray-600'; // color gris si no hay cambio en el precio
    },
    priceChangeIcon() {
      if (this.priceChangePercentage > 0) return 'fa-solid fa-arrow-up-long';
      if (this.priceChangePercentage < 0) return 'fa-solid fa-arrow-down-long';
      return null; // sin icono si el precio no cambia
    }
  },
  methods: {
    formatDate(date) {
      const parsedDate = new Date(date);
      return format(parsedDate, 'dd \'de\' MMMM, Y', { locale: es }); // Formato personalizado
    },
    openImage(url) {
      window.open(url, '_blank');
    },
    showOverlay() {
      this.imageHovered = true;
    },
    hideOverlay() {
      this.imageHovered = false;
    },
  }
};
</script>