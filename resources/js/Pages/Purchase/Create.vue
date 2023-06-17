<template>
    <div>
        <AppLayout title="Órdenes de compra - Crear">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('purchases.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2">
                <h2 class="font-semibold text-xl leading-tight">Crear órden de compra</h2>
            </div>
        </div>
        </template>



        <!-- Form -->
            <form @submit.prevent="store"> 
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
                    <div>
                                <el-select v-model="form.supplier_id" class="mt-2" placeholder="Selecciona un cliente">
                                    <el-option
                                    v-for="item in suppliers"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id"
                                    />
                                </el-select>
                    </div>

                        <el-divider content-position="left">Logística</el-divider>
                    <div class="md:grid gap-6 mb-6 grid-cols-2">
                        <div>
                            <IconInput v-model="form.address" inputPlaceholder="Paquetería" inputType="text">
                                <i class="fa-solid fa-truck-fast"></i>
                            </IconInput>
                            <InputError :message="form.errors.address" />
                        </div>
                        <div>
                            <IconInput v-model="form.post_code" inputPlaceholder="Guía" inputType="text">
                                <i class="fa-solid fa-magnifying-glass-location"></i>
                            </IconInput>
                            <InputError :message="form.errors.post_code" />
                        </div>
                    </div>


                        <el-divider content-position="left">Datos de la órden</el-divider>
                        <div class="col-span-2">
                        <label for="">Fecha esperada de llegada</label>
                            <IconInput v-model="form.phone" inputPlaceholder="Fecha esperada de llegada *" inputType="date">
                                <i class="fa-solid fa-calendar"></i>
                            </IconInput>
                            <InputError :message="form.errors.phone" />
                        </div>

                        <el-divider content-position="left">Productos</el-divider>
                        <div class="space-y-3 bg-[#b8b7b7] rounded-lg p-5">
                            <div class="md:grid gap-6 mb-6 grid-cols-2">
                                <div>
                                    <el-select v-model="form.supplier_id" class="mt-2" placeholder="Selecciona un cliente">
                                            <el-option
                                            v-for="item in raw_materials"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.id"
                                            />
                                        </el-select>
                                    <InputError :message="form.errors.address" />
                                </div>
                                <div>
                                    <IconInput v-model="form.post_code" inputPlaceholder="Cantidad" inputType="number">
                                        #
                                    </IconInput>
                                    <InputError :message="form.errors.post_code" />
                                </div>
                            </div>
                        </div>
                        <div class="flex">
                            <span class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="form.description" class="textarea" autocomplete="off" placeholder="Notas" required></textarea>
                            <InputError :message="form.errors.description" />
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
        status: null,
        notes: null,
        expected_delivery_date: null,
        is_iva_included: null,
        supplier_id: null,
        contact_id: null,

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
    suppliers: Array,
    raw_materials: Array,
  },
methods:{
    store(){
        this.form.post(route('purchases.store'));
    }
},
};
</script>
