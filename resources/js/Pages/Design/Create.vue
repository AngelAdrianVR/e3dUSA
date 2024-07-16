<template>
  <div>
    <AppLayout title="Órdenes de diseño - Crear">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Crear órden de diseño
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <!-- <div style="margin-top: 20px">
            <el-radio-group v-model="form.original_design_id">
              <el-radio-button label="Nuevo diseño" />
              <el-radio-button label="Registrado" />
            </el-radio-group>
          </div> -->

          <div class="grid md:grid-cols-2 gap-3">
            <div class="col-span-2">
              <el-select
                v-model="form.company_branch_name"
                clearable
                filterable
                placeholder="Selecciona un cliente"
                no-data-text="No hay clientes registrados"
                no-match-text="No se encontraron coincidencias"
              >
                <el-option
                  v-for="item in company_branches"
                  :key="item.id"
                  :label="item.name"
                  :value="item.name"
                />
              </el-select>
              <InputError :message="form.errors.company_branch_name" />
            </div>

            <div v-if="form.company_branch_name" class="flex items-center mt-3 col-span-full">
              <el-tooltip content="Contacto" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                >
                  <i class="fa-solid fa-id-badge"></i>
                </span>
              </el-tooltip>
              <el-radio-group v-model="form.contact_name" size="small">
                <el-radio-button
                  v-for="contact in company_branches.find(
                    (cb) => cb.name == form.company_branch_name
                  )?.contacts"
                  :key="contact.id"
                  :label="contact.name"
                >
                  {{ contact.name }} ({{ contact.email }})
                </el-radio-button>
              </el-radio-group>
              <p v-if="!form.contact_name" class="text-xs text-primary ml-2">No olvides seleccionar el contacto</p>
              <InputError :message="form.errors.contact_id" />
            </div>
            <div class="flex items-center">
              <span
                class="font-bold text-xl inline-flex items-center text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
              >
                <el-tooltip content="Seleccionar Diseñador(a)" placement="top">
                  <i class="fa-solid fa-user text-sm"></i>
                </el-tooltip>
              </span>
              <el-select
                v-model="form.designer_id"
                clearable
                filterable
                placeholder="Selecciona un(a) diseñador(a)"
                no-data-text="No hay diseñadores registrados"
                no-match-text="No se encontraron coincidencias"
              >
                <el-option
                  v-for="item in designers"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
              <InputError :message="form.errors.designer_id" />
            </div>

            <div>
              <IconInput
                v-model="form.name"
                inputPlaceholder="Nombre del diseño"
                inputType="text"
              >
                <el-tooltip content="Nombre del diseño" placement="top">
                  A
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.name" />
            </div>

            <div class="flex items-center">
              <span
                class="font-bold text-xl inline-flex items-center text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
              >
                <el-tooltip content="Tipo de diseño" placement="top">
                  C
                </el-tooltip>
              </span>
              <el-select
                v-model="form.design_type_id"
                clearable
                filterable
                placeholder="Clasificación"
                no-data-text="No hay tipos de diseño registrados"
                no-match-text="No se encontraron coincidencias"
              >
                <el-option
                  v-for="item in design_types"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
              <InputError :message="form.errors.design_type_id" />
            </div>

            <div>
              <IconInput
                v-model="form.dimensions"
                inputPlaceholder="Medidas (Dimensiones)"
                inputType="text"
              >
                <el-tooltip content="Medidas (Dimensiones)" placement="top">
                  <i class="fa-solid fa-text-width"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.dimensions" />
            </div>

            <div class="flex items-center my-2">
              <el-tooltip content="Unidad de medida" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"
                >
                  <i class="fa-solid fa-ruler-vertical"></i>
                </span>
              </el-tooltip>
              <el-select
                v-model="form.measure_unit"
                clearable
                placeholder="Busca unidad de medida"
                no-data-text="No hay unidades de medida registradas"
                no-match-text="No se encontraron coincidencias"
              >
                <el-option
                  v-for="(item, index) in mesureUnits"
                  :key="index"
                  :label="item"
                  :value="item"
                />
              </el-select>
              <InputError :message="form.errors.measure_unit" />
            </div>

            <div>
              <IconInput
                v-model="form.pantones"
                inputPlaceholder="Pantones"
                inputType="text"
              >
                <el-tooltip content="Pantones" placement="top">
                  <i class="fa-solid fa-palette"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.pantones" />
            </div>

            <label class="flex items-center w-1/3">
              <Checkbox v-model:checked="form.has_priority" name="priority"
                class="bg-transparent" />
              <span class="ml-2 text-sm text-[#555555]">Prioridad alta</span>
            </label>

            <div class="flex ml-3 col-span-2">
              <el-tooltip
                content="Requerimientos/Especificaiones"
                placement="top"
              >
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
                >
                  ...
                </span>
              </el-tooltip>
              <textarea
                v-model="form.specifications"
                class="textarea"
                autocomplete="off"
                placeholder="Requerimientos/Especificaiones"
              ></textarea>
              <InputError :message="form.errors.specifications" />
            </div>
          </div>

          <div class="col-span-full">
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9"
              >
                <el-tooltip content="Imagen del plano" placement="top">
                  <i class="fa-solid fa-object-group"></i>
                </el-tooltip>
              </span>
              <input
                @input="form.media_plano = $event.target.files[0]"
                class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                aria-describedby="file_input_help"
                id="file_input"
                type="file"
              />
            </div>
            <p
              class="mt-1 text-xs text-right text-gray-500"
              id="file_input_help"
            >
              PDF, SVG, PNG, JPG, GIF, etc. (MAX. 4 MB).
            </p>
          </div>

          <div class="col-span-full">
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9"
              >
                <el-tooltip content="Imagen del logo" placement="top">
                  <i class="fa-solid fa-file-invoice"></i>
                </el-tooltip>
              </span>
              <input
                @input="form.media_logo = $event.target.files[0]"
                class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                aria-describedby="file_input_help"
                id="file_input"
                type="file"
              />
            </div>
            <p
              class="mt-1 text-xs text-right text-gray-500"
              id="file_input_help"
            >
              PDF, SVG, PNG, JPG, GIF, etc. (MAX. 4 MB).
            </p>
          </div>

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Crear órden de diseño
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
import Checkbox from "@/Components/Checkbox.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      // original_design_id: 'Nuevo diseño',
      company_branch_name: null,
      contact_name: null,
      designer_id: null,
      name: null,
      design_type_id: null,
      dimensions: null,
      measure_unit: null,
      pantones: null,
      has_priority: false,
      specifications: null,
      media_plano: null,
      media_logo: null,
    });

    return {
      form,
      mesureUnits: ["Metro(s)", "Centrimetro(s)", "Pulgada(s)", "milimetro(s)"],
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Checkbox,
    Back,
    Link
  },
  props: {
    designers: Array,
    design_types: Array,
    companies: Array,
    company_branches: Array,
  },
  methods: {
    store() {
      this.form.post(route("designs.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Nueva órden registrada",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
  },
};
</script>
