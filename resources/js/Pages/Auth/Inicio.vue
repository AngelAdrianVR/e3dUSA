<template>
  <div class="inicio-container">
    <transition name="logo-grow">
        <figure class="rounded-3xl border p-3 shadow-md shadow-black/30 size-28  md:size-36 text-center">
            <img src="@/../../public/images/isoLogoEmblems.png" alt="Logo" />
            <i class="fa-solid fa-spinner text-primary fa-spin text-4xl mt-7"></i>
        </figure>
    </transition>
  </div>
</template>

<script>
export default {
  mounted() {
    // Después de 3 segundos, oculta el logo y redirige al login
    setTimeout(() => {
      this.$inertia.visit(this.route('login'));
    }, 3000);
  },
};
</script>

<style scope>
.inicio-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background-color: #f3f4f6; /* Cambia el color de fondo si deseas */
}

.logo-grow-enter-active {
  transition: transform 3s ease;
}

.logo-grow-enter {
  transform: scale(0.3); /* Tamaño inicial pequeño */
}

.logo-grow-enter-to {
  transform: scale(0.5); /* Tamaño final */
}

/* Personaliza el tamaño y estilo del contenedor del logotipo */
.logo-container {
  display: inline-block;
  transform-origin: center;
}
</style>
