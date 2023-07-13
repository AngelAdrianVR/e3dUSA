<template>
  <div>
    <AppLayout title="Scrap">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('storages.scraps.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Mandar producto a scrap
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div>
            <el-select
              v-model="form.storage_id"
              @change="storageableObj"
              class="my-2"
              placeholder="Selecciona el producto"
            >
              <el-option
                v-for="item in storages"
                :key="item.id"
                :label="item.storageable.name"
                :value="item.id"
              />
            </el-select>
            <InputError :message="form.errors.storage_id" />
          </div>
          <div class="md:grid gap-6 mb-6 grid-cols-2">
            <div>
              <IconInput
                v-model="form.quantity"
                inputPlaceholder="Cantidad para scrap"
                inputType="number"
                inputStep="0.1"
              >
              <el-tooltip content="Cantidad para scrap" placement="top">
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
            v-show="storage_selected"
            class="text-gray-500 bg-secondary-gray rounded-lg p-4 flex justify-between"
          >
          <div class="bg-gray-300 h-48 w-1/2 rounded-lg">
                imagen
         </div>
         
            <ul>
              <li>
                <label class="text-primary">Nombre: </label>
                {{ storage_selected?.storageable.name }}
              </li>
              <li>
                <label class="text-primary">Número de parte: </label>
                {{ storage_selected?.storageable.part_number }}
              </li>
              <li>
                <label class="text-primary">Descripción: </label>
                {{ storage_selected?.storageable.description }}
              </li>
              <li>
                <label class="text-primary">Tipo: </label> {{
                  storage_selected?.type
                }}
              </li>
              <li>
                <label class="text-primary">Stock: </label>
                {{ storage_selected?.quantity }} {{ storage_selected?.storageable.measure_unit  }}
              </li>
              <li>
                <label class="text-primary">costo: </label> ${{
                  storage_selected?.storageable.cost * form.quantity
                }}
              </li>
            </ul>
                
          </div>
          <div class="mt-2 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Mandar a scrap
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
import { ref } from "vue";

export default {
  data() {
    const form = useForm({
      storage_id: null,
      quantity: 1,
      location: null,
      type: "scrap",
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
      this.form.post(route("storages.scraps.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Producto mandado a scrap",
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
