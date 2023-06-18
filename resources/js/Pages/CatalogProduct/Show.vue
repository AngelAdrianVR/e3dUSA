<template>
    <div>
        <AppLayout title="Catalogo de productos - Editar">
        <template #header>
        <div class="flex justify-between">
        <Link :href="route('catalog-products.index')" class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
        </Link>
            <div class="flex items-center space-x-2">
                <h2 class="font-semibold text-xl leading-tight">Editar producto "{{ catalog_product.name }}"</h2>
            </div>
        </div>
        </template>



        <!-- Form -->
            <form @submit.prevent="update"> 
                <div class="md:w-2/3 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <IconInput v-model="form.name" inputPlaceholder="Nombre" inputType="text">
                                A
                            </IconInput>
                            <InputError :message="form.errors.name" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.part_number" inputPlaceholder="Número de parte" inputType="text">
                                #
                            </IconInput>
                            <InputError :message="form.errors.part_number" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.measure_unit" inputPlaceholder="Unidad de medida" inputType="text">
                                <i class="fa-solid fa-ruler-vertical"></i>
                            </IconInput>
                            <InputError :message="form.errors.measure_unit" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.cost" inputPlaceholder="Costo" inputType="number" inputStep="0.1">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </IconInput>
                            <InputError :message="form.errors.cost" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.min_quantity" inputPlaceholder="Cantidad mínima" inputType="number">
                                <i class="fa-solid fa-minus"></i>
                            </IconInput>
                            <InputError :message="form.errors.min_quantity" class="mb-3" />
                        </div>
                        <div>
                            <IconInput v-model="form.max_quantity" inputPlaceholder="Cantidad máxima" inputType="number">
                                <i class="fa-solid fa-plus"></i>
                            </IconInput>
                            <InputError :message="form.errors.max_quantity" class="mb-3" />
                        </div>
                        <div class="flex">
                            <span class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="form.description" id="description" class="textarea" autocomplete="off" placeholder="Descripción" required></textarea>
                            <InputError :message="form.errors.description" class="mb-3" />
                        </div>
                        
                    </div>
                        <div class="mt-2 mx-3 md:text-right">
                            <PrimaryButton :disabled="form.processing"> Actualizar </PrimaryButton>
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
        name: this.catalog_product.name,
        part_number: this.catalog_product.part_number,
        measure_unit: this.catalog_product.measure_unit,
        cost: this.catalog_product.cost,
        min_quantity: this.catalog_product.min_quantity,
        max_quantity: this.catalog_product.max_quantity,
        description: this.catalog_product.description
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
    catalog_product: Object
  },
methods:{
    update(){
        this.form.put(route('catalog-products.update', this.catalog_product));
    }
},
};
</script>
