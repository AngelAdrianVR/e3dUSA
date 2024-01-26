<template>
  <div>
    <AppLayout title="Seguimiento de muestras - Editar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar muestra "{{ sample.name }}"</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">

        <p class="text-base rounded-lg p-3 border border-primary text-primary">Si el producto es nuevo y no está registrado en el catálogo, deja en blanco el primer campo</p>
            <div class="w-full flex items-center">
            <el-tooltip content="Producto de catálogo" placement="top">
            <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-box"></i>
              </span>
            </el-tooltip>
            <el-select v-model="form.catalog_product_id" clearable filterable placeholder="Buscar producto de catálogo"
                no-data-text="No hay producto registrado" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in catalog_products" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.catalog_product_id" />
            </div>

            <div class="w-full flex items-center">
            <el-tooltip content="Cliente *" placement="top">
                <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-building"></i>
                </span>
            </el-tooltip>
            <el-select @change="selectCurrentCompanyBranch()" v-model="form.company_branch_id" clearable filterable placeholder="Buscar Cliente"
                no-data-text="No hay clientes registrados" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in company_branches" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.company_branch_id" />
            </div>

            <div class="w-full flex items-center">
            <el-tooltip content="Contacto *" placement="top">
                <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-address-card"></i>
                </span>
            </el-tooltip>
            <el-select v-model="form.contact_id" clearable filterable placeholder="Buscar Contacto"
                no-data-text="No hay contactos registrados" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in currentCompanyBranch?.contacts" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
            <InputError :message="form.errors.contact_id" />
            </div>
            <div class="flex items-center">
              <div class="flex items-center">
                <el-tooltip content="Fecha de envío de muestra *" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-calendar"></i>
                  </span>
                </el-tooltip>
                <el-date-picker v-model="form.sent_at" type="date" placeholder="Fecha de envío de muestra * "
                  format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
                <InputError :message="form.errors.sent_at" />
              </div>
              <label v-if="form.catalog_product_id" class="flex items-center ml-3">
                <Checkbox class="bg-transparent" v-model:checked="form.will_back" />
                <span
                  class="ml-2 text-xs">
                  La muestra volverá
                </span>
              </label>
            </div>
            <div v-if="form.will_back" class="flex items-center">
                <el-tooltip content="Fecha tentativa de devolución *" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-calendar"></i>
                  </span>
                </el-tooltip>
                <el-date-picker v-model="form.devolution_date" type="date" placeholder="Fecha tentativa de devolución * "
                  format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDateBefore" />
                <InputError :message="form.errors.devolution_date" />
              </div>
          <div class="md:grid md:grid-cols-2 gap-2">
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Nombre de la muestra *" inputType="text">
              <el-tooltip content="Nombre de la muestra *" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
            <div>
              <IconInput v-model="form.quantity" inputPlaceholder="Cantidad de muestras enviadas *" inputType="number" inputStep="0.01">
                <el-tooltip content="Cantidad de muestras enviadas *" placement="top">
                  #
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.quantity" />
            </div>
            <div v-if="!form.catalog_product_id" class="col-span-full">
              <div class="flex space-x-2 mb-1">
                  <IconInput v-model="newProduct" inputPlaceholder="Poducto(s) que lleva la muestra" inputType="text"
                      class="w-full">
                      <el-tooltip content="Producto(s) que contiene la muestra (Si es kit agregar todos los productos, si es uno solo, escribirlo)" placement="top">
                          <i class="fa-solid fa-box"></i>
                      </el-tooltip>
                  </IconInput>
                  <SecondaryButton @click="addProduct" type="button">
                      Agregar
                      <i class="fa-solid fa-arrow-down ml-2"></i>
                  </SecondaryButton>
              </div>
              <el-select v-model="form.products" multiple clearable placeholder="Productos"
                  no-data-text="Agrega primero una caracteristica">
                  <el-option v-for="product in form.products" :key="product" :label="product"
                      :value="product"></el-option>
              </el-select>
              <InputError :message="form.errors.products" />
          </div>
          <div v-if="!form.catalog_product_id" class="grid grid-cols-2 lg:grid-cols-3 gap-4 col-span-full">
              <InputFilePreview v-for="(file,index) in form.media" :key="index" :canDelete="index == (form.media?.length - 2)"
                  @imagen="saveImage" @cleared="handleCleared(index)" :imageUrl="sample?.media[index]?.original_url" class="p-2" />
          </div>
            <div class="flex col-span-2">
              <el-tooltip content="Comentarios/notas" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                  <i class="fa-solid fa-grip-lines"></i>
                </span>
              </el-tooltip>
              <textarea v-model="form.comments" class="textarea w-full" autocomplete="off"
                placeholder="Comentarios/notas"></textarea>
              <InputError :message="form.errors.comments" />
            </div>

          </div>
          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Actualizar muestra
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
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: this.sample.name,
      quantity: this.sample.quantity,
      catalog_product_id: this.sample.catalog_product_id,
      company_branch_id: this.sample.company_branch_id,
      contact_id: this.sample.contact_id,
      sent_at: this.sample.sent_at,
      comments: this.sample.comments,
      products: this.sample.products,
      media: [...this.sample.media, null],
      will_back: !! this.sample.will_back,
      devolution_date: this.sample.devolution_date,
      media_edited: false,
    });

    return {
      form,
      currentCompanyBranch: null,
      newProduct: null,
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    InputFilePreview,
    PrimaryButton,
    InputError,
    IconInput,
    Checkbox,
    Back,
    Link
  },
  props: {
    catalog_products: Array,
    company_branches: Array,
    sample: Object
  },
  methods: {
    update() {
      if (this.form.media_edited === true) {
        this.form.post(route("samples.update-with-media", this.sample.id), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se actualizó correctamente",
              type: "success",
            });
          },
        });
      } else {
        this.form.put(route("samples.update", this.sample.id), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se actualizó correctamente",
              type: "success",
            });
          },
        });
      }
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
    selectCurrentCompanyBranch(){
      this.currentCompanyBranch = this.company_branches.find(item => item.id == this.form.company_branch_id);
    },
    addProduct() {
        if (this.newProduct.trim() !== '') {
            this.form.products.push(this.newProduct);
            // this.products.push(this.newProduct);
            this.newProduct = '';
        }
    },
    saveImage(image) {
        const currentIndex = this.form.media.length -1;
        this.form.media[currentIndex] = image;
        this.form.media.push(null);
        this.form.media_edited = true;
    },
    handleCleared(index) {
      // Eliminar el componente y su informacion correspondiente cuando se borra la imagen
      this.form.media.splice(index, 1);
      this.form.media_edited = true;
    },
  },
};
</script>
