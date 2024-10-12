<template>
  <AppLayout title="Registrar tarifa">
    <template #header>
      <div class="flex justify-between">
        <Back />
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Registrar tarifa</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="store">
      <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg py-4 px-9 shadow-md space-y-4">
        <h2>Detalles del producto</h2>

        <section class="grid grid-cols-3 gap-5">
          <article class="col-span-2">
            <div class="mb-3">
              <div class="flex justify-between">
                <InputLabel value="Producto*" />
                <InputLabel v-if="catalogProductSelected" :value="'N. Parte:' + catalogProductSelected?.part_number" />
              </div>
              <el-select @change="fetchCatalogProductInfo()" class="w-full" filterable v-model="form.catalog_product_id"
                placeholder="Seleccione" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in catalog_products" :key="item" :label="item.name" :value="item.id">
                  <p class="flex items-center justify-between md:w-[610px]">
                    <span>{{ item.name }}</span>
                    <span class="text-[10px] text-gray99">({{ item.part_number }})</span>
                  </p>
                </el-option>
              </el-select>
              <InputError :message="form.errors.catalog_product_id" />
            </div>
            <div>
              <InputLabel value="Cantidad*" />
              <el-input v-model="form.quantity" placeholder="Ej.300">
                <template #append>unidades</template>
              </el-input>
              <InputError :message="form.errors.quantity" />
            </div>

            <label class="inline-flex items-center mt-4">
              <Checkbox v-model:checked="form.is_fragile" class="bg-transparent disabled:border-gray-400" />
              <span class="ml-2 text-xs">El producto es frágil</span>
            </label>
          </article>

          <figure class="border border-[#999999] flex items-center justify-center rounded-lg h-40">
            <div v-if="loading" class="flex justify-center">
              <i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i>
            </div>

            <div class="h-full" v-else>
              <p v-if="!form.catalog_product_id" class="text-sm mx-4 my-5"><i class="fa-solid fa-arrow-left"></i>
                Selecciona
                el producto para visualizar la imagen</p>
              <img v-if="catalogProductSelected" class="object-contain h-full"
                :src="catalogProductSelected.media[0].original_url" alt="Producto de catálogo">
            </div>
          </figure>
        </section>

        <!-- Detalles de empaque -->
        <section class="pt-7">
          <article class="flex justify-between mb-3">
            <div>
              <h2>Detalles de empaque</h2>

              <div class="my-3">
                <InputLabel value="Número de cajas*" class="mb-1 ml-3" />
                <el-input-number v-model="form.boxes_amount" :min="1" :max="20" />
              </div>

              <label v-if="form.boxes_amount > 1" class="inline-flex items-center">
                <Checkbox @change="handleChecked()" v-model:checked="form.all_boxes_are_same"
                  class="bg-transparent disabled:border-gray-400" />
                <span class="ml-2 text-xs">Todas las cajas tienen el mismo peso, tamaño y cantidad.</span>
              </label>
            </div>

            <div class="text-center pt-5">
              <figure class="w-48 mx-auto">
                <img src="@/../../public/images/paralelepipedo.png" alt="">
              </figure>
            </div>
          </article>

          <!-- tarjeta de cajas -->
          <article v-for="(packageCard, index) in form.boxes" :key="packageCard" class="rounded-lg py-5 px-2"
            :class="index % 2 === 0 ? 'bg-[#C0C0C0]' : 'bg-[#9F9FA2]'">
            <div class="mb-3">
              <div class="flex justify-between">
                <InputLabel value="Caja*" />
                <button v-if="index == 0" @click="showBoxFormModal = true" type="button"
                  class="rounded-full border border-primary size-4 flex items-center justify-center mr-3">
                  <i class="fa-solid fa-plus text-primary text-[9px]"></i>
                </button>
              </div>
              <el-select class="w-full" filterable v-model="form.boxes[index].box_id"
                @change="fillBoxSizes(index, form.boxes[index].box_id)" placeholder="Seleccione"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">

                <!-- Primera opción: Caja personalizada -->
                <el-option :value="0" label="Caja personalizada" />

                <el-option v-for="item in box_types" :key="item" :label="item.name" :value="item.id">
                  <p class="flex items-center justify-between">
                    <span>{{ item.name }}</span>
                    <span class="text-[10px] text-gray99">
                      ({{
                        'L-' + item.length + 'cm x ' + 'An-' + item.width + 'cm x' +
                        'Al-' + item.height + 'cm'
                      }})</span>
                  </p>
                </el-option>
              </el-select>
              <InputError :message="form.errors[`boxes.${index}.box_id`]" />
            </div>

            <section class="grid grid-cols-3 gap-3">
              <div>
                <InputLabel value="Largo*" />
                <el-input v-model="form.boxes[index].length" placeholder="Largo">
                  <template #append>cm</template>
                </el-input>
                <InputError :message="form.errors[`boxes.${index}.length`]" />
              </div>
              <div>
                <InputLabel value="Ancho*" />
                <el-input v-model="form.boxes[index].width" placeholder="Ancho">
                  <template #append>cm</template>
                </el-input>
                <InputError :message="form.errors[`boxes.${index}.width`]" />
              </div>
              <div>
                <InputLabel value="Alto*" />
                <el-input v-model="form.boxes[index].height" placeholder="Alto">
                  <template #append>cm</template>
                </el-input>
                <InputError :message="form.errors[`boxes.${index}.height`]" />
              </div>
              <div>
                <InputLabel value="Peso*" />
                <el-input v-model="form.boxes[index].weight" placeholder="Peso">
                  <template #append>Kg</template>
                </el-input>
                <InputError :message="form.errors[`boxes.${index}.weight`]" />
              </div>
              <div>
                <InputLabel value="Cantidad*" />
                <el-input v-model="form.boxes[index].quantity" placeholder="Cantidad">
                  <template #append>unidades</template>
                </el-input>
                <InputError :message="form.errors[`boxes.${index}.quantity`]" />
              </div>
              <div>
                <InputLabel value="Costo" />
                <el-input v-model="form.boxes[index].cost" placeholder="Tarifa de la caja"
                  :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                  <template #prepend>$</template>
                </el-input>
                <InputError :message="form.errors[`boxes.${index}.cost`]" />
              </div>
            </section>
          </article>
        </section>
        <div class="flex space-x-3 mt-9 mx-3 justify-end">
          <PrimaryButton :disabled="form.processing">
            <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
            Guardar tarifa
          </PrimaryButton>
        </div>
      </div>
    </form>

    <!-- Box form -->
    <DialogModal :show="showBoxFormModal" @close="showBoxFormModal = false">
      <template #title> Agregar caja </template>
      <template #content>
        <form>
          <div>
            <InputLabel value="Nombre*" />
            <el-input v-model="boxForm.name" placeholder="Nombre" />
            <InputError :message="boxForm.errors.name" />
          </div>

          <div class="grid grid-cols-3 gap-3 mt-3">
            <div>
              <InputLabel value="Largo*" />
              <el-input v-model="boxForm.length" placeholder="Largo">
                <template #append>cm</template>
              </el-input>
              <InputError :message="boxForm.errors.length" />
            </div>
            <div>
              <InputLabel value="Ancho*" />
              <el-input v-model="boxForm.width" placeholder="Ancho">
                <template #append>cm</template>
              </el-input>
              <InputError :message="boxForm.errors.width" />
            </div>
            <div>
              <InputLabel value="Alto*" />
              <el-input v-model="boxForm.height" placeholder="Alto">
                <template #append>cm</template>
              </el-input>
              <InputError :message="boxForm.errors.height" />
            </div>
          </div>

          <div class="text-center pt-5">
            <figure class="w-48 mx-auto">
              <img src="@/../../public/images/paralelepipedo.png" alt="">
            </figure>
          </div>
        </form>
      </template>
      <template #footer>
        <CancelButton @click="showBoxFormModal = false" :disabled="boxForm.processing">Cancelar</CancelButton>
        <PrimaryButton @click="storeBox()" :disabled="boxForm.processing">Crear</PrimaryButton>
      </template>
    </DialogModal>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import InputError from "@/Components/InputError.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
  data() {
    const form = useForm({
      catalog_product_id: null,
      quantity: null,
      all_boxes_are_same: false,
      is_fragile: false,
      boxes_amount: 1,
      boxes: [
        {
          box_id: null,
          name: null,
          length: null, //largo
          width: null, //ancho
          height: null, //alto
          weight: null, //peso
          quantity: null, //cantidad de unidades
          cost: null, //tarifa por la caja
        },
      ],
      routePage: this.route, //para retornar a la ruta en que fue accionado
      idRoute: this.idRoute //para retornar a la ruta en que fue accionado
    });

    const boxForm = useForm({
      name: null,
      length: null,
      height: null,
      width: null,
    });

    return {
      form,
      boxForm,
      loading: false,
      showBoxFormModal: false,
      catalogProductSelected: null,
    };
  },
  components: {
    SecondaryButton,
    PrimaryButton,
    ThirthButton,
    CancelButton,
    DialogModal,
    InputError,
    InputLabel,
    AppLayout,
    Checkbox,
    Back,
    Link
  },
  props: {
    catalog_product_id: Number,
    route: String,
    idRoute: Number,
    catalog_products: Array,
    box_types: Array,
    dismiss_window: {
      type: Boolean,
      default: false
    }
  },
  watch: {
    // Observa cambios en boxes_amount
    'form.boxes_amount'(newAmount) {
      //si esta checkeado que todas las cajas son iguales no se crea otro objeto
      if (!this.form.all_boxes_are_same) {
        const currentAmount = this.form.boxes.length;
        if (newAmount > currentAmount) {
          // Agregar cajas si boxes_amount es mayor que la longitud actual
          const boxesToAdd = newAmount - currentAmount;
          for (let i = 0; i < boxesToAdd; i++) {
            this.form.boxes.push({
              box_id: null,
              name: null,
              length: null,
              width: null,
              height: null,
              weight: null,
              quantity: null,
              cost: null,
            });
          }
        } else if (newAmount < currentAmount) {
          // Eliminar cajas si boxes_amount es menor que la longitud actual
          this.form.boxes.splice(newAmount, currentAmount - newAmount);
        }
      }
    },
    // Observa cambios en all_boxes_are_same
    'form.all_boxes_are_same'(newValue) {
      if (newValue) {
        // Si el checkbox es marcado (true), igualar todas las cajas al primer objeto
        const firstBox = this.form.boxes[0];
        this.form.boxes = this.form.boxes.map(() => ({ ...firstBox }));
      }
    },
  },
  methods: {
    store() {
      this.form.post(route("shipping-rates.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "",
            type: "success",
          });

          if (this.dismiss_window) {
            window.close();
          }
        },
      });
    },
    storeBox() {
      this.boxForm.post(route("boxes.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "",
            type: "success",
          });

          this.showBoxFormModal = false;
          this.form.reset();
        },
      });
    },
    fillBoxSizes(index, box_id) {
      //si el valor de la caja es 0 no guarda las dimensiones porque es personalizada
      if (!box_id) {
        return
      }
      //busca la caja seleccionada en el arreglo de todas las cajas
      const box = this.box_types.find(item => item.id === box_id);

      //llena los campos de tamaños del paquete
      this.form.boxes[index].name = box.name;
      this.form.boxes[index].length = box.length;
      this.form.boxes[index].width = box.width;
      this.form.boxes[index].height = box.height;
    },
    handleChecked() {
      if (this.form.all_boxes_are_same) {
        //se deja solo 1 objeto en el arreglo si todas las cajas son iguales
        this.form.boxes.splice(1);
      } else {
        const currentAmount = this.form.boxes.length;
        if (this.form.boxes_amount > currentAmount) {
          // Agregar cajas si boxes_amount es mayor que la longitud actual
          const boxesToAdd = this.form.boxes_amount - currentAmount;
          for (let i = 0; i < boxesToAdd; i++) {
            this.form.boxes.push({
              box_id: null,
              name: null,
              length: null,
              width: null,
              height: null,
              weight: null,
              quantity: null,
              cost: null,
            });
          }
        }
      }
    },
    async fetchCatalogProductInfo() {
      this.loading = true;
      try {
        const response = await axios.get(route('shipping-rates.fetch-catalog-product-info', this.form.catalog_product_id));
        if (response.status === 200) {
          this.catalogProductSelected = response.data.item;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loading = false;
      }
    }
  },
  mounted() {
    if (this.catalog_product_id) {
      this.form.catalog_product_id = parseInt(this.catalog_product_id);
      this.fetchCatalogProductInfo();
    }
  }
};
</script>
