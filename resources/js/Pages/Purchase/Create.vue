<template>
    <div>
        <AppLayout title="Órdenes de compra - Crear">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('purchases.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2 text-gray-600">
                <h2 class="font-semibold text-xl leading-tight">Crear órden de compra</h2>
            </div>
        </div>
        </template>



        <!-- Form -->
            <form @submit.prevent="store"> 
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
                    <div>
                        <select class="input h-10">
                            <option selected disabled>-- Selecciona un proveedor --</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" value="US">{{ supplier.name }}</option>
                        </select>
                    </div>
                        <h3 class="text-lg text-secondary text-center font-bold my-2">Logistica</h3>
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <IconInput v-model="form.address" inputPlaceholder="Paquetería" inputType="text">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </IconInput>
                            <InputError :message="form.errors.address" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.post_code" inputPlaceholder="Guía" inputType="text">
                                <i class="fa-solid fa-envelopes-bulk"></i>
                            </IconInput>
                            <InputError :message="form.errors.post_code" class="mb-3" />
                        </div>
                    </div>
                        <h3 class="text-lg text-secondary text-center font-bold my-2">Datos de la órden</h3>
                        <div class="md:col-span-2">
                        <label for="">Fecha esperada de llegada</label>
                            <IconInput v-model="form.phone" inputPlaceholder="Fecha esperada de llegada *" inputType="date">
                                <i class="fa-solid fa-calendar"></i>
                            </IconInput>
                            <InputError :message="form.errors.phone" class="mb-3" />
                        </div>
                        <div class="flex">
                            <span class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="form.description" class="textarea" autocomplete="off" placeholder="Notas" required></textarea>
                            <InputError :message="form.errors.description" class="mb-3" />
                        </div>
                        <div class="mt-2 mx-3 md:text-right">
                            <PrimaryButton :disabled="form.processing"> Crear órden </PrimaryButton>
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
    suppliers: Array
  },
methods:{
    store(){
        this.form.post(route('purchases.store'));
    }
},
};
</script>
