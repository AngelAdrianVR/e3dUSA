<template>
  <div>
    <AppLayout title="Costos de producción - Editar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar costo de producción "{{ production_cost.name }}"</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
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
              Guardar cambios
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
      name: this.production_cost.name,
      time: this.production_cost.time,
      cost: this.production_cost.cost,
      description: this.production_cost.description,
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
    production_cost: Object
  },
  methods: {
    update() {
      this.form.put(route("production-costs.update", this.production_cost), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Costo de producción editado",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
  },
};
</script>
