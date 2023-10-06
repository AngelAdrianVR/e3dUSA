<template>
  <div>
    <AppLayout title="Crear Oportunidad |">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('oportunities.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear Oportunidad</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
          <div>
              <label>Nombre de la oportunidad *</label>
              <input v-model="form.name" class="input" type="text">
              <InputError :message="form.errors.name" />
          </div>
          <div class="relative">
        <i :class="getColorStatus(form.status)"
          class="fa-solid fa-circle text-xs top-1 left-16 absolute z-30"></i>
        <label>Estatus</label> <br />
        <div class="flex items-center space-x-4">
          <el-select class="lg:w-1/2 mt-2" v-model="form.status" clearable
            filterable placeholder="Seleccionar estatus" no-data-text="No hay estatus registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
        </div>
        <InputError :message="form.errors.status" />
      </div>
          <div>
              <label>Creado por</label>
              <input disabled v-model="owner" class="input text-gray-400" type="text">
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
            <el-tooltip content="Fecha estimada de cierre" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
            </el-tooltip>
            <el-date-picker v-model="form.limit_date" type="date" placeholder="Fecha estimada de cierre *"
              format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
            <InputError :message="form.errors.limit_date" :disabled-date="disabledDate" />
          </div>
        </div>
      <div>
        <label>Descripción *</label>
        <RichText v-model="form.description" />
      </div>
      <p @click="activateFileInput" class="text-primary cursor-pointer">+ Adjuntar archivos</p>
        <div class="ml-4 -mt-5">
            <ul>
            <li class="text-secondary text-sm" v-for="fileName in form.mediaNames" :key="fileName">{{ fileName }}</li>
            </ul>
        </div>
        <input  @input="form.media = $event.target.files" multiple type="file" id="fileInput" style="display: none;" @change="handleFileUpload">

<div class="flex items-center space-x-4">
    <div class="lg:w-1/2">
       <label class="block">Etiquetas <i class="fa-solid fa-circle-plus ml-7 text-primary text-xs cursor-pointer"></i></label>
        <div class="flex items-center space-x-4">
          <el-select class="lg:w-1/2" v-model="form.tag" clearable
            filterable placeholder="Escriba el nombre de etiqueta" no-data-text="No hay etiquetas registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
        </div>
        <InputError :message="form.errors.tag" /> 
    </div>  
    <div class="lg:w-1/2">
        <label>Probabilidad %</label>
        <input v-model="form.probability" class="input text-gray-400" type="number">
    </div>
</div>
<div class="flex items-center space-x-4">
    <div class="lg:w-1/2">
       <label class="block">Prioridad</label>
        <div class="flex items-center space-x-4">
          <el-select class="lg:w-1/2" v-model="form.priority" clearable
            filterable placeholder="Seleccione" no-data-text="No hay opciones registradas"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in priorities" :key="item" :label="item.label" :value="item.label">
              <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
              <span style="float: center; margin-left: 5px; font-size: 13px">{{
                item.label
              }}</span>
            </el-option>
          </el-select>
        </div>
        <InputError :message="form.errors.priority" /> 
    </div>  
    <div class="lg:w-1/2">
        <label>Causa oportunidad perdida 
            <el-tooltip content="Escribe la causa por lo que se PERDIÓ esta oportunidad" placement="top">
                <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
             </el-tooltip>
        </label>
        <input v-model="form.lostOportunityRazon" class="input text-gray-400" type="text">
    </div>
</div>

    <div class="text-sm">
      <h3 class="font-bold text-lg mb-2 mt-10">Acceso al proyecto</h3>
      <div class="my-1">
        <input v-model="form.type_access_project" value="Publico" class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-transparent" type="radio" name="type_access_project"> Público
        <p class="text-[#9A9A9A] ml-4">Los usuarios del portal solo pueden  ver, seguir y comentar, mientras que los usuarios del proyecto tendrán acceso directo.</p>
      </div>
      <div class="my-1">
        <input v-model="form.type_access_project" value="Privado" class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-transparent" type="radio" name="type_access_project"> Privado
        <p class="text-[#9A9A9A] ml-4">Solo los usuarios de proyecto pueden ver y acceder a este proyecto</p>
      </div>
    </div>

    <section class="rounded-lg bg-[#CCCCCC] py-4 px-7 h-[545px]">
      <div class="text-right">
        <ThirthButton type="button" @click.stop="editAccesFlag = !editAccesFlag">{{ editAccesFlag ? 'Actualizar' : 'Editar'}}</ThirthButton>
      </div>

      <div class="flex justify-between overflow-y-scroll h-[460px] mt-4">
        <div class="w-full">
          <div class="flex">
            <h2 class="font-bold border-b border-[#9A9A9A] w-2/3 pl-3">Usuarios</h2>
            <h2 class="font-bold border-b border-[#9A9A9A] w-1/3">Permisos</h2>
          </div>
          <div class="pl-3">
            <figure class="flex mt-2 border-b border-[#9A9A9A]"  v-for="user in (users)" :key="user">
              <div class="w-2/3 flex space-x-2">
                <div v-if="$page.props.jetstream.managesProfilePhotos"
                    class="flex text-sm rounded-full w-12">
                    <img class="h-10 w-10 rounded-full object-cover" :src="user.profile_photo_url"
                    :alt="user.name" />
                </div>
                <div class="text-sm w-full">
                    <p class="font-bold">{{ user.name }}</p>
                    <p v-if="user.employee_properties">{{ 'Depto.' + user.employee_properties?.department }}</p>
                    <p v-else>Super admin</p>
                </div>
              </div>

              <div class="w-1/3">
          <div class="space-y-1 mb-2">
            <label class="flex items-center">
              <Checkbox :disabled="!editAccesFlag" v-model:checked="form.is_strict_proyect" class="bg-transparent disabled:border-gray-400"/>
              <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''" class="ml-2 text-xs">Crea tarea</span>
            </label>
            <label class="flex items-center">
              <Checkbox v-model:checked="form.is_strict_proyect" class="bg-transparent disabled:border-gray-400"/>
              <span class="ml-2 text-xs">Ver</span>
            </label>
            <label class="flex items-center">
              <Checkbox :disabled="!editAccesFlag" v-model:checked="form.is_strict_proyect" class="bg-transparent disabled:border-gray-400"/>
              <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''" class="ml-2 text-xs">Editar</span>
            </label>
            <label class="flex items-center">
              <Checkbox :disabled="!editAccesFlag" v-model:checked="form.is_strict_proyect" class="bg-transparent disabled:border-gray-400"/>
              <span :class="!editAccesFlag ? 'text-gray-500/80 cursor-not-allowed' : ''" class="ml-2 text-xs">Eliminar</span>
            </label>
            <label class="flex items-center">
              <Checkbox v-model:checked="form.is_strict_proyect" class="bg-transparent disabled:border-gray-400"/>
              <span class="ml-2 text-xs">Comentar</span>
            </label>
          </div>
        </div>

            </figure>
          </div>
        </div>
      </div>
    </section>

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
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      status: null,
      description: null,
      tag: null,
      probability: null,
      priority: null,
      type_access_project: 'Publico',
      lostOportunityRazon: null,
      media: [],

    });

    return {
      form,
      mediaNames: [], // Agrega esta propiedad para almacenar los nombres de los archivos
      editAccesFlag: false,
      company_branch_obj: null,
      owner: this.$page.props.auth.user.name,
      statuses: [
        {
          label: "Nueva",
          color: "text-[#9A9A9A]",
        },
        {
          label: "Pendiente",
          color: "text-[#F3FD85]",
        },
        {
          label: "En proceso",
          color: "text-[#FEDBBD]",
        },
        {
          label: "Pagado",
          color: "text-[#AFFDB2]",
        },
        {
          label: "Perdida",
          color: "text-[#F7B7FC]",
        },
      ],
      priorities:[
         {
          label: "Baja",
          color: "text-[#87CEEB]",
        },
         {
          label: "Media",
          color: "text-[#D97705]",
        },
         {
          label: "Alta",
          color: "text-[#D90537]",
        },
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
    ThirthButton,
    RichText
  },
  props: {
    companies: Array,
    users: Array,
  },
  methods: {
    store() {
      this.form.post(route("oportunities.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado una nueva oportunidad",
            type: "success",
          });

        },
      });
    },
    saveCompanyBranchAddress(){
      this.company_branch_obj = this.companies.find((item) => item.id == this.form.company_id)?.company_branches[0];
      // console.log(this.company_branch_obj);
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
    getColorStatus(oportunityStatus) {
      if (oportunityStatus === "Nueva") {
        return "text-[#9A9A9A]";
      } else if (oportunityStatus === "Pendiente") {
        return "text-[#F3FD85]";
      } else if (oportunityStatus === "En proceso") {
        return "text-[#FEDBBD]";
      } else if (oportunityStatus === "Pagado") {
        return "text-[#AFFDB2]";
      } else if (oportunityStatus === "Perdida") {
        return "text-[#F7B7FC]";
      } else {
        return "text-transparent";
      }
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
};
</script>

<style scoped>

/* Estilo para el hover de las opciones */
.el-select-dropdown .el-select-dropdown__item:hover {
  background-color: #d8224d; /* Color de fondo al hacer hover */
  color: white; /* Color del texto al hacer hover */
  border-radius: 20px; /* Redondeo */
}
</style>
