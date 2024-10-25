<template>
  <div>
    <AppLayout title="Costos de producción - Agregar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Agregar Costo de producción</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
              <el-tooltip content="Nombre *" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <div class="flex">
              <el-tooltip content="Tiempo invertido por unidad *" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                  <i class="fa-regular fa-clock"></i>
                </span>
              </el-tooltip>
              <el-time-picker v-model="form.time" placeholder="Seleccione" />
            </div>
            <InputError :message="form.errors.time" />
          </div>
          <!-- {{form}} -->
          <div>
            <IconInput v-model="form.cost" inputPlaceholder="Costo *" inputType="number" inputStep="0.01">
              <el-tooltip content="Costo *" placement="top">
                <i class="fa-solid fa-sack-dollar"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.cost" />
          </div>

          <div class="flex">
              <el-tooltip content="Descripción *" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                  <i class="fa-solid fa-grip-lines"></i>
                </span>
              </el-tooltip>
              <textarea v-model="form.description" class="textarea w-full" autocomplete="off"
                placeholder="Descripción *"></textarea>
              <InputError :message="form.errors.description" />
            </div>

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Agregar costo de producción
            </PrimaryButton>
          </div>
        </div>
      </form>
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: null,
      time: null,
      cost: null,
      description: null,
    });

    return {
      form,
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Back,
    Link
  },
  props: {

  },
  methods: {
    store() {
      this.form.post(route("production-costs.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se agregó un nuevo costo de producción",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
  },
};
</script>
