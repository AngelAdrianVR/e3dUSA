<template>
  <div>
    <AppLayout title="Agregar Insumo">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('storages.consumables.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Agregar Insumo
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md md:space-y-4">
          <div>
            <IconInput v-model="brand" @change="generatePartNumber" inputPlaceholder="Marca del producto *" inputType="text">
              <el-tooltip content="Marca del producto (si no tiene marca colocar 'Generico')" placement="top">
                <i class="fa-solid fa-copyright"></i>
              </el-tooltip>
            </IconInput>
          </div>
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
              <el-tooltip content="Nombre" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div class="md:grid gap-x-6 gap-y-2 md:mb-6 grid-cols-2">
            <div class="flex items-center">
              <el-tooltip content="Número de parte *" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  #
                </span>
              </el-tooltip>
              <input v-model="form.part_number" type="text" class="input disabled:cursor-not-allowed disabled:opacity-80"
                placeholder="Número de parte *" disabled>
              <InputError :message="form.errors.part_number" />
            </div> 
            <div>
              <IconInput v-model="form.min_quantity" inputPlaceholder="Stock mínimo" inputType="number" inputStep="0.01">
                <el-tooltip content="Cantidad mínima que puede haber en stock" placement="top">
                  <i class="fa-solid fa-minus"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.min_quantity" />
            </div>
            <div>
              <IconInput v-model="form.max_quantity" inputPlaceholder="Stock máximo" inputType="number" inputStep="0.01">
                <el-tooltip content="Cantidad máxima que puede haber en stock" placement="top">
                  <i class="fa-solid fa-plus"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.max_quantity" />
            </div>
            <div>
              <IconInput v-model="form.initial_stock" inputPlaceholder="Stock de apertura" inputType="number" inputStep="0.01">
                <el-tooltip content="Stock inicial" placement="top">
                  123
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.initial_stock" />
            </div>
            <div class="flex items-center my-2">
              <el-tooltip content="Materias primas" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  <i class="fa-solid fa-ruler-vertical"></i>
                </span>
              </el-tooltip>
              <el-select v-model="form.measure_unit" clearable placeholder="Busca unidad de medida"
                no-data-text="No hay unidades de medida registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="(item, index) in mesureUnits" :key="index" :label="item" :value="item" />
              </el-select>
              <InputError :message="form.errors.measure_unit" />
            </div>
            <div>
              <IconInput v-model="form.cost" inputPlaceholder="Costo *" inputType="number" inputStep="0.01">
                <el-tooltip content="Cuánto le cuesta a e3d adquirir esta materia prima" placement="top">
                  <i class="fa-solid fa-dollar"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.cost" />
            </div>
          </div>
          <div>
            <IconInput v-model="form.location" inputPlaceholder="Ubicaión *" inputType="text">
              <el-tooltip content="Ubicación en almacén" placement="top">
                <i class="fa-solid fa-box"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.location" />
          </div>

          <div class="flex mb-1">
            <span
              class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
              <el-tooltip content="Descripción del producto" placement="top">
                ...
              </el-tooltip>
            </span>
            <textarea v-model="form.description" class="textarea" autocomplete="off" placeholder="Descripción *"
              required></textarea>
            <InputError :message="form.errors.description" />
          </div>
          <div class="col-span-full">
            <div class="flex space-x-2 mb-1">
              <IconInput v-model="newFeature" inputPlaceholder="Ingresa una caracteristica" inputType="text"
                class="w-full">
                <el-tooltip content="Caracteristicas" placement="top">
                  <i class="fa-solid fa-palette"></i>
                </el-tooltip>
              </IconInput>
              <SecondaryButton @click="addFeature" type="button">
                Agregar
                <i class="fa-solid fa-arrow-down ml-2"></i>
              </SecondaryButton>
            </div>
            <el-select v-model="form.features" multiple clearable placeholder="Caracteristicas"
              no-data-text="Agrega primero una caracteristica">
              <el-option v-for="feature in features" :key="feature" :label="feature" :value="feature"></el-option>
            </el-select>
          </div>
          <div class="col-span-full mt-2">
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                <el-tooltip content="Imagen del producto" placement="top">
                  <i class="fa-solid fa-images"></i>
                </el-tooltip>
              </span>
              <input @input="form.media = $event.target.files[0]"
                class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                aria-describedby="file_input_help" id="file_input" type="file" />
            </div>
            <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
              SVG, PNG, JPG o GIF (MAX. 4 MB).
            </p>
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
      name: null,
      part_number: null,
      measure_unit: null,
      min_quantity: null,
      max_quantity: null,
      cost: null,
      description: null,
      initial_stock: null,
      location: null,
      type: 'consumible',
      features: null,
      media: null,
      brand: null,
    });

    return {
      form,
      newFeature: null,
      features: [],
      mesureUnits: [
        'Pieza(s)',
        'Litro(s)',
        'Par(es)',
        'kilogramo(s)',
        'Metro(s)',
        'Rollo(s)',
        'Galon(es)',
        'Cubeta(s)',
        'Bote(s)',
      ],
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
  props: {},
  methods: {
    store() {
      this.form.post(route("raw-materials.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Insumo agregado",
            type: "success",
          });
        },
      });
    },
    generatePartNumber() {
      const partNumber = 'INS' + '-' + this.brand?.toUpperCase().substr(0,3) + '-';
      this.form.part_number = partNumber;
    },
    addFeature() {
      if (this.newFeature.trim() !== "") {
        this.form.features.push(this.newFeature);
        this.features.push(this.newFeature);
        this.newFeature = "";
      }
    },

  },
};
</script>
