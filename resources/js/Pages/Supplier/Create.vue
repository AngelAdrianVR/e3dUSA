<template>
    <div>
        <AppLayout title="Proveedores - Crear">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('suppliers.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2 text-gray-600">
                <h2 class="font-semibold text-xl leading-tight">Agregar proveedor</h2>
            </div>
        </div>
        </template>



        <!-- Form -->
            <form @submit.prevent="store"> 
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
                    <div>
                        <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
                            A
                        </IconInput>
                        <InputError :message="form.errors.name" class="mb-3" />
                    </div>
                    <div class="grid gap-6 mb-6 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <IconInput v-model="form.address" inputPlaceholder="Dirección" inputType="text">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </IconInput>
                            <InputError :message="form.errors.address" class="mb-3" />
                        </div>
                        <div class="md:col-span-1">
                            <IconInput v-model="form.post_code" inputPlaceholder="C.P." inputType="text">
                                <i class="fa-solid fa-envelopes-bulk"></i>
                            </IconInput>
                            <InputError :message="form.errors.post_code" class="mb-3" />
                        </div>
                        <div class="md:col-span-2">
                            <IconInput v-model="form.phone" inputPlaceholder="Teléfono *" inputType="text">
                                <i class="fa-solid fa-phone"></i>
                            </IconInput>
                            <InputError :message="form.errors.phone" class="mb-3" />
                        </div>
                    </div>
                    <div class="flex justify-center items-center cursor-pointer bg-secondary-gray rounded-md">
                        <h3 class="text-lg text-secondary text-center font-bold my-2 mx-2">Datos Bancarios</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="flex justify-center items-center cursor-pointer bg-secondary-gray rounded-md">
                        <h3 class="text-lg text-secondary text-center font-bold my-2 mx-2">Contactos</h3>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>                     
                        <div class="mt-2 mx-3 md:text-right">
                            <PrimaryButton :disabled="form.processing"> Agregar proveedor </PrimaryButton>
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
import { ref } from 'vue';

export default {
  data() {
    const form = useForm({
        name: null,
        address: null,
        post_code: null,
        phone: null,

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
methods:{
    store(){
        this.form.post(route('suppliers.store'));
    }
},
};
</script>
