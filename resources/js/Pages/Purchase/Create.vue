<template>
  <div>
    <AppLayout title="Órdenes de compra - Crear">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Crear órden de compra
            </h2>
          </div>
        </div>
      </template>
      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div>
            <el-radio-group v-model="form.is_spanish_template" size="small">
                <el-radio :label="1">Plantilla en español</el-radio>
                <el-radio :label="0">Plantilla en inglés</el-radio>
            </el-radio-group>
          </div>
          <div class="flex items-center">
            <el-tooltip content="Selecciona un provedor" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-boxes-packing"></i>
              </span>
            </el-tooltip>
            <el-select @change="fetchSupplier" v-model="form.supplier_id" class="mt-2" clearable filterable
              placeholder="Selecciona proveedor">
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

          <div class="flex items-center pb-3">
            <el-tooltip content="Moneda" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md ">
                <i class="fa-solid fa-dollar-sign mr-2"></i>
              </span>
            </el-tooltip>
            <el-select v-model="form.currency" placeholder="Moneda *" :fit-input-width="true">
              <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                <span style="float: left">{{ item.label }}</span>
                <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
              </el-option>
            </el-select>
            <InputError :message="form.errors.currency" />
          </div>

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
                  <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                    title="¿Continuar?" @confirm="deleteProduct(index)">
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
                    <el-option v-for="item in rawMaterials" :key="item.id"
                      :label="item.name + ' (' + item.part_number + ')'" :value="item.id" />
                  </el-select>
                </div>
                <p v-if="productSelectedObj" class="text-sm ml-5">
                  Stock actual: {{ productSelectedObj?.storages[0].quantity }} unidades
                </p>

                <div class="mt-2">
                  <IconInput v-model="form.quantity" inputPlaceholder="Cantidad *" inputType="number" inputStep="0.01">
                    <el-tooltip content="Cantidad requerida del producto seleccionado" placement="top">
                      #
                    </el-tooltip>
                  </IconInput>
                  <InputError :message="form.errors.quantity" />
                </div>
                <div class="mt-2">
                  <label class="flex items-center space-x-1 text-xs mb-1 ml-1">
                    <span>Stock a favor</span>
                    <el-tooltip content="Piezas pendientes en la fabrica del proveedor" placement="top">
                      <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                      </div>
                    </el-tooltip>
                  </label>
                  <el-input v-model="form.additional_stock" placeholder="Piezas pendientes (opcional)"
                    :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    :parser="(value) => value.replace(/\D/g, '')" />
                  <InputError :message="form.errors.additional_stock" />
                </div>
                <div class="grid grid-cols-2 gap-x-2 gap-y-1 mt-2">
                  <div>
                    <label class="flex items-center space-x-1 text-xs mb-1 ml-1">Piezas en avión</label>
                    <el-input v-model="form.plane_stock" placeholder="Cantidad (opcional)"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\D/g, '')" />
                    <InputError :message="form.errors.plane_stock" />
                  </div>
                  <div>
                    <label class="flex items-center space-x-1 text-xs mb-1 ml-1">Piezas en barco</label>
                    <el-input v-model="form.ship_stock" placeholder="Cantidad (opcional)"
                      :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                      :parser="(value) => value.replace(/\D/g, '')" />
                    <InputError :message="form.errors.ship_stock" />
                  </div>
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
            <el-checkbox @change="handleCheckShowPrices" v-model="form.show_prices" label="Mostar precios" size="small" />
            <el-checkbox v-model="form.is_iva_included" label="Calcular IVA y mostrarlo" size="small" :disabled="!form.show_prices" />
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
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Crear orden
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
      notes: null,
      is_spanish_template: 1,
      expected_delivery_date: null,
      is_iva_included: false,
      show_prices: false,
      supplier_id: null,
      contact_id: null,
      bank_information: null,
      additional_stock: null,
      plane_stock: null,
      ship_stock: null, 
      currency: null, 
      products: [],
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
      currencies: [
        { value: "$MXN", label: "MXN" },
        { value: "$USD", label: "USD" },
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
    Checkbox,
    Link
  },
  props: {
    suppliers: Array,
  },
  methods: {
    saveBankObj() {
      this.form.bank_information = this.currentSupplier.banks[this.bank_index];
    },
    handleCheckShowPrices() {
      if (!this.form.show_prices) {
        this.form.is_iva_included = false;
      }
    },
    store() {
      this.form.post(route("purchases.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Órden de compra creada",
            type: "success",
          });

          this.form.reset();
        },
      });
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
        additional_stock: this.form.additional_stock,
        plane_stock: this.form.plane_stock,
        ship_stock: this.form.ship_stock,
      };

      if (this.editProductIndex !== null) {
        this.form.products[this.editProductIndex] = product;
        this.editProductIndex = null;
      } else {
        this.form.products.push(product);
      }
      this.productSelectedId = null;
      this.form.quantity = null;
      this.form.additional_stock = null;
      this.form.plane_stock = null;
      this.form.ship_stock = null;
    },
    deleteProduct(index) {
      this.form.products.splice(index, 1);
    },
    editProduct(index) {
      const product = { ...this.form.products[index] };
      this.productSelectedId = product.id;
      this.form.quantity = product.quantity;
      this.form.additional_stock = product.additional_stock;
      this.form.plane_stock = product.plane_stock;
      this.form.ship_stock = product.ship_stock;
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
      //resetea el formulario cuando se cambia de proveedor
      this.form.products = [];
      this.form.bank_information = null;
      this.form.contact_id = null;
      this.bank_index = null;
      try {
        const response = await axios.get(route('suppliers.fetch-supplier', this.form.supplier_id));

        if (response.status === 200) {
          this.currentSupplier = response.data.item;
          this.fetchSupplierItems();
        }
      } catch (error) {
        console.log(error);
      } finally {
      }
    },
    getProductSelected() {
      this.productSelectedObj = this.rawMaterials.find(item => item.id === this.productSelectedId);
    }
  },
};
</script>
