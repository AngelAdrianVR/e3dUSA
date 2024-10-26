<template>
  <div>
    <AppLayout title="Órdenes de diseño - Editar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar órden de diseño</h2>
          </div>
        </div>
      </template>

      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg px-9 py-5 shadow-md space-y-3">
          <div class="flex justify-end mb-5">
            <el-radio-group v-model="is_customer" @change="handleChangeModelId()" size="small">
              <el-radio :value="1">Para cliente</el-radio>
              <el-radio :value="0">Para prospecto</el-radio>
            </el-radio-group>
          </div>
          <div v-if="is_customer">
            <InputLabel value="Cliente*" />
            <el-select @change="handleSelectedCustomer()" v-model="form.company_branch_name" clearable filterable
              placeholder="Selecciona un cliente" no-data-text="No hay clientes registrados"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in company_branches" :key="item.id" :label="item.name" :value="item.name" />
            </el-select>
            <InputError :message="form.errors.company_branch_name" />
          </div>
          <div v-else>
            <InputLabel>
              <div class="flex items-center justify-between">
                <span>Prospecto*</span>
                <el-tooltip content="Creación rápida de prospecto" placement="top">
                  <i @click="showProspectFormModal = true"
                    class="fa-solid fa-circle-plus text-primary mr-2 text-sm cursor-pointer"></i>
                </el-tooltip>
              </div>
            </InputLabel>
            <el-select @change="saveProspectContactName()" v-model="form.company_branch_name" clearable filterable
              placeholder="Selecciona un prospecto" no-data-text="No hay prospectos registrados"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in prospects" :key="item.id" :label="item.name" :value="item.name" />
            </el-select>
            <InputError :message="form.errors.company_branch_name" />
          </div>
          <div class="grid md:grid-cols-2 gap-3">
            <div v-if="is_customer && form.company_branch_name" class="col-span-full">
              <InputLabel value="Contacto*" />
              <el-select v-model="form.contact_name" placeholder="Selecciona el contacto"
                no-data-text="Primero selecciona al cliente o al prospecto"
                no-match-text="No se encontraron coincidencias" class="!w-full">
                <el-option v-for="contact in company_branches.find(
                  (cb) => cb.name == form.company_branch_name
                )?.contacts" :key="contact.id" :label="`${contact.charge}: ${contact.name} (${contact.email})`"
                  :value="contact.name">
                  {{ contact.charge }}: {{ contact.name }} ({{ contact.email }})
                </el-option>
              </el-select>
              <InputError :message="form.errors.contact_name" />
            </div>
            <div>
              <InputLabel value="Diseñador(a)*" />
              <el-select v-model="form.designer_id" clearable filterable placeholder="Selecciona un(a) diseñador(a)"
                no-data-text="No hay diseñadores registrados" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in designers" :key="item.id" :label="item.name" :value="item.id" />
              </el-select>
              <InputError :message="form.errors.designer_id" />
            </div>
            <div>
              <InputLabel value="Nombre del diseño*" />
              <el-input v-model="form.name" placeholder="Ej. Render de llavero modelo..." />
              <InputError :message="form.errors.name" />
            </div>
            <div>
              <InputLabel value="Tipo de diseño*" />
              <el-select v-model="form.design_type_id" clearable filterable placeholder="Clasificación"
                no-data-text="No hay tipos de diseño registrados" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in design_types" :key="item.id" :label="item.name" :value="item.id" />
              </el-select>
              <InputError :message="form.errors.design_type_id" />
            </div>
            <div>
              <InputLabel value="Dimensiones*" />
              <el-input v-model="form.dimensions" placeholder="Ej. 20x10x2 cm" />
              <InputError :message="form.errors.dimensions" />
            </div>
            <div>
              <InputLabel value="Unidad de medida*" />
              <el-select v-model="form.measure_unit" clearable placeholder="Busca unidad de medida"
                no-data-text="No hay unidades de medida registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="(item, index) in mesureUnits" :key="index" :label="item" :value="item" />
              </el-select>
              <InputError :message="form.errors.measure_unit" />
            </div>
            <div>
              <InputLabel value="Pantones*" />
              <el-input v-model="form.pantones" placeholder="Ej. 2427 C" />
              <InputError :message="form.errors.pantones" />
            </div>
            <label class="flex items-center">
              <Checkbox v-model:checked="form.has_priority" name="priority" class="bg-transparent" />
              <span class="ml-2 text-sm">Prioridad alta</span>
            </label>
            <div class="col-span-full">
              <InputLabel value="Requerimientos / Especificaiones*" />
              <el-input v-model="form.specifications" :rows="3" maxlength="800" placeholder="..." show-word-limit
                type="textarea" />
              <InputError :message="form.errors.specifications" />
            </div>
          </div>

          <!-- Historial de diseños y productos del cliente seleccionado -->
          <section v-if="form.company_branch_name && is_customer">
            <p class="text-amber-600 text-sm">
              **En el siguiente recuadro se muestra el historial de diseños y los productos
              asociados a este cliente. Por favor, revisa cuidadosamente si el diseño que
              deseas crear no ha sido solicitado anteriormente, para evitar duplicaciones.**
            </p>

            <article class="border border-[#9A9A9A] rounded-lg p-3 my-3 min-h-28">
              <div v-if="loading" class="text-xs my-4 text-center">
                Cargando <i class="fa-sharp fa-solid fa-circle-notch fa-spin ml-2 text-primary"></i>
              </div>
              <div v-else>
                <!-- Historial de diseños -->
                <div class="px-3 max-h-48 overflow-auto text-sm">
                  <p class="font-bold text-[#373737] dark:text-gray-500 mb-2">Historial de diseños</p>
                  <table v-if="companyBranchDesigns?.length" class="w-full h-full mx-auto text-xs table-fixed">
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
                      <tr v-for="design in companyBranchDesigns" :key="design" class="mb-4">
                        <td class="py-2 pl-2 rounded-l-full">{{ design.id }}</td>
                        <td @click="handleDesignSelection(design.id)"
                          class="py-2 underline text-secondary cursor-pointer">{{
                            design.name }}</td>
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
                  <p class="text-sm font-bold text-[#373737] dark:text-gray-500 mb-2">Productos</p>
                  <table v-if="companyProducts?.length" class="w-full h-full mx-auto text-xs">
                    <thead>
                      <tr class="text-center *:font-bold *:pb-3 *:text-left">
                        <th class="pl-2 w-2">N° de parte</th>
                        <th class="w-36">Nombre</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="companyProduct in companyProducts" :key="companyProduct" class="mb-4">
                        <td class="py-2 pl-2 rounded-l-full w-2">{{ companyProduct.catalog_product.part_number }}</td>
                        <td @click="handleProductSelection(companyProduct.catalog_product.id)"
                          class="py-2 underline text-secondary cursor-pointer w-36">{{
                            companyProduct.catalog_product.name }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <p v-else class="text-xs text-gray-500">No hay productos relacionados a este cliente</p>
                </div>
              </div>
            </article>
          </section>
          <div>
            <InputLabel value="Plano" />
            <FileUploader @files-selected="handlePlanoSelected" :multiple="false"
              :existingFileUrls="media_plane_url ? [media_plane_url] : []" />
            <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
              PDF, SVG, PNG, JPG, GIF, etc. (MAX. 4 MB).
            </p>
          </div>
          <div>
            <InputLabel value="Logo" />
            <FileUploader @files-selected="handleLogoSelected" :multiple="false"
              :existingFileUrls="media_logo_url ? [media_logo_url] : []" />
            <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
              PDF, SVG, PNG, JPG, GIF, etc. (MAX. 4 MB).
            </p>
          </div>
          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Guardar cambios
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
              <InputLabel value="Nombre de la empresa*" />
              <el-input v-model="prospectForm.name" placeholder="Escribe el nombre de la empresa" :maxlength="100"
                clearable />
              <InputError :message="prospectForm.errors.name" />
            </div>
            <div>
              <InputLabel value="Nombre del contacto*" />
              <el-input v-model="prospectForm.contact_name" placeholder="Escribe el nombre del contacto"
                :maxlength="100" clearable />
              <InputError :message="prospectForm.errors.contact_name" />
            </div>
            <div>
              <InputLabel value="Puesto*" />
              <el-input v-model="prospectForm.contact_charge" placeholder="Ej. Supervisor" :maxlength="100" clearable />
              <InputError :message="prospectForm.errors.contact_charge" />
            </div>
            <div>
              <InputLabel value="Correo electrónico*" />
              <el-input v-model="prospectForm.contact_email" placeholder="Escribe el correo electrónico del contacto"
                :maxlength="100" required clearable />
              <InputError :message="prospectForm.errors.contact_email" />
            </div>
            <div>
              <InputLabel value="Teléfono" />
              <el-input v-model="prospectForm.contact_phone" :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')
                " :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                placeholder="Escribe el número de teléfono del contacto" />
              <InputError :message="prospectForm.errors.contact_phone" />
            </div>
          </form>
        </template>
        <template #footer>
          <div class="flex items-center space-x-2">
            <CancelButton @click="showProspectFormModal = false" :disabled="prospectForm.processing">Cancelar
            </CancelButton>
            <PrimaryButton @click="storeProspect()" :disabled="prospectForm.processing">
              <i v-if="prospectForm.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
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
import Checkbox from "@/Components/Checkbox.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import DialogModal from "@/Components/DialogModal.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import InputLabel from "@/Components/InputLabel.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
  data() {
    const form = useForm({
      // original_design_id: 'Nuevo diseño',
      company_branch_name: this.design.company_branch_name,
      contact_name: this.design.contact_name,
      designer_id: this.design.designer_id,
      name: this.design.name,
      design_type_id: this.design.design_type_id,
      dimensions: this.design.dimensions,
      measure_unit: this.design.measure_unit,
      pantones: this.design.pantones,
      has_priority: !!this.design.has_priority,
      specifications: this.design.specifications,
      media_plano: null,
      media_logo: null,
      media_edited: false,
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
    CancelButton,
    PrimaryButton,
    InputError,
    InputLabel,
    Checkbox,
    Back,
    Link,
    DialogModal,
    FileUploader,
  },
  props: {
    design: Object,
    designers: Array,
    design_types: Array,
    company_branches: Array,
    prospects: Array,
    media_logo_url: String,
    media_plane_url: String,
  },
  methods: {
    handlePlanoSelected(files, mediaUpdated) {
      this.form.media_plano = files;
      this.form.media_edited = mediaUpdated;
    },
    handleLogoSelected(files, mediaUpdated) {
      this.form.media_logo = files;
      this.form.media_edited = mediaUpdated;
    },
    update() {
      if (this.form.media_edited) {
        this.form.post(route("designs.update-with-media", this.design), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Orden de diseño actualizada",
              type: "success",
            });
          },
        });
      } else {
        // borrar los archivos para evitar error al enviar formulario por PUT
        this.form.media_logo = null;
        this.form.media_plano = null;
        
        this.form.put(route("designs.update", this.design), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Orden de diseño actualizada",
              type: "success",
            });
          },
        });
      }
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
      if (design.finished_at) {
        return 'text-green-600';
      } else if (design.started_at) {
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
        if (response.status === 200) {
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
  mounted() {
    this.handleSelectedCustomer();
  }
};
</script>
