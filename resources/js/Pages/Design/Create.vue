<template>
  <div>
    <AppLayout title="Órdenes de diseño - Crear">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear órden de diseño</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div class="flex justify-end mb-5">
            <el-radio-group
              v-model="is_customer"
              @change="handleChangeModelId()"
              size="small"
            >
              <el-radio :label="1">Para cliente</el-radio>
              <el-radio :label="0">Para prospecto</el-radio>
            </el-radio-group>
          </div>

          <div class="grid md:grid-cols-2 gap-3">

            <!-- selector de cliente -->
            <div v-if="is_customer" class="col-span-full">
              <p class="text-sm ml-3">Cliente</p>
              <el-select @change="handleSelectedCustomer()"
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

            <!-- selector de prospecto -->
            <div v-else class="col-span-full">
              <div class="flex items-center justify-between">
                <p class="text-sm ml-3">Prospecto</p>
                <el-tooltip content="Creación rápida de prospecto" placement="top">
                  <i
                    @click="showProspectFormModal = true"
                    class="fa-solid fa-circle-plus text-primary mr-2 text-sm cursor-pointer"
                  ></i>
                </el-tooltip>
              </div>
              <el-select
                @change="saveProspectContactName()"
                v-model="form.company_branch_name"
                clearable
                filterable
                placeholder="Selecciona un prospecto"
                no-data-text="No hay prospectos registrados"
                no-match-text="No se encontraron coincidencias"
              >
                <el-option
                  v-for="item in prospects"
                  :key="item.id"
                  :label="item.name"
                  :value="item.name"
                />
              </el-select>
              <InputError :message="form.errors.company_branch_name" />
            </div>

            <div
              v-if="form.company_branch_name"
              class="flex items-center mt-3 col-span-full"
            >
              <el-tooltip v-if="is_customer" content="Contacto" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                >
                  <i class="fa-solid fa-id-badge"></i>
                </span>
              </el-tooltip>

              <!-- Selector de contacto si es cliente -->
              <el-radio-group v-if="is_customer" v-model="form.contact_name" size="small">
                <el-radio-button
                  v-for="contact in company_branches.find(
                    (cb) => cb.name == form.company_branch_name
                  )?.contacts"
                  :key="contact.id"
                  :label="contact.name"
                >
                  {{ contact.name }} ({{ contact.email }})
                </el-radio-button>
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

              <p v-if="!form.contact_name" class="text-xs text-primary ml-2">
                No olvides seleccionar el contacto
              </p>
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
                <el-tooltip content="Nombre del diseño" placement="top"> A </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.name" />
            </div>

            <div class="flex items-center">
              <span
                class="font-bold text-xl inline-flex items-center text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
              >
                <el-tooltip content="Tipo de diseño" placement="top"> C </el-tooltip>
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
              <Checkbox
                v-model:checked="form.has_priority"
                name="priority"
                class="bg-transparent"
              />
              <span class="ml-2 text-sm text-[#555555]">Prioridad alta</span>
            </label>

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
          
          <!-- Historial de diseños y productos del cliente seleccionado -->
          <section v-if="form.company_branch_name && is_customer">
            <p class="text-base text-amber-700">
              **En el siguiente recuadro se muestra el historial de diseños y los productos
              asociados a este cliente. Por favor, revisa cuidadosamente si el diseño que
              deseas crear no ha sido solicitado anteriormente, para evitar duplicaciones.**
            </p>

            <article class="border border-[#9A9A9A] rounded-lg p-3 my-3">
              <div v-if="loading" class="text-xs my-4 text-center">
                  Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
              </div>

              <div v-else>
                <!-- Historial de diseños -->
                <div  class="px-3 max-h-48 overflow-auto">
                  <p class="text-sm font-bold text-[#373737] mb-2">Historial de diseños</p>
                  <table v-if="companyBranchDesigns?.length" class="w-full h-full mx-auto text-xs">
                    <thead>
                      <tr class="text-center *:font-bold *:pb-3 *:text-left">
                        <th class="pl-2">ID</th>
                        <th>Diseño</th>
                        <th>Solicitante</th>
                        <th>Clasificación</th>
                        <th>Solicitado el</th>
                        <th>Estatus</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="design in companyBranchDesigns" :key="design"
                        class="mb-4">
                        <td class="py-2 pl-2 rounded-l-full">{{ design.id }}</td>
                        <td @click="handleDesignSelection(design.id)" class="py-2 underline text-secondary cursor-pointer">{{ design.name }}</td>
                        <td class="py-2">{{ design.user.name }}</td>
                        <td class="py-2">{{ design.design_type.name }}</td>
                        <td class="py-2">{{ design.created_at.split('T')[0] }}</td>
                        <td :class="statusStyles(design)" class="py-2 font-bold">{{ design.finished_at ? 'Terminado' 
                          : design.started_at ? 'En proceso' 
                          : 'Sin empezar' }}</td>
                      </tr>
                    </tbody>
                  </table>
                  <p v-else class="text-xs text-gray-500">No hay diseños para este cliente</p>
                </div>

                <div class="border-t border-[#9A9A9A] my-4"></div>

                <!-- Historial de diseños -->
                <div class="px-3 max-h-52 overflow-auto">
                  <p class="text-sm font-bold text-[#373737] mb-2">Productos</p>
                  <table v-if="companyProducts?.length" class="w-full h-full mx-auto text-sm">
                    <thead>
                      <tr class="text-center *:font-bold *:pb-3 *:text-left">
                        <th class="pl-2">N° de parte</th>
                        <th>Nombre</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="companyProduct in companyProducts" :key="companyProduct"
                        class="mb-4">
                        <td class="py-2 pl-2 rounded-l-full">{{ companyProduct.catalog_product.part_number }}</td>
                        <td @click="handleProductSelection(companyProduct.catalog_product.id)" class="py-2 underline text-secondary cursor-pointer">{{ companyProduct.catalog_product.name }}</td>
                      </tr>
                    </tbody>
                  </table>
                  <p v-else class="text-xs text-gray-500">No hay productos relacionados a este cliente</p>
                </div>
              </div>
            </article>
          </section>

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
            <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
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
            <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
              PDF, SVG, PNG, JPG, GIF, etc. (MAX. 4 MB).
            </p>
          </div>

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Crear órden de diseño
            </PrimaryButton>
          </div>
        </div>
      </form>

      <!-- prospect form -->
      <DialogModal :show="showProspectFormModal" @close="showProspectFormModal = false">
        <template #title>Creación rápida de nuevo prospecto </template>
        <template #content>
          <form class="grid grid-cols-2 gap-3 mt-4">
            <div>
              <InputLabel value="Nombre de la empresa*" class="ml-3 mb-1" />
              <el-input
                v-model="prospectForm.name"
                placeholder="Escribe el nombre de la empresa"
                :maxlength="100"
                clearable
              />
              <InputError :message="prospectForm.errors.name" />
            </div>
            <div>
              <InputLabel value="Nombre del contacto*" class="ml-3 mb-1" />
              <el-input
                v-model="prospectForm.contact_name"
                placeholder="Escribe el nombre del contacto"
                :maxlength="100"
                clearable
              />
              <InputError :message="prospectForm.errors.contact_name" />
            </div>
            <div>
              <InputLabel value="Puesto*" class="ml-3 mb-1" />
              <el-input
                v-model="prospectForm.contact_charge"
                placeholder="Ej. Supervisor"
                :maxlength="100"
                clearable
              />
              <InputError :message="prospectForm.errors.contact_charge" />
            </div>
            <div>
              <InputLabel value="Correo electrónico*" class="ml-3 mb-1" />
              <el-input
                v-model="prospectForm.contact_email"
                placeholder="Escribe el correo electrónico del contacto"
                :maxlength="100"
                required
                clearable
              />
              <InputError :message="prospectForm.errors.contact_email" />
            </div>
            <div>
              <InputLabel value="Teléfono" class="ml-3 mb-1" />
              <el-input
                v-model="prospectForm.contact_phone"
                :formatter="
                  (value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')
                "
                :parser="(value) => value.replace(/\D/g, '')"
                maxlength="10"
                clearable
                placeholder="Escribe el número de teléfono del contacto"
              />
              <InputError :message="prospectForm.errors.contact_phone" />
            </div>
          </form>
        </template>
        <template #footer>
          <div class="flex items-center space-x-2">
            <CancelButton
              @click="showProspectFormModal = false"
              :disabled="prospectForm.processing"
              >Cancelar
            </CancelButton>
            <PrimaryButton @click="storeProspect()" :disabled="prospectForm.processing">
              <i
                v-if="prospectForm.processing"
                class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"
              ></i>
              Crear
            </PrimaryButton>
          </div>
        </template>
      </DialogModal>
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
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

    const prospectForm = useForm({
      name: null,
      contact_name: null,
      contact_charge: null,
      contact_email: null,
      contact_phone: null,
      status: "Contacto inicial",
      quick_creation: true,
      abstract: "Agregado en creación rápida en formulario de creación de diseño",
    });

    return {
      form,
      prospectForm,
      showProspectFormModal: false,
      is_customer: 1, //es a cliente o prospecto
      companyBranchDesigns: null, //historial de diseños del cliente
      companyProducts: null, //productos relacionados al cliente matriz
      mesureUnits: ["Metro(s)", "Centrimetro(s)", "Pulgada(s)", "milimetro(s)"],
      loading: false,
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    CancelButton,
    DialogModal,
    InputError,
    InputLabel,
    IconInput,
    Checkbox,
    Back,
    Link,
  },
  props: {
    designers: Array,
    design_types: Array,
    company_branches: Array,
    prospects: Array,
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
    storeProspect() {
      this.prospectForm.post(route("prospects.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Nueva prospecto registrado",
            type: "success",
          });

          this.showProspectFormModal = false;
          this.prospectForm.reset();
        },
      });
    },
    handleDesignSelection(designId) {
      const url = this.route('designs.show', designId);
      window.open(url, '_blank');
    },
    handleProductSelection(catalogProductId) {
      const url = this.route('catalog-products.show', catalogProductId);
      window.open(url, '_blank');
    },
    handleChangeModelId() {
      this.form.company_branch_name = null;
      this.form.contact_name = null;
    },
    saveProspectContactName() {
      const prospect = this.prospects.find(
        (prospect) => prospect.name === this.form.company_branch_name
      );
      this.form.contact_name = prospect.contact_name;
    },
    statusStyles(design) {
      if ( design.finished_at ) {
        return 'text-green-600';
      } else if ( design.started_at ) {
        return 'text-blue-600';
      } else {
        return 'text-amber-600';
      }
    },
    async handleSelectedCustomer() {
      this.loading = true;
      try {
        //busco el registro de la sucursal seleccionada y guardo el id para hacer peticion y recuperar diseños y productos.
        const customerId = this.company_branches.find(cb => cb.name === this.form.company_branch_name);
        const response = await axios.get(route('company-branches.fetch-design-info', customerId));
        if ( response.status === 200 ) {
          this.companyBranchDesigns = response.data.company_branch_designs;
          this.companyProducts = response.data.company_products;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    }
  },
};
</script>
