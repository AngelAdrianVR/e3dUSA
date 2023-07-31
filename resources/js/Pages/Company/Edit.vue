<template>
  <div>
    <AppLayout title="Editar cliente">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('companies.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar cliente "{{ company.business_name }}"
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <!-- ---------------- Company starts ----------------- -->
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md"
        >
          <div class="md:grid gap-6 mb-6 grid-cols-2 pb-4">
            <div>
              <IconInput
                v-model="form.business_name"
                inputPlaceholder="Razon social *"
                inputType="text"
              >
                <el-tooltip content="Razon social" placement="top">
                  A
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.business_name" />
            </div>
            <div>
              <IconInput
                v-model="form.phone"
                inputPlaceholder="Teléfono *"
                inputType="text"
              >
                <el-tooltip content="Teléfono" placement="top">
                  <i class="fa-solid fa-phone"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.phone" />
            </div>
            <div>
              <IconInput
                v-model="form.rfc"
                inputPlaceholder="RFC *"
                inputType="text"
              >
                <el-tooltip content="RFC" placement="top">
                  <i class="fa-solid fa-sheet-plastic"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.rfc" />
            </div>
            <div>
              <IconInput
                v-model="form.post_code"
                inputPlaceholder="C.P. *"
                inputType="text"
              >
                <el-tooltip content="C.P." placement="top">
                  <i class="fa-solid fa-envelopes-bulk"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.post_code" />
            </div>
            <div class="col-span-full">
              <IconInput
                v-model="form.fiscal_address"
                inputPlaceholder="Domicilio fiscal *"
              >
                <el-tooltip content="Domicilio fiscal" placement="top">
                  <i class="fa-solid fa-building"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.fiscal_address" />
            </div>
          </div>
          <!-- ---------------- Company ends ----------------- -->

          <!-- ---------------- Company Branch starts ----------------- -->
          <el-divider content-position="left">Sucursales</el-divider>
          <ol
            v-if="form.company_branches.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 mb-2"
          >
            <template
              v-for="(item, index) in form.company_branches"
              :key="index"
            >
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{ item.name }}
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editBranchIndex == index">En edición</el-tag>
                  <el-button @click="editBranch(index)" type="primary" circle>
                    <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                  </el-button>
                  <el-popconfirm
                    confirm-button-text="Si"
                    cancel-button-text="No"
                    icon-color="#FF0000"
                    title="¿Continuar?"
                    @confirm="deleteBranch(index)"
                  >
                    <template #reference>
                      <el-button type="danger" circle
                        ><i class="fa-sharp fa-solid fa-trash"></i
                      ></el-button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>
          <div
            class="space-y-3 md:w-[92%] mx-auto border-2 border-[#b8b7b7] rounded-lg p-5"
          >
            <div>
              <IconInput
                v-model="branch.name"
                inputPlaceholder="Nombre de sucursal *"
                inputType="text"
              >
                <el-tooltip content="Nombre de sucursal" placement="top">
                  A
                </el-tooltip>
              </IconInput>
              <!-- <InputError :message="branch.errors.name" /> -->
            </div>
            <div class="md:col-span-2">
              <IconInput
                v-model="branch.address"
                inputPlaceholder="Dirección *"
                inputType="text"
              >
                <el-tooltip content="Dirección" placement="top">
                  <i class="fa-solid fa-map-location-dot"></i>
                </el-tooltip>
              </IconInput>
              <!-- <InputError :message="branch.errors.address" /> -->
            </div>
            <div>
              <IconInput
                v-model="branch.post_code"
                inputPlaceholder="C.P. *"
                inputType="text"
              >
                <el-tooltip content="C.P." placement="top">
                  <i class="fa-solid fa-envelopes-bulk"></i>
                </el-tooltip>
              </IconInput>
              <!-- <InputError :message="branch.errors.post_code" /> -->
            </div>
            <div class="flex items-center">
              <el-tooltip content="Método de pago" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                >
                  sat
                </span>
              </el-tooltip>
              <el-select
                v-model="branch.sat_method"
                clearable
                placeholder="Método de pago"
              >
                <el-option
                  v-for="item in sat_method"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                />
              </el-select>
              <!-- <InputError :message="branch.errors.sat_method" /> -->
            </div>
            <div class="flex items-center">
              <el-tooltip content="Medio de pago" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                >
                  sat
                </span>
              </el-tooltip>
              <el-select
                v-model="branch.sat_way"
                clearable
                placeholder="Medio de pago"
              >
                <el-option
                  v-for="item in sat_ways"
                  :key="item.value"
                  :label="item.label"
                  :value="item.label"
                />
              </el-select>
              <!-- <InputError :message="branch.errors.sat_way" /> -->
            </div>
            <div class="flex items-center">
              <el-tooltip content="Uso de factura" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                >
                  sat
                </span>
              </el-tooltip>
              <el-select
                v-model="branch.sat_type"
                clearable
                placeholder="Uso de factura"
              >
                <el-option
                  v-for="item in sat_types"
                  :key="item.value"
                  :label="item.label"
                  :value="item.value"
                />
              </el-select>
              <!-- <InputError :message="form.errors.sat_types" /> -->
            </div>
            <div class="pb-7">
              <SecondaryButton
                @click="addBranch"
                :disabled="
                  contacts.length == 0 ||
                  !branch.name ||
                  !branch.address ||
                  !branch.post_code ||
                  !branch.sat_method ||
                  !branch.sat_type ||
                  !branch.sat_way
                "
              >
                {{
                  editBranchIndex !== null
                    ? "Actualizar sucursal"
                    : "Agregar sucursal a lista"
                }}
              </SecondaryButton>
            </div>
            <!-- ---------------- Company Branch ends ----------------- -->

            <!-- ---------------- Company Contacts starts ----------------- -->
            <el-divider content-position="left">Contactos</el-divider>
            <ol
              v-if="contacts.length"
              class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1"
            >
              <template v-for="(item, index) in contacts" :key="index">
                <li class="flex justify-between items-center">
                  <p class="text-sm">
                    <span class="text-primary">{{ index + 1 }}.</span>
                    {{ item.name }} | {{ item.email }}
                  </p>
                  <div class="flex space-x-2 items-center">
                    <el-tag v-if="editContactIndex == index">En edición</el-tag>
                    <el-button
                      @click="editContact(index)"
                      type="primary"
                      circle
                    >
                      <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                    </el-button>
                    <el-popconfirm
                      confirm-button-text="Si"
                      cancel-button-text="No"
                      icon-color="#FF0000"
                      title="¿Continuar?"
                      @confirm="deleteContact(index)"
                    >
                      <template #reference>
                        <el-button type="danger" circle
                          ><i class="fa-sharp fa-solid fa-trash"></i
                        ></el-button>
                      </template>
                    </el-popconfirm>
                  </div>
                </li>
              </template>
            </ol>
            <div class="md:w-[92%] mx-auto pt-3 m:space-y-3 rounded-lg p-5">
              <div>
                <IconInput
                  v-model="contact.name"
                  inputPlaceholder="Nombre de contacto *"
                  inputType="text"
                >
                  <el-tooltip content="Nombre de contacto" placement="top">
                    A
                  </el-tooltip>
                </IconInput>
                <!-- <InputError :message="form.errors.name_contact" /> -->
              </div>
              <div class="md:grid gap-6 m:mb-6 grid-cols-2">
                <div>
                  <IconInput
                    v-model="contact.email"
                    inputPlaceholder="Correo electrónico *"
                    inputType="email"
                  >
                    <el-tooltip content="Correo electrónico" placement="top">
                      <i class="fa-solid fa-envelope"></i>
                    </el-tooltip>
                  </IconInput>
                  <!-- <InputError :message="form.errors.email" /> -->
                </div>
                <div>
                  <IconInput
                    v-model="contact.phone"
                    inputPlaceholder="Teléfono *"
                    inputType="text"
                  >
                    <el-tooltip content="Teléfono" placement="top">
                      <i class="fa-solid fa-phone"></i>
                    </el-tooltip>
                  </IconInput>
                  <!-- <InputError :message="form.errors.phone" /> -->
                </div>
              </div>
              <div>
                <div class="flex items-center">
                  <el-tooltip content="Cumpleaños" placement="top">
                    <span
                      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                    >
                      <i class="fa-solid fa-cake"></i>
                    </span>
                  </el-tooltip>
                  <div class="grid grid-cols-2 gap-2">
                    <el-select
                      v-model="contact.birthdate_day"
                      clearable
                      placeholder="Dia"
                    >
                      <el-option
                        v-for="day in 31"
                        :key="day"
                        :label="day"
                        :value="day"
                      />
                    </el-select>
                    <el-select
                      v-model="contact.birthdate_month"
                      clearable
                      placeholder="Mes"
                    >
                      <el-option
                        v-for="(month, index) in months"
                        :key="index"
                        :label="month"
                        :value="index"
                      />
                    </el-select>
                  </div>
                  <!-- <InputError :message="form.errors.sat_way" /> -->
                </div>
                <!-- <InputError :message="form.errors.birthdate" /> -->
              </div>
            </div>
            <SecondaryButton
              @click="addContact"
              :disabled="
                !this.contact.name || !this.contact.email || !this.contact.phone
              "
            >
              {{
                editContactIndex !== null
                  ? "Actualizar contacto"
                  : "Agregar Contacto a lista"
              }}
            </SecondaryButton>
          </div>
          <!-- ---------------- Company Contacts ends ----------------- -->

          <!-- ---------------- Company Products starts ----------------- -->
          <el-divider content-position="left">Productos del cliente</el-divider>
          <ol
            v-if="form.products.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1"
          >
            <template v-for="(item, index) in form.products" :key="index">
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{
                    catalog_products.find(
                      (prd) => prd.id === item.catalog_product_id
                    )?.name
                  }}
                  ({{ item.new_price }} {{ item.new_currency }} / unidad)
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editProductIndex == index">En edición</el-tag>
                  <el-button @click="editProduct(index)" type="primary" circle>
                    <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                  </el-button>
                  <el-popconfirm
                    confirm-button-text="Si"
                    cancel-button-text="No"
                    icon-color="#FF0000"
                    title="¿Continuar?"
                    @confirm="deleteProduct(index)"
                  >
                    <template #reference>
                      <el-button type="danger" circle
                        ><i class="fa-sharp fa-solid fa-trash"></i
                      ></el-button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>

          <div class="space-y-3 rounded-lg p-5">
            <div class="flex items-center">
              <el-tooltip content="Producto de catálogo" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                >
                  <i class="fa-solid fa-magnifying-glass"></i>
                </span>
              </el-tooltip>
              <el-select
                v-model="product.catalog_product_id"
                clearable
                placeholder="Buscar producto"
              >
                <el-option
                  v-for="item in catalog_products"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                />
              </el-select>
            </div>
            <div class="md:grid gap-6 mb-6 grid-cols-3">
              <div>
                <IconInput
                  v-model="product.old_price"
                  inputPlaceholder="Precio anterior *"
                  inputType="number"
                  inputStep="0.01"
                >
                  <el-tooltip content="Precio anterior" placement="top">
                    <i class="fa-solid fa-money-bill"></i>
                  </el-tooltip>
                </IconInput>
                <!-- <InputError :message="form.errors.old_price" /> -->
              </div>
              <div class="flex items-center">
                <el-tooltip content="Moneda" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                  >
                    <i class="fa-solid fa-dollar-sign"></i>
                  </span>
                </el-tooltip>
                <el-select
                  v-model="product.old_currency"
                  placeholder="Moneda *"
                  :fit-input-width="true"
                >
                  <el-option
                    v-for="item in currencies"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                  >
                    <span style="float: left">{{ item.label }}</span>
                    <span
                      style="float: right; color: #cccccc; font-size: 13px"
                      >{{ item.value }}</span
                    >
                  </el-option>
                </el-select>
              </div>
              <div class="flex items-center">
                <el-date-picker
                  v-model="product.old_date"
                  type="date"
                  placeholder="Fecha"
                  format="YYYY/MM/DD"
                  value-format="YYYY-MM-DD"
                  :disabled-date="disabledDate"
                />
                <!-- <InputError :message="form.errors.branches.old_date" /> -->
              </div>
              <div>
                <IconInput
                  v-model="product.new_price"
                  inputPlaceholder="Precio nuevo *"
                  inputType="number"
                  inputStep="0.01"
                >
                  <el-tooltip content="Precio nuevo" placement="top">
                    <i class="fa-solid fa-money-bill"></i>
                  </el-tooltip>
                </IconInput>
                <!-- <InputError :message="form.errors.new_price" /> -->
              </div>
              <div class="flex items-center">
                <el-tooltip content="Moneda" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12"
                  >
                    <i class="fa-solid fa-dollar-sign"></i>
                  </span>
                </el-tooltip>
                <el-select
                  v-model="product.new_currency"
                  placeholder="Moneda *"
                  :fit-input-width="true"
                >
                  <el-option
                    v-for="item in currencies"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value"
                  >
                    <span style="float: left">{{ item.label }}</span>
                    <span
                      style="float: right; color: #cccccc; font-size: 13px"
                      >{{ item.value }}</span
                    >
                  </el-option>
                </el-select>
              </div>
              <div class="flex items-center">
                <el-date-picker
                  v-model="product.new_date"
                  type="date"
                  placeholder="Fecha"
                  format="YYYY/MM/DD"
                  value-format="YYYY-MM-DD"
                  :disabled-date="disabledDate"
                />
                <!-- <InputError :message="form.errors.branches.old_date" /> -->
              </div>
            </div>

            <SecondaryButton
              @click="addProduct"
              :disabled="
                !product.catalog_product_id ||
                !product.new_date ||
                !product.new_currency ||
                !product.new_price
              "
            >
              {{
                editProductIndex !== null
                  ? "Actualizar producto"
                  : "Agregar producto a lista"
              }}
            </SecondaryButton>
          </div>
          <!-- ---------------- Company Products ends ----------------- -->
          <el-divider />
          <div class="md:text-right">
            <PrimaryButton :disabled="form.processing">
              Actualizar Cliente
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

export default {
  data() {
    const form = useForm({
      business_name: this.company.business_name,
      phone: this.company.phone,
      rfc: this.company.rfc,
      post_code: this.company.post_code,
      fiscal_address: this.company.fiscal_address,
      company_branches: [],
      products: [],
    });

    return {
      form,
      contacts: [],
      editContactIndex: null,
      editProductIndex: null,
      editBranchIndex: null,
      contact: {
        name: null,
        email: null,
        phone: null,
        birthdate_day: null,
        birthdate_month: null,
      },
      branch: {
        name: null,
        address: null,
        post_code: null,
        sat_method: null,
        sat_type: null,
        sat_way: null,
      },
      product: {
        catalog_product_id: null,
        old_date: null,
        new_date: null,
        old_currency: null,
        new_currency: null,
        old_price: null,
        new_price: null,
      },
      currencies: [
        { value: "$MXN", label: "MXN" },
        { value: "$USD", label: "USD" },
      ],
      months: [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
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
        {
          value: "I02",
          label: "Mobiliario y equipo de oficina por inversiones",
        },
        { value: "I03", label: "Equipo de transporte" },
        { value: "I04", label: "Equipo de cómputo y accesorios" },
        {
          value: "I05",
          label: "Dados, troqueles, moldes, matrices y herramental",
        },
        { value: "I06", label: "Comunicaciones telefónicas" },
        { value: "I07", label: "Comunicaciones satelitales" },
        { value: "I08", label: "Otra maquinaria y equipo" },
        {
          value: "D01",
          label: "Honorarios médicos, dentales y gastos hospitalarios",
        },
        {
          value: "D02",
          label: "Gastos médicos por incapacidad o discapacidad",
        },
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
    company: Array,
    catalog_products: Array,
    raw_materials: Array,
  },
  methods: {
    update() {
      this.form.put(route("companies.update", this.company.id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Cliente actualizado",
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
    // contacts
    addContact() {
      const contact = { ...this.contact };

      if (this.editContactIndex !== null) {
        this.contacts[this.editContactIndex] = contact;
        this.editContactIndex = null;
      } else {
        this.contacts.push(contact);
      }

      this.contact.name = null;
      this.contact.email = null;
      this.contact.phone = null;
      this.contact.birthdate_day = null;
      this.contact.birthdate_month = null;
    },
    deleteContact(index) {
      this.contacts.splice(index, 1);
    },
    editContact(index) {
      const contact = { ...this.contacts[index] };
      this.contact = contact;
      this.editContactIndex = index;
    },
    // branches
    addBranch() {
      let branch = { ...this.branch };
      branch.contacts = this.contacts;

      if (this.editBranchIndex !== null) {
        this.form.company_branches[this.editBranchIndex] = branch;
        this.editBranchIndex = null;
      } else {
        this.form.company_branches.push(branch);
      }

      // reser branch form & list of contacts
      this.branch.name = null;
      this.branch.address = null;
      this.branch.post_code = null;
      this.branch.sat_method = null;
      this.branch.sat_type = null;
      this.branch.sat_way = null;
      this.contacts = [];
    },
    deleteBranch(index) {
      this.form.company_branches.splice(index, 1);
    },
    editBranch(index) {
      const branch = { ...this.form.company_branches[index] };

      this.form.company_branches[index].contacts.forEach((element) => {
        const contact = {
          name: element.name,
          email: element.email,
          phone: element.phone,
          birthdate_day: element.birthdate_day,
          birthdate_month: element.birthdate_month,
        };

        this.contacts.push(contact);
      });

      this.branch = branch;
      this.editBranchIndex = index;
    },
    // products
    addProduct() {
      let product = { ...this.product };

      if (this.editProductIndex !== null) {
        this.form.products[this.editProductIndex] = product;
        this.editProductIndex = null;
      } else {
        this.form.products.push(product);
      }

      // reser product form
      this.product.catalog_product_id = null;
      this.product.old_date = null;
      this.product.new_date = null;
      this.product.old_currency = null;
      this.product.new_currency = null;
      this.product.old_price = null;
      this.product.new_price = null;
    },
    deleteProduct(index) {
      this.form.products.splice(index, 1);
    },
    editProduct(index) {
      const product = { ...this.form.products[index] };

      this.product = product;
      this.editProductIndex = index;
    },
  },
  mounted() {
    this.company.company_branches.forEach((element) => {
      let branch = {
        name: element.name,
        address: element.address,
        post_code: element.post_code,
        sat_method: element.sat_method,
        sat_type: element.sat_type,
        sat_way: element.sat_way,
        contacts: [],
      };

      element.contacts.forEach((item) => {
        const contact = {
          name: item.name,
          email: item.email,
          phone: item.phone,
          birthdate_day: item.birthdate_day,
          birthdate_month: item.birthdate_month,
        };

        branch.contacts.push(contact);
      });

      this.form.company_branches.push(branch);
    });

    this.company.catalog_products.forEach((element) => {
      const product = {
        catalog_product_id: element.pivot.catalog_product_id,
        old_date: element.pivot.old_date,
        new_date: element.pivot.new_date,
        old_currency: element.pivot.old_currency,
        new_currency: element.pivot.new_currency,
        old_price: element.pivot.old_price,
        new_price: element.pivot.new_price,
      };

      this.form.products.push(product);
    });
  },
};
</script>
  