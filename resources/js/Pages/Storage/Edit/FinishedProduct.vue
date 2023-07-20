<template>
  <div>
    <AppLayout title="Editar Producto terminado">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('storages.finished-products.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar producto final
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div>
              <el-select
                v-model="form.storageable_id"
                @change="storageableObj"
                class="my-2"
                placeholder="Selecciona un producto del catálogo"
              >
                <el-option
                  v-for="item in catalog_products"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
              <!-- <InputError :message="form.errors.storageable_id" /> -->
            </div>
          <div class="md:grid gap-6 mb-6 grid-cols-2">
            <div>
              <IconInput
                v-model="form.quantity"
                inputPlaceholder="Cantidad en stock"
                inputType="number"
              >
              <el-tooltip content="Cantidad en stock del producto terminado" placement="top">
                123
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.quantity" />
            </div>

            <div>
              <IconInput
                v-model="form.location"
                inputPlaceholder="Ubicaión *"
                inputType="text"
              >
              <el-tooltip content="Ubicación en almacén" placement="top">
                <i class="fa-solid fa-box"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.location" />
            </div>
          </div>
          
          <div
            v-show="catalog_product_selected"
            class="text-gray-500 bg-secondary-gray rounded-lg p-4 flex justify-between"
          >
          <div class="bg-gray-300 h-48 w-1/2 rounded-lg">
                imagen
         </div>
            <ul>
              <li>
                <label class="text-primary">Nombre: </label>
                {{ catalog_product_selected?.name }}
              </li>
              <li>
                <label class="text-primary">Número de parte: </label>
                {{ catalog_product_selected?.part_number }}
              </li>
              <li>
                <label class="text-primary">Descripción: </label>
                {{ catalog_product_selected?.description }}
              </li>
              <li>
                <label class="text-primary">Stock: </label>
                {{ catalog_product_selected?.quantity }} {{ catalog_product_selected?.measure_unit  }}
              </li>
              <li>
                <label class="text-primary">costo: </label> ${{
                  catalog_product_selected?.cost * form.quantity
                }}
              </li>
            </ul>
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import { ref } from "vue";

export default {
  data() {
    const form = useForm({
      storageable_id: this.storage.storageable_id,
      quantity: this.storage.quantity,
      location: this.storage.location,
      type: 'producto-terminado',
    });

    return {
      form,
      catalog_product_selected: null
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
    catalog_products: Array,
    storage: Object
  },
  methods: {
    update() {
      this.form.put(route("storages.update", this.storage), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Producto editado",
            type: "success",
          });
        },
      });
    },
    storageableObj() {
      //save the storageable obj using storageable id form form.
      this.catalog_product_selected = null;
      this.catalog_product_selected = this.catalog_products.find(
        (item) => item.id == this.form.storageable_id
      );
    },
  },

  mounted() {
    this.catalog_product_selected = null;
      this.catalog_product_selected = this.catalog_products.find(
        (item) => item.id == this.form.storageable_id
      );
  },
};
</script>
