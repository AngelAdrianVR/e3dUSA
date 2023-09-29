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
              <label>Título del proyecto *</label>
              <input v-model="form.project_name" class="input" type="text">
              <InputError :message="form.errors.project_name" />
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
          <Checkbox v-model:checked="form.is_strict_proyect" class="bg-transparent"/>
          <span class="ml-2 text-sm text-[#9A9A9A]">Proyecto estricto</span>
        </label>
        <el-tooltip content="Las tareas no pueden comenzar ni finalizar fuera de las fechas programadas de un proyecto " placement="top">
          <i class="fa-solid fa-circle-info text-primary text-xs"></i>
        </el-tooltip>
      </div>
      <div>
        <label>Descripción *</label>
        <textarea v-model="form.description" class="textarea w-full">
        </textarea>
      </div>
      <div class="w-1/2">
          <el-select v-model="form.group" clearable filterable placeholder="Grupo"
          no-data-text="No hay grupos registrados" no-match-text="No se encontraron coincidencias">
          <el-option v-for="group in groups" :key="group" :label="group" :value="group" />
        </el-select>
          <InputError :message="form.errors.group" />
      </div>
      <div class="inline-block">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.is_internal_project" class="bg-transparent" />
          <span class="ml-2 text-sm text-[#9A9A9A]">Proyecto interno</span>
        </label>
      </div>

      <section v-if="!form.is_internal_project">
        <h2 class="font-bold my-3">Campos adicionales</h2>

        <div class="flex space-x-7 justify-between">
          <div class="w-1/2">
          <label>Cliente *</label> <br>
            <el-select v-model="form.company_id" clearable filterable placeholder="Seleccione"
            no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="company in companies" :key="company" :label="company.business_name" :value="company.id" />
            </el-select>
            <InputError :message="form.errors.company_id" />
          </div>
          <div class="w-1/2">
          <label>Sucursal *</label> <br>
            <el-select @change="saveCompanyBranchAddress" v-model="form.company_branch" clearable filterable placeholder="Seleccione"
            no-data-text="No hay sucursales registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="company_branch in companies.find((item) => item.id == form.company_id)?.company_branches"
             :key="company_branch" :label="company_branch.name" :value="company_branch.name" />
            </el-select>
            <InputError :message="form.errors.company_branch" />
          </div>
        </div>

        <div class="flex space-x-7 justify-between my-2">
          <div class="w-1/2">
          <label>Dirección de entrega <span @click="form.shipping_address = company_branch_obj?.address" class="text-primary text-xs cursor-pointer ml-4">Dirección de sucursal</span></label> <br>
            <input v-model="form.shipping_address" class="input text-gray-600" type="text">
            <InputError :message="form.errors.shipping_address" />
          </div>
          <div class="w-1/2">
          <label>OV *</label> <br>
            <el-select v-model="form.sale_id" clearable filterable placeholder="Seleccione"
            no-data-text="No hay órdenes registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="ov in company_branch_obj?.sales" :key="ov?.id" :label="'OV-0' + ov?.id" :value="ov?.id" />
            </el-select>
            <InputError :message="form.errors.sale_id" />
          </div>
        </div>
      </section>

        <h2 class="font-bold">Presupuesto</h2>

        <div class="flex space-x-7 justify-between">
          <div class="w-1/2">
          <label>Moneda</label> <br>
            <el-select v-model="form.currency" clearable filterable placeholder="Seleccione"
            no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="currency in currencies" :key="currency" :label="currency" :value="currency" />
            </el-select>
            <InputError :message="form.errors.currency" />
          </div>
          <div class="w-1/2">
          <label>Monto</label> <br>
            <input v-model="form.budget" class="input" type="number" min="0">
            <InputError :message="form.errors.budget" />
          </div>
        </div>

        <div class="w-1/2">
        <label>Método de facturación</label>
          <el-select v-model="form.sat_method" clearable filterable placeholder="seleccione"
          no-data-text="No hay metodos registrados" no-match-text="No se encontraron coincidencias">
          <el-option v-for="sat_method in sat_methods" :key="sat_method" :label="sat_method" :value="sat_method" />
        </el-select>
          <InputError :message="form.errors.sat_method" />
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
<!-- {{form}} -->

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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  data() {
    const form = useForm({
      project_name: null,
      start_date: null,
      limit_date: null,
      is_strict_proyect: false,
      description: null,
      group: null,
      is_internal_project: false,
      company_id: null,
      company_branch: null,
      shipping_address: null,
      sale_id: null,
      currency: null,
      budget: null,
      sat_method: null,
      type_access_project: 'Publico',

    });

    return {
      form,
      editAccesFlag: false,
      company_branch_obj: null,
      owner: this.$page.props.auth.user.name,
      groups: [
        'Compras',
        'Ventas',
        'Producción',
        'Diseño',
        'Marketing',
        'Finanzas',
      ],
      currencies: [
          'MXN - Peso Mexicano',
          'USD - Dolar',
      ],
      sat_methods: [
          'Facturación al contado',
          'Facturación a crédito',
          'Facturación por adelantado',
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
    ThirthButton
  },
  props: {
    companies: Array,
    users: Array,
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
