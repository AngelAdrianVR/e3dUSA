<template>
  <AppLayout title="Editar interacción Whatsapp">
    <template #header>
      <div class="flex justify-between">
        <Back />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Editar interacción WhatsApp</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="update">
      <div class="md:w-1/2 md:mx-auto grid grid-cols-2 gap-3 text-sm my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md">
        <h2 class="text-[#373737] font-bold col-span-full">Datos del cliente</h2>
        <div>
          <InputLabel value="Folio de la oportunidad*" />
          <el-select @change="getCompany" class="w-full" v-model="form.oportunity_id" clearable filterable
            placeholder="Seleccione" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="oportunity in oportunities" :key="oportunity"
              :label="oportunity.folio + ' - ' + oportunity.name" :value="oportunity.id" />
          </el-select>
          <InputError :message="form.errors.oportunity_id" />
        </div>
        <div>
          <InputLabel value="Vendedor*" />
          <el-select class="w-full" v-model="form.seller_id" clearable filterable placeholder="Selecciona"
            no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="seller in users.filter(item => item.employee_properties?.department == 'Ventas')"
              :key="seller" :label="seller.name" :value="seller.id" />
          </el-select>
          <InputError :message="form.errors.seller_id" />
        </div>
        <div>
          <InputLabel value="Cliente*" />
          <el-input v-model="form.company_name" type="text" disabled placeholder="Llenado automático" />
          <InputError :message="form.errors.company_id" />
        </div>
        <div>
          <InputLabel value="Sucursal*" />
          <el-select @change="saveCompanyBranchAddress" v-model="form.company_branch_id" clearable filterable
            placeholder="Seleccione" no-data-text="Primero selecciona la oportunidad"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="company_branch in companies.find((item) => item.id == form.company_id)?.company_branches"
              :key="company_branch" :label="company_branch.name" :value="company_branch.id" />
          </el-select>
          <InputError :message="form.errors.company_branch_id" />
        </div>
        <div v-if="!has_contact">
          <InputLabel value="Contacto*" />
          <el-select @change="getContactPhone" v-model="form.contact_id" clearable filterable placeholder="Selecciona"
            no-data-text="Primero selecciona la oportunidad" no-match-text="No se encontraron coincidencias">
            <el-option v-for="contact in company_branch_obj?.contacts" :key="contact" :label="contact.name"
              :value="contact.id" />
          </el-select>
          <InputError :message="form.errors.contact_id" />
        </div>
        <div>
          <InputLabel value="Teléfono*" />
          <el-input v-model="form.contact_phone" type="text"
            :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
            :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable placeholder="Ej. 3312479856" />
          <InputError :message="form.errors.contact_phone" />
        </div>
        <h2 class="text-[#373737] font-bold col-span-full">Interacción de Whatsapp</h2>
        <div>
          <InputLabel value="Fecha*" />
          <el-date-picker v-model="form.date" type="date" placeholder="Fecha de pago *" format="YYYY/MM/DD"
            class="!w-full" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
          <InputError :message="form.errors.date" />
        </div>
        <div class="col-span-full">
          <InputLabel value="Notas" />
          <el-input v-model="form.notes" :rows="3" maxlength="800" placeholder="..." show-word-limit type="textarea" />
          <InputError :message="form.errors.notes" />
        </div>
        <div class="col-span-full">
          <InputLabel value="Evidencias" />
          <FileUploader @files-selected="handleMediaSelected" :existingFileUrls="media_urls" />
        </div>
        <div class="flex justify-end items-center col-span-full">
          <PrimaryButton :disabled="form.processing">
            Guardar cambios
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
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      oportunity_id: this.whatsapp_monitor.data.oportunity?.id,
      company_id: this.whatsapp_monitor.data.company?.id,
      contact_id: this.whatsapp_monitor.data.contact.id,
      company_branch_id: this.whatsapp_monitor.data.companyBranch?.id,
      company_name: this.whatsapp_monitor.data.company_name,
      seller_id: this.whatsapp_monitor.data.seller?.id,
      contact_phone: this.whatsapp_monitor.data.contact_phone,
      date: this.whatsapp_monitor.data.date_raw,
      notes: this.whatsapp_monitor.data.notes,
      media: [],
    });

    return {
      form,
      company_branch_obj: null,
      has_contact: false,
      mediaEdited: false,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
    };
  },
  components: {
    PrimaryButton,
    FileUploader,
    InputError,
    AppLayout,
    Back,
    Link,
    InputLabel,
  },
  props: {
    oportunities: Object,
    whatsapp_monitor: Object,
    companies: Array,
    users: Array,
    media_urls: {
      type: Array,
      default: []
    },
  },
  methods: {
    handleMediaSelected(files, mediaUpdated) {
      this.form.media = files;
      this.mediaEdited = mediaUpdated;
    },
    update() {
      if (this.mediaEdited) {
        this.form.post(route("whatsapp-monitors.update-with-media", this.whatsapp_monitor.data.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Interaccion por whatsapp editada",
              type: "success",
            });
          },
        });
      } else {
        this.form.media = null;
        this.form.put(route("whatsapp-monitors.update", this.whatsapp_monitor.data.id), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Interaccion por whatsapp editada",
              type: "success",
            });
          },
        });
      }
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
    saveCompanyBranchAddress() {
      this.company_branch_obj = this.companies.find((item) => item.id == this.form.company_id)?.company_branches[0];
    },
    getContactPhone() {
      this.form.contact_phone = this.company_branch_obj?.contacts?.find(contact => contact.id == this.form.contact_id)?.phone;
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
    getCompany() {
      const oportunity = this.oportunities.find(oportunity => oportunity.id === this.form.oportunity_id);
      // console.log(oportunity);

      this.form.company_id = null;
      this.form.company_branch_id = null;
      this.form.contact_id = null;
      this.form.company_name = null;
      this.form.contact_phone = null;

      if (oportunity.company_name) {
        this.form.company_name = oportunity.company_name;
        this.form.contact_phone = oportunity.contact_phone;
        this.form.company_id = null;
      } else {
        this.form.company_id = oportunity.company.id;
        this.form.company_name = oportunity.company.business_name;
      }
    },
  },
  mounted() {
    this.company_branch_obj = this.companies.find((item) => item.id == this.whatsapp_monitor.data.company.id)?.company_branches[0];
    const oportunity = this.oportunities.find(oportunity => oportunity.id === this.form.oportunity_id);

    if (oportunity.company_name) {
      this.form.company_name = oportunity.company_name;
      this.form.contact_phone = oportunity.contact_phone;
      this.form.company_id = null;
    } else {
      this.form.company_id = oportunity.company.id;
      this.form.company_name = oportunity.company.business_name;
    }
  }
};
</script>