<template>
  <div>
    <AppLayout title="Clientes - Crear">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('companies.index')"
            class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Agregar nuevo cliente</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <!-- ---------------- Company starts ----------------- -->
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
          <div class="md:grid gap-6 mb-6 grid-cols-2 pb-4">
            <div>
              <IconInput
                v-model="form.business_name"
                inputPlaceholder="Nombre *"
                inputType="text"
              >
                A
              </IconInput>
              <InputError :message="form.errors.business_name" />
            </div>
            <div>
              <IconInput
                v-model="form.phone"
                inputPlaceholder="Teléfono *"
                inputType="text"
              >
                <i class="fa-solid fa-phone"></i>
              </IconInput>
              <InputError :message="form.errors.phone" />
            </div>
            <div>
              <IconInput v-model="form.rfc" inputPlaceholder="RFC *" inputType="text">
                <i class="fa-solid fa-sheet-plastic"></i>
              </IconInput>
              <InputError :message="form.errors.rfc" />
            </div>
            <div>
              <IconInput
                v-model="form.post_code"
                inputPlaceholder="C.P. *"
                inputType="text"
              >
                <i class="fa-solid fa-envelopes-bulk"></i>
              </IconInput>
              <InputError :message="form.errors.post_code" />
            </div>
            <div>
              <IconInput
                v-model="form.fiscal_address"
                inputPlaceholder="Domicilio fiscal *"
              >
                <i class="fa-solid fa-building"></i>
              </IconInput>
              <InputError :message="form.errors.fiscal_address" />
            </div>
          </div>
          <!-- ---------------- Company ends ----------------- -->

          <!-- ---------------- Company Branch starts ----------------- -->
          <el-divider content-position="left">Sucursales</el-divider>
          <div class="space-y-3 md:w-[92%] mx-auto bg-[#b8b7b7] rounded-lg p-5">
            <div>
              <IconInput
                v-model="form.name"
                inputPlaceholder="Nombre de sucursal *"
                inputType="text"
              >
                A
              </IconInput>
              <InputError :message="form.errors.name" />
            </div>
            <div class="md:col-span-2">
              <IconInput
                v-model="form.address"
                inputPlaceholder="Dirección *"
                inputType="text"
              >
                <i class="fa-solid fa-map-location-dot"></i>
              </IconInput>
              <InputError :message="form.errors.address" />
            </div>
            <div>
              <IconInput
                v-model="form.post_code_branch"
                inputPlaceholder="C.P. *"
                inputType="text"
              >
                <i class="fa-solid fa-envelopes-bulk"></i>
              </IconInput>
              <InputError :message="form.errors.post_code_branch" />
            </div>
            <div>
              <el-select
                v-model="form.sat_method"
                class="my-2"
                placeholder="Método de pago"
              >
                <el-option
                  v-for="item in sat_method"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                />
              </el-select>
              <InputError :message="form.errors.sat_method" />
            </div>
            <div>
              <el-select v-model="form.sat_way" class="my-2" placeholder="Medio de pago">
                <el-option
                  v-for="item in sat_ways"
                  :key="item.value"
                  :label="item.label"
                  :value="item.label"
                />
              </el-select>
              <InputError :message="form.errors.sat_way" />
            </div>
            <div>
              <el-select
                v-model="form.sat_type"
                class="mt-2"
                placeholder="Uso de factura"
              >
                <el-option
                  v-for="item in sat_types"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                />
              </el-select>
              <InputError :message="form.errors.sat_types" />
            </div>
            <div class="flex flex-col text-red-400 text-sm font-bold space-y-1">
              <div
                class="flex justify-center items-center space-x-2"
                v-for="branch in company_branches"
                :key="branch"
              >
                <i class="fa-solid fa-building mr-1"></i> {{ branch.name }}
                <i class="fa-solid fa-map-location-dot mr-1"></i> {{ branch.address }}
              </div>
            </div>
            <div class="pb-7">
              <SecondaryButton :disabled="!company_contacts.length" @click="addCompanyBranch">
                Agregar Sucursal
              </SecondaryButton>
            </div>
            <!-- ---------------- Company Branch ends ----------------- -->

            <!-- ---------------- Company Contacts starts ----------------- -->
            <el-divider content-position="left">Contactos</el-divider>
            <div class="md:w-[92%] mx-auto pt-3 space-y-3 bg-[#acabab] rounded-lg p-5">
              <div>
                <IconInput
                  v-model="form.name_contact"
                  inputPlaceholder="Nombre de contacto *"
                  inputType="text"
                >
                  A
                </IconInput>
                <InputError :message="form.errors.name_contact" />
              </div>
              <div class="md:grid gap-6 mb-6 grid-cols-2">
                <div>
                  <IconInput
                    v-model="form.email"
                    inputPlaceholder="Correo electrónico *"
                    inputType="email"
                  >
                    <i class="fa-solid fa-envelope"></i>
                  </IconInput>
                  <InputError :message="form.errors.email" />
                </div>
                <div>
                  <IconInput
                    v-model="form.phone_contact"
                    inputPlaceholder="Teléfono *"
                    inputType="text"
                  >
                    <i class="fa-solid fa-phone"></i>
                  </IconInput>
                  <InputError :message="form.errors.phone_contact" />
                </div>
              </div>
              <div>
                <IconInput
                  v-model="form.birthdate"
                  inputPlaceholder="Cumpleaños"
                  inputType="date"
                >
                  <i class="fa-solid fa-cake"></i>
                </IconInput>
                <InputError :message="form.errors.birthdate" />
              </div>
            </div>
            <div class="flex flex-col text-blue-400 text-sm font-bold space-y-1">
              <div
                class="flex justify-center items-center space-x-2"
                v-for="contact in company_contacts"
                :key="contact"
              >
                <i class="fa-solid fa-user mr-1"></i> {{ contact.name }}
                <i class="fa-solid fa-envelope mr-1"></i> {{ contact.email }}
                <i class="fa-solid fa-phone mr-1"></i> {{ contact.phone }}
              </div>
            </div>
            <SecondaryButton
              @click="addCompanyContact"
              :disabled="
                !this.form.name_contact || !this.form.email || !this.form.phone_contact
              "
            >
              Agregar Contacto
            </SecondaryButton>
          </div>
          <!-- ---------------- Company Contacts ends ----------------- -->


          <!-- ---------------- Company Products starts ----------------- -->
          <el-divider content-position="left">Productos del cliente</el-divider>
          <div class="space-y-3 bg-[#b8b7b7] rounded-lg p-5">
            <div style="margin-top: 20px">
                <el-radio-group v-model=form.radio size="small">
                <el-radio-button label="Producto terminado" />
                <el-radio-button label="Materia prima" />
                </el-radio-group>
            </div>
            <div>
                <el-select
                v-model="form.catalog_product_id"
                class="mt-2"
                placeholder="Buscar producto"
              >
                <el-option
                  v-for="item in form.radio == 'Producto terminado' ? catalog_products : raw_materials"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </div>
            <div class="md:grid gap-6 mb-6 grid-cols-3">
                <div>
                  <IconInput
                    v-model="form.old_price"
                    inputPlaceholder="Precio anterior *"
                    inputType="number"
                  >
                    <i class="fa-solid fa-money-bill"></i>
                  </IconInput>
                  <InputError :message="form.errors.old_price" />
                </div>
                <div>
                  <el-select
                    v-model="form.old_currency"
                    class="mt-2"
                    placeholder="Moneda"
                >
                    <el-option
                    v-for="item in currencies"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                    />
              </el-select>
                </div>
                <div>
                  <IconInput
                    v-model="form.old_date"
                    inputPlaceholder="Precio anterior *"
                    inputType="date"
                  >
                    <i class="fa-solid fa-calendar"></i>
                  </IconInput>
                  <InputError :message="form.errors.old_price" />
                </div>
                <div>
                  <IconInput
                    v-model="form.new_price"
                    inputPlaceholder="Precio nuevo *"
                    inputType="number"
                  >
                    <i class="fa-solid fa-money-bill"></i>
                  </IconInput>
                  <InputError :message="form.errors.new_price" />
                </div>
                <div>
                  <el-select
                    v-model="form.new_currency"
                    class="mt-2"
                    placeholder="Moneda"
                >
                    <el-option
                    v-for="item in currencies"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                    />
              </el-select>
                </div>
                <div>
                  <IconInput
                    v-model="form.new_date"
                    inputType="date"
                  >
                    <i class="fa-solid fa-calendar"></i>
                  </IconInput>
                  <InputError :message="form.errors.old_price" />
                </div>
            </div>
                <SecondaryButton
              @click="addCompanyProduct"
              :disabled="!company_branches.length"
            >
              Agregar Producto
            </SecondaryButton>
          </div>
          <!-- ---------------- Company Products ends ----------------- -->
          <div class="mt-4 mx-3 md:text-right">
            <el-divider />
            <PrimaryButton :disabled="form.processing"> Agregar Cliente </PrimaryButton>
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

export default {
  data() {
    const form = useForm({
      business_name: null,
      phone: null,
      rfc: null,
      post_code: null,
      fiscal_address: null,
      // --- company_branch ---
      name: null,
      address: null,
      post_code_branch: null,
      sat_method: null,
      sat_type: null,
      sat_way: null,
      // --- company_contact ---
      name_contact: null,
      email: null,
      phone_contact: null,
      birthdate: null,
      // --- company_product ---
      radio:'Producto terminado',
      old_date: null,
      new_date: null,
      old_currency: null,
      new_currency: null,
      old_price: null,
      new_price: null,
      catalog_product_id: null,
      company_id: null,
    });

    return {
      form,
      company_contacts: [],
      company_branches: [],
      company_products: [],

      currencies: [
        {value: "MXN", label:"MXN"},
        {value: "USD", label:"USD"},
      ],

      sat_method: [
        { value: "PUE", label: "PUE: Pago en una sola exhibición" },
        { value: "PPD", label: "PPD: Pago en parcialidades o diferido" },
      ],
      sat_types: [
        { value: "G01", label: "Adquisición de mercancías" },
        { value: "G02", label: "Devoluciones, descuentos o bonificaciones" },
        { value: "G03", label: "Gastos en general" },
        { value: "I01", label: "Construcciones" },
        { value: "I02", label: "Mobiliario y equipo de oficina por inversiones" },
        { value: "I03", label: "Equipo de transporte" },
        { value: "I04", label: "Equipo de cómputo y accesorios" },
        { value: "I05", label: "Dados, troqueles, moldes, matrices y herramental" },
        { value: "I06", label: "Comunicaciones telefónicas" },
        { value: "I07", label: "Comunicaciones satelitales" },
        { value: "I08", label: "Otra maquinaria y equipo" },
        { value: "D01", label: "Honorarios médicos, dentales y gastos hospitalarios" },
        { value: "D02", label: "Gastos médicos por incapacidad o discapacidad" },
        { value: "D03", label: "Gastos funerales" },
        { value: "D04", label: "Donativos" },
        {
          value: "D05",
          label:
            "Initereses reales efectivamente pagados por créditos hipotecarios (casa habitación)",
        },
        { value: "D06", label: "Aportaciones voluntarias al SAR" },
        { value: "D07", label: "Primas por seguro de gastos médicos" },
        { value: "D08", label: "Gastos de transportación escolar obligatoria" },
        {
          value: "D09",
          label:
            "Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones",
        },
        { value: "D10", label: "Pagos por servicio educativo (colegiaturas)" },
        { value: "P01", label: "Por definir" },
      ],
      sat_ways: [
        { value: "1", label: "Efectivo" },
        { value: "2", label: "cheque nominativo" },
        { value: "3", label: "Transferencia electrónica de fondos" },
        { value: "4", label: "Tarheta de crédito" },
        { value: "5", label: "Monedero electrónico" },
        { value: "6", label: "Dinero electrónico" },
        { value: "7", label: "Vales de despensa" },
        { value: "8", label: "Dación en pago" },
        { value: "9", label: "Pago por subrogación" },
        { value: "10", label: "Pago por consignación" },
        { value: "12", label: "Condonación" },
        { value: "13", label: "Compensación" },
        { value: "14", label: "Novación" },
        { value: "15", label: "Confusión" },
        { value: "16", label: "Remisión de deuda" },
        { value: "17", label: "Prescripción o caducidad" },
        { value: "18", label: "A satisfaccipon del acreedor" },
        { value: "19", label: "Tarjeta de débito" },
        { value: "20", label: "Tarjeta de servicios" },
        { value: "21", label: "Aplicación de anticipos" },
        { value: "22", label: "Por definir" },
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
  },
  props: {
    catalog_products: Array,
    raw_materials: Array
  },
  methods: {
    store() {
      this.form.post(route("companies.store"));
    },
    addCompanyBranch() {
      this.company_branches.push({
        name: this.form.name,
        address: this.form.address,
        post_code: this.form.post_code_branch,
        sat_method: this.form.sat_method,
        sat_type: this.form.sat_type,
        sat_way: this.form.sat_way,
      });
      this.form.name = null;
      this.form.address = null;
      this.form.post_code_branch = null;
      this.form.sat_method = null;
      this.form.sat_type = null;
      this.form.sat_way = null;
    },
    addCompanyContact() {
      this.company_contacts.push({
        name: this.form.name_contact,
        email: this.form.email,
        phone: this.form.phone_contact,
        birthdate: this.form.birthdate,
      });
      this.form.name_contact = null;
      this.form.email = null;
      this.form.phone_contact = null;
      this.form.birthdate = null;
    },
  },
};
</script>
