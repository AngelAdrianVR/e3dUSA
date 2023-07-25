<template>
  <div>
    <AppLayout title="Costos de producción - Agregar">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('production-costs.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Agregar Costo de producción</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
              <el-tooltip content="Nombre *" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>

          <div>
            <IconInput v-model="form.cost" inputPlaceholder="Costo *" inputType="number" inputStep="0.01">
              <el-tooltip content="Costo *" placement="top">
                <i class="fa-solid fa-sack-dollar"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.cost" />
          </div>

          <div class="flex ml-3">
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
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";

export default {
  data() {
    const form = useForm({
      name: null,
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
    Link,
    InputError,
    IconInput,
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
