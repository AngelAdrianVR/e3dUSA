<template>
    <div>
        <AppLayout title="Órdenes de venta - Crear">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('sales.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2 text-gray-600">
                <h2 class="font-semibold text-xl leading-tight">Crear órden de venta</h2>
            </div>
        </div>
        </template>



        <!-- Form -->
            <form @submit.prevent="store"> 
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
                    <div>
                    <el-select v-model="form.company_branch_id" class="mt-2" placeholder="Selecciona un cliente">
                                    <el-option
                                    v-for="item in companies"
                                    :key="item.id"
                                    :label="item.business_name"
                                    :value="item.id"
                                    />
                    </el-select>
                    </div>

                    <el-divider content-position="left">Logistica</el-divider>


                    <div class="grid gap-6 mb-6 md:grid-cols-3">
                        <div>
                            <IconInput v-model="form.business_name" inputPlaceholder="Paquetería" inputType="text">
                                <i class="fa-solid fa-truck-fast"></i>
                            </IconInput>
                            <InputError :message="form.errors.business_name" />
                        </div>
                        <div>
                            <IconInput v-model="form.phone" inputPlaceholder="Costo logística" inputType="text">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </IconInput>
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div>
                            <IconInput v-model="form.rfc" inputPlaceholder="Moneda" inputType="text">
                                <i class="fa-solid fa-coins"></i>
                            </IconInput>
                            <InputError :message="form.errors.rfc" />
                        </div>
                        <div>
                            <IconInput v-model="form.post_code" inputPlaceholder="Guía" inputType="text">
                                <i class="fa-solid fa-magnifying-glass-location"></i>
                            </IconInput>
                            <InputError :message="form.errors.post_code" />
                        </div>
                    </div>

                        <el-divider content-position="left">Datos de la órden</el-divider>
                        <div class="grid gap-6 mb-6 md:grid-cols-3">
                            <div>
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Prioridad">
                                    <i class="fa-solid fa-exclamation"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" />
                            </div>
                            <div>
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Medio de petición">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" />
                            </div>
                            <div>
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Factura">
                                    <i class="fa-solid fa-money-check-dollar"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" />
                            </div>
                            <div class="md:col-span-3">
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="Nombre/folio OCE">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" />
                            </div>
                            <div>
                                <label for="">Archivo OCE</label>
                                <input type="file" name="" id="">
                            </div>
                        </div>

                        <!-- products -->
                        <el-divider content-position="left">Agregar productos</el-divider>
                        <div v-if="form.company_branch_id" class="grid gap-6 mb-6 md:grid-cols-4 rounded-lg bg-[#b8b7b7] px-5 py-3 col-span-full space-y-1 my-7">
                            <div class="md:col-span-3">
                                <el-select v-model="form.company_branch_id" class="mt-2" placeholder="Selecciona un cliente">
                                    <el-option
                                    v-for="item in companies"
                                    :key="item.id"
                                    :label="item.business_name"
                                    :value="item.id"
                                    />
                                </el-select>
                            </div>
                            <div class="md:col-span-1">
                                <IconInput v-model="form.fiscal_address" inputPlaceholder="cantidad" inputType="number">
                                </IconInput>
                                <InputError :message="form.errors.fiscal_address" />
                            </div>
                        <div class="flex md:col-span-4">
                            <span class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="form.notes" class="textarea" autocomplete="off" placeholder="Notas" required></textarea>
                            <InputError :message="form.errors.notes" />
                        </div>
                        <SecondaryButton class="md:col-span-2 md:col-start-2 mx-auto" :disabled="form.processing"> Agregar Producto </SecondaryButton>
                        </div>
                        <div class="flex">
                            <span class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="form.notes" class="textarea" autocomplete="off" placeholder="Notas" required></textarea>
                            <InputError :message="form.errors.notes" />
                        </div>
                        <div class="mt-7 mx-3 md:text-right">
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import { ref } from 'vue';

export default {
  data() {
    const form = useForm({
        company_branch_id: null,
        contact_id: null,
        shipping_company: null,
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
    SecondaryButton,
    PrimaryButton,
    Link,
    InputError,
    IconInput,
  },
  props: {
    companies: Array
  },
methods:{
    store(){
        this.form.post(route('sales.store'));
    }
},
};
</script>
