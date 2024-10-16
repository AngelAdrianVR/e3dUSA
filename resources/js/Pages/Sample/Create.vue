<template>
  <div>
    <AppLayout title="Crear Seguimiento de muestra">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Registrar muestra</h2>
          </div>
        </div>
      </template>
      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-3">
          <el-radio-group v-model="isProductRegistered" size="small">
            <el-radio :value="1">Producto registrado en sistema</el-radio>
            <el-radio :value="0">Producto no registrado en sistema o kit de varios productos</el-radio>
          </el-radio-group>
          <div v-if="isProductRegistered">
            <InputLabel value="Producto de catálogo*" />
            <el-select v-model="form.catalog_product_id" clearable filterable placeholder="Buscar producto de catálogo"
              no-data-text="No hay producto registrado" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in catalog_products" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.catalog_product_id" />
          </div>
          <div v-else>
            <div>
              <InputLabel>
                <div class="flex items-center space-x-2">
                  <span>Poducto(s) que lleva la muestra</span>
                  <el-tooltip placement="top">
                    <template #content>
                      <p>
                        Ingresa manualmente el nombre de los productos <br>
                        que se van en la muestra y también agrega las imagenes
                      </p>
                    </template>
                    <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                </div>
              </InputLabel>
              <div class="flex space-x-2 mb-1">
                <el-input v-model="newProduct" placeholder="Nombre de la muestra" />
                <SecondaryButton @click="addProduct" type="button">
                  Agregar
                  <i class="fa-solid fa-arrow-down ml-2"></i>
                </SecondaryButton>
              </div>
              <el-select v-model="form.products" multiple clearable placeholder="Productos"
                no-data-text="Agrega primero un producto">
                <el-option v-for="product in form.products" :key="product" :label="product"
                  :value="product"></el-option>
              </el-select>
              <InputError :message="form.errors.products" />
            </div>
            <div>
              <div class="grid grid-cols-2 lg:grid-cols-3 gap-10">
                <InputFilePreview v-for="(file, index) in form.media" :key="index"
                  :canDelete="index == (form.media.length - 2)" @imagen="saveImage" @cleared="handleCleared(index)"
                  class="p-2" />
              </div>
            </div>
          </div>
          <div>
            <InputLabel value="Cliente*" />
            <el-select @change="selectCurrentCompanyBranch()" v-model="form.company_branch_id" clearable filterable
              placeholder="Buscar Cliente" no-data-text="No hay clientes registrados"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in company_branches" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.company_branch_id" />
          </div>
          <div>
            <InputLabel value="Contacto*" />
            <el-select v-model="form.contact_id" clearable filterable placeholder="Buscar Contacto"
              no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in currentCompanyBranch?.contacts" :key="item.id" :label="item.name"
                :value="item.id" />
            </el-select>
            <InputError :message="form.errors.contact_id" />
          </div>
          <div class="md:grid md:grid-cols-2 gap-3">
            <div>
              <InputLabel value="Fecha de envío de muestra*" />
              <el-date-picker v-model="form.sent_at" type="date" placeholder="Fecha de envío de muestra * "
                format="YYYY/MM/DD" value-format="YYYY-MM-DD" class="!w-full" />
              <InputError :message="form.errors.sent_at" />
            </div>
            <div v-if="form.will_back">
              <InputLabel value="Fecha tentativa de devolución*" />
              <el-date-picker v-model="form.devolution_date" type="date" placeholder="Fecha de devolución*"
                format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDateBefore" class="!w-full" />
              <InputError :message="form.errors.devolution_date" />
            </div>
          </div>
          <div class="md:grid md:grid-cols-2 gap-2">
            <div>
              <InputLabel value="Fecha tentativa de devolución*" />
              <el-input v-model="form.name" placeholder="Nombre de la muestra" />
              <InputError :message="form.errors.name" />
            </div>
            <div>
              <InputLabel value="Cantidad de muestras enviadas*" />
              <el-input v-model="form.quantity" type="text"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 5">
              </el-input>
              <InputError :message="form.errors.quantity" />
            </div>
          </div>
          <div>
            <InputLabel value="Comentarios / notas" />
            <el-input v-model="form.comments" rows="3" maxlength="800" placeholder="..." show-word-limit
              type="textarea" />
            <InputError :message="form.errors.comments" />
          </div>
          <div class="pt-5 flex justify-end">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Registrar muestra
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
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      quantity: null,
      catalog_product_id: null,
      company_branch_id: null,
      contact_id: null,
      sent_at: null,
      products: [],
      media: [null],
      comments: null,
      will_back: true,
      devolution_date: null,
    });

    return {
      form,
      currentCompanyBranch: null,
      newProduct: null,
      isProductRegistered: 1,
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    InputFilePreview,
    PrimaryButton,
    InputError,
    InputLabel,
    Back,
    Link
  },
  props: {
    catalog_products: Array,
    company_branches: Array
  },
  methods: {
    store() {
      this.form.post(route("samples.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Muestra registrada",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    selectCurrentCompanyBranch() {
      this.currentCompanyBranch = this.company_branches.find(item => item.id == this.form.company_branch_id);
    },
    // disabledDateAfter(time) {
    //   const today = new Date();
    //   today.setHours(0, 0, 0, 0);
    //   return time.getTime() > today.getTime();
    // },
    disabledDateBefore(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    addProduct() {
      if (this.newProduct.trim() !== '') {
        this.form.products.push(this.newProduct);
        // this.products.push(this.newProduct);
        this.newProduct = '';
      }
    },
    saveImage(image) {
      const currentIndex = this.form.media.length - 1;
      this.form.media[currentIndex] = image;
      this.form.media.push(null);
    },
    handleCleared(index) {
      // Eliminar el componente y su informacion correspondiente cuando se borra la imagen
      this.form.media.splice(index, 1);
    }
  },
};
</script>
