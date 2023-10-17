<template>
    <AppLayout title="Registro de pago">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('client-monitors.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Registro de pago o transacción</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto text-sm my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
        <h2 class="text-secondary">Datos del cliente</h2>
        
            <div class="flex items-center space-x-2">
                <div class="w-1/2">
                    <label>Folio de la oportunidad *</label>
                    <el-select @change="getCompany" class="w-full" v-model="form.oportunity_id" clearable filterable placeholder="Seleccione"
                        no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="oportunity in oportunities.data" :key="oportunity" :label="oportunity.folio" :value="oportunity.id" />
                    </el-select>
                    <InputError :message="form.errors.oportunity_id" />
                </div>
                <div class="w-1/2">
                    <label>Cliente</label>
                    <input v-model="company_name" disabled class="input cursor-not-allowed" type="text">
                </div>
            </div>
            <div class="w-1/2">
                <label>Vendedor</label>
                <input v-model="seller_name" disabled class="input cursor-not-allowed" type="text">
            </div>

            <h2 class="text-secondary pt-4">Detalles del pago</h2>

            <div class="lg:flex items-center pt-3">
                <div class="flex items-center lg:w-1/2 lg:mt-0">
                <el-tooltip content="Fecha de pago *" placement="top">
                    <span class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-calendar"></i>
                    </span>
                </el-tooltip>
                <el-date-picker v-model="form.paid_at" type="date" placeholder="Fecha de pago *" />
                <InputError :message="form.errors.paid_at" />
                </div>
                <div class="w-1/2">
                    <label>Monto pagado *</label>
                    <input v-model="form.amount" class="input" type="number" min="0">
                    <InputError :message="form.errors.amount" />
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-1/2">
                    <label>Método de pago *</label>
                    <el-select class="w-full" v-model="form.payment_method" clearable filterable placeholder="Seleccione"
                        no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="payment_method in payment_methods" :key="payment_method" :label="payment_method" :value="payment_method" />
                    </el-select>
                    <InputError :message="form.errors.payment_method" />
                </div>
                <div class="w-1/2">
                    <label>Concepto</label>
                    <input v-model="form.concept" class="input" type="text">
                    <InputError :message="form.errors.concept" />
                </div>
            </div>
            <div>
                <label>Observaciones</label>
                <textarea v-model="form.notes" class="textarea" rows="2">
                </textarea>
                <InputError :message="form.errors.notes" />
            </div>
            <div class="ml-2 mt-2 col-span-full flex">
              <FileUploader @files-selected="this.form.media = $event" />
            </div>
          <div class="flex justify-end items-center">
            <PrimaryButton :disabled="form.processing">
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
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";

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
    Link,
  },
  props: {
    oportunities: Object,
  },
  methods: {
    store(){
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
    const oportunity = this.oportunities.data.find(oportunity => oportunity.id === this.form.oportunity_id);
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
};
</script>

<style scoped>

/* Estilo para el hover de las opciones */
.el-select-dropdown .el-select-dropdown__item:hover {
  background-color: #D90537; /* Color de fondo al hacer hover */
  color: white; /* Color del texto al hacer hover */
  border-radius: 20px; /* Redondeo */
}
</style>
