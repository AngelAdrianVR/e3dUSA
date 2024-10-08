<template>
  <div>
    <AppLayout title="Proveedores - Editar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar proveedor
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
              <el-tooltip content="Nombre del proveedor" placement="top">
                A
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <IconInput v-model="form.nickname" inputPlaceholder="Alias *" inputType="text">
              <el-tooltip content="Nombre con el que conoces a este proveedor" placement="top">
                N
              </el-tooltip>
            </IconInput>
          </div>
          <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-3">
            <div class="col-span-2">
              <IconInput v-model="form.address" inputPlaceholder="Dirección" inputType="text">
                <el-tooltip content="Dirección del proveedor" placement="top">
                  <i class="fa-solid fa-map-location-dot"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.address" />
            </div>
            <div class="col-span-1">
              <IconInput v-model="form.post_code" inputPlaceholder="C.P." inputType="text">
                <el-tooltip content="Código postal" placement="top">
                  <i class="fa-solid fa-envelopes-bulk"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.post_code" />
            </div>
            <div class="col-span-2">
              <IconInput v-model="form.phone" inputPlaceholder="Teléfono *" inputType="text">
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

          <ol v-if="form.banks.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
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
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                    @confirm="deleteBank(index)">
                    <template #reference>
                      <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
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
                  <IconInput v-model="bank.beneficiary_name" inputPlaceholder="Nombre del beneficiario *"
                    inputType="text">
                    <el-tooltip content="Nombre del beneficiario" placement="top">
                      A
                    </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput v-model="bank.accountNumber" inputPlaceholder="Número de cuenta *" inputType="text">
                    <el-tooltip content="Número de cuenta" placement="top">
                      <i class="fa-solid fa-money-check-dollar"></i>
                    </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput v-model="bank.clabe" inputPlaceholder="Clabe *" inputType="text">
                    <el-tooltip content="Clabe" placement="top"> # </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput v-model="bank.bank_name" inputPlaceholder="Banco *" inputType="text">
                    <el-tooltip content="Nombre del banco" placement="top">
                      <i class="fa-solid fa-building-columns"></i>
                    </el-tooltip>
                  </IconInput>
                </div>
              </div>
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
            <!-- ---------------- Datos bancarios ends ----------------- -->

            <!-- ---------------- contacts starts ----------------- -->
            <el-divider content-position="left">Contactos</el-divider>
            <InputError :message="form.errors.contacts" />

            <ol v-if="form.contacts.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
              <template v-for="(item, index) in form.contacts" :key="index">
                <li class="flex justify-between items-center">
                  <p class="text-sm">
                    <span class="text-primary">{{ index + 1 }}.</span>
                    {{ item.name }} | {{ item.email }}
                  </p>
                  <div class="flex space-x-2 items-center">
                    <el-tag v-if="editContactIndex == index">En edición</el-tag>
                    <el-button @click="editContact(index)" type="primary" circle>
                      <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                    </el-button>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                      title="¿Continuar?" @confirm="deleteContact(index)">
                      <template #reference>
                        <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
                      </template>
                    </el-popconfirm>
                  </div>
                </li>
              </template>
            </ol>

            <div class="rounded-lg p-5">
              <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-2">
                <div class="col-span-2">
                  <IconInput v-model="contact.name" inputPlaceholder="Nombre *" inputType="text">
                    <el-tooltip content="Nombre del contacto" placement="top">
                      A
                    </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput v-model="contact.email" inputPlaceholder="Correo *" inputType="text">
                    <el-tooltip content="Correo electrónico" placement="top">
                      <i class="fa-solid fa-envelope"></i>
                    </el-tooltip>
                  </IconInput>
                </div>
                <div>
                  <IconInput v-model="contact.phone" inputPlaceholder="Teléfono *" inputType="text">
                    <el-tooltip content="Teléfono" placement="top">
                      <i class="fa-solid fa-phone"></i>
                    </el-tooltip>
                  </IconInput>
                </div>
              </div>
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
          </div>
          <!-- ---------------- contacts ends ----------------- -->

          <!-- ---------------- supplier rawMaterials starts ----------------- -->
          <el-divider content-position="left">Productos del proveedor</el-divider>

          <ol v-if="form.rawMaterials.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
            <template v-for="(item, index) in form.rawMaterials" :key="index">
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary">{{ index + 1 }}.</span>
                  {{
                    item.name
                  }}
                  (${{ item.cost?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} / unidad)
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editRawMaterialIndex == index">En edición</el-tag>
                  <el-button @click="editProduct(index)" type="primary" circle>
                    <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                  </el-button>
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                    @confirm="deleteProduct(index)">
                    <template #reference>
                      <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
                    </template>
                  </el-popconfirm>
                </div>
              </li>
            </template>
          </ol>

          <div class="lg:grid grid-cols-3 gap-x-5 space-y-3 rounded-lg p-5">
            <div class="flex items-center col-span-2">
              <el-tooltip content="Producto de catálogo" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md ">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </span>
              </el-tooltip>
              <el-select @change="fetchRawMaterial" v-model="rawMaterialId" clearable filterable
                placeholder="Buscar producto">
                <el-option v-for="item in raw_materials" :key="item.id" :label="item.name" :value="item.id" />
              </el-select>
            </div>
            <div v-if="loading" class="rounded-md bg-[#CCCCCC] text-xs text-gray-500 text-center p-4">
              cargando imagen...
            </div>
            <figure v-else-if="selectedRawaterial" class="rounded-md">
              <img :src="selectedRawaterial.media[0]?.original_url" class="rounded-md">
            </figure>
            <div></div>

            <div class="col-span-full flex space-x-3" v-if="selectedRawaterial">
              <div class="w-1/2">
                <IconInput v-model="selectedRawaterialCost" inputPlaceholder="Precio *" inputType="number"
                  inputStep="0.01">
                  <el-tooltip content="Precio unitario de la materia prima" placement="top">
                    <i class="fa-solid fa-money-bill"></i>
                  </el-tooltip>
                </IconInput>
              </div>
              <div class="w-1/2">
                <IconInput v-model="selectedRawaterialMinQuantity" inputPlaceholder="Cantidad mínima de pedido"
                  inputType="number" inputStep="0.1">
                  <el-tooltip content="Cantidad mínima de pedido" placement="top">
                    #
                  </el-tooltip>
                </IconInput>
              </div>
            </div>
            <div class="w-full flex items-center col-span-full">
              <el-tooltip content="Notas" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                  ...
                </span>
              </el-tooltip>
              <textarea v-model="selectedRawaterialNotes" class="textarea" autocomplete="off" placeholder="Notas">
                </textarea>
            </div>

            <div class="col-start-1 pt-2">
              <SecondaryButton @click="addProduct" :disabled="!rawMaterialId">
                {{
                  editRawMaterialIndex !== null
                  ? "Actualizar producto"
                  : "Agregar producto a lista"
                }}
              </SecondaryButton>
            </div>
          </div>
          <!-- ---------------- supplier Products ends ----------------- -->
          <div class="mt-2 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Guardar cambios
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

export default {
  data() {
    const form = useForm({
      name: this.supplier.name,
      nickname: this.supplier.nickname,
      address: this.supplier.address,
      post_code: this.supplier.post_code,
      phone: this.supplier.phone,
      banks: this.supplier.banks,
      contacts: this.supplier.contacts,
      rawMaterials: this.supplier.raw_materials_id ?? [],
    });

    return {
      form,
      editIndex: null,
      editContactIndex: null,
      editRawMaterialIndex: null,
      contacts: [],
      loading: false,
      bank: {
        beneficiary_name: null,
        accountNumber: null,
        clabe: null,
        bank_name: null,
      },
      contact: {
        id: null,
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
    Link
  },
  props: {
    supplier: Object,
    raw_materials: Array
  },
  methods: {
    update() {
      this.form.put(route("suppliers.update", this.supplier), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Proveedor Actualizado",
            type: "success",
          });
        },
      });
    },

    //banks
    addBank() {
      let bank_info = { ...this.bank };
      bank_info.contacts = this.contacts;

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
      this.contacts = this.supplier.contacts;
      this.editIndex = index;
    },
    resetBankForm() {
      this.bank.beneficiary_name = null;
      this.bank.accountNumber = null;
      this.bank.clabe = null;
      this.bank.bank_name = null;
      this.contacts = [];
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

      console.log(this.form.contacts);
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
    async fetchSupplierItems() {
      try {
          const response = await axios.get(route('raw-materials.fetch-supplier-items', {
              raw_materials_ids: this.supplier.raw_materials_id?.join(',')
          }));
          
          if (response.status === 200) {
              this.form.rawMaterials = response.data.items;
          }
      } catch (error) {
          console.log(error);
      }
    }
  },
  mounted() {
    this.form.contacts = this.supplier.contacts.map(contact => {
      return {
        id: contact.id,
        name: contact.name,
        email: contact.email,
        phone: contact.phone,
        birthdate_day: contact.birthdate_day,
        birthdate_month: contact.birthdate_month,
      }
    });

    this.fetchSupplierItems();

  }
};
</script>
