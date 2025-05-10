<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed inset-0 z-50 space-y-2 flex flex-col items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
    >
      <!-- <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-white"></div> -->
      <div class="flex-col gap-4 w-full flex items-center justify-center">
        <div class="w-28 h-28 border-8 text-primary text-4xl animate-spin border-gray-300 flex items-center justify-center border-t-primary rounded-full">
          <div class="animate-ping w-1/2">
            <img src="@/../../public/images/isoLogoEmblems.png" alt="Logo" />
          </div>
        </div>
      </div>
      <p class="text-white">Cargando...</p>
    </div>
  </transition>
</template>

<script>
export default {
  name: "FullScreenLoader",
  props: {
    visible: {
      type: Boolean,
      required: true
    }
  }
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
