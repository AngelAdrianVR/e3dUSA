<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref } from 'vue';

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      remember: form.remember ? "on" : "",
    }))
    .post(route("login"), {
      onFinish: () => form.reset("password"),
    });
};

const showPassword = ref(false);
const password = ref('');

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

</script>

<template>
  <Head title="Log in" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <div class="border-b border-gray-300 mb-12 mt-9 text-center w-[80%] mx-auto">
       <span class="inline-block border-b-2 border-[#D90537] px-3 text-gray-600">Iniciar sesión</span>
    </div>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full bg-transparent placeholder:text-[#9A9A9A]"
          required
          autofocus
          autocomplete="username"
          placeholder="ID de usuario"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4 relative">
        <div class="flex justify-center items-center">
          <TextInput
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            class="mt-1 block w-full bg-transparent placeholder:text-[#9A9A9A]"
            required
            autocomplete="current-password"
            placeholder="Contraseña"
          />
          <i
            :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"
            class="text-gray-400 ml-2 cursor-pointer absolute right-3 top-4"
            @click="togglePasswordVisibility"
          ></i>
        </div>
        <InputError class="mt-2" :message="form.errors.password" />
      </div>


      <div class="block mt-4">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember" class="bg-transparent"/>
          <span class="ml-2 text-sm text-[#9A9A9A]">No cerrar sesión</span>
        </label>
      </div>

      <div class="flex flex-col items-center justify-center mt-4 space-y-4">
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="underline text-sm text-[#9A9A9A] hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          Olvidé mi contraseña
        </Link>

        <PrimaryButton
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Iniciar sesión <i class="fa-solid fa-arrow-right ml-2"></i>
        </PrimaryButton>
      </div>
    </form>
  </AuthenticationCard>
</template>
