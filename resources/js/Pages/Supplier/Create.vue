<template>
  <div>
    <AppLayout title="Proveedores - Crear">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Agregar proveedor
            </h2>
          </div>
        </div>
      </template>
      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto grid grid-cols-2 gap-3 mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
          <div>
            <InputLabel value="Nombre del proveedor*" />
            <el-input v-model="form.name" placeholder="Ej. Proveedora de cajas sa de cv" />
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <InputLabel>
              <div class="flex items-center space-x-2">
                <span>Alias</span>
                <el-tooltip placement="top">
                  <template #content>
                    <p>Nombre con el que conoces a este proveedor</p>
                  </template>
                  <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                  </div>
                </el-tooltip>
              </div>
            </InputLabel>
            <el-input v-model="form.nickname" placeholder="Ej. Cajas de cartón" />
            <InputError :message="form.errors.nickname" />
          </div>
          <div>
            <InputLabel value="Dirección" />
            <el-input v-model="form.address" placeholder="Ej. Av Manuel Avila #555" />
            <InputError :message="form.errors.address" />
          </div>
          <div>
            <InputLabel value="C.P." />
            <el-input v-model="form.post_code" placeholder="Ej. 49500" />
            <InputError :message="form.errors.post_code" />
          </div>
          <div>
            <InputLabel value="Télefono*" />
            <el-input v-model="form.phone" type="text"
              :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
              :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable placeholder="Ej. 3312479856" />
            <InputError :message="form.errors.phone" />
          </div>

          <!-- ---------------- Datos bancarios starts ----------------- -->
          <el-divider content-position="left" class="col-span-full">Datos bancarios</el-divider>
          <div>
            <InputLabel value="Nombre del beneficiario*" />
            <el-input v-model="bank.beneficiary_name" placeholder="Ej. José Rodriguez" />
          </div>
          <div>
            <InputLabel value="Número de cuenta*" />
            <el-input v-model="bank.accountNumber" placeholder="Ej. 112233445566" />
          </div>
          <div>
            <InputLabel value="CLABE*" />
            <el-input v-model="bank.clabe" placeholder="18 digitos" />
          </div>
          <div>
            <InputLabel value="Banco*" />
            <el-input v-model="bank.bank_name" placeholder="Ej. BBVA" />
          </div>
          <div class="col-span-full">
            <SecondaryButton @click="addBank" :disabled="!bank.beneficiary_name ||
              !bank.accountNumber ||
              !bank.clabe ||
              !bank.bank_name
              " type="button">
              {{
                editIndex !== null
                  ? "Actualizar datos bancarios"
                  : "Agregar banco a lista"
              }}
            </SecondaryButton>
          </div>
          <InputError :message="form.errors.banks" />
          <ol v-if="form.banks.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 divide-y-[1px]">
            <template v-for="(item, index) in form.banks" :key="index">
              <li class="flex justify-between items-center border-[#999999] py-1">
                <p class="text-xs">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{ item.beneficiary_name }} - {{ item.bank_name }}
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editIndex == index" @close="editIndex = null; resetBankForm()" closable>
                    En edición
                  </el-tag>
                  <button @click="editBank(index)" type="button"
                    class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                    title="¿Continuar?" @confirm="deleteBank(index)">
                    <template #reference>
                      <button type="button"
                        class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                      </button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>
          <!-- ---------------- contacts starts ----------------- -->
          <el-divider content-position="left" class="col-span-full">Contactos</el-divider>
          <div>
            <InputLabel value="Nombre del contacto*" />
            <el-input v-model="contact.name" placeholder="Ej. Francisco Navarrete" />
          </div>
          <div>
            <InputLabel value="Correo*" />
            <el-input v-model="contact.email" placeholder="Ej. franciasco@cajas.com" />
          </div>
          <div>
            <InputLabel value="Télefono*" />
            <el-input v-model="contact.phone" type="text"
              :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')"
              :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable placeholder="Ej. 3312479856" />
          </div>
          <div class="col-span-full">
            <SecondaryButton @click="addContact" :disabled="!this.contact.name ||
              !this.contact.email ||
              !this.contact.phone
              ">
              {{
                editContactIndex !== null
                  ? "Actualizar contacto"
                  : "Agregar Contacto a lista"
              }}
            </SecondaryButton>
          </div>
          <InputError :message="form.errors.contacts" />
          <ol v-if="form.contacts.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 divide-y-[1px]">
            <template v-for="(item, index) in form.contacts" :key="index">
              <li class="flex justify-between items-center border-[#999999] py-1">
                <p class="text-xs">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{ item.name }} | {{ item.email }}
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editContactIndex == index" @close="editContactIndex = null; resetContactForm()" closable>
                    En edición
                  </el-tag>
                  <button @click="editContact(index)" type="button"
                    class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                    title="¿Continuar?" @confirm="deleteContact(index)">
                    <template #reference>
                      <button type="button"
                        class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                      </button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>
          <!-- ---------------- supplier rawMaterials starts ----------------- -->
          <el-divider content-position="left" class="col-span-full">Productos del proveedor</el-divider>
          <div>
            <InputLabel value="Materia prima*" />
            <el-select @change="fetchRawMaterial" v-model="rawMaterialId" clearable filterable placeholder="Selecciona">
              <el-option v-for="item in raw_materials" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div v-if="loading" class="rounded-md bg-[#CCCCCC] text-xs text-gray-500 text-center p-4">
            cargando imagen...
          </div>
          <figure v-else-if="selectedRawaterial" class="rounded-md">
            <img :src="selectedRawaterial.media[0]?.original_url" class="rounded-md">
          </figure>
          <div v-if="selectedRawaterial">
            <InputLabel value="Precio unitario*" />
            <el-input v-model="selectedRawaterialCost" type="text"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 36.85" />
          </div>
          <div v-if="selectedRawaterial">
            <InputLabel value="Cantidad mínima de pedido" />
            <el-input v-model="selectedRawaterialMinQuantity" type="text"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 50" />
          </div>
          <div class="col-span-full">
            <InputLabel value="Notas" />
            <el-input v-model="selectedRawaterialNotes" :rows="3" maxlength="800" placeholder="..." show-word-limit
              type="textarea" />
          </div>
          <div class="col-span-full">
            <SecondaryButton @click="addProduct" :disabled="!rawMaterialId">
              {{
                editRawMaterialIndex !== null
                  ? "Actualizar producto"
                  : "Agregar producto a lista"
              }}
            </SecondaryButton>
          </div>
          <ol v-if="form.rawMaterials.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 divide-y-[1px]">
            <template v-for="(item, index) in form.rawMaterials" :key="index">
              <li class="flex justify-between items-center border-[#999999] py-1">
                <p class="text-xs">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{
                    item.name
                  }}
                  (${{ item.cost?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} / unidad)
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editRawMaterialIndex == index" @close="editRawMaterialIndex = null; resetRawMaterialForm()" closable>
                    En edición
                  </el-tag>
                  <button @click="editProduct(index)" type="button"
                    class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="size-4">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                  </button>
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                    title="¿Continuar?" @confirm="deleteProduct(index)">
                    <template #reference>
                      <button type="button"
                        class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                      </button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>
          <div class="col-span-full flex justify-end">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
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
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      nickname: null,
      address: null,
      post_code: null,
      phone: null,
      banks: [],
      contacts: [],
      rawMaterials: [],
    });

    return {
      form,
      loading: false,
      editIndex: null,
      editRawMaterialIndex: null,
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
      rawMaterialId: null,
      selectedRawaterialCost: null,
      selectedRawaterialMinQuantity: null,
      selectedRawaterialNotes: null,
      selectedRawaterial: null,
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
    InputError,
    IconInput,
    Back,
    Link,
    InputLabel,
  },
  props: {
    raw_materials: Array
  },
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

      this.resetContactForm();
    },
    resetContactForm() {
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

    //products
    async fetchRawMaterial() {
      this.loading = true;
      try {
        const response = await axios.get(route('raw-materials.fetch', this.rawMaterialId));

        if (response.status === 200) {
          this.selectedRawaterial = response.data.item;
        }

      } catch (error) {
        console.log(error);

      } finally {
        this.loading = false;
      }

    },
    addProduct() {
      if (this.editRawMaterialIndex === null) {
        //agrega varias propiedades al objeto de raw_material recuperado del metodo fetchRawMaterial
        this.selectedRawaterial.cost = this.selectedRawaterialCost;
        this.selectedRawaterial.min_quantity_purchase = this.selectedRawaterialMinQuantity;
        this.selectedRawaterial.notes = this.selectedRawaterialNotes;
        this.form.rawMaterials.push(this.selectedRawaterial);
      } else {
        this.form.rawMaterials[this.editRawMaterialIndex].cost = this.selectedRawaterialCost;
        this.form.rawMaterials[this.editRawMaterialIndex].min_quantity_purchase = this.selectedRawaterialMinQuantity;
        this.form.rawMaterials[this.editRawMaterialIndex].notes = this.selectedRawaterialNotes;
        this.editRawMaterialIndex = null;
      }

      // reset rawMaterialId and selectedrawmaterial price form
      this.resetRawMaterialForm();
    },
    resetRawMaterialForm() {
      this.rawMaterialId = null;
      this.selectedRawaterialCost = null;
      this.selectedRawaterialMinQuantity = null;
      this.selectedRawaterialNotes = null;
    },
    deleteProduct(index) {
      this.form.rawMaterials.splice(index, 1);
    },
    editProduct(index) {
      this.rawMaterialId = this.form.rawMaterials[index].id;
      this.selectedRawaterialCost = this.form.rawMaterials[index].cost;
      this.selectedRawaterialMinQuantity = this.form.rawMaterials[index].min_quantity_purchase;
      this.selectedRawaterialNotes = this.form.rawMaterials[index].notes;
      this.editRawMaterialIndex = index;
      this.fetchRawMaterial();
    },
  },
};
</script>
