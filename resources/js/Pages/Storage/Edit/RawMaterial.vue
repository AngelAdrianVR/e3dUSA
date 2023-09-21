<template>
  <div>
    <AppLayout title="Editar Materia prima">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('storages.raw-materials.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar "{{ raw_material.data.name }}"
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md md:space-y-4">
        <div class="flex items-center">
            <el-tooltip content="Tipo de producto (necesario para generar el número de parte)" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-tag"></i>
              </span>
            </el-tooltip>
            <el-select @change="generatePartNumber" v-model="productType" placeholder="Tipo de producto *">
              <el-option v-for="item in productTypes" :key="item.value" :label="item.label" :value="item.value">
                <span style="float: left">{{ item.label }}</span>
                <span style="
                  float: right;
                  color: #cccccc;
                  font-size: 13px;
                  ">{{ item.value }}</span>
              </el-option>
            </el-select>
          </div>
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
              <IconInput v-model="form.initial_stock" inputPlaceholder="Stock actual" inputType="number" inputStep="0.01">
                <el-tooltip content="Stock actual" placement="top">
                  123
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.initial_stock" />
            </div>
            <div class="flex items-center ">
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
              <div>
                <IconInput v-model="form.cost" inputPlaceholder="Costo *" inputType="number" inputStep="0.01">
                  <el-tooltip content="Cuánto le cuesta a e3d adquirir esta materia prima" placement="top">
                    <i class="fa-solid fa-dollar"></i>
                  </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.cost" />
              </div>
            </div>
            <div class="col-span-2">
              <IconInput v-model="form.location" inputPlaceholder="Ubicaión *" inputType="text">
                <el-tooltip content="Ubicación en almacén" placement="top">
                  <i class="fa-solid fa-box"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.location" />
            </div>

            <div class="flex items-center mb-1 col-span-2">
              <span
                class="self-start font-bold text-xl inline-flex items-center text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md">
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
            <div class="col-span-full">
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
          </div>

          <el-divider />
          <div class="mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing"> Guardar cambios </PrimaryButton>
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
      name: this.raw_material.data.name,
      part_number: this.raw_material.data.part_number,
      measure_unit: this.raw_material.data.measure_unit,
      min_quantity: this.raw_material.data.min_quantity,
      max_quantity: this.raw_material.data.max_quantity,
      cost: this.raw_material.data.cost,
      initial_stock: this.raw_material.data.storages[0]?.quantity,
      location: this.raw_material.data.storages[0]?.location,
      type: 'materia-prima',
      description: this.raw_material.data.description,
      features: this.raw_material.data.features,
      media: null,
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
      productType: this.raw_material.data.part_number.split('-')[0],
      brand: this.raw_material.data.part_number.split('-')[1],
      productTypes: [
        {
          label: 'Porta-placa',
          value: 'PP',
        },
        {
          label: 'Emblema',
          value: 'EM',
        },
        {
          label: 'Llavero',
          value: 'LL',
        },
        {
          label: 'Parasol',
          value: 'PS',
        },
        {
          label: 'Tapete',
          value: 'TP',
        },
        {
          label: 'Porta-documento',
          value: 'PD',
        },
        {
                    label: 'Manta',
                    value: 'MT',
                },
                {
                    label: 'Carpeta',
                    value: 'CP',
                },
                {
                    label: 'Separador',
                    value: 'SP',
                },
        {
          label: 'Termo',
          value: 'TM',
        },
        {
          label: 'Placa de estireno',
          value: 'PE',
        },
        {
          label: 'Etiqueta',
          value: 'ET',
        },
        {
          label: 'Overlay',
          value: 'OV',
        },
        {
          label: 'Accesorio para llavero',
          value: 'ALL',
        },
        {
          label: 'Pin',
          value: 'PI',
        },
        {
          label: 'Prenda',
          value: 'PR',
        },
        {
          label: 'Botella',
          value: 'BT',
        },
        {
          label: 'Hielera',
          value: 'HI',
        },
        {
          label: 'Funda para auto',
          value: 'FA',
        },
        {
          label: 'Perfumero',
          value: 'PF',
        },
        {
          label: 'Funda para llave',
          value: 'FLL',
        },
        {
          label: 'Bocina',
          value: 'BC',
        },
        {
          label: 'Impresión',
          value: 'IM',
        },
        {
          label: "Paraguas",
          value: "PG",
        },
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
  props: {
    raw_material: Object,
  },
  methods: {
    update() {
      if (this.form.media !== null) {
        this.form.post(route("raw-materials.update-with-media", this.raw_material.data), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se actualizó correctamente",
              type: "success",
            });
          },
        });
      } else {
        this.form.put(route("raw-materials.update", this.raw_material.data), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se actualizó correctamente",
              type: "success",
            });
          },
        });
      }
    },
    generatePartNumber() {
      const partNumber = this.productType + '-' + this.brand?.toUpperCase().substr(0,3) + '-';
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
