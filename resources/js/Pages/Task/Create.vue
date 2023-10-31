<template>
  <AppLayout title="Crear tarea">
    <template #header>
      <div class="flex justify-between">
        <Link :href="route('projects.index')"
          class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
        <i class="fa-solid fa-chevron-left"></i>
        </Link>
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Nueva tarea</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="store">
      <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
        <div>
          <label class="block" for="">Proyecto *</label>
          <el-select @change="getProject()" class="w-full mt-2" v-model="form.project_id" clearable filterable
            placeholder="Seleccionar proyecto" no-data-text="No hay proyectos registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in projects" :key="item.id" :label="item.project_name" :value="item.id" />
          </el-select>
          <InputError :message="form.errors.project_id" />
        </div>
        <div>
          <label>Nombre de la tarea *</label>
          <input v-model="form.title" class="input" type="text" placeholder="Escribe el nombre">
          <InputError :message="form.errors.title" />
        </div>
        <div>
          <label>Departamento *</label>
          <el-select class="w-full mt-2" v-model="form.department" clearable filterable
            placeholder="Seleccionar departamento" no-data-text="No hay departamentos registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in departments" :key="item" :label="item" :value="item" />
          </el-select>
          <InputError :message="form.errors.department" />
        </div>
        <div class="mt-5 col-span-full">
          <label>Descripción</label>
          <RichText @content="updateDescription($event)" />
        </div>
        <FileUploader @files-selected="this.form.media = $event" />
        <div>
          <label>Participantes *</label>
          <el-select v-model="form.participants" clearable multiple placeholder="Seleccione" class="w-full mt-1"
            no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
            <el-option v-for="(item, index) in projects.find(item => item.id === form.project_id).users" :key="item.id"
              :label="item.name" :value="item.id">
              <div v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex text-sm rounded-full items-center mt-[3px]">
                <img class="h-7 w-7 rounded-full object-cover mr-4" :src="item.profile_photo_url" :alt="item.name" />
                <p>{{ item.name }}</p>
              </div>
            </el-option>
          </el-select>
          <InputError :message="form.errors.participants" />
        </div>
        <div class="col-span-full">
          <label class="ml-2">
            Prioridad * <i class="fa-solid fa-circle text-xs ml-2" :class="{
              'text-transparent': !form.priority,
              'text-[#87CEEB]': form.priority == 'Baja',
              'text-[#D97705]': form.priority == 'Media',
              'text-[#D90537]': form.priority == 'Alta'
            }"></i>
          </label>
          <el-select v-model="form.priority" clearable placeholder="Seleccione" class="w-1/2 mt-1"
            no-data-text="No hay opciones para mostrar" no-match-text="No se encontraron coincidencias">
            <el-option v-for="(item, index) in priorities" :key="item.id" :label="item" :value="item" />
          </el-select>
          <InputError :message="form.errors.priority" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block">Fecha de inicio *</label>
            <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha de inicio *"
              :disabled-date="disabledStartOrLimitDate" />
            <InputError :message="form.errors.start_date" />
          </div>
          <div>
            <label class="block">Fecha límite *</label>
            <el-date-picker v-model="form.end_date" type="date" placeholder="Fecha de final *"
              :disabled-date="disabledStartOrLimitDate" />
            <InputError :message="form.errors.end_date" />
          </div>
        </div>
        <!-- <div>
            <div class="flex items-center">
                <label>Recordatorio</label>
                <i @click="remainderModal = true" class="fa-regular fa-clock text-xs text-primary ml-5 cursor-pointer"></i>
                <p @click="remainderModal = true" class="text-xs text-primary ml-2 cursor-pointer">Elige un horario y fecha</p>
            </div>
            <el-select class="w-full mt-2" v-model="form.reminder" clearable filterable placeholder="Seleccionar una opción"
                no-data-text="No hay opciones disponibles" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in reminders" :key="item" :label="item" :value="item">
                    <span style="float: left">{{ item }}</span>
                  <span style="float: right; color: #cccccc; font-size: 13px;">{{ '8:00 am' }}</span>
                </el-option>
            </el-select>
            <InputError :message="form.errors.reminder" />
        </div> -->



        <div class="flex md:text-left items-center">
          <PrimaryButton :disabled="form.processing">
            Agregar
          </PrimaryButton>
          <!-- <p class="text-primary text-sm ml-5 cursor-pointer">+ Agregar tarea consecutiva </p>
             <el-tooltip content="Son las tareas relacionadas a la tarea principal" placement="top">
            <i class="fa-regular fa-circle-question text-xs ml-2"></i>
            </el-tooltip> -->
        </div>
      </div>
      {{ selectedProject }}
    </form>

    <!-- -------------- Remainder Modal -------------- -->
    <Modal :show="remainderModal" @close="remainderModal = false">
      <div class="mx-7 my-4 space-y-4 relative">
        <div @click="remainderModal = false"
          class="cursor-pointer w-5 h-5 rounded-full flex items-center justify-center absolute top-0 right-0">
          <i class="fa-solid fa-xmark"></i>
        </div>
        <div class="flex items-center">
          <i class="fa-regular fa-clock text-sm text-primary ml-5 cursor-pointer"></i>
          <p class="text-sm text-primary ml-2 cursor-pointer">Elige un horario y fecha</p>
        </div>

        <div class="text-center">
          <el-date-picker v-model="form.reminder" type="datetime" placeholder="Selecciona la fecha y hora" />
        </div>

        <div class="flex justify-end space-x-3 pt-5 pb-1">
          <PrimaryButton>Agregar</PrimaryButton>
          <CancelButton @close="remainderModal = false">Cancelar</CancelButton>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import RichText from "@/Components/MyComponents/RichText.vue";

export default {
  data() {
    const form = useForm({
      project_id: parseInt(this.parent_id),
      title: null,
      description: null,
      department: null,
      participants: null,
      priority: null,
      reminder: null,
      start_date: '',
      end_date: '',
      media: [],
    });

    return {
      form,
      remainderModal: false,
      selectedProject: null,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
      priorities: [
        'Baja',
        'Media',
        'Alta',
      ],
      departments: [
        'Produccion',
        'Ventas',
        'Diseño',
        'Marketing',
      ],
      reminders: [
        'Cada día',
        'Un dia antes de la fecha de vencimiento',
        'En la fecha de vencimiento',
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    Link,
    InputError,
    Modal,
    CancelButton,
    FileUploader,
    RichText,
  },
  props: {
    projects: Array,
    parent_id: Number,
    users: Array,
  },
  methods: {
    store() {
      this.form.post(route("tasks.store"), {
        // _method: 'post',
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado una nueva tarea",
            type: "success",
          });
        },
      });
    },
    updateDescription(content) {
      this.form.description = content;
    },
    getProject() {
      this.selectedProject = this.projects.find(item => item.id == this.form.project_id);
    },
    // Función para deshabilitar fechas fuera del rango [start_date, limit_date]
    disabledStartOrLimitDate(time) {
      if (this.selectedProject && this.selectedProject.is_strict_project) {
        const startTime = new Date(this.selectedProject.start_date).getTime();
        const limitTime = new Date(this.selectedProject.limit_date).getTime();
        return time.getTime() < startTime || time.getTime() > limitTime;
      }
      return false;
    },

  },
};
</script>

<style scoped>
/* Estilo para el hover de las opciones */
.el-select-dropdown .el-select-dropdown__item:hover {
  background-color: #D90537;
  /* Color de fondo al hacer hover */
  color: white;
  /* Color del texto al hacer hover */
  border-radius: 20px;
  /* Redondeo */
}
</style>
