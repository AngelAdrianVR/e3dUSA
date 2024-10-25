<template>
  <AppLayout title="Registro de calidad">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear registro de calidad</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg lg:p-9  p-4 shadow-md space-y-4 mx-3">
        <h1 class="font-bold text-lg text-left">Registro de supervisión de producción</h1>
        <div>
            <InputLabel value="Folio de producción *" class="ml-2" />
            <div class="flex items-center space-x-4">
                <el-select @change="getProduction" class="lg:w-1/2" v-model="form.production_id" filterable
                placeholder="Orden de producción" no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in productions.filter(production => production.percentage != 100)" :key="item" :label="item.folio" :value="item.id" />
                </el-select>
            </div>
            <InputError :message="form.errors.production_id" />
        </div>
        <section v-if="productionObj">
            <p class="font-semibold text-sm mb-2">Información de orden de producción</p>
            <ul>
                <li class="text-sm ml-2 my-2" v-for="product in productionObj.catalog_product_company_sales" :key="product">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle text-[5px] mr-2"></i>
                        {{ product.catalog_product_company?.catalog_product?.name }}
                        <i class="fa-solid fa-arrow-right mx-2 text-xs"></i>
                        {{ product.quantity }} Piezas.
                    </div>
                </li>
            </ul>
            <div class="mt-14" v-for="(product, index) in productionObj.catalog_product_company_sales" :key="index">
                <p class="text-secondary text-sm">{{ 'Producto ' + (index + 1) + ': ' + product.catalog_product_company?.catalog_product?.name }}</p>
                <div class="flex space-x-3">
                    <div class="w-full">
                        <InputLabel value="Estado general del progreso *" class="ml-2" />
                        <el-select class="lg:w-full" v-model="form.product_inspection[index].status" filterable
                            placeholder="Seleccione" no-data-text="No hay opciones registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in statuses" :key="item" :label="item" :value="item" />
                        </el-select>
                        <!-- <InputError :message="form.errors?.product_inspection[index]?.status" /> -->
                    </div>
                    <div class="w-full">
                        <InputLabel value="Avance del trabajo *" class="ml-2" />
                        <el-select class="lg:w-full" v-model="form.product_inspection[index].progress" filterable
                            placeholder="Seleccione" no-data-text="No hay opciones registradas"
                            no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in progresses" :key="item" :label="item" :value="item" />
                        </el-select>
                        <!-- <InputError :message="form.errors.product_inspection[index]?.progress" /> -->
                    </div>
                </div>
                <div class="flex space-x-3 mt-2">
                    <div class="w-full">
                        <InputLabel value="Piezas planificadas" class="ml-2" />
                        <input disabled :value="product.quantity" type="number" step="0.1" class="input text-gray-500/80" />
                    </div>
                    <div class="w-full">
                        <InputLabel value="Piezas producidas hasta el momento *" class="ml-2" />
                        <input v-model="form.product_inspection[index].pieces" type="number" :max="product.quantity" step="0.1" class="input" />
                        <!-- <InputError :message="form.errors.product_inspection[index]?.pieces" /> -->
                    </div>
                </div>
                <div v-if="form.product_inspection[index].stop_explanation == 'Detenido'" class="w-full mt-2">
                    <InputLabel value="Motivo de detención *" class="ml-2" />
                    <textarea v-model="form.product_inspection[index].stop_explanation" class="textarea" rows="2"></textarea>
                    <!-- <InputError :message="form.errors.product_inspection[index]?.stop_explanation" /> -->
                </div>

                <h2 class="font-bold text-left text-sm my-2">Incidencias o problemas detectados </h2>
                <div class="flex space-x-3">
                    <div class="w-full">
                        <InputLabel value="Descripción" class="ml-2" />
                        <input v-model="form.product_inspection[index].problem_description" type="text" class="input" />
                        <!-- <InputError :message="form.errors.product_inspection[index]?.problem_description" /> -->
                    </div>
                    <div class="w-full">
                        <InputLabel value="Acciones correctivas" class="ml-2" />
                        <input v-model="form.product_inspection[index].corrective_actions" type="text" class="input" />
                        <!-- <InputError :message="form.errors.product_inspection[index]?.corrective_actions" /> -->
                    </div>
                </div>
                <div class="w-full mt-2">
                    <InputLabel value="Observaciones o comentarios generales" class="ml-2" />
                    <textarea v-model="form.product_inspection[index].notes" class="textarea" rows="2"></textarea>
                    <!-- <InputError :message="form.errors.product_inspection[index]?.notes" /> -->
                </div>
            </div>
                <div class="ml-2 mt-2 col-span-full flex">
                    <FileUploader @files-selected="form.media = $event" />
                </div>
            <div class="w-full mt-5">
                <InputLabel value="Estatus general de la orden de producción *" class="ml-2" />
                <el-select class="lg:w-full" v-model="form.status" filterable
                    placeholder="Seleccione" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in statuses" :key="item" :label="item" :value="item" />
                </el-select>
                <InputError :message="form.errors?.status" />
            </div>
        </section>
        <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                Guardar
            </PrimaryButton>
          </div>
        </div>
      </form>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    const form = useForm({
      production_id: null,
      status: null,
      product_inspection: [],
      media: []
    });
    return {
        form,
        productionObj: null,
        statuses:[
            'Sin iniciar',
            'En proceso',
            'Detenido',
            'Terminada',
        ],
        progresses:[
            'Cumple con el plan',
            'Desviación del plan',
        ],
    }
},
components:{
AppLayout,
PrimaryButton,
ThirthButton,
CancelButton,
FileUploader,
InputError,
InputLabel,
Back,
Link
},
props:{
productions: Array,
},
methods:{
    store() {
      this.form.post(route("qualities.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha registrado la inspección de producción",
            type: "success",
          });
        },
      });
    },
   async getProduction() {
    try {
        const response = await axios.get(route('qualities.get-production', this.form.production_id));
        if (response.status === 200) {
          this.productionObj = response.data.item;

            // Limpiar el array antes de agregar nuevas instancias
                this.form.product_inspection = [];

            // Iterar sobre los productos y agregar instancias al array
            this.productionObj.catalog_product_company_sales.forEach((item) => {
                this.form.product_inspection.push({
                    name: item.catalog_product_company?.catalog_product?.name,
                    status: null,
                    progress: null,
                    total_pieces: parseInt(item.quantity),
                    pieces: null,
                    stop_explanation: null,
                    problem_description: null,
                    corrective_actions: null,
                    notes: null,
                    // media: []
                });
            });
        }
        
    } catch (error) {
        console.log(error);

        this.$notify({
            title: "Error de servidor",
            message: "No se encontró la orden de producción",
            type: "error",
          });
    }
    },
}
};
</script>
