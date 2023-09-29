<template>
  <div>
    <AppLayout title="Seguimiento de muestras - crear">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('samples.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Registrar muestra</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">

        <p class="text-xs text-secondary">En caso de estar registrado en catalogo de productos Seleccionarlo, si es nuevo producto dejar esa opcion en blanco</p>
            <div class="w-full flex items-center">
            <el-tooltip content="Producto de catálogo *" placement="top">
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
            <el-tooltip content="Fecha de envío de muestra *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
            </el-tooltip>
            <el-date-picker v-model="form.sent_at" type="date" placeholder="Fecha de envío de muestra * "
              format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
            <InputError :message="form.errors.sent_at" />
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
                    <div v-if="!form.catalog_product_id" class="col-span-full mt-2">
                        <div class="flex items-center">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                <el-tooltip content="Imagen del producto" placement="top">
                                    <i class="fa-solid fa-images"></i>
                                </el-tooltip>
                            </span>
                            <input @input="form.media = $event.target.files[0]" class="input h-12 rounded-lg
                            file:mr-4 file:py-1 file:px-2
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary file:text-white
                            file:cursor-pointer
                            hover:file:bg-red-600" aria-describedby="file_input_help" id="file_input" type="file">
                        </div>
                        <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">SVG, PNG, JPG o
                            GIF (MAX. 4 MB).</p>
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";

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
      media: null,
      comments: null,
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
    PrimaryButton,
    Link,
    InputError,
    IconInput,
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
    selectCurrentCompanyBranch(){
      this.currentCompanyBranch = this.company_branches.find(item => item.id == this.form.company_branch_id);
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
    addProduct() {
            if (this.newProduct.trim() !== '') {
                this.form.products.push(this.newProduct);
                // this.products.push(this.newProduct);
                this.newProduct = '';
            }
        }
  },
};
</script>
