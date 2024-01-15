<template>
  <AppLayout title="Crear actividad de oportunidad">
    <template #header>
      <div class="flex justify-between">
        <Back />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Nueva actividad de oportunidad</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="store">
      <div
        class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4 mx-2 text-sm lg:text-base">
        <div class="flex items-center space-x-2">
          <div class="w-1/2">
            <label>Nombre de la actividad *</label>
            <input v-model="form.name" class="input" type="text" placeholder="Escribe el nombre">
            <InputError :message="form.errors.name" />
          </div>
          <div class="w-1/2">
            <label>Asignado a *</label>
            <el-select class="w-full" v-model="form.asigned_id" clearable filterable placeholder="Seleccionar usuario"
              no-data-text="No hay usuarios registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="user in users" :key="user" :label="user.name" :value="user.id">
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                  class="flex text-sm rounded-full items-center mt-[3px]">
                  <img class="h-7 w-7 rounded-full object-cover mr-4" :src="user.profile_photo_url" :alt="user.name" />
                  <p>{{ user.name }}</p>
                </div>
              </el-option>
            </el-select>
            <InputError :message="form.errors.asigned_id" />
          </div>
        </div>
        <div class="lg:flex items-center pt-3">
          <div class="lg:w-1/2 lg:mt-0">
            <label class="block">Fecha límite *</label>
            <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha límite *" format="YYYY/MM/DD"
              value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
            <InputError :message="form.errors.limit_date" />
          </div>
          <div class="w-1/2">
            <label>Hora *</label>
            <el-time-select v-model="form.time" start="07:00" step="00:15" end="23:30"
              placeholder="Seleccione una hora" />
            <InputError :message="form.errors.time" />
          </div>
        </div>
        <div>
          <div class="lg:w-1/2 relative">
            <i :class="getColorPriority(form.priority)"
              class="fa-solid fa-circle text-xs top-1 left-20 absolute z-30"></i>
            <label class="block">Prioridad</label>
            <div class="flex items-center space-x-4">
              <el-select class="lg:w-1/2" v-model="form.priority" clearable filterable placeholder="Seleccione"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
                  <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
                  <span style="float: center; margin-left: 5px; font-size: 13px">{{
                    item.label
                  }}</span>
                </el-option>
              </el-select>
              <InputError :message="form.errors.priority" />
            </div>
          </div>
          <!-- <label>Prioridad *</label> -->
          <!-- <el-select class="w-full mt-2" v-model="form.priority" clearable filterable placeholder="Seleccionar prioridad"
            no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
          <InputError :message="form.errors.priority" /> -->
        </div>
        <div class="mt-5 col-span-full">
          <label>Descripción</label>
          <RichText @content="updateDescription($event)" />
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
import InputError from "@/Components/InputError.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

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
      priorities: [
        {
          label: "Baja",
          color: "text-[#87CEEB]",
        },
        {
          label: "Media",
          color: "text-orange-500",
        },
        {
          label: "Alta",
          color: "text-red-600",
        },
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    InputError,
    FileUploader,
    RichText,
    Back,
    Link,
  },
  props: {
    users: Array,
    oportunity_id: Number,
  },
  methods: {
    store() {
      this.form.post(route("oportunity-tasks.store", this.oportunity_id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado una nueva actividad",
            type: "success",
          });
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
      return time < today;
    },
    updateDescription(content) {
      this.form.description = content;
    },
    getColorPriority(oportunityPriority) {
      if (oportunityPriority === "Baja") {
        return "text-[#87CEEB]";
      } else if (oportunityPriority === "Media") {
        return "text-[#D97705]";
      } else if (oportunityPriority === "Alta") {
        return "text-[#D90537]";
      } else {
        return "text-transparent";
      }
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
