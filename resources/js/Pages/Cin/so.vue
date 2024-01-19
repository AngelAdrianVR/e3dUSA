<template>
  <div>
    <h1 class="font-bold text-center mt-5 text-xl">Me perdonas? :(</h1>
    <div class="flex items-center justify-center mt-9 space-x-5">
      <SecondaryButton @click="handleYesButtonClick">SIII!!</SecondaryButton>
      <PrimaryButton
        ref="noButton"
        @click="handleNoButtonClick"
        :style="{ left: noButtonLeft + '280px', top: noButtonTop + '85px', position: 'absolute' }"
      >
        NO
      </PrimaryButton>
    </div>
    <div class="text-center mt-28" v-if="si">
      <p class="text-lg font-bold">Gracias!</p>
      <p>Ahorita te marco. Me llegará una notificación via correo electrónico de que sí me perdonaste! :)</p>
    </div>
    <div class="text-center mt-28" v-if="no">
      <p class="text-lg font-bold">Me atrapaste u.u</p>
    </div>
  </div>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

export default {
  data() {
    return {
      si: false,
      no: false,
      noButtonLeft: 0,
      noButtonTop: 0,
    };
  },
  components: {
    PrimaryButton,
    SecondaryButton,
  },
  methods: {
    handleYesButtonClick() {
      this.si = true;
    },
    handleNoButtonClick() {
      this.no = true;
    },
    handleNoButtonHover() {
      const button = this.$refs.noButton;

      const newLeft = Math.random() * 350;
      const newTop = Math.random() * 900;

      this.noButtonLeft = newLeft;
      this.noButtonTop = newTop;
    },
  },
  mounted() {
    // Cambiar la posición del botón cada segundo
    setInterval(this.handleNoButtonHover, 250);
  },
};
</script>
