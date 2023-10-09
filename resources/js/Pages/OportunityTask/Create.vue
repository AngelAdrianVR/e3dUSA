<template>
    <AppLayout title="Crear actividad de oportunidad">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('oportunities.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Nueva atividad de oportunidad</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
            <div class="flex items-center space-x-2">
                <div class="w-1/2">
                    <label>Nombre de la actividad *</label>
                    <input v-model="form.name" class="input" type="text">
                    <InputError :message="form.errors.name" />
                </div>
                <div class="w-1/2">
                    <label>Asignado a *</label>
                    <el-select class="w-full" v-model="form.asigned_id" clearable filterable placeholder="Seleccionar usuario"
                        no-data-text="No hay usuarios registrados" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="user in users" :key="user" :label="user.name" :value="user.id" />
                    </el-select>
                    <InputError :message="form.errors.asigned_id" />
                </div>
            </div>
            <div class="lg:flex items-center pt-3">
                <div class="flex items-center lg:w-1/2 lg:mt-0">
                <el-tooltip content="Fecha limite *" placement="top">
                    <span class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-calendar"></i>
                    </span>
                </el-tooltip>
                <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha limite *"
                    :disabled-date="disabledDate" />
                <InputError :message="form.errors.limit_date" />
                </div>
                <div class="w-1/2">
                    <label>Hora *</label>
                    <input v-model="form.time" class="input" type="time">
                    <InputError :message="form.errors.time" />
                </div>
            </div>
            <div>
                <label>Prioridad *</label>
                <el-select class="w-full mt-2" v-model="form.priority" clearable filterable placeholder="Seleccionar proyecto"
                    no-data-text="No hay proyectos registrados" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in priorities" :key="item" :label="item" :value="item" />
                </el-select>
                <InputError :message="form.errors.priority" />
            </div>
            <div>
                <label>Descripción</label>
                <input v-model="form.description" name="taski_name" class="input" type="text">
                <InputError :message="form.errors.description" />
            </div>
            <p @click="activateFileInput" class="text-primary cursor-pointer">+ Adjuntar archivos</p>
            <div class="ml-4 -mt-5">
              <ul>
                <li class="text-secondary text-sm" v-for="fileName in form.mediaNames" :key="fileName">{{ fileName }}</li>
              </ul>
            </div>
            <input  @input="form.media = $event.target.files" multiple type="file" id="fileInput" style="display: none;" @change="handleFileUpload">
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
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      asigned_id: null,
      limit_date: null,
      time: null,
      priority: null,
      description: null,
      media: [],
          });

    return {
      form,
       mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
      priorities:[
        'Baja',
        'Media',
        'Alta',
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    Link,
    InputError,
  },
  props: {
    users: Array,
  },
  methods: {
    store(){
      this.form.post(route("oportunity-tasks.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado una nueva actividad",
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
    // Función para deshabilitar fechas fuera del rango [start_date, limit_date]
    disabledDate(time) {
        const today = new Date(); // Obtener la fecha de hoy
        return time.getTime() < today.getTime();
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
