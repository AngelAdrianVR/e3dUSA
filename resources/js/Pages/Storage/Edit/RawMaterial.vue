<template>
  <div>
    <AppLayout title="Editar Materia prima">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar "{{ raw_material.data.name }}"
            </h2>
          </div>
        </div>
      </template>
      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 grid grid-cols-2 gap-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md">
          <div>
            <InputLabel value="Tipo de producto*" />
            <el-select @change="generatePartNumber" v-model="productType" placeholder="Selecciona">
              <el-option v-for="item in productTypes" :key="item.value" :label="item.label" :value="item.value">
                <span style="float: left">{{ item.label }}</span>
                <span style="float: right; color: #cccccc; font-size: 13px">
                  {{ item.value }}
                </span>
              </el-option>
            </el-select>
          </div>
          <div>
            <InputLabel>
              <div class="flex items-center justify-between">
                <span>Marca del producto *</span>
                <button class="text-primary mr-2" type="button" @click="showCreateBrandModal = true">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>
                </button>
              </div>
            </InputLabel>
            <el-select v-model="form.brand" @change="generatePartNumber" filterable clearable placeholder="Selecciona"
              no-data-text="No hay unidades de medida registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in brands" :key="item.id" :label="item.name" :value="item.name" />
            </el-select>
          </div>
          <div>
            <InputLabel value="Nombre*" />
            <el-input v-model="form.name" placeholder="Ej. Rollo de estireno blanco" />
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <InputLabel value="Número de parte" />
            <el-input v-model="form.part_number" placeholder="Llenado automático" disabled />
            <InputError :message="form.errors.part_number" />
          </div>
          <div>
            <InputLabel value="Cantidad mínima" />
            <el-input v-model="form.min_quantity" type="text"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 50" />
            <InputError :message="form.errors.min_quantity" />
          </div>
          <div>
            <InputLabel value="Cantidad máxima" />
            <el-input v-model="form.max_quantity" type="text"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 500" />
            <InputError :message="form.errors.max_quantity" />
          </div>
          <div>
            <InputLabel value="Stock de apertura o actual" />
            <el-input v-model="form.initial_stock" type="text"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 290" />
            <InputError :message="form.errors.initial_stock" />
          </div>
          <div>
            <InputLabel value="Costo de compra a proveedor*" />
            <el-input v-model="form.cost" type="text"
              :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
              :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 19.90" />
            <InputError :message="form.errors.cost" />
          </div>
          <div>
            <InputLabel value="Unidad de medida" />
            <el-select v-model="form.measure_unit" clearable placeholder="Selecciona"
              no-data-text="No hay unidades de medida registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="(item, index) in mesureUnits" :key="index" :label="item" :value="item" />
            </el-select>
            <InputError :message="form.errors.measure_unit" />
          </div>
          <div>
            <InputLabel value="Material*" />
            <el-input v-model="form.material" placeholder="Ej. flex chrome, solid chrome, aluminio, etc." />
            <InputError :message="form.errors.material" />
          </div>
          <label class="flex items-center w-1/3">
            <Checkbox @change="form.large = null; form.height = null" v-model:checked="form.is_circular" name="remember"
              class="bg-transparent" />
            <span class="ml-2 text-sm">Es circular</span>
          </label>
          <div class="col-span-full grid grid-cols-3 gap-3">
            <div>
              <InputLabel value="Ancho(mm)*" />
              <el-input v-model="form.width" type="text"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 25.5" />
              <InputError :message="form.errors.width" />
            </div>
            <div v-if="!form.is_circular">
              <InputLabel value="Largo(mm)*" />
              <el-input v-model="form.large" type="text"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 96" />
              <InputError :message="form.errors.large" />
            </div>
            <div v-if="!form.is_circular">
              <InputLabel value="Alto(mm)*" />
              <el-input v-model="form.height" type="text"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 10" />
              <InputError :message="form.errors.height" />
            </div>
            <div v-if="form.is_circular">
              <InputLabel value="Diámetro(mm)*" />
              <el-input v-model="form.diameter" type="text"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 43.8" />
              <InputError :message="form.errors.diameter" />
            </div>
          </div>
          <div class="col-span-full flex items-center justify-center space-x-4">
            <figure v-if="!form.is_circular" class="w-48 dark:bg-gray-400 rounded-lg p-2">
              <img class="mx-auto" src="@/../../public/images/paralelepipedo.png" alt="">
            </figure>
            <figure v-else class="w-32">
              <img src="@/../../public/images/diameter.png" alt="">
            </figure>
          </div>
          <div>
            <InputLabel value="Ubicaión*" />
            <el-input v-model="form.location" placeholder="Ej. S-10" />
            <InputError :message="form.errors.location" />
          </div>
          <div class="col-span-full">
            <InputLabel value="Descripción del producto" />
            <el-input v-model="form.description" :rows="3" maxlength="800" placeholder="..." show-word-limit
              type="textarea" />
            <InputError :message="form.errors.description" />
          </div>
          <div class="col-span-full">
            <InputLabel value="Imagen" />
            <el-upload action="#" list-type="picture-card" :auto-upload="false" :on-change="handleChange"
              class="col-span-full" :on-remove="handleRemoveImage" v-model:file-list="fileList" ref="upload">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-7 text-black">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
              <template #file="{ file }">
                <div>
                  <img class="el-upload-list__item-thumbnail" :src="file.url" alt="" />
                  <span class="el-upload-list__item-actions">
                    <span class="el-upload-list__item-preview" @click="handlePictureCardPreview(file)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6" />
                      </svg>
                    </span>
                    <span class="el-upload-list__item-delete" @click="handleDownloadImage(file)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                      </svg>
                    </span>
                    <span class="el-upload-list__item-delete" @click="handleRemoveImage(file)">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                      </svg>
                    </span>
                  </span>
                </div>
              </template>
            </el-upload>
            <el-dialog v-model="dialogVisible">
              <img class="mx-auto" w-full :src="dialogImageUrl" alt="Vista previa" />
            </el-dialog>
          </div>
          <div class="col-span-full text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Guardar cambios
            </PrimaryButton>
          </div>
        </div>
      </form>
    </AppLayout>
    <DialogModal :show="showCreateBrandModal" @close="showCreateBrandModal = false" maxWidth="lg">
      <template #title>
        Crear nueva marca
      </template>
      <template #content>
        <InputLabel value="Nombre *" />
        <el-input v-model="brandForm.name" placeholder="Escribe el nombre de la marca" />
        <InputError :message="brandForm.errors.name" />
        <label class="flex items-center mt-2 w-1/3 col-span-full">
          <Checkbox v-model:checked="brandForm.is_luxury" class="bg-transparent" />
          <span class="ml-2 text-sm">Es marca de lujo</span>
        </label>
      </template>
      <template #footer>
        <CancelButton @click="showCreateBrandModal = false" :disabled="brandForm.processing">Cancelar</CancelButton>
        <PrimaryButton @click="storeBrand()" class="bg-primary" :disabled="brandForm.processing">Crear</PrimaryButton>
      </template>
    </DialogModal>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";

export default {
  data() {
    const form = useForm({
      name: this.raw_material.data.name,
      brand: this.raw_material.data.brand,
      material: this.raw_material.data.material,
      width: this.raw_material.data.width,
      large: this.raw_material.data.large,
      height: this.raw_material.data.height,
      diameter: this.raw_material.data.diameter,
      part_number: this.raw_material.data.part_number,
      measure_unit: this.raw_material.data.measure_unit,
      min_quantity: this.raw_material.data.min_quantity,
      max_quantity: this.raw_material.data.max_quantity,
      cost: this.raw_material.data.cost,
      initial_stock: this.raw_material.data.storages[0]?.quantity,
      location: this.raw_material.data.storages[0]?.location,
      type: 'materia-prima',
      description: this.raw_material.data.description,
      // features: this.raw_material.data.features,
      media: [],
      is_circular: this.raw_material.data.diameter ? true : false,
    });

    const brandForm = useForm({
      name: null,
      is_luxury: false,
    });

    return {
      form,
      brandForm,
      // upload de E+
      dialogVisible: false,
      dialogImageUrl: '',
      showCreateBrandModal: false,
      fileList: [],
      mesureUnits: [
        'Pieza(s)',
        'Paquete(s)',
        'Litro(s)',
        'Par(es)',
        'kilogramo(s)',
        'Metro(s)',
        'Rollo(s)',
        'Galon(es)',
        'Cubeta(s)',
        'Bote(s)',
      ],
      productType: this.raw_material.data.part_number.split('-')[0],
      // brand: this.raw_material.data.part_number.split('-')[1],
      productTypes: [
        {
          label: 'Porta-placa',
          value: 'PP',
        },
        {
          label: 'Emblema',
          value: 'EM',
        },
        {
          label: 'Llavero',
          value: 'LL',
        },
        {
          label: 'Parasol',
          value: 'PS',
        },
        {
          label: 'Tapete',
          value: 'TP',
        },
        {
          label: 'Porta-documento',
          value: 'PD',
        },
        {
          label: 'Manta',
          value: 'MT',
        },
        {
          label: 'Carpeta',
          value: 'CP',
        },
        {
          label: 'Separador',
          value: 'SP',
        },
        {
          label: 'Termo',
          value: 'TM',
        },
        {
          label: 'Placa de estireno',
          value: 'PE',
        },
        {
          label: 'Etiqueta',
          value: 'ET',
        },
        {
          label: 'Overlay',
          value: 'OV',
        },
        {
          label: 'Accesorio para llavero',
          value: 'ALL',
        },
        {
          label: 'Pin',
          value: 'PI',
        },
        {
          label: 'Prenda',
          value: 'PR',
        },
        {
          label: 'Botella',
          value: 'BT',
        },
        {
          label: 'Hielera',
          value: 'HI',
        },
        {
          label: 'Funda para auto',
          value: 'FA',
        },
        {
          label: 'Perfumero',
          value: 'PF',
        },
        {
          label: 'Funda para llave',
          value: 'FLL',
        },
        {
          label: 'Bocina',
          value: 'BC',
        },
        {
          label: 'Impresión',
          value: 'IM',
        },
        {
          label: "Paraguas",
          value: "PG",
        },
      ],
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Checkbox,
    Back,
    Link,
    InputLabel,
    CancelButton,
    DialogModal,
  },
  props: {
    raw_material: Object,
    media: Array,
    brands: Array,
  },
  methods: {
    update() {
      if (this.form.media !== null) {
        this.form.post(route("raw-materials.update-with-media", this.raw_material.data), {
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
        this.form.put(route("raw-materials.update", this.raw_material.data), {
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
    storeBrand() {
      this.brandForm.post(route('brands.store'), {
        onSuccess: () => {
          this.$notify({
            title: 'Éxito',
            message: 'Marca agregada correctamente',
            type: 'success'
          });

          this.form.brand = this.brandForm.name;
          this.brandForm.reset();
          this.showCreateBrandModal = false;
          this.generatePartNumber();
        }
      });
    },
    handleChange(file, fileList) {
      this.form.media = fileList.map(item => item.raw); // Actualiza form.media con los archivos
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },
    handleDownloadImage(file) {
      // Verifica si el archivo tiene una URL válida
      if (file.url) {
        // Crea un enlace temporal para descargar el archivo
        const link = document.createElement('a');
        link.href = file.url;
        link.download = file.name || 'download'; // nombre del archivo a descargar
        link.click();
      } else {
        console.error('No hay URL disponible para descargar el archivo.');
      }
    },
    handleRemoveImage(file, fileList) {
      this.$confirm('¿Estás seguro de eliminar este archivo?', 'Confirmar', {
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        type: 'warning'
      }).then(() => {

        // Remover de form.media
        const mediaIndex = this.form.media.indexOf(file.raw);
        if (mediaIndex !== -1) {
          this.form.media.splice(mediaIndex, 1); // Elimina el archivo de form.media
        }
        // Remover del componente
        const mediaUploadIndex = this.fileList.indexOf(file);
        if (mediaUploadIndex !== -1) {
          this.fileList.splice(mediaUploadIndex, 1); // Elimina el archivo de form.media
        }

      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Eliminación cancelada'
        });
      });
    },
    generatePartNumber() {
      const partNumber = this.productType + '-' + this.form.brand?.toUpperCase().substr(0, 3) + '-';
      this.form.part_number = partNumber;
    },
  },
  mounted() {
    // Transformar media para que sea compatible con el-upload
    this.fileList = this.media.map(item => ({
      name: item.file_name, // Nombre del archivo
      url: item.original_url, // URL de la imagen
      id: item.id // ID del archivo
    }));
  }
};
</script>
