<template>
  <div>
    <AppLayout title="Órdenes de compra - Editar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar órden de compra
            </h2>
          </div>
        </div>
      </template>
      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div class="flex items-center">
            <el-tooltip content="Selecciona un provedor" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-boxes-packing"></i>
              </span>
            </el-tooltip>
            <el-select @change="fetchSupplier" v-model="form.supplier_id" class="mt-2" clearable filterable
              placeholder="Selecciona proveedor" disabled>
              <el-option v-for="item in suppliers" :key="item.id"
                :label="item.nickname ? item.nickname + ' - ' + item.name : item.name" :value="item.id" />
            </el-select>
          </div>
          <InputError :message="form.errors.supplier_id" />

          <div class="flex items-center">
            <el-tooltip content="Selecciona la información bancaria" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-money-check-dollar"></i>
              </span>
            </el-tooltip>
            <el-select v-model="form.bank_information" class="mt-2" clearable filterable
              placeholder="Selecciona la información bancaria">
              <el-option v-for="(item, index) in currentSupplier?.banks" :key="item.id"
                :label="item['beneficiary_name'] + '-' + item['bank_name']" :value="index" />
            </el-select>
          </div>
          <InputError :message="form.errors.bank_information" />

          <div class="flex items-center pb-2">
            <el-tooltip content="Selecciona un contacto" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-address-card"></i>
              </span>
            </el-tooltip>
            <el-select v-model="form.contact_id" class="mt-2" clearable filterable placeholder="Selecciona un contacto">
              <el-option v-for="item in currentSupplier?.contacts" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <InputError :message="form.errors.contact_id" />

          <!-- --------------- Order info ----------------------------- -->
          <el-divider content-position="left">Datos de la órden</el-divider>
          <div class="pb-4 pt-3">
            <div class="flex items-center">
              <el-tooltip content="fecha de entrega esperada" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  <i class="fa-solid fa-calendar"></i>
                </span>
              </el-tooltip>
              <el-date-picker v-model="form.expected_delivery_date" type="date" placeholder="fecha de entrega esperada"
                :disabled-date="disabledDate" />
            </div>
            <InputError :message="form.errors.expected_delivery_date" />
          </div>

          <!-- --------------- Products to buy ----------------------------- -->
          <el-divider content-position="left">Productos</el-divider>
          <ol v-if="form.products.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
            <template v-for="(item, index) in form.products" :key="index">
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary">{{ index + 1 }}.
                    <span class="text-gray-700">
                      {{ item.name }} - {{ item.quantity + ' unidades' }}</span></span>
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editProductIndex == index">En edición</el-tag>
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
          <div class="space-y-3 bg-[#b8b7b7] rounded-lg p-5">
            <div class="md:grid gap-x-6 mb-6 grid-cols-2">
              <div>
                <div class="flex items-center mb-3">
                  <el-tooltip content="Selecciona un producto" placement="top">
                    <span
                      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                      <i class="fa-brands fa-product-hunt"></i>
                    </span>
                  </el-tooltip>
                  <el-select v-model="productSelectedId" @change="getProductSelected" clearable filterable
                    placeholder="Selecciona un producto">
                    <el-option v-for="item in rawMaterials" :key="item.id" :label="item.name + ' (' + item.part_number + ')'" :value="item.id" />
                  </el-select>
                </div>

                <div class="mt-2">
                  <IconInput v-model="form.quantity" inputPlaceholder="Cantidad *" inputType="number" inputStep="0.01">
                    <el-tooltip content="Cantidad requerida del producto seleccionado" placement="top">
                      #
                    </el-tooltip>
                  </IconInput>
                  <InputError :message="form.errors.quantity" />
                </div>
              </div>
              <figure v-if="productSelectedObj?.media[0] != null && productSelectedId" class="rounded-md">
                <img :src="productSelectedObj?.media[0]?.original_url" class="rounded-md object-cover h-32">
              </figure>
            </div>
            <SecondaryButton :disabled="!form.quantity" @click="addProduct" type="button">
              {{
                editProductIndex !== null
                ? "Actualizar producto"
                : "Agregar producto a la órden"
              }}
            </SecondaryButton>
          </div>
          <InputError :message="form.errors.products" />
          <p class="text-xs text-red-600" v-if="productValidation">
            No puedes agregar dos veces el mísmo producto
          </p>
          <div>
            <label class="flex items-center">
              <Checkbox v-model:checked="form.is_iva_included" class="bg-transparent" />
              <span class="ml-2 text-xs">Calcular IVA y mostrarlo</span>
            </label>
          </div>
          <div class="flex">
            <span
              class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
              <el-tooltip content="Notas" placement="top"> ... </el-tooltip>
            </span>
            <textarea v-model="form.notes" class="textarea" autocomplete="off" placeholder="Notas"></textarea>
            <InputError :message="form.errors.notes" />
          </div>
          <div class="mt-2 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
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
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    const form = useForm({
      notes: this.purchase.notes,
      expected_delivery_date: this.purchase.expected_delivery_date,
      is_iva_included: Boolean(this.purchase.is_iva_included),
      supplier_id: this.purchase.supplier_id,
      contact_id: this.purchase.contact_id,
      bank_information: this.purchase.bank_information,
      products: this.purchase.products,
    });

    return {
      form,
      currentSupplier: null,
      bank_index: null,
      productSelectedId: null,
      productSelectedObj: null,
      editProductIndex: null,
      productValidation: false,
      rawMaterials: [],
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Back,
    Checkbox,
    Link
  },
  props: {
    suppliers: Array,
    raw_materials: Array,
    contacts: Array,
    purchase: Object,
  },
  methods: {
    update() {
      this.form.put(route("purchases.update", this.purchase.id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Órden de compra editada",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    saveBankObj() {
      this.form.bank_information = this.currentSupplier?.banks[this.bank_index];
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    addProduct() {
      let product = {
        id: this.productSelectedObj.id,
        name: this.productSelectedObj.name,
        quantity: this.form.quantity,
      };

      if (this.editProductIndex !== null) {
        this.form.products[this.editProductIndex] = product;
        this.editProductIndex = null;
      } else {
        this.form.products.push(product);
      }
      this.productSelectedId = null;
      this.form.quantity = null;
    },

    deleteProduct(index) {
      this.form.products.splice(index, 1);
    },

    editProduct(index) {
      const product = { ...this.form.products[index] };
      this.productSelectedId = product.id;
      this.form.quantity = product.quantity;
      this.editProductIndex = index;
      this.getProductSelected();
    },
    async fetchSupplierItems() {
      this.productSelectedObj = null;
      this.productSelectedId = null;
      this.rawMaterials = [];
      try {
        const response = await axios.get(route('raw-materials.fetch-supplier-items', {
          raw_materials_ids: this.currentSupplier.raw_materials_id.join(',')
        }));

        if (response.status === 200) {
          this.rawMaterials = response.data.items;
        }
      } catch (error) {
        console.log(error);
      } finally {
      }
    },
    async fetchSupplier() {
      try {
        const response = await axios.get(route('suppliers.fetch-supplier', this.form.supplier_id));

        if (response.status === 200) {
          this.currentSupplier = response.data.item;
          this.getBank(); //recupera el el banco de la compra
          this.fetchSupplierItems(); //recupera los productos registrados del proveedor
        }
      } catch (error) {
        console.log(error);
      } finally {
      }
    },
    getProductSelected() {
      this.productSelectedObj = this.rawMaterials.find(item => item.id === this.productSelectedId);
    },
    getBank() {
      // Encontrar el índice del elemento en "banks" que coincide con la "clabe" de "bank_information"
      const index = this.currentSupplier.banks.findIndex(item => item.clabe === this.purchase.bank_information?.clabe);

      // El resultado será el índice del elemento o -1 si no se encuentra
      if (index != -1) {
        this.bank_index = index;
      } else {
        this.bank_index = null;
      }
    }
  },
  mounted() {
    this.fetchSupplier();
  }
};
</script>
