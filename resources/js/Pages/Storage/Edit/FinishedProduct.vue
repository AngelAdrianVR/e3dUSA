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
              Editar "{{ finished_product?.name }}
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div class="md:grid gap-6 mb-6 grid-cols-2">
          <div>
              <el-select
                v-model="form.storageable_id"
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
              <InputError :message="form.errors.storageable_id" />
            </div>
            <div>
              <IconInput
                v-model="form.quantity"
                inputPlaceholder="Stock de apertura"
                inputType="number"
              >
                123
              </IconInput>
              <InputError :message="form.errors.quantity" />
            </div>
          </div>
          <div class="text-gray-500">
          <ul>
          <li><label for="">detalle del producto temrinado</label></li>
          <li><label for="">detalle del producto temrinado</label></li>
          <li><label for="">detalle del producto temrinado</label></li>
          <li><label for="">detalle del producto temrinado</label></li>
          <li><label for="">detalle del producto temrinado</label></li>
          </ul>
          </div>
          <div class="mt-2 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing"> Agregar </PrimaryButton>
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
      storageable_id: null,
      quantity: null,
      type: 'producto-terminado',
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
    finished_product: Object
  },
  methods: {
    update() {
      this.form.put(route("storages.update", this.storage), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Producto agregado",
            type: "success",
          });
        },
      });
    },
  },
};
</script>
