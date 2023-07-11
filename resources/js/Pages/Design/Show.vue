<template>
  <div>
    <AppLayoutNoHeader title="Órdenes de diseño">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de diseño</label>
          <Link
            :href="route('designs.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
          >
            <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <el-select
            v-model="selectedDesign"
            clearable
            filterable
            placeholder="Buscar órden de diseño"
            no-data-text="No hay órdenes registradas"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in designs.data"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Marcar orden como iniciada" placement="top">
                <button class="rounded-lg bg-primary text-sm text-white p-2">
                  Iniciar
                </button>
            </el-tooltip>

          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ selectedDesign.name }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div
        class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 "
      >
        <div class="flex">
          <p
            @click="tabs = 1"
            :class="
              tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base"
          >
            Datos de la órden
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="tabs = 2"
            :class="
              tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
          >
            Modificaciones
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc]">
        <div
            class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center"
        >
            <span class="text-gray-500">ID</span>
            <span>{{ currentDesign?.id }}</span>
            <span class="text-gray-500 my-2">Solicitada por</span>
            <span>{{ currentDesign?.user.name }}</span>
            <span class="text-gray-500 my-2">Solicitada el</span>
            <span>{{ currentDesign?.created_at }}</span>
            <span class="text-gray-500 my-2">Autorizado</span>
            <span>{{ currentDesign?.authorized_user_name }} {{ currentDesign?.authorized_at }}</span>
            <span class="text-gray-500 my-2">Diseñador(a)</span>
            <span>{{ currentDesign?.designer.name }}</span>
            <span class="text-gray-500 my-2">Estimado de entrega</span>
            <span>{{ currentDesign?.expected_end_at }}</span>
            <span class="text-gray-500 my-2">Estatus</span>
            <span>{{ currentDesign?.status }}</span>
        </div>

        <div
            class="grid grid-cols-2 text-left p-4 md:ml-10 items-center"
        >

            <span class="text-gray-500">Cliente</span>
            <span>{{ currentDesign?.company_branch_name }}</span>
            <span class="text-gray-500 my-2">Contacto</span>
            <span>{{ currentDesign?.contact_name }}</span>
            <span class="text-gray-500 my-2">Nombre del diseño</span>
            <span>{{ currentDesign?.name }}</span>
            <span class="text-gray-500 my-2">Clasificación</span>
            <span>{{ currentDesign?.design_type_id }}</span>

            <p class="text-secondary col-span-2 mt-7">Especificaciones</p>

            <span class="text-gray-500 my-2">Requerimientos</span>
            <span>{{ currentDesign?.design_data}}</span>

            

            <span class="text-gray-500 my-2">Unidad</span>
            <span>{{ currentDesign?.mesure_unit }}</span>
            <span class="text-gray-500 my-2">Dimensiones</span>
            <span>{{ currentDesign?.dimensions }}</span>
            <span class="text-gray-500 my-2">Pantones</span>
            <span>{{ currentDesign?.pantones }}</span>
            <span class="text-gray-500 my-2">Imágenes</span>
            <span>{{ currentDesign?.pantones }}</span>

        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 modifications starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <p class="text-secondary">Productos Ordenados</p>
        <div class="grid lg:grid-cols-3 md:grid-cols-2 mt-7 gap-10">
          <RawMaterialCard
            v-for="product in currentDesign?.products"
            :key="product.id"
            :raw_material="product"
          />
        </div>
      </div>

      <!-- ------------- tab 2 modifications ends ------------ -->

    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedDesign: "",
      currentDesign: null,
      tabs: 1,
    };
  },
  props: {
    design: Object,
    designs: Array,
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    Link,
    CancelButton,
    PrimaryButton,

  },
  methods: {
    
  },

  watch: {
    selectedDesign(newVal) {
      this.currentDesign = this.designs.data.find((item) => item.id == newVal);
    },
  },

  mounted() {
    this.selectedDesign = this.design.id;
  },
};
</script>