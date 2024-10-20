<template>
  <AppLayout title="Registro de pago">
    <template #header>
      <div class="flex justify-between">
        <Back />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Registro de pago o transacción</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="store">
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
          <el-input v-model="company_name" type="text" disabled placeholder="Selección automática" />
        </div>
        <div>
          <InputLabel value="Vendedor*" />
          <el-input v-model="seller_name" type="text" disabled placeholder="Selección automática" />
        </div>
        <h2 class="text-[#373737] font-bold col-span-full">Detalles del pago</h2>
        <div>
          <InputLabel value="Fecha de pago*" />
          <el-date-picker v-model="form.paid_at" type="date" placeholder="Fecha de pago *" format="YYYY/MM/DD" class="!w-full"
            value-format="YYYY-MM-DD" />
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
          <el-input v-model="form.notes" :rows="3" maxlength="800" placeholder="..." show-word-limit
            type="textarea" />
          <InputError :message="form.errors.notes" />
        </div>
        <div class="col-span-full">
          <FileUploader @files-selected="this.form.media = $event" />
        </div>
        <div class="col-span-full flex justify-end items-center">
          <PrimaryButton :disabled="form.processing">
            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
            Agregar
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
      oportunity_id: null,
      company_id: null,
      seller_id: this.$page.props.auth.user.id,
      paid_at: null,
      amount: null,
      payment_method: null,
      concept: null,
      priority: null,
      notes: null,
      media: [],
    });

    return {
      form,
      company_name: null,
      seller_name: this.$page.props.auth.user.name,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
      payment_methods: [
        'Transferencia electrónica',
        'Otro',
      ],
    };
  },
  components: {
    PrimaryButton,
    FileUploader,
    InputError,
    AppLayout,
    Back,
    Link,
    InputLabel
  },
  props: {
    oportunities: Object,
    opportunity: Object,
  },
  methods: {
    store() {
      this.form.post(route('payment-monitors.store'), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Registro de pago exitoso",
            type: "success",
          });
        },
      });
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

      if (oportunity.company) {
        this.form.company_id = oportunity.company.id;
        this.company_name = oportunity.company.business_name;
      } else {
        this.company_name = 'Nuevo cliente. Contacto: ' + oportunity.contact;
        this.form.company_id = null;
      }
    },
  },
  mounted() {
    if (this.opportunity) {
      this.form.oportunity_id = this.opportunity.id;
      this.getCompany();
      this.form.company_branch_id = this.opportunity.company_branch_id;
    }
  }
};
</script>