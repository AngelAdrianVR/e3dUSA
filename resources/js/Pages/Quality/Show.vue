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
          <el-select @change="getQuality" v-model="qualitySelectedId" clearable filterable placeholder="Buscar supervisión"
            no-data-text="No hay inspecciones registradas" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in qualities" :key="item.id" :label="item.folio" :value="item.id" />
          </el-select>
        </div>

        <div class="flex items-center space-x-2">
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar registro de calidad')"
            content="Editar registro de calidad" placement="top">
            <Link :href="route('qualities.edit', qualitySelectedId)">
            <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
              <i class="fa-solid fa-pen text-sm"></i>
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
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
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
        <div class="flex space-x-7">
            <p>Folio de producción</p>
            <p>{{ quality.data.folio }}</p>
        </div>
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
