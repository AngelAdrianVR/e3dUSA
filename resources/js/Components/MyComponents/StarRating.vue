<template>
  <div class="flex">
    <span v-for="index in 3" :key="index" class="text-xs">
      <i v-if="index <= filledStars" class="fa-solid fa-star text-yellow-400"></i>
      <i v-else-if="index - 0.5 === roundedRating && index !== filledStars"
        class="fa-solid fa-star-half-stroke text-yellow-400"></i>
      <i v-else class="fa-regular fa-star text-[#ccc]"></i>
    </span>
  </div>
</template>

<script>
export default {
  props: {
    rating: {
      type: Number,
      required: true,
    }
  },
  computed: {
    filledStars() {
      return Math.floor(this.roundedRating);
    },
    roundedRating() {
      return Math.ceil(this.rating * 2) / 2;
    }
  }
};
</script>
