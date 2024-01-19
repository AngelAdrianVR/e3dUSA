<template>
  <AppLayout title="Interacción email">
    <template #header>
      <div class="flex justify-between">
        <Back />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Interacción email</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="store">
      <div class="md:w-1/2 md:mx-auto text-sm my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">

        <div class="w-1/2">
          <label>Folio de la oportunidad *</label>
          <el-select @change="getCompany" class="w-full" v-model="form.oportunity_id" clearable filterable
            placeholder="Seleccione" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="oportunity in oportunities.data" :key="oportunity"
              :label="oportunity.folio + ' - ' + oportunity.name" :value="oportunity.id" />
          </el-select>
          <InputError :message="form.errors.oportunity_id" />
        </div>

        <h2 class="text-secondary">Datos del cliente</h2>
        <div class="flex space-x-7 justify-between">
          <div class="w-1/2">
            <label>Cliente *</label> <br>
            <el-select disabled v-model="form.company_id" clearable filterable placeholder="Seleccione"
              no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="company in companies" :key="company" :label="company.business_name" :value="company.id" />
            </el-select>
            <InputError :message="form.errors.company_id" />
          </div>
          <div class="w-1/2">
            <label>Sucursal *</label> <br>
            <el-select @change="saveCompanyBranchAddress" v-model="form.company_branch_id" clearable filterable
              placeholder="Seleccione" no-data-text="No hay sucursales registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="company_branch in companies.find((item) => item.id == form.company_id)?.company_branches"
                :key="company_branch" :label="company_branch.name" :value="company_branch.id" />
            </el-select>
            <InputError :message="form.errors.company_branch" />
          </div>
        </div>
        <div class="flex space-x-7 justify-between">
          <div v-if="!has_contact" class="w-1/2">
            <label>Contacto</label>
            <el-select @change="getContactEmail" v-model="form.contact_id" clearable filterable placeholder="Seleccione"
              no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="contact in company_branch_obj?.contacts" :key="contact" :label="contact.name"
                :value="contact.id" />
            </el-select>
            <InputError :message="form.errors.contact_id" />
          </div>
          <div class="w-1/2">
            <label>Email</label>
            <input v-model="form.contact_email" class="input" type="text">
            <InputError :message="form.errors.contact_email" />
          </div>
        </div>

        <div>
          <label>Asunto</label>
          <input v-model="form.subject" class="input" type="text">
          <InputError :message="form.errors.subject" />
        </div>
        <div>
          <label>Contenido</label>
          <textarea v-model="form.content" class="textarea" rows="4">
                </textarea>
          <InputError :message="form.errors.content" />
        </div>
        <!-- <div class="ml-2 mt-2 col-span-full flex">
              <FileUploader @files-selected="this.form.media = $event" />
            </div> -->
        <div class="flex justify-end items-center">
          <PrimaryButton :disabled="form.processing">
            Guardar
          </PrimaryButton>

        </div>
      </div>
    </form>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      oportunity_id: null,
      company_id: null,
      company_branch_id: null,
      contact_id: null,
      contact_name: null,
      contact_email: null,
      subject: null,
      content: null,
      media: [],
    });

    return {
      form,
      company_branch_obj: null,
      has_contact: false,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
    };
  },
  components: {
    PrimaryButton,
    FileUploader,
    InputError,
    AppLayout,
    Back,
    Link
  },
  props: {
    oportunities: Object,
    companies: Array,
    users: Array,
    opportunity: Object,
  },
  methods: {
    store() {
      this.form.post(route('email-monitors.store'), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se registró interacción de correo electrónico",
            type: "success",
          });
        },
      });
    },
    getCompany() {
      const oportunity = this.oportunities.data.find(oportunity => oportunity.id === this.form.oportunity_id);
      // console.log(oportunity);
      this.form.company_id = null;
      this.form.company_branch_id = null;
      this.form.contact_id = null;
      this.form.contact_email = null;

      this.form.company_id = oportunity.company.id;

    },
    saveCompanyBranchAddress() {
      this.company_branch_obj = this.companies.find((item) => item.id == this.form.company_id)?.company_branches[0];
    },
    getContactEmail() {
      this.form.contact_email = this.company_branch_obj?.contacts?.find(contact => contact.id == this.form.contact_id)?.email;
      this.form.contact_name = this.company_branch_obj?.contacts?.find(contact => contact.id == this.form.contact_id)?.name;
    },
    activateFileInput() {
      // Simula un clic en el campo de entrada de archivos al hacer clic en el párrafo
      document.getElementById('fileInput').click();
    },
    handleFileUpload(event) {
      // Este método se llama cuando se selecciona un archivo en el input file
      const selectedFiles = event.target.files;
      const fileNames = [];

      // Obtén los nombres de los archivos seleccionados y guárdalos en form.mediaNames
      for (let i = 0; i < selectedFiles.length; i++) {
        fileNames.push(selectedFiles[i].name);
      }

      // Actualiza la propiedad form.media con los archivos seleccionados
      this.form.media = selectedFiles;
      // Actualiza la propiedad form.mediaNames con los nombres de los archivos
      this.form.mediaNames = fileNames;
    },
  },
  mounted() {
    if (this.opportunity) {
      this.form.oportunity_id = this.opportunity.id;
      this.getCompany();
      this.form.company_branch_id = this.opportunity.company_branch_id;
      this.saveCompanyBranchAddress();
    }
  }
};
</script>

