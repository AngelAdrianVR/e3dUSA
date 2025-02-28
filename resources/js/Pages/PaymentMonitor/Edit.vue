<template>
  <AppLayout title="Editar pago o transacción">
    <template #header>
      <div class="flex justify-between">
        <Back />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Editar pago o transacción</h2>
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
          <InputLabel value="Cliente*" />
          <el-input v-model="company_name" type="text" disabled placeholder="Llenado automático" />
        </div>
        <div>
          <InputLabel value="Vendedor*" />
          <el-input v-model="seller_name" type="text" disabled placeholder="Llenado automático" />
        </div>
        <h2 class="text-[#373737] font-bold col-span-full">Detalles del pago</h2>
        <div>
          <InputLabel value="Fecha de pago*" />
          <el-date-picker v-model="form.paid_at" type="date" placeholder="Fecha de pago *" format="YYYY/MM/DD"
            class="!w-full" value-format="YYYY-MM-DD" />
          <InputError :message="form.errors.paid_at" />
        </div>
        <div>
          <InputLabel value="Monto pagado*" />
          <el-input v-model="form.amount" type="text"
            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
            :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 5,000" />
          <InputError :message="form.errors.amount" />
        </div>
        <div>
          <InputLabel value="Método de pago*" />
          <el-select class="w-full" v-model="form.payment_method" clearable filterable placeholder="Selecciona"
            no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="payment_method in payment_methods" :key="payment_method" :label="payment_method"
              :value="payment_method" />
          </el-select>
          <InputError :message="form.errors.payment_method" />
        </div>
        <div>
          <InputLabel value="Concepto*" />
          <el-input v-model="form.concept" type="text" placeholder="Ej. Anticipo" />
          <InputError :message="form.errors.concept" />
        </div>
        <div class="col-span-full">
          <InputLabel value="Observaciones" />
          <el-input v-model="form.notes" :rows="3" maxlength="800" placeholder="..." show-word-limit type="textarea" />
          <InputError :message="form.errors.notes" />
        </div>
        <div class="col-span-full">
          <InputLabel value="Evidencias" />
          <FileUploader @files-selected="handleMediaSelected"
            :existingFileUrls="media_urls" />
        </div>
        <div class="col-span-full flex justify-end items-center">
          <PrimaryButton :disabled="form.processing">
            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
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
      oportunity_id: this.payment_monitor.data.oportunity?.id,
      company_id: this.payment_monitor.data.clientMonitor.company_id,
      paid_at: this.payment_monitor.data.paid_at_raw,
      amount: this.payment_monitor.data.amount,
      payment_method: this.payment_monitor.data.payment_method,
      concept: this.payment_monitor.data.concept,
      notes: this.payment_monitor.data.notes,
      media: [],
    });

    return {
      form,
      company_name: this.payment_monitor.data.clientMonitor.company.business_name,
      seller_name: this.$page.props.auth.user.name,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
      payment_methods: ["Transferencia electrónica", "Otro"],
      mediaEdited: false,
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
    payment_monitor: Object,
    oportunities: Object,
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
        this.form.post(route("payment-monitors.update-with-media", this.payment_monitor.data.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Registro de pago editado",
              type: "success",
            });
          },
        });
      } else {
        this.form.media = null;
        this.form.put(route("payment-monitors.update", this.payment_monitor.data.id), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Registro de pago editado",
              type: "success",
            });
          },
        });
      }
    },
    activateFileInput() {
      // Simula un clic en el campo de entrada de archivos al hacer clic en el párrafo
      document.getElementById("fileInput").click();
    },
    getCompany() {
      const oportunity = this.oportunities.find(oportunity => oportunity.id === this.payment_monitor.data.oportunity?.id);

      // if (oportunity.company) {
      this.form.company_id = oportunity.company.id;
      this.company_name = oportunity.company.business_name;
      // } else {
      //   this.company_name = 'Nuevo cliente. Contacto: ' + oportunity.contact; 
      //   this.form.company_id = null;
      // }
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
    mounted() {
      const oportunity = this.oportunities.find(oportunity => oportunity.id === this.form.oportunity_id);

      if (oportunity.company) {
        this.form.company_id = oportunity.company.id;
        this.company_name = oportunity.company.business_name;
      } else {
        this.company_name = 'Nuevo cliente. Contacto: ' + oportunity.contact;
        this.form.company_id = null;
      }
    },
  },
};
</script>
