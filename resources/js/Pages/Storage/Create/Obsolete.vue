<template>
  <div>
    <AppLayout title="Agregar producto a obsoleto">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('storages.obsolete.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Mandar producto a obsoleto
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-1">
          <div class="flex items-center">
            <span
              class="font-bold text-xl inline-flex items-center text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
              <el-tooltip content="Selecciona el producto obsoleto" placement="top">
                <i class="fa-brands fa-product-hunt"></i>
              </el-tooltip>
            </span>
            <el-select v-model="form.storage_id" @change="storageableObj" class="my-2"
              placeholder="Selecciona el producto" filterable>
              <el-option v-for="item in storages" :key="item.id" :label="item.storageable?.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.storage_id" />
          </div>
          <div>
            <IconInput v-model="form.location" inputPlaceholder="Ubicaión *" inputType="text">
              <el-tooltip content="Ubicación en almacén" placement="top">
                <i class="fa-solid fa-box"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.location" />
          </div>
          <div v-show="storage_selected" class="text-gray-500 bg-secondary-gray rounded-lg p-4 flex">
            <figure class="bg-[#D9D9D9] h-52 rounded-[10px] mr-5">
              <el-image style="height: 100%; border-radius: 10px;"
                :src="storage_selected?.storageable?.media[0]?.original_url" fit="contain">
                <template #error>
                  <div class="flex justify-center items-center text-[#ababab]">
                    <i class="fa-solid fa-image text-6xl"></i>
                  </div>
                </template>
              </el-image>
            </figure>
            <ul class="px-4">
              <li>
                <label class="text-primary">Nombre: </label>
                {{ storage_selected?.storageable?.name }}
              </li>
              <li>
                <label class="text-primary">Número de parte: </label>
                {{ storage_selected?.storageable?.part_number }}
              </li>
              <li>
                <label class="text-primary">Descripción: </label>
                {{ storage_selected?.storageable?.description }}
              </li>
              <li>
                <label class="text-primary">Tipo: </label> {{
                  storage_selected?.type
                }}
              </li>
              <li>
                <label class="text-primary">Stock: </label>
                {{ storage_selected?.quantity }} {{ storage_selected?.storageable?.measure_unit }}
              </li>
              <li>
                <label class="text-primary">costo: </label> ${{
                  (storage_selected?.storageable?.cost * form.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                }}
              </li>
            </ul>
          </div>
          <div class="pt-6 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Mandar a obsoleto
            </PrimaryButton>
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

export default {
  data() {
    const form = useForm({
      storage_id: null,
      location: null,
    });

    return {
      form,
      storage_selected: null,
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
    storages: Array,
  },
  methods: {
    store() {
      this.form.post(route("storages.obsolete.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Producto mandado a obsoleto",
            type: "success",
          });
        },
      });
    },
    storageableObj() {
      //save the storageable obj using storageable id form form.
      this.storage_selected = null;
      this.storage_selected = this.storages.find(
        (item) => item.id == this.form.storage_id
      );
    },
  },
};
</script>
