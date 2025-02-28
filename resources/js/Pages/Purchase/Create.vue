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
        <div class="md:w-1/2 md:mx-auto grid grid-cols-2 gap-3 mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md">
          <div class="col-span-full">
            <el-radio-group v-model="form.is_spanish_template" size="small">
              <el-radio :value="1">Plantilla en español</el-radio>
              <el-radio :value="0">Plantilla en inglés</el-radio>
            </el-radio-group>
          </div>
          <div>
            <InputLabel value="Provedor*" />
            <el-select @change="fetchSupplier" v-model="form.supplier_id" clearable filterable placeholder="Selecciona">
              <el-option v-for="item in suppliers" :key="item.id"
                :label="item.nickname ? item.nickname + ' - ' + item.name : item.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.supplier_id" />
          </div>
          <div>
            <InputLabel value="Información bancaria*" />
            <el-select v-model="form.bank_information" clearable filterable placeholder="Selecciona">
              <el-option v-for="(item, index) in currentSupplier?.banks" :key="item.id"
                :label="item['beneficiary_name'] + '-' + item['bank_name']" :value="index" />
            </el-select>
            <InputError :message="form.errors.bank_information" />
          </div>
          <div>
            <InputLabel value="Contacto*" />
            <el-select v-model="form.contact_id" clearable filterable placeholder="Selecciona">
              <el-option v-for="item in currentSupplier?.contacts" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.contact_id" />
          </div>
          <div>
            <InputLabel value="Moneda*" />
            <el-select v-model="form.currency" placeholder="Selecciona" :fit-input-width="true">
              <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                <span style="float: left">{{ item.label }}</span>
                <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
              </el-option>
            </el-select>
            <InputError :message="form.errors.currency" />
          </div>
          <!-- --------------- Order info ----------------------------- -->
          <el-divider content-position="left" class="col-span-full">Datos de la órden</el-divider>
          <div>
            <InputLabel value="Fecha de entrega esperada*" />
            <el-date-picker v-model="form.expected_delivery_date" type="date" placeholder="fecha de entrega esperada"
              class="col-span-full" :disabled-date="disabledDate" />
            <InputError :message="form.errors.expected_delivery_date" />
          </div>
          <!-- --------------- Products to buy ----------------------------- -->
          <el-divider content-position="left" class="col-span-full">Productos</el-divider>
          <div class="col-span-full">
            <el-radio-group v-model="form.is_for_production" size="small">
              <el-radio :value="1">Para producción</el-radio>
              <el-radio :value="0">Para muestras</el-radio>
            </el-radio-group>
          </div>
          <div>
            <InputLabel value="Producto*" />
            <el-select v-model="productSelectedId" @change="getProductSelected" clearable filterable
              placeholder="Selecciona">
              <el-option v-for="item in rawMaterials" :key="item.id" :label="item.name + ' (' + item.part_number + ')'"
                :value="item.id" />
            </el-select>
          </div>
          <p v-if="productSelectedObj" class="text-sm ml-5">
            Stock actual: {{ productSelectedObj?.storages[0].quantity }} unidades
          </p>
          <div>
            <InputLabel value="Cantidad*" />
            <el-input v-model="form.quantity" type="text"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 300" />
            <InputError :message="form.errors.quantity" />
          </div>
          <div v-if="form.is_for_production">
            <InputLabel>
              <div class="flex items-center space-x-2">
                <span>Stock a favor</span>
                <el-tooltip placement="top">
                  <template #content>
                    <p>Piezas pendientes en la fabrica del proveedor</p>
                  </template>
                  <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                  </div>
                </el-tooltip>
              </div>
            </InputLabel>
            <el-input v-model="form.additional_stock" placeholder="(opcional)"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/\D/g, '')" />
            <InputError :message="form.errors.additional_stock" />
          </div>
          <div v-if="form.is_for_production">
            <InputLabel value="Piezas en avión" />
            <el-input v-model="form.plane_stock" placeholder="(opcional)"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/\D/g, '')" />
            <InputError :message="form.errors.plane_stock" />
          </div>
          <div v-if="form.is_for_production">
            <InputLabel value="Piezas en barco" />
            <el-input v-model="form.ship_stock" placeholder="(opcional)"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/\D/g, '')" />
            <InputError :message="form.errors.ship_stock" />
          </div>
          <figure v-if="productSelectedObj?.media[0] != null && productSelectedId" class="rounded-md">
            <img :src="productSelectedObj?.media[0]?.original_url" class="rounded-md object-cover h-32">
          </figure>
          <div class="col-span-full">
            <SecondaryButton :disabled="!form.quantity" @click="addProduct" type="button">
              {{
                editProductIndex !== null
                  ? "Actualizar producto"
                  : "Agregar producto a la órden"
              }}
            </SecondaryButton>
          </div>
          <ol v-if="form.products.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1 divide-y-[1px]">
            <template v-for="(item, index) in form.products" :key="index">
              <li class="flex justify-between items-center border-[#999999] py-1">
                <p class="text-xs">
                  <span class="text-primary">{{ index + 1 }}.
                    <span class="text-gray-700">
                      {{ item.name }} - {{ item.quantity + ' unidades' }}</span></span>
                </p>
                <div class="flex space-x-2 items-center">
                  <el-tag v-if="editProductIndex == index" @close="editProductIndex = null; resetProductForm()"
                    closable>
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
          <InputError :message="form.errors.products" />
          <p class="text-xs text-red-600" v-if="productValidation">
            No puedes agregar dos veces el mísmo producto
          </p>
          <div>
            <el-checkbox @change="handleCheckShowPrices" v-model="form.show_prices" label="Mostrar precios"
              size="small" />
            <el-checkbox v-model="form.is_iva_included" label="Calcular IVA y mostrarlo" size="small"
              :disabled="!form.show_prices" />
          </div>
          <div class="col-span-full">
            <InputLabel value="Notas" />
            <el-input v-model="form.notes" :rows="3" maxlength="800" placeholder="..." show-word-limit
              type="textarea" />
            <InputError :message="form.errors.notes" />
          </div>
          <div class="col-span-full flex justify-end">
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
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      notes: null,
      is_spanish_template: 1,
      is_for_production: 1,
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
    Link,
    InputLabel,
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
      this.resetProductForm();
    },
    resetProductForm() {
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
