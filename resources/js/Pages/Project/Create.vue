<template>
  <div>
    <AppLayout title="Crear proyecto |">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('projects.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear proyecto</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.project_name" inputPlaceholder="Título del proyecto *" inputType="text">
              <el-tooltip content="Título del proyecto" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div class="lg:flex space-x-3 pt-3 relative">
            <el-select class="lg:w-1/ mt-2" v-model="form.owner" clearable filterable placeholder="Propietario"
            no-data-text="No hay propietarios registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in projects?.data" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
            <el-select class="lg:w-1/2 mt-2" v-model="form.group" clearable filterable placeholder="Grupo"
            no-data-text="No hay grupos registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="group in groups" :key="group" :label="group" :value="group" />
          </el-select>
          <p class="absolute -top-2 right-0 text-primary text-xs cursor-pointer">Agregar grupo nuevo</p>
          </div>
          <div>
            <div class="flex space-x-3 items-center pt-3">
              <IconInput v-model="form.name" class="lg:w-1/2" inputPlaceholder="Escriba un nombre de etiqueta *" inputType="text">
                <el-tooltip content="Escriba un nombre de etiqueta" placement="top"> <i class="fa-solid fa-tag"></i> </el-tooltip>
              </IconInput>
              <i class="fa-solid fa-circle-plus text-primary cursor-pointer"></i>
            </div>
            <InputError :message="form.errors.name" />
          </div>
        <div class="lg:flex pt-3">
          <div class="flex items-center lg:w-1/2 mt-2 lg:mt-0">
            <el-tooltip content="Fecha de inicio *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
            </el-tooltip>
            <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha de inicio *"
              format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
            <InputError :message="form.errors.start_date" :disabled-date="disabledDate" />
          </div>
          <div class="flex items-center lg:w-1/2 mt-2 lg:mt-0">
            <el-tooltip content="Fecha de final *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
            </el-tooltip>
            <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha de final *"
              format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
            <InputError :message="form.errors.limit_date" :disabled-date="disabledDate" />
          </div>
        </div>
        <div class="flex justify-end items-center space-x-3 mr-auto mt-2">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember" class="bg-transparent"/>
          <span class="ml-2 text-sm text-[#9A9A9A]">Proyecto estricto</span>
        </label>
        <el-tooltip content="Las tareas no pueden comenzar ni finalizar fuera de las fechas programadas de un proyecto " placement="top">
          <i class="fa-solid fa-circle-info text-primary text-xs cursor-pointer"></i>
        </el-tooltip>
      </div>

    <div>
      <h3 class="font-bold text-lg mb-2">Descripción</h3>
      <div class="rounded-lg border border-gray-600 h-40">

      </div>
    </div>

    <div>
      <h3 class="font-bold text-lg mb-2">Presupuesto</h3>
      <div class="flex space-x-3">
        <el-select class="w-1/2" v-model="selectedProyect" clearable filterable placeholder="Grupo"
          no-data-text="No hay grupos registrados" no-match-text="No se encontraron coincidencias">
          <el-option v-for="item in projects?.data" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
        <div class="w-1/2">
          <IconInput v-model="form.name" inputPlaceholder="Monto *" inputType="number">
            <el-tooltip content="Título del proyecto" placement="top"> $ </el-tooltip>
          </IconInput>
          <InputError :message="form.errors.name" />
        </div>
      </div>
    </div>

    <div class="text-sm lg:text-base">
      <h3 class="font-bold text-lg mb-2 mt-10">Acceso al proyecto</h3>
      <div class="my-1">
        <input class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-transparent" type="radio" name="access_proyect"> Privado
        <p class="text-[#9A9A9A] ml-4">Solo los usuarios de proyecto pueden ver y acceder a este proyecto</p>
      </div>
      <div class="my-1">
        <input class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-transparent" type="radio" name="access_proyect"> Público
        <p class="text-[#9A9A9A] ml-4">Los usuarios del portal solo pueden  ver, seguir y comentar, mientras que los usuarios del proyecto tendrán acceso directo.</p>
      </div>
    </div>


          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Crear proyecto
            </PrimaryButton>
          </div>
        </div>
      </form>
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  data() {
    const form = useForm({
      project_name: null,
      group: null,

    });

    return {
      form,
      groups:[
        'Compras',
        'Ventas',
        'Producción',
        'Diseño',
      ],
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    Link,
    InputError,
    IconInput,
    Checkbox,
  },
  props: {

  },
  methods: {
    store() {
      this.form.post(route("projects.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado un nuevo proyecto",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
  },
};
</script>
