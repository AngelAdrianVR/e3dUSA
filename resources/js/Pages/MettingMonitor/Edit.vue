<template>
  <AppLayout title="Editar cita">
    <template #header>
      <div class="flex justify-between">
        <Back />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Editar cita</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="update">
      <div class="md:w-1/2 md:mx-auto grid grid-cols-2 gap-3 text-sm my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md">
        <h2 class="text-[#373737] font-bold col-span-full">Datos del cliente</h2>

        <!-- <label class="inline-flex items-center">
              <Checkbox @change="clearOportunityForm" v-model:checked="form.is_oportunity" class="bg-transparent disabled:border-gray-400"/>
              <span class="ml-2 text-xs">Agendar cita a oportunidad</span>
            </label> -->
        <div v-if="form.is_oportunity">
          <InputLabel value="Folio de la oportunidad*" />
          <el-select @change="getCompany" class="w-full" v-model="form.oportunity_id" clearable filterable
            placeholder="Selecciona" no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="oportunity in oportunities" :key="oportunity"
              :label="oportunity.folio + ' - ' + oportunity.name" :value="oportunity.id" />
          </el-select>
          <InputError :message="form.errors.oportunity_id" />
        </div>
        <div>
          <InputLabel value="Cliente*" />
          <el-select :disabled="form.is_oportunity" v-model="form.company_id" clearable filterable
            placeholder="Selección automática" no-data-text="Primero selecciona la oportunidad"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="company in companies" :key="company" :label="company.business_name" :value="company.id" />
          </el-select>
          <InputError :message="form.errors.company_id" />
        </div>
        <div>
          <InputLabel value="Sucursal*" />
          <el-select @change="saveCompanyBranchAddress" v-model="form.company_branch_id" clearable filterable
            placeholder="Selecciona" no-data-text="Primero selecciona la oportunidad"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="company_branch in companies.find((item) => item.id == form.company_id)?.company_branches"
              :key="company_branch" :label="company_branch.name" :value="company_branch.id" />
          </el-select>
          <InputError :message="form.errors.company_branch" />
        </div>
        <div v-if="!has_contact">
          <InputLabel value="Contacto*" />
          <el-select @change="getContactPhone" v-model="form.contact_id" clearable filterable placeholder="Selecciona"
            no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
            <el-option v-for="contact in company_branch_obj?.contacts" :key="contact" :label="contact.name"
              :value="contact.id" />
          </el-select>
          <InputError :message="form.errors.contact_id" />
        </div>
        <div v-if="has_contact">
          <InputLabel value="Contacto*" />
          <el-input v-model="form.contact_name" type="text" disabled placeholder="Inrgesa el nombre" />
          <InputError :message="form.errors.contact_name" />
        </div>
        <div>
          <InputLabel value="Télefono*" />
          <el-input v-model="form.phone" type="text"
            :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
            :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable placeholder="Ej. 3312479856" />
          <InputError :message="form.errors.phone" />
        </div>
        <h2 class="text-[#373737] font-bold col-span-full">Detalles de la cita</h2>
        <div>
          <InputLabel value="Fecha*" />
          <el-date-picker v-model="form.meeting_date" type="date" placeholder="Fecha*" format="YYYY/MM/DD"
            class="!w-full" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
          <InputError :message="form.errors.meeting_date" />
        </div>
        <div>
          <InputLabel value="Hora*" />
          <el-time-select v-model="form.time" start="07:00" step="00:15" end="23:30"
            placeholder="Selecciona una hora" />
          <InputError :message="form.errors.time" />
        </div>
        <div>
          <InputLabel value="Vía de cita*" />
          <el-select class="w-full" v-model="form.meeting_via" clearable filterable placeholder="Selecciona"
            no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
            <el-option v-for="meeting_via in meetingVias" :key="meeting_via" :label="meeting_via"
              :value="meeting_via" />
          </el-select>
          <InputError :message="form.errors.meeting_via" />
        </div>
        <div class="relative">
          <InputLabel value="Ubicación*" />
          <el-input v-model="form.location" type="text" clearable placeholder="Ej. Mariscos Cocos locos" />
          <label @click="getCompanyBranchAddress"
            class="text-primary text-xs underline cursor-pointer absolute -bottom-5 left-3">Agregar ubicación de la
            sucursal</label>
          <InputError :message="form.errors.location" />
        </div>
        <div>
          <InputLabel value="Participante(s)*" />
          <el-select v-model="form.participants" clearable filterable multiple
            placeholder="Seleccionar participantes" no-data-text="No hay usuarios registrados"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id" />
          </el-select>
          <InputError :message="form.errors.participants" />
        </div>
        <div class="col-span-full">
          <InputLabel value="Descripción*" />
          <el-input v-model="form.description" :rows="3" maxlength="800"
            placeholder="Coloca tema a tratar o motivo de la reunión" show-word-limit type="textarea" />
          <InputError :message="form.errors.description" />
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
import RichText from "@/Components/MyComponents/RichText.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      is_oportunity: null,
      oportunity_id: this.metting_monitor.data.oportunity?.id,
      time: this.metting_monitor.data.time,
      company_id: this.metting_monitor.data.company?.id,
      meeting_date: this.metting_monitor.data.meeting_date_raw,
      company_branch_id: this.metting_monitor.data.companyBranch?.id,
      contact_id: this.metting_monitor.data.contact?.id,
      contact_name: this.metting_monitor.data.contact_name,
      phone: this.metting_monitor.data.phone,
      meeting_via: this.metting_monitor.data.meeting_via,
      location: this.metting_monitor.data.location,
      description: this.metting_monitor.data.description,
      participants: this.metting_monitor.data.participants,
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
    Back,
    Link,
    InputLabel,
  },
  props: {
    oportunities: Object,
    metting_monitor: Object,
    companies: Array,
    users: Array,
  },
  methods: {
    update() {
      this.form.put(route("meeting-monitors.update", this.metting_monitor.data.id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha editado la cita",
            type: "success",
          });
        },
      });
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
    getCompany() {
      const oportunity = this.oportunities.find(oportunity => oportunity.id === this.form.oportunity_id);
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
  mounted() {
    this.company_branch_obj = this.metting_monitor.data.companyBranch;
    if (this.metting_monitor.data.oportunity?.id) {
      this.form.is_oportunity = true;
    } else {
      this.form.is_oportunity = false;
    }

    // mostrar contacto
    this.saveCompanyBranchAddress();
  }
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
