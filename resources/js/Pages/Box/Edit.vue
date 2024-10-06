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
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
              <el-tooltip content="Nombre *" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          
          <div class="grid grid-cols-3 gap-3">
            <div>
                <IconInput v-model="form.length" inputPlaceholder="Largo cm*" inputType="text">
                <el-tooltip content="Largo cm*" placement="top"> <i class="fa-solid fa-text-width"></i> </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.length" />
            </div>
            <div>
                <IconInput v-model="form.width" inputPlaceholder="Ancho cm*" inputType="text">
                <el-tooltip content="Ancho cm*" placement="top"> <i class="fa-solid fa-up-right-and-down-left-from-center"></i> </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.width" />
            </div>
            <div>
                <IconInput v-model="form.height" inputPlaceholder="Alto cm*" inputType="text">
                <el-tooltip content="Alto cm*" placement="top"> <i class="fa-solid fa-text-height"></i> </el-tooltip>
                </IconInput>
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
import IconInput from "@/Components/MyComponents/IconInput.vue";
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
    InputError,
    AppLayout,
    IconInput,
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
