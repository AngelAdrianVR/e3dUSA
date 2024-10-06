<template>
    <AppLayout title="Registrar tarifa">
      <template #header>
        <div class="flex justify-between">
          <Back :route="'boxes.index'"/>
          <div class="flex items-center space-x-2">
                <h2 class="font-semibold text-xl leading-tight">Registrar tarifa</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg py-4 px-9 shadow-md space-y-4">
            <h2>Detalles del producto</h2>

            <section class="grid grid-cols-3 gap-5">
                <article class="col-span-2">
                    <div class="mb-3">
                        <div class="flex justify-between mb-1 ml-3">
                            <InputLabel value="Producto*" />
                            <InputLabel v-if="form.catalog_product_id" :value="'N. Parte:' + catalog_products.find(item => item.id === form.catalog_product_id).part_number" />
                        </div>
                        <el-select @change="fetchCatalogProductInfo()" class="w-full" filterable v-model="form.catalog_product_id"
                            placeholder="Seleccione" no-data-text="No hay opciones registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in catalog_products" :key="item" :label="item.name" :value="item.id">
                                <p class="flex items-center justify-between">
                                    <span>{{ item.name }}</span>
                                    <span class="text-[10px] text-gray99">({{ item.number_part }})</span>
                                </p>
                            </el-option>
                        </el-select>
                        <InputError :message="form.errors.catalog_product_id" />
                    </div>
                    <div>
                        <InputLabel value="Cantidad*" />
                        <el-input v-model="form.quantity" placeholder="Ej. 300" :maxlength="5" clearable />
                        <InputError :message="form.errors.quantity" />
                    </div>
                </article>

                <figure class="border border-[#999999] rounded-lg">
                    <p v-if="!form.catalog_product_id" class="text-sm mx-4 my-5"><i class="fa-solid fa-arrow-left"></i> Selecciona el producto para visualizar la imagen</p>
                    <img v-if="catalogProductSelected" class="object-contain" :src="catalogProductSelected.media[0].original_url" alt="Producto de catálogo">
                </figure>
            </section>

            <div class="text-center pt-5">
                <figure class="w-48 mx-auto">
                    <img src="@/../../public/images/paralelepipedo.png" alt="">
                </figure>
            </div>

            <div class="mt-9 mx-3 md:text-right">
                <PrimaryButton :disabled="form.processing">
                    <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                    Guardar
                </PrimaryButton>
            </div>
        </div>
      </form>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    const form = useForm({
      catalog_product_id: null,
      quantity: null,
      height: null,
      width: null,
    });

    return {
      form,
      catalogProductSelected: null,
    };
  },
  components: {
    SecondaryButton,
    PrimaryButton,
    InputError,
    InputLabel,
    AppLayout,
    Back,
    Link
  },
  props:{
    catalog_products: Array
  },
  methods: {
    store() {
      this.form.post(route("boxes.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    async fetchCatalogProductInfo() {
        try {
            const response = await axios.get(route('shipping-rates.fetch-catalog-product-info', this.form.catalog_product_id));
            if ( response.status === 200 ) {
                this.catalogProductSelected = response.data.item;
                console.log(this.shipping-rates.fetch-catalog-product-info);
            }
        } catch (error) {
            console.log(error);
        }
    }
  },
};
</script>
