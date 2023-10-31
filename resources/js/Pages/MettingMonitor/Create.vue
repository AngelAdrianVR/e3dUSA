<template>
  <AppLayout title="Agendar cita">
    <template #header>
      <div class="flex justify-between">
        <Link :href="route('client-monitors.index')"
          class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
        <i class="fa-solid fa-chevron-left"></i>
        </Link>
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Agendar una cita</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="store">
      <div class="md:w-1/2 md:mx-auto text-sm my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
        <h2 class="text-secondary">Datos del cliente</h2>

        <!-- <label class="inline-flex items-center">
              <Checkbox @change="clearOportunityForm" v-model:checked="form.is_oportunity" class="bg-transparent disabled:border-gray-400"/>
              <span class="ml-2 text-xs">Agendar cita a oportunidad</span>
            </label> -->
        <div v-if="form.is_oportunity" class="w-1/2">
          <label>Folio de la oportunidad *</label>
          <el-select @change="getCompany" class="w-full" v-model="form.oportunity_id" clearable filterable
            placeholder="Seleccione" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="oportunity in oportunities.data" :key="oportunity" :label="oportunity.folio"
              :value="oportunity.id" />
          </el-select>
          <InputError :message="form.errors.oportunity_id" />
        </div>
        <div class="flex space-x-7 justify-between">
          <div class="w-1/2">
            <label>Cliente *</label> <br>
            <el-select :disabled="form.is_oportunity" v-model="form.company_id" clearable filterable
              placeholder="Seleccione" no-data-text="No hay clientes registrados"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="company in companies" :key="company" :label="company.business_name" :value="company.id" />
            </el-select>
            <InputError :message="form.errors.company_id" />
          </div>
          <div class="w-1/2">
            <label>Sucursal</label> <br>
            <el-select @change="saveCompanyBranchAddress" v-model="form.company_branch_id" clearable filterable
              placeholder="Seleccione" no-data-text="No hay sucursales registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="company_branch in companies.find((item) => item.id == form.company_id)?.company_branches"
                :key="company_branch" :label="company_branch.name" :value="company_branch.id" />
            </el-select>
            <InputError :message="form.errors.company_branch" />
          </div>
        </div>
        <div class="flex justify-between space-x-7">
          <div v-if="!has_contact" class="w-1/2">
            <label>Contacto</label>
            <el-select @change="getContactPhone" v-model="form.contact_id" clearable filterable placeholder="Seleccione"
              no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="contact in company_branch_obj?.contacts" :key="contact" :label="contact.name"
                :value="contact.id" />
            </el-select>
            <InputError :message="form.errors.contact_id" />
          </div>
          <div v-if="has_contact" class="w-1/2">
            <label>Contacto</label>
            <input v-model="form.contact_name" class="input" type="text">
            <InputError :message="form.errors.contact_name" />
          </div>
          <div class="w-1/2">
            <label>Telefono</label>
            <input v-model="form.phone" class="input" type="text">
            <InputError :message="form.errors.phone" />
          </div>
        </div>

        <h2 class="text-secondary pt-4">Detalles de la cita</h2>

        <div class="lg:flex items-center pt-3">
          <div class="lg:w-1/2 lg:mt-0">
            <label class="block">Fecha*</label>
            <el-date-picker v-model="form.meeting_date" type="date" placeholder="Fecha*" format="YYYY/MM/DD"
              value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
            <InputError :message="form.errors.meeting_date" />
          </div>
          <div class="w-1/2">
            <label>Hora *</label>
            <el-time-select v-model="form.time" start="07:00" step="00:15" end="23:30"
              placeholder="Seleccione una hora" />
            <InputError :message="form.errors.time" />
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-1/2">
            <label>Vía de cita *</label>
            <el-select class="w-full" v-model="form.meeting_via" clearable filterable placeholder="Seleccione"
              no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
              <el-option v-for="meeting_via in meetingVias" :key="meeting_via" :label="meeting_via"
                :value="meeting_via" />
            </el-select>
            <InputError :message="form.errors.meeting_via" />
          </div>
          <div class="w-1/2 relative">
            <label>Ubicacion</label>
            <input v-model="form.location" class="input" type="text">
            <label @click="getCompanyBranchAddress"
              class="text-primary text-xs underline cursor-pointer absolute -bottom-5 left-3">Agregar ubicación de la
              sucursal</label>
            <InputError :message="form.errors.location" />
          </div>
        </div>
        <div>
          <label class="block" for="">Participante(s) *</label>
          <el-select class="w-full mt-2" v-model="form.participants" clearable filterable multiple
            placeholder="Seleccionar participantes" no-data-text="No hay usuarios registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id" />
          </el-select>
          <InputError :message="form.errors.participants" />
        </div>
        <div class="mt-5 col-span-full">
          <label>Descripción</label>
          <RichText @content="updateDescription($event)" />
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
import RichText from "@/Components/MyComponents/RichText.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  data() {
    const form = useForm({
      is_oportunity: true,
      oportunity_id: null,
      time: null,
      company_id: null,
      meeting_date: null,
      company_branch_id: null,
      contact_id: null,
      contact_name: null,
      phone: null,
      meeting_via: null,
      location: null,
      description: null,
      participants: null,
    });

    return {
      form,
      has_contact: false,
      company_branch_obj: null,
      meetingVias: [
        'Presencial',
        'Videoconferencia',
        'Llamada',
        'Otro',
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    InputError,
    Checkbox,
    RichText,
    Link,
  },
  props: {
    oportunities: Object,
    companies: Array,
    users: Array,
  },
  methods: {
    store() {
      this.form.post(route("meeting-monitors.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha agendado una nueva cita",
            type: "success",
          });
        },
      });
    },
      getCompany() {
        const oportunity = this.oportunities.data.find(oportunity => oportunity.id === this.form.oportunity_id);
        this.form.company_branch_id = null;
        this.form.contact_id = null;
        this.form.contact_name = null;
        this.form.phone = null;
        this.company_branch_obj = null;
  
        if (oportunity.company) {
          this.form.company_id = oportunity.company.id;
          this.has_contact = false;
        } else {
          this.has_contact = true;
          this.form.company_id = null;
          this.form.contact_name = oportunity.contact;
        }
      },
    saveCompanyBranchAddress() {
      this.company_branch_obj = this.companies.find((item) => item.id == this.form.company_id)?.company_branches[0];
    },
    getContactPhone() {
      this.form.phone = this.company_branch_obj?.contacts?.find(contact => contact.id == this.form.contact_id)?.phone;
    },
    getCompanyBranchAddress() {
      this.form.location = this.company_branch_obj?.address;
    },
    clearOportunityForm() {
      if (!this.form.is_oportunity) {
        this.form.oportunity_id = null;
        this.form.company_id = null;
        this.form.company_branch_id = null;
        this.form.contact_id = null;
        this.form.phone = null;
      } else {
        return
      }

    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
      return time < today;
    },
    updateDescription(content) {
      this.form.description = content;
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
