<template>
  <div>
    <AppLayout title="Usuarios - Crear">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('users.index')"
            class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Crear nuevo usuario
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
        <div class="md:grid grid-cols-2 gap-3">
        <div>
            <IconInput
              v-model="form.name"
              inputPlaceholder="Nombre completo *"
              inputType="text"
            >
              <el-tooltip content="Nombre completo" placement="top">
                A
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
        <div>
            <IconInput
              v-model="form.email"
              inputPlaceholder="Correo electrónico"
              inputType="text"
            >
              <el-tooltip content="Correo electrónico" placement="top">
                 <i class="fa-solid fa-at"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.email" />
          </div>
        </div>



          <div class="col-span-full">
            <label class="text-[#747474]">Imágenes de perfil</label>
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9"
              >
                <el-tooltip content="Imágen de perfil " placement="top">
                  <i class="fa-solid fa-file-invoice"></i>
                </el-tooltip>
              </span>
              <input
                @input="form.media = $event.target.files[0]"
                class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                aria-describedby="file_input_help"
                id="file_input"
                type="file"
              />
            </div>
            <p
              class="mt-1 text-xs text-right text-gray-500"
              id="file_input_help"
            >
              SVG, PNG, JPG o GIF (MAX. 4 MB).
            </p>
          </div>

          <div class="mx-3 text-right">
            <PrimaryButton :disabled="form.processing">
              Registrar mantenimiento
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
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      supplier: null,
      quantity: null,
      cost: null,
      location: null,
      description: null,
    });

    return {
      form,
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    Link,
    InputError,
    IconInput,
  },
  props: {
  },
  methods: {
    store() {
      this.form.post(route("users.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Usuario creado",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
  },
};
</script>
