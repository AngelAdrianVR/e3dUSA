<template>
  <div>
    <AppLayout title="Proveedores - Crear">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('suppliers.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Agregar proveedor
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div>
            <IconInput
              v-model="form.name"
              inputPlaceholder="Nombre *"
              inputType="text"
            >
              <el-tooltip content="Nombre del proveedor" placement="top">
                A
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-3">
            <div class="col-span-2">
              <IconInput
                v-model="form.address"
                inputPlaceholder="Dirección"
                inputType="text"
              >
                <el-tooltip content="Dirección del proveedor" placement="top">
                  <i class="fa-solid fa-map-location-dot"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.address" />
            </div>
            <div class="col-span-1">
              <IconInput
                v-model="form.post_code"
                inputPlaceholder="C.P."
                inputType="text"
              >
                <el-tooltip content="Código postal" placement="top">
                  <i class="fa-solid fa-envelopes-bulk"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.post_code" />
            </div>
            <div class="col-span-2">
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
          </div>

          <!-- ---------------- Datos bancarios starts ----------------- -->
          <el-divider content-position="left">Datos bancarios</el-divider>
          <InputError :message="form.errors.banks" />

          <ol
            v-if="form.banks.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1"
          >
            <template v-for="(item, index) in form.banks" :key="index">
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{ item.beneficiary_name }} - {{ item.bank_name }}
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editIndex == index">En edición</el-tag>
                  <el-button @click="editBank(index)" type="primary" circle>
                    <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                  </el-button>
                  <el-popconfirm
                    confirm-button-text="Si"
                    cancel-button-text="No"
                    icon-color="#0355B5"
                    title="¿Continuar?"
                    @confirm="deleteBank(index)"
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

          <div class="p-4">
            <div class="rounded-lg p-5">
              <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-2">
                <div>
                  <IconInput
                    v-model="bank.beneficiary_name"
                    inputPlaceholder="Nombre del beneficiario *"
                    inputType="text"
                  >
                    <el-tooltip
                      content="Nombre del beneficiario"
                      placement="top"
                    >
                      A
                    </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput
                    v-model="bank.accountNumber"
                    inputPlaceholder="Número de cuenta *"
                    inputType="text"
                  >
                    <el-tooltip content="Número de cuenta" placement="top">
                      <i class="fa-solid fa-money-check-dollar"></i>
                    </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput
                    v-model="bank.clabe"
                    inputPlaceholder="Clabe *"
                    inputType="text"
                  >
                    <el-tooltip content="Clabe" placement="top"> # </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput
                    v-model="bank.bank_name"
                    inputPlaceholder="Banco *"
                    inputType="text"
                  >
                    <el-tooltip content="Nombre del banco" placement="top">
                      <i class="fa-solid fa-building-columns"></i>
                    </el-tooltip>
                  </IconInput>
                </div>
              </div>
              <SecondaryButton
                @click="addBank"
                :disabled="
                  !bank.beneficiary_name ||
                  !bank.accountNumber ||
                  !bank.clabe ||
                  !bank.bank_name
                "
                type="button"
              >
                {{
                  editIndex !== null
                    ? "Actualizar datos bancarios"
                    : "Agregar banco a lista"
                }}
              </SecondaryButton>
            </div>
            <!-- ---------------- Datos bancarios ends ----------------- -->

            <!-- ---------------- contacts starts ----------------- -->
            <el-divider content-position="left">Contactos</el-divider>
            <InputError :message="form.errors.contacts" />

            <ol
              v-if="form.contacts.length"
              class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1"
            >
              <template v-for="(item, index) in form.contacts" :key="index">
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
                      icon-color="#0355B5"
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

            <div class="rounded-lg p-5">
              <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-2">
                <div class="col-span-2">
                  <IconInput
                    v-model="contact.name"
                    inputPlaceholder="Nombre *"
                    inputType="text"
                  >
                    <el-tooltip content="Nombre del contacto" placement="top">
                      A
                    </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput
                    v-model="contact.email"
                    inputPlaceholder="Correo *"
                    inputType="text"
                  >
                    <el-tooltip content="Correo electrónico" placement="top">
                      <i class="fa-solid fa-envelope"></i>
                    </el-tooltip>
                  </IconInput>
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
                  </div>
                </div>
              </div>
              <SecondaryButton
                @click="addContact"
                :disabled="
                  !this.contact.name ||
                  !this.contact.email ||
                  !this.contact.phone
                "
              >
                {{
                  editContactIndex !== null
                    ? "Actualizar contacto"
                    : "Agregar Contacto a lista"
                }}
              </SecondaryButton>
            </div>
          </div>
          <!-- ---------------- contacts ends ----------------- -->
          <div class="mt-2 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Agregar proveedor
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
      name: null,
      address: null,
      post_code: null,
      phone: null,
      banks: [],
      contacts: [],
    });

    return {
      form,
      editIndex: null,
      editContactIndex: null,
      bank: {
        beneficiary_name: null,
        accountNumber: null,
        clabe: null,
        bank_name: null,
      },
      contact: {
        name: null,
        email: null,
        phone: null,
        birthdate_day: null,
        birthdate_month: null,
      },
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
  props: {},
  methods: {
    store() {
      this.form.post(route("suppliers.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Proveedor creado",
            type: "success",
          });

          this.form.reset();
        },
      });
    },

    //banks
    addBank() {
      let bank_info = { ...this.bank };

      if (this.editIndex !== null) {
        this.form.banks[this.editIndex] = bank_info;
        this.editIndex = null;
      } else {
        this.form.banks.push(bank_info);
      }

      this.resetBankForm();
    },

    deleteBank(index) {
      this.form.banks.splice(index, 1);
    },

    editBank(index) {
      const bank_info = { ...this.form.banks[index] };
      this.bank = bank_info;
      this.editIndex = index;
    },
    resetBankForm() {
      this.bank.beneficiary_name = null;
      this.bank.accountNumber = null;
      this.bank.clabe = null;
      this.bank.bank_name = null;
    },

    // contacts
    addContact() {
      const contact = { ...this.contact };

      if (this.editContactIndex !== null) {
        this.form.contacts[this.editContactIndex] = contact;
        this.editContactIndex = null;
      } else {
        this.form.contacts.push(contact);
      }

      this.contact.name = null;
      this.contact.email = null;
      this.contact.phone = null;
      this.contact.birthdate_day = null;
      this.contact.birthdate_month = null;
    },
    deleteContact(index) {
      this.form.contacts.splice(index, 1);
    },
    editContact(index) {
      const contact = { ...this.form.contacts[index] };
      this.contact = contact;
      this.editContactIndex = index;
    },
  },
};
</script>
