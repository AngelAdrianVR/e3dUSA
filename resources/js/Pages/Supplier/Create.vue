<template>
    <div>
        <AppLayout title="Proveedores - Crear">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('suppliers.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2">
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
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="md:grid gap-6 mb-6 grid-cols-3">
                        <div class="col-span-2">
                            <IconInput v-model="form.address" inputPlaceholder="Dirección" inputType="text">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </IconInput>
                            <InputError :message="form.errors.address" />
                        </div>
                        <div class="col-span-1">
                            <IconInput v-model="form.post_code" inputPlaceholder="C.P." inputType="text">
                                <i class="fa-solid fa-envelopes-bulk"></i>
                            </IconInput>
                            <InputError :message="form.errors.post_code" />
                        </div>
                        <div class="col-span-2">
                            <IconInput v-model="form.phone" inputPlaceholder="Teléfono *" inputType="text">
                                <i class="fa-solid fa-phone"></i>
                            </IconInput>
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>

                            <!-- ---------------- Datos bancarios starts ----------------- -->
                            <el-divider content-position="left">Datos bancarios</el-divider>
                            <div class="bg-[#b8b7b7] rounded-lg p-5">
                            <div class="md:grid gap-6 mb-6 grid-cols-2">
                                <div>
                                    <IconInput v-model="form.phone" inputPlaceholder="Nombre del beneficiario *" inputType="text">
                                        A
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                                <div>
                                    <IconInput v-model="form.phone" inputPlaceholder="Número de cuenta *" inputType="text">
                                        <i class="fa-solid fa-money-check-dollar"></i>
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                                <div>
                                    <IconInput v-model="form.phone" inputPlaceholder="Clabe *" inputType="text">
                                        #
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                                <div>
                                    <IconInput v-model="form.phone" inputPlaceholder="Banco *" inputType="text">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                            </div>
                                <SecondaryButton :disabled="form.processing"> Agregar Información </SecondaryButton>
                            </div>
                            <!-- ---------------- Datos bancarios ends ----------------- -->

                            <!-- ---------------- contacts starts ----------------- -->
                            <el-divider content-position="left">Contactos</el-divider> 
                            <div class="bg-[#b8b7b7] rounded-lg p-5">
                                <div class="md:grid gap-6 mb-6 grid-cols-2">
                                <div class="col-span-2">
                                    <IconInput v-model="form.phone" inputPlaceholder="Nombre *" inputType="text">
                                        A
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                                <div>
                                    <IconInput v-model="form.phone" inputPlaceholder="Correo *" inputType="text">
                                        <i class="fa-solid fa-money-check-dollar"></i>
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                                <div>
                                    <IconInput v-model="form.phone" inputPlaceholder="Teléfono *" inputType="text">
                                        #
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                                <div>
                                    <IconInput v-model="form.phone" inputPlaceholder="Banco *" inputType="date">
                                        <i class="fa-solid fa-cake"></i>
                                    </IconInput>
                                    <InputError :message="form.errors.phone" />
                                </div>
                            </div>            
                                <SecondaryButton :disabled="form.processing"> Agregar Contacto </SecondaryButton>
                            </div>
                            <!-- ---------------- contacts ends ----------------- -->
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
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
    SecondaryButton,
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
