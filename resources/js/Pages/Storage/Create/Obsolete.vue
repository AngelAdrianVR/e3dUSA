<template>
  <div>
    <AppLayout title="Agregar producto a obsoleto">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Mandar producto a obsoleto
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md space-y-1">
          <h1 class="font-bold text-lg">Cambiar producto de almacén a obsoletos</h1>
          <div class="flex items-center bg-secondarylight text-secondary px-3 py-1 rounded-[5px]">
            <div class="rounded-full border border-secondary w-3 h-3 flex items-center justify-center mr-2">
              <i class="fa-solid fa-info text-secondary text-[7px]"></i>
            </div>
            <p class="text-xs">Este almacen es para aquellos productos que han sido reemplazados por versiones recientes. </p>
          </div>
          <div>
            <label class="text-sm ml-2">Producto Obsoleto *</label>
            <el-select v-model="form.storage_id" @change="storageableObj" placeholder="Selecciona el producto obsoleto"
              filterable>
              <el-option v-for="item in storages" :key="item.id" :label="item.storageable?.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.storage_id" />
          </div>
          <div>
            <div class="flex space-x-2 items-center">
              <label class="text-sm ml-2">Ubicación *</label>
              <el-tooltip content="Es el lugar en almacén donde pueden encontrar el producto" placement="top">
                <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                  <i class="fa-solid fa-info text-primary text-[7px]"></i>
                </div>
              </el-tooltip>
            </div>
            <input v-model="form.location" placeholder="Escribe la ubicación del producto" class="input" type="text"
              required>
            <InputError :message="form.errors.location" />
          </div>
          <div v-show="storage_selected" class="flex space-x-2 items-center">
            <label class="text-sm ml-2">Información del producto</label>
            <el-tooltip content="Verifica que los datos del producto seleccionado sean correctos" placement="top">
              <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                <i class="fa-solid fa-info text-primary text-[7px]"></i>
              </div>
            </el-tooltip>
          </div>
          <div v-show="storage_selected" class="bg-secondary-gray grid grid-cols-3 gap-x-2 rounded-lg p-4 text-sm">
            <figure class="h-full rounded-[10px] mr-5">
              <el-image style="height: 100%; border-radius: 10px;"
                :src="storage_selected?.storageable?.media" fit="contain">
                <template #error>
                  <div class="flex justify-center items-center text-[#ababab]">
                    <i class="fa-solid fa-image text-6xl"></i>
                  </div>
                </template>
              </el-image>
            </figure>
            <ul class="px-4 col-span-2">
              <li class="flex">
                <label class="font-bold mr-2 w-1/3">Nombre: </label>
                {{ storage_selected?.storageable?.name }}
              </li>
              <li class="flex">
                <label class="font-bold mr-2 w-1/3">Número de parte: </label>
                {{ storage_selected?.storageable?.part_number }}
              </li>
              <li class="flex">
                <label class="font-bold mr-2 w-1/3">Descripción: </label>
                {{ storage_selected?.storageable?.description }}
              </li>
              <li class="flex">
                <label class="font-bold mr-2 w-1/3">Tipo: </label> {{
                  storage_selected?.type
                }}
              </li>
              <li class="flex">
                <label class="font-bold mr-2 w-1/3">Stock: </label>
                {{ storage_selected?.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ storage_selected?.storageable?.measure_unit }}
              </li>
              <li class="flex">
                <label class="font-bold mr-2 w-1/3">costo: </label> ${{
                  (storage_selected?.storageable?.cost *  storage_selected?.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                }}
              </li>
            </ul>
          </div>
          <div class="pt-6 md:text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Guardar
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
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

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
    InputError,
    IconInput,
    Back,
    Link
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
