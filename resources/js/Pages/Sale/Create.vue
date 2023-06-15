<template>
    <div>
        <AppLayout title="Órdenes de venta - Crear">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('sales.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2 text-gray-600">
                <h2 class="font-semibold text-xl leading-tight mr-24">Crear órden de venta</h2>
            </div>
        </div>
        </template>



        <!-- Form -->
            <form @submit.prevent="store"> 
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
                    <div>
                        <IconInput v-model="form.business_name" inputPlaceholder="Cliente" inputType="text">
                            A
                        </IconInput>
                        <InputError :message="form.errors.business_name" class="mb-3" />
                    </div>
                        <h3 class="text-lg text-secondary text-center font-bold my-2">Logistica</h3>
                    <div class="grid gap-6 mb-6 md:grid-cols-3">
                        <div>
                            <IconInput v-model="form.business_name" inputPlaceholder="Paquetería" inputType="text">
                                A
                            </IconInput>
                            <InputError :message="form.errors.business_name" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.phone" inputPlaceholder="Costo logística" inputType="text">
                                <i class="fa-solid fa-phone"></i>
                            </IconInput>
                            <InputError :message="form.errors.phone" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.rfc" inputPlaceholder="Moneda" inputType="text">
                                <i class="fa-solid fa-sheet-plastic"></i>
                            </IconInput>
                            <InputError :message="form.errors.rfc" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.post_code" inputPlaceholder="Guía" inputType="text">
                                <i class="fa-solid fa-envelopes-bulk"></i>
                            </IconInput>
                            <InputError :message="form.errors.post_code" class="mb-3" />
                        </div>
                    </div>
                        <h3 class="text-lg text-secondary text-center font-bold my-2">Datos de la órden</h3>
                        <div class="grid gap-6 mb-6 md:grid-cols-3">
                            <div>
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Prioridad">
                                    <i class="fa-solid fa-building"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" class="mb-3" />
                            </div>
                            <div>
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Medio de petición">
                                    <i class="fa-solid fa-building"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" class="mb-3" />
                            </div>
                            <div>
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Factura">
                                    <i class="fa-solid fa-building"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" class="mb-3" />
                            </div>
                            <div class="md:col-span-3">
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Nombre/folio OCE">
                                    <i class="fa-solid fa-building"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" class="mb-3" />
                            </div>
                            <div>
                                <label for="">Archivo OCE</label>
                                <input type="file" name="" id="">
                            </div>
                        </div>
                        <div class="mt-2 mx-3 md:text-right">
                            <PrimaryButton :disabled="form.processing"> Crear órden de venta </PrimaryButton>
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
        shopping_company: null,
        freight_cost: null,
        status: null,
        oce_name: null,
        order_via: null,
        tracking_guide: null,
        authorized_user_name: null,
        notes: null,

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
        this.form.post(route('sales.store'));
    }
},
};
</script>
