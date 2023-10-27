<template>
  <Head title="Encuesta de satisfacción" />
  <div class="h-screen bg-[#F2F2F2]">
    <div class="shrink-0 flex items-center py-5 pl-10 border-b border-[#9A9A9A]">
      <figure>
        <ApplicationMark class="w-1/3" />
      </figure>
    </div>
    <form v-if="!is_sent" @submit.prevent="store">
      <div class="md:w-1/2 md:mx-auto mt-20 bg-transparent rounded-lg lg:p-9 p-4 space-y-4">
        <h1 class="text-2xl font-bold">Encuesta de satisfacción</h1>
        <p class="mt-3">En Emblemas 3d USA valoramos mucho tu opinión y nos gustaría conocer tus comentario para seguir
          mejorando.</p>
        <p class="mt-3">1. En una escala del 0 al 10, ¿qué tan satisfecho/a estás con la calidad de nuestros productos?
        </p>
        <div class="w-full">
          <div class="flex items-center">
            <div class="w-1/12 text-gray-500 text-xs">0</div>
            <div class="w-10/12 h-3 bg-transparent">
            </div>
            <div class="w-1/12 text-gray-500 text-xs text-right">10</div>
          </div>
          <el-tooltip :content="`${form.p1}`" placement="top">
            <input type="range" min="0" max="10" v-model="form.p1" class="w-full" @input="updatePercentage" />
          </el-tooltip>
        </div>
        <InputError :message="form.errors.p1" />
        <p class="mt-3">2. ¿Nuestros productos cumplieron con tus expectativas?</p>
        <div class="flex space-x-7 items-center lg:ml-5">
          <label>
            <input v-model="form.p2" value="Si" type="radio" name="group1"
              class="mr-1 bg-transparent border-black checked:bg-primary focus:ring-primary focus:text-primary hover:text-red-500">
            Sí
          </label>
          <label>
            <input v-model="form.p2" value="No" type="radio" name="group1"
              class="mr-1 bg-transparent border-black checked:bg-primary focus:ring-primary focus:text-primary hover:text-red-500">
            No
          </label>
          <label>
            <input v-model="form.p2" value="Rebasaron mis expectativas" type="radio" name="group1"
              class="mr-1 bg-transparent border-black checked:bg-primary focus:ring-primary focus:text-primary hover:text-red-500">
            Rebasaron mis expectativas
          </label>
          <InputError :message="form.errors.p2" />
        </div>
        <p class="mt-3">3. ¿Consideras que nuestro equipo de trabajo fue profesional y cortés?</p>
        <div class="flex space-x-7 items-center lg:ml-5">
          <label>
            <input v-model="form.p3" value="Si" type="radio" name="group2"
              class="mr-1 bg-transparent border-black checked:bg-primary focus:ring-primary focus:text-primary hover:text-red-500">
            Sí
          </label>
          <label>
            <input v-model="form.p3" value="No" type="radio" name="group2"
              class="mr-1 bg-transparent border-black checked:bg-primary focus:ring-primary focus:text-primary hover:text-red-500">
            No
          </label>
          <InputError :message="form.errors.p3" />
        </div>
        <p class="mt-3">4. ¿Recomendarías nuestros productos a otros?</p>
        <div class="flex space-x-7 items-center lg:ml-5">
          <label>
            <input v-model="form.p4" value="Si" type="radio" name="group3"
              class="mr-1 bg-transparent border-black checked:bg-primary focus:ring-primary focus:text-primary hover:text-red-500">
            Sí
          </label>
          <label>
            <input v-model="form.p4" value="No" type="radio" name="group3"
              class="mr-1 bg-transparent border-black checked:bg-primary focus:ring-primary focus:text-primary hover:text-red-500">
            No
          </label>
          <InputError :message="form.errors.p4" />
        </div>
        <p class="mt-3">5. ¿Qué aspectos crees que podríamos perfeccionar que nos ayude a brindarte un mejor servicio?</p>
        <RichText @content="updateDescription($event)" v-model="form.p5" />
        <InputError :message="form.errors.p5" />
        <PrimaryButton :disabled="form.processing">Enviar</PrimaryButton>
      </div>
    </form>

    <div class="text-center" v-else>
      <div class="flex items-center justify-center mt-10">
        <p class="font-bold">Encuesta enviada</p>
        <i class="fa-solid fa-check text-green-500 ml-3"></i>
      </div>
      <p class="mt-6">Agradecemos tu tiempo y comentarios :)</p>
      <i class="fa-regular fa-face-laugh-beam text-9xl text-gray-400/20 mt-16"></i>
    </div>

  </div>
</template>

<script>
import { Link, useForm, Head } from "@inertiajs/vue3";
import RichText from "@/Components/MyComponents/RichText.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import InputError from "@/Components/InputError.vue";

export default {
  data() {
    const form = useForm({
      p1: 5, // Valor inicial de la calificación
      p2: null,
      p3: null,
      p4: null,
      p5: null,
    });
    return {
      form,
    }
  },
  components: {
    PrimaryButton,
    ApplicationMark,
    RichText,
    InputError,
    RichText,
    Link,
    Head,
  },
  props: {
    oportunity_id: Number,
    is_sent: Boolean,
  },
  methods: {
    store() {
      this.form.post(route("surveys.store", this.oportunity_id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Muchas gracias por tu tiempo. Se ha enviado la encuesta!",
            type: "success",
          });
          this.is_sent = true;
        },
      });
    },
    updatePercentage() {
      // Actualiza el valor de la calificación al cambiar el rango
      this.form.rating = parseInt(this.form.rating);
    },
    updateDescription(content) {
      this.form.p5 = content;
    }
  },
}
</script>