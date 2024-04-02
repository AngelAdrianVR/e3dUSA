<template>
  <div class="star-rating">
    <span v-for="index in 3" :key="index" class="star">
      <i v-if="index <= filledStars" class="fa-solid fa-star text-yellow-400"></i>
      <i v-else-if="index - 0.5 === rating && index !== filledStars" class="fa-solid fa-star-half-stroke text-yellow-400"></i>
      <i v-else class="fa-regular fa-star" style="color: #ccc;"></i>
    </span>
  </div>
</template>

<script>
export default {
  props: {
    rating: {
      type: Number,
      required: true,
      validator: (value) => value >= 0 && value <= 3
    }
  },
  computed: {
    filledStars() {
      return Math.floor(this.rating);
    },
  }
};
</script>

<style scoped>
.star-rating {
  display: flex;
}

.star {
  cursor: default;
}

.fa-star,
.fa-star-half-stroke {
  font-size: 12px;
}
</style>
