<template>
    <AppLayout title="Editar caja">
      <template #header>
        <div class="flex justify-between">
          <Back :route="'boxes.index'"/>
          <div class="flex items-center space-x-2">
                <h2 class="font-semibold text-xl leading-tight">Editar caja</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md space-y-4">
          <div>
            <InputLabel value="Nombre*" />
            <el-input v-model="form.name" placeholder="Nombre" />
            <InputError :message="form.errors.name" />
          </div>
          
          <div class="grid grid-cols-3 gap-3">
            <div>
              <InputLabel value="Largo*" />
              <el-input
                v-model="form.length" placeholder="Largo">
                <template #append>cm</template>
              </el-input>
              <InputError :message="form.errors.length" />
            </div>
            <div>
              <InputLabel value="Ancho*" />
              <el-input
                v-model="form.width" placeholder="Ancho">
                <template #append>cm</template>
              </el-input>
              <InputError :message="form.errors.width" />
            </div>
            <div>
              <InputLabel value="Alto*" />
              <el-input
                v-model="form.height" placeholder="Alto">
                <template #append>cm</template>
              </el-input>
              <InputError :message="form.errors.height" />
            </div>
          </div>

          <div class="text-center pt-5">
            <figure v-if="!form.is_circular" class="w-48 mx-auto">
            <img src="@/../../public/images/paralelepipedo.png" alt="">
            </figure>
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
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: this.box.name,
      length: this.box.length,
      height: this.box.height,
      width: this.box.width,
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
    Link
  },
  props:{
    box: Object
  },
  methods: {
    update() {
      this.form.put(route("boxes.update", this.box.id), {
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
