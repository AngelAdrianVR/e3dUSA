<template>
  <div>
    <AppLayout title="Órdenes de diseño - Editar">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('designs.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar órden de diseño</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
        <div style="margin-top: 20px">
            <el-radio-group v-model="form.original_design_id">
              <el-radio-button label="Nuevo diseño" />
              <el-radio-button label="Registrado" />
            </el-radio-group>
          </div>

          <div class="grid md:grid-cols-2 gap-3">

            <div v-if="form.original_design_id == 'Nuevo diseño'">
            <IconInput
              v-model="form.company_branch_name"
              inputPlaceholder="Cliente"
              inputType="text"
            >
              <el-tooltip content="Cliente" placement="top"> <i class="fa-solid fa-building"></i> </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.company_branch_name" />
          </div>

            <div v-if="form.original_design_id == 'Nuevo diseño'">
            <IconInput
              v-model="form.contact_name"
              inputPlaceholder="Contacto"
              inputType="text"
            >
              <el-tooltip content="Contacto" placement="top"> <i class="fa-regular fa-address-card"></i> </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.contact_name" />
          </div>

          <div class="col-span-2" v-if="form.original_design_id == 'Registrado'">
            <el-select
            v-model="form.company_branch_name"
            clearable
            filterable
            placeholder="Selecciona un cliente"
            no-data-text="No hay clientes registrados"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in companies"
              :key="item.id"
              :label="item.business_name"
              :value="item.business_name"
            />
          </el-select>
            <InputError :message="form.errors.company_branch_name" />
          </div>

            <div>
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
              <el-tooltip content="Nombre del diseño" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>

          <div>
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
              <el-tooltip content="Medidas (Dimensiones)" placement="top"> <i class="fa-solid fa-text-width"></i> </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.dimensions" />
          </div>

          <div class="flex items-center my-2">
                        <el-tooltip content="Unidad de medida" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                <i class="fa-solid fa-ruler-vertical"></i>
                            </span>
                        </el-tooltip>
                        <el-select v-model="form.measure_unit" clearable
                            placeholder="Busca unidad de medida" no-data-text="No hay unidades de medida registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="(item, index) in mesureUnits" :key="index" :label="item" :value="item" />
                        </el-select>
                        <InputError :message="form.errors.measure_unit" />
                    </div>

          <div>
            <IconInput
              v-model="form.pantones"
              inputPlaceholder="Pantones"
              inputType="text"
            >
              <el-tooltip content="Pantones" placement="top"> <i class="fa-solid fa-palette"></i> </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.pantones" />
          </div>

          <div class="flex ml-3 col-span-2">
                <el-tooltip content="Requerimientos/Especificaiones" placement="top">
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
              SVG, PNG, JPG, GIF, etc. (MAX. 4 MB).
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
              SVG, PNG, JPG, GIF, etc. (MAX. 4 MB).
            </p>
          </div>

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Actualizar órden de diseño
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
    original_design_id: 'Nuevo diseño',
    company_branch_name: this.design.company_branch_name,
    contact_name: this.design.contact_name,
    designer_id: this.design.designer_id,
    name: this.design.name,
    design_type_id: this.design.design_type_id,
    dimensions: this.design.dimensions,
    measure_unit: this.design.measure_unit,
    pantones: this.design.pantones,
    specifications: this.design.specifications,
    media_plano: null,
    media_logo: null,
    });

    return {
      form,
      mesureUnits: [
                'Metro(s)',
                'Centimetro(s)',
                'Pulgada(s)',
                'Milimetro(s)',
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
    design: Object,
    designers: Array,
    design_types: Array,
    companies: Array
  },
  methods: {
    update() {
      if (this.form.media_plano || this.form.media_logo) {
        this.form.post(
          route("designs.update-with-media", this.design),
          {
            onSuccess: () => {
              this.$notify({
                title: "Éxito",
                message: "Orden de diseño actualizada",
                type: "success",
              });
            },
          }
        );
      } else {
        this.form.put(
          route("designs.update", this.design),
          {
            onSuccess: () => {
              this.$notify({
                title: "Éxito",
                message: "Orden de diseño actualizada",
                type: "success",
              });
            },
          }
        );
      }
    },
  },
};
</script>
