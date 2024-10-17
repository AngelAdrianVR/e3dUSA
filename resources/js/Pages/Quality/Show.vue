<template>
  <AppLayoutNoHeader title="supervisión de calidad">
    <div class="flex flex-col md:mx-3 md:my-7 space-y-3 m-1">
      <div class="flex justify-between">
        <label class="text-lg">supervisión de calidad</label>
        <Link :href="route('qualities.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
        <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>

        <div class="flex justify-between">
        <div class="md:w-1/3 mr-2">
          <el-select @change="getQuality" v-model="qualitySelectedId" filterable placeholder="Buscar supervisión"
            no-data-text="No hay inspecciones registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in qualities" :key="item.id" :label="item.folio" :value="item.id" />
          </el-select>
        </div>

        <div class="flex items-center space-x-2">
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar registro de calidad')"
            content="Editar registro de calidad" placement="top">
            <Link :href="route('qualities.edit', qualitySelectedId)">
              <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </button>
            </Link>
          </el-tooltip>
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Crear registro de calidad')"
            content="Crear registro de calidad" placement="top">
            <Link :href="route('qualities.create')">
            <PrimaryButton class="rounded-md">Nueva supervisión</PrimaryButton>
            </Link>
          </el-tooltip>


          <Dropdown align="right" width="48">
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center justify-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar registro de calidad')"
              @click="showConfirmModal = true" as="button">
                Eliminar
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>
      <div class="flex items-center justify-center space-x-5 mb-4">
        <p class="text-center font-bold text-lg">
          {{ quality?.folio }}
        </p>
      </div>
      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 overflow-x-auto">
        <div class="flex items-center justify-center">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="h-10 w-40 lg:w-auto p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base text-center">
            Información general
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Historial de folio
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
        </div>
      </div>
    </div>
    <!-- ------------- tabs section ends ------------- -->

     <!-- ------------- Informacion general Starts 1 ------------- -->
    <div v-if="tabs == 1" class="text-sm md:px-12 px-1">
        <h1 class="font-bold text-lg">Supervisición de producción</h1>
        <p class="font-bold text-base mt-7 mb-3">Información general</p>
        <div class="flex space-x-7">
            <p>Nombre del supervisor</p>
            <p>{{ quality.data.supervisor?.name }}</p>
        </div>
        <div class="flex space-x-7">
            <p>Número de inspección</p>
            <p>{{ quality.data.id }}</p>
        </div>
        <div class="flex space-x-[90px]">
            <p>Fecha y hora</p>
            <p>{{ quality.data.created_at }}</p>
        </div>
        <div class="flex space-x-12">
            <p>Folio de producción</p>
            <a class="text-secondary hover:underline" :href="route('productions.show', quality.data.production.id)"><p>{{ quality.data.production.folio }}</p></a>
        </div>
        <div class="flex flex-col mt-7">
            <p>Productos y cantidades solicitadas</p>
            <ul>
              <li class="text-sm ml-2 my-2" v-for="product in quality.data.production.catalogProductCompanySales" :key="product">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle text-[5px] mr-2"></i>
                    {{ product.catalog_product_company?.catalog_product?.name }}
                    <i class="fa-solid fa-arrow-right mx-2 text-xs"></i>
                    {{ product.quantity }} Piezas.
                </div>
              </li>
            </ul>
        </div>
        <div class="text-sm mb-9 mt-3" v-for="(inspection, index) in quality.data.product_inspection" :key="inspection">
          <h2 class="text-secondary mb-1">Producto {{ (index + 1) + '. ' + inspection.name }}</h2>
          <div class="flex space-x-12">
            <p class="text-gray-500">Estado del progreso</p>
            <p>{{ inspection.status }}</p>
          </div>
          <div class="flex space-x-[56px]">
            <p class="text-gray-500">Avance del trabajo</p>
            <p>{{ inspection.progress }}</p>
          </div>
          <div class="flex space-x-[55px]">
            <p class="text-gray-500">Piezas planificadas</p>
            <p>{{ inspection?.total_pieces }} unidades</p>
          </div>
          <div class="flex space-x-12">
            <p class="text-gray-500">Piezas completadas</p>
            <p>{{ inspection?.pieces }} unidades</p>
          </div>
          <div v-if="inspection.stop_explanation" class="flex space-x-4">
            <p class="text-gray-500">Explicación de detención</p>
            <p>{{ inspection?.stop_explanation }}</p>
          </div>
          <h3 class="font-bold my-3">Incidencias o problemas detectados</h3>
          <div v-if="inspection.problem_description" class="flex space-x-24">
            <p class="text-gray-500">Descripción</p>
            <p>{{ inspection?.problem_description }}</p>
          </div>
          <div v-if="inspection.problem_description" class="flex space-x-10">
            <p class="text-gray-500">Acciones correctivas</p>
            <p>{{ inspection?.corrective_actions }}</p>
          </div>
          <h3 class="font-bold my-3">Observaciones y comentarios generales</h3>
          <div class="flex space-x-32">
            <p class="text-gray-500">Notas</p>
            <p>{{ inspection?.notes ?? '--' }}</p>
          </div>
        </div>
          <h3 class="font-bold my-3">Archivos adjuntos</h3>
          <div class="mb-9" v-if="quality.data.media?.length">
          <li v-for="file in quality.data.media" :key="file"
            class="flex items-center justify-between col-span-full">
            <a :href="file.original_url" target="_blank" class="flex items-center">
              <i :class="getFileTypeIcon(file.file_name)"></i>
              <span class="ml-2">{{ file.file_name }}</span>
            </a>
          </li>
        </div>
        <p class="text-sm text-gray-400 mb-9" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos</p>
    </div>




    <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
      <template #title> Eliminar supervición </template>
      <template #content> ¿Continuar con la eliminación? </template>
      <template #footer>
        <div>
          <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
          <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
        </div>
      </template>
    </ConfirmationModal>
<!-- {{quality}} -->
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    return {
        qualitySelectedId: "",
        tabs: 1,
        showConfirmModal: false,
    }
},
components:{
AppLayoutNoHeader,
Dropdown,
DropdownLink,
PrimaryButton,
SecondaryButton,
ConfirmationModal,
CancelButton,
Link,
},
props:{
quality: Object,
qualities: Array,
},
methods:{
    getQuality() {
        this.$inertia.get(route('qualities.show',this.qualitySelectedId));
    },
    deleteItem() {
     this.$inertia.delete(route('qualities.destroy', this.qualitySelectedId));
      window.location.reload();
    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
},
// watch: {
//     qualitySelectedId(newVal) {
//     //   this.currentOportunity = this.oportunities.data.find((item) => item.id == newVal);
//       this.qualitySelectedId = this.quality.id;
//     },
//   },
  mounted() {
    this.qualitySelectedId = this.quality.data.id;
  },
};
</script>
