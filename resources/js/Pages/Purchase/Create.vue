<template>
  <div>
    <AppLayout title="Órdenes de compra - Crear">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('purchases.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Crear órden de compra
            </h2>
          </div>
        </div>
      </template>
      {{ productSelected }}
      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div class="flex items-center">
            <el-tooltip content="Selecciona un provedor" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"
              >
                <i class="fa-solid fa-boxes-packing"></i>
              </span>
            </el-tooltip>
            <el-select
              @change="selectedSupplier"
              v-model="form.supplier_id"
              class="mt-2"
              clearable
              placeholder="Selecciona proveedor"
            >
              <el-option
                v-for="item in suppliers"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </div>

          <div class="flex items-center">
            <el-tooltip
              content="Selecciona la información bancaria"
              placement="top"
            >
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"
              >
                <i class="fa-solid fa-money-check-dollar"></i>
              </span>
            </el-tooltip>
            <el-select
              v-model="form.bank_information"
              class="mt-2"
              clearable
              placeholder="Selecciona la información bancaria"
            >
              <el-option
                v-for="item in currentSupplier?.banks"
                :key="item.id"
                :label="item['beneficiary_name'] + '-' + item['bank_name']"
                :value="item"
              />
            </el-select>
          </div>

          <div class="flex items-center pb-2">
            <el-tooltip content="Selecciona un contacto" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"
              >
                <i class="fa-solid fa-address-card"></i>
              </span>
            </el-tooltip>
            <el-select
              v-model="form.contact_id"
              class="mt-2"
              clearable
              placeholder="Selecciona un contacto"
            >
              <el-option
                v-for="item in currentSupplier?.contacts"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </div>

          <!-- --------------- Order info ----------------------------- -->
          <el-divider content-position="left">Datos de la órden</el-divider>
          <div class="pb-4 pt-3">
            <div class="flex items-center">
              <el-tooltip content="fecha de entrega esperada" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"
                >
                  <i class="fa-solid fa-calendar"></i>
                </span>
              </el-tooltip>
              <el-date-picker
                v-model="form.expected_delivery_date"
                type="date"
                placeholder="fecha de entrega esperada"
                :disabled-date="disabledDate"
              />
            </div>
            <InputError :message="form.errors.expected_delivery_date" />
          </div>

          <!-- --------------- Products to buy ----------------------------- -->
          <el-divider content-position="left">Productos</el-divider>
          <ol
            v-if="form.products.length"
            class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1"
          >
            <template v-for="(item, index) in form.products" :key="index">
              <li class="flex justify-between items-center">
                <p class="text-sm">
                  <span class="text-primary"
                    >{{ index + 1 }}.
                    <span class="text-gray-700">
                      {{ item.product }} - {{ item.quantity }}</span
                    ></span
                  >
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
          <div class="space-y-3 bg-[#b8b7b7] rounded-lg p-5">
            <div class="md:grid gap-6 mb-6 grid-cols-2">
              <div class="flex items-center mb-3">
              <el-tooltip content="Selecciona un contacto" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"
              >
                <i class="fa-brands fa-product-hunt"></i>
              </span>
            </el-tooltip>
                <el-select
                  v-model="productSelected"
                  class="mt-2"
                  clearable
                  placeholder="Selecciona un producto"
                >
                  <el-option
                    v-for="item in raw_materials.data"
                    :key="item.id"
                    :label="item.name"
                    :value="item"
                  />
                </el-select>
              </div>
              <div>
                <IconInput
                  v-model="form.quantity"
                  inputPlaceholder="Cantidad *"
                  inputType="number"
                >
                  <el-tooltip
                    content="Cantidad requerida del producto seleccionado"
                    placement="top"
                  >
                    #
                  </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.quantity" />
              </div>
            </div>
            <SecondaryButton
              :disabled="!productSelected || !form.quantity"
              @click="addProduct"
              type="button"
            >
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
          <div class="flex">
            <span
              class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
            >
              <el-tooltip content="Notas" placement="top"> ... </el-tooltip>
            </span>
            <textarea
              v-model="form.notes"
              class="textarea"
              autocomplete="off"
              placeholder="Notas"
            ></textarea>
            <InputError :message="form.errors.notes" />
          </div>
          <div class="mt-2 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Crear órden
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
      notes: null,
      expected_delivery_date: null,
      is_iva_included: null,
      supplier_id: null,
      contact_id: null,
      bank_information: null,
      // currentSupplier: null,
      products: [],
    });

    return {
      form,
      currentSupplier: null,
      productSelected: null,
      editProductIndex: null,
      productValidation: false,
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
    suppliers: Array,
    raw_materials: Array,
    contacts: Array,
  },
  methods: {
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
        id: this.productSelected.id,
        product: this.productSelected.name,
        part_number: this.productSelected.part_number,
        description: this.productSelected.description,
        measure_unit: this.productSelected.measure_unit,
        min_quantity: this.productSelected.min_quantity,
        max_quantity: this.productSelected.max_quantity,
        cost: this.productSelected.cost,
        features: this.productSelected.features,
        quantity: this.form.quantity,
        media: this.productSelected.media,
      };

      // this.productValidation = this.form.products.some(function (item) {
      //   return item.product === this.productSelected;
      // });

      // if(!this.productValidation){
      if (this.editProductIndex !== null) {
        this.form.products[this.editProductIndex] = product;
        this.editProductIndex = null;
      } else {
        this.form.products.push(product);
      }
      this.productSelected = null;
      this.form.quantity = null;
      // }
    },

    deleteProduct(index) {
      this.form.products.splice(index, 1);
    },

    editProduct(index) {
      const product = { ...this.form.products[index] };
      this.productSelected = product.product;
      this.form.quantity = product.quantity;
      this.editProductIndex = index;
    },

    selectedSupplier() {
      this.currentSupplier = this.suppliers.find(
        (item) => (item.id = this.form.supplier_id)
      );
    },
  },
};
</script>
