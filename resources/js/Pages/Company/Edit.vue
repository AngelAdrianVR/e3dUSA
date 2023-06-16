<template>
    <div>
        <AppLayout title="Clientes - Editar">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('companies.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2 text-gray-600">
                <h2 class="font-semibold text-xl leading-tight">Editar cliente {{ company.business_name }}</h2>
            </div>
        </div>
        </template>



        <!-- Form -->
            <form @submit.prevent="update"> 
                <div class="md:w-2/3 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <IconInput v-model="form.business_name" inputPlaceholder="Nombre" inputType="text">
                                A
                            </IconInput>
                            <InputError :message="form.errors.business_name" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.phone" inputPlaceholder="Teléfono" inputType="text">
                                <i class="fa-solid fa-phone"></i>
                            </IconInput>
                            <InputError :message="form.errors.phone" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.rfc" inputPlaceholder="RFC" inputType="text">
                                <i class="fa-solid fa-sheet-plastic"></i>
                            </IconInput>
                            <InputError :message="form.errors.rfc" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.post_code" inputPlaceholder="Código postal" inputType="text">
                                <i class="fa-solid fa-envelopes-bulk"></i>
                            </IconInput>
                            <InputError :message="form.errors.post_code" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.fiscal_address" inputPlaceholder="Domicilio fiscal">
                                <i class="fa-solid fa-building"></i>
                            </IconInput>
                            <InputError :message="form.errors.fiscal_address" class="mb-3" />
                        </div>
                    </div>
                        <div class="mt-2 mx-3 md:text-right">
                            <PrimaryButton :disabled="form.processing"> Actualizar Cliente </PrimaryButton>
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
        business_name: this.company.business_name,
        phone: this.company.phone,
        rfc: this.company.rfc,
        post_code: this.company.post_code,
        fiscal_address: this.company.fiscal_address,

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
    company: Object
  },
methods:{
    store(){
        this.form.put(route('companies.update', this.company));
    }
},
};
</script>
