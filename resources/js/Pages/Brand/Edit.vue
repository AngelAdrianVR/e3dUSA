<template>
  <AppLayout title="Editar marca">
    <template #header>
      <div class="flex justify-between">
        <Back :route="'brands.index'" />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Editar marca</h2>
        </div>
      </div>
    </template>

    <form @submit.prevent="update">
      <div
        class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md space-y-4">
        <p class="text-sm text-amber-700 dark:text-gray-400">
          Al editar el nombre de la marca, se actualizará en todos los productos que la tengan asignada.
        </p>
        <div>
          <InputLabel value="Nombre *" />
          <el-input v-model="form.name" placeholder="Escribe el nombre de la marca" />
          <InputError :message="form.errors.name" />
        </div>
        <label class="flex items-center mt-2">
          <Checkbox v-model:checked="form.is_luxury" class="bg-transparent" />
          <span class="ml-2 text-sm">Es marca de lujo</span>
        </label>
        <div class="mt-9 mx-3 md:text-right">
          <PrimaryButton :disabled="form.processing">
            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
            Guardar cambios
          </PrimaryButton>
        </div>
      </div>
    </form>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  data() {
    const form = useForm({
      name: this.brand.name,
      is_luxury: !! this.brand.is_luxury,
    });

    return {
      form,
    };
  },
  components: {
    SecondaryButton,
    PrimaryButton,
    InputLabel,
    InputError,
    AppLayout,
    Back,
    Link,
    Checkbox,
  },
  props: {
    brand: Object
  },
  methods: {
    update() {
      this.form.put(route("brands.update", this.brand.id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "",
            type: "success",
          });
          this.form.reset();
        },
      });
    },
  },
};
</script>
