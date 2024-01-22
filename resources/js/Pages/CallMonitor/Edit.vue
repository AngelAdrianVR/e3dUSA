<template>
    <AppLayout title="Editar interacción por llamada">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar interacción por llamada</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto text-sm my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
        <h2 class="text-secondary">Datos del cliente</h2>
        
            <div class="flex items-center space-x-2">
                <div class="w-1/2">
                    <label>Folio de la oportunidad *</label>
                    <el-select @change="getCompany" class="w-full" v-model="form.oportunity_id" clearable filterable placeholder="Seleccione"
                        no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="oportunity in oportunities" :key="oportunity" :label="oportunity.folio + ' - ' + oportunity.name" :value="oportunity.id" />
                    </el-select>
                    <InputError :message="form.errors.oportunity_id" />
                </div>
                <div class="w-1/2">
                    <label>Vendedor*</label>
                    <el-select class="w-full" v-model="form.seller_id" clearable filterable placeholder="Seleccione"
                        no-data-text="No hay registros" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="seller in users.filter(item => item.employee_properties?.department == 'Ventas')" :key="seller" :label="seller.name" :value="seller.id" />
                    </el-select>
                    <InputError :message="form.errors.seller_id" />
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-1/2">
                    <label>Cliente *</label>
                    <input v-model="form.company_name" disabled class="input" type="text">
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
            <div class="flex items-center space-x-2">
                <div v-if="!has_contact" class="w-1/2">
                  <label>Contacto</label>
                  <el-select @change="getContactPhone" v-model="form.contact_id" clearable filterable placeholder="Seleccione"
                    no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="contact in company_branch_obj?.contacts" :key="contact" :label="contact.name"
                      :value="contact.id" />
                  </el-select>
                  <InputError :message="form.errors.contact_id" />
                </div>
                <div class="w-1/2">
                    <label>Teléfono</label>
                    <input v-model="form.contact_phone" class="input" type="text">
                    <InputError :message="form.errors.contact_phone" />
                </div>
              </div>

            <h2 class="text-secondary pt-4">Información de la interacción</h2>

                <div class="lg:w-1/2 lg:mt-0">
                <label class="block">Fecha *</label>
                <el-date-picker v-model="form.date" type="date" placeholder="Fecha de pago *" format="YYYY/MM/DD"
                value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
                <InputError :message="form.errors.date" />
                </div>
            <div>
                <label>Resumen*</label>
                <textarea v-model="form.notes" class="textarea" rows="2">
                </textarea>
                <InputError :message="form.errors.notes" />
            </div>
          <div class="flex justify-end items-center">
            <PrimaryButton :disabled="form.processing">
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
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
    oportunity_id: this.call_monitor.data.oportunity?.id,
    company_id: this.call_monitor.data.company?.id,
    contact_id: this.call_monitor.data.contact.id,
    company_branch_id: this.call_monitor.data.companyBranch?.id,
    company_name: this.call_monitor.data.company_name,
    seller_id: this.call_monitor.data.seller?.id,
    contact_phone: this.call_monitor.data.contact_phone,
    date: this.call_monitor.data.date_raw,
    notes: this.call_monitor.data.notes,
        });

    return {
      form,
      company_branch_obj: null,
      has_contact: false,
    };
  },
  components: {
    PrimaryButton,
    InputError,
    AppLayout,
    Back,
    Link
  },
  props: {
    oportunities: Object,
    call_monitor: Object,
    companies: Array,
    users: Array,
  },
  methods: {
    update() {
        this.form.put(route("call-monitors.update", this.call_monitor.data.id), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se editó el registro de llamada",
              type: "success",
            });
          },
        });
    },
    disabledDate(time) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return time.getTime() > today.getTime();
    },
    saveCompanyBranchAddress() {
      this.company_branch_obj = this.companies.find((item) => item.id == this.form.company_id)?.company_branches[0];
    },
    getContactPhone() {
      this.form.contact_phone = this.company_branch_obj?.contacts?.find(contact => contact.id == this.form.contact_id)?.phone;
    },
    getCompany() {
        const oportunity = this.oportunities.find(oportunity => oportunity.id === this.form.oportunity_id);
        // console.log(oportunity);
        
        this.form.company_id = null;
        this.form.company_branch_id = null;
        this.form.contact_id = null;
        this.form.company_name = null;
        this.form.contact_phone = null;

        if (oportunity.company_name) { 
        this.form.company_name = oportunity.company_name; 
        this.form.contact_phone = oportunity.contact_phone; 
        this.form.company_id = null;
        } else {
        this.form.company_id = oportunity.company.id;
        this.form.company_name = oportunity.company.business_name;
        }
    },
  },
  mounted(){
     this.company_branch_obj = this.companies.find((item) => item.id == this.call_monitor.data.company.id)?.company_branches[0];
     const oportunity = this.oportunities.find(oportunity => oportunity.id === this.form.oportunity_id);

      if (oportunity.company_name) { 
      this.form.company_name = oportunity.company_name; 
      this.form.contact_phone = oportunity.contact_phone; 
      this.form.company_id = null;
      } else {
      this.form.company_id = oportunity.company.id;
      this.form.company_name = oportunity.company.business_name;
      }
  }
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
