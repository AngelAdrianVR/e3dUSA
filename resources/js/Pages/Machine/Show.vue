<template>
  <div>
    <AppLayoutNoHeader title="Ver maquinaria">
      <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Maquinaria</span>
        <Link :href="route('machines.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
        <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>
      <div class="flex justify-between mt-5 mx-14">
        <div class="md:w-1/3 mr-2">
          <el-select v-model="selectedMachine" clearable filterable placeholder="Buscar máquina"
            no-data-text="No hay maquinaria registrada" no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in machines.data" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </div>
        <div class="flex items-center space-x-2">
          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar maquinas') && currentMachine" content="Editar"
            placement="top">
            <Link :href="route('machines.edit', selectedMachine)">
            <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
              <i class="fa-solid fa-pen text-sm"></i>
            </button>
            </Link>
          </el-tooltip>
          <Dropdown align="right" width="48"
            v-if="$page.props.auth.user.permissions.includes('Crear maquinas') && $page.props.auth.user.permissions.includes('Crear mantenimientos') && $page.props.auth.user.permissions.includes('Crear refacciones') && $page.props.auth.user.permissions.includes('Eliminar maquinas') && currentMachine">
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink :href="route('machines.create')"
                v-if="$page.props.auth.user.permissions.includes('Crear maquinas')">
                Agregar nueva máquina
              </DropdownLink>
              <DropdownLink :href="route('maintenances.create', selectedMachine)"
                v-if="$page.props.auth.user.permissions.includes('Crear mantenimientos')">
                Registrar mantenimiento
              </DropdownLink>
              <DropdownLink :href="route('spare-parts.create', selectedMachine)"
                v-if="$page.props.auth.user.permissions.includes('Crear refacciones')">
                Registrar refacción
              </DropdownLink>
              <DropdownLink @click="uploadFilesModal = true" as="button"
                v-if="$page.props.auth.user.permissions.includes('Crear maquinas')">
                Subir archivos
              </DropdownLink>
              <DropdownLink @click="showConfirmModal = true" as="button"
                v-if="$page.props.auth.user.permissions.includes('Eliminar maquinas')">
                Eliminar
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>
      <div class="lg:grid grid-cols-3 mt-12 border-b-2">
        <div class="px-7">
          <h2 class="text-xl font-bold text-center mb-6">
            {{ currentMachine?.name }}
          </h2>
          <div class="flex items-center justify-center">
          <i :class="currentIndexMachine == 0 ? 'hidden' : 'block'" @click="previus" class="fa-solid fa-chevron-left mr-4 text-lg text-gray-600 cursor-pointer p-1 rounded-full"></i>
          <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
            :class="currentMachine?.media?.length ? 'bg-transparent' : 'bg-[#D9D9D9]'"
            class="w-full h-60 rounded-lg relative flex items-center justify-center">
            <!-- <el-image style="height: 100%; " :src="currentMachine?.media[0]?.original_url" fit="fit">
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image> -->
            <img class="object-contain h-60" :src="currentMachine?.media[0]?.original_url" alt="">
            <div v-if="imageHovered" @click="openImage(currentMachine?.media[0]?.original_url)"
              class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
              <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
            </div>
          </figure>
           <i :class="currentIndexMachine == machines.data.length - 1 ? 'hidden' : 'block'" @click="next" class="fa-solid fa-chevron-right ml-4 text-lg text-gray-600 cursor-pointer p-1 mb-2 rounded-full"></i>
          </div>
        </div>

        <!-- ------------------------Information panel tabs--------------------- -->
        <div class="col-span-2 border-2">
          <div class="border-b-2 px-7 py-3 flex">
            <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base leading-none">
              Información general
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
              Mantenimiento
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              " class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
              Refacciones
            </p>
          </div>

          <!-- --------------------- Tab 1 Información general starts------------------ -->
          <div v-if="tabs == 1" class="px-7 py-7 text-sm">
            <div class="flex space-x-2 mb-6">
              <p class="w-1/3 text-[#9A9A9A]">Fecha de adquisición</p>
              <p>{{ currentMachine?.aquisition_date }}</p>
            </div>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Número de serie</p>
              <p>{{ currentMachine?.serial_number }}</p>
            </div>
            <p class="mt-7 mb-3">Dimensiones</p>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Peso</p>
              <p>{{ currentMachine?.weight }} kg</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Ancho</p>
              <p>{{ currentMachine?.width }} cm</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Largo</p>
              <p>{{ currentMachine?.large }} cm</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Alto</p>
              <p>{{ currentMachine?.height }} cm</p>
            </div>
            <p class="mt-7 mb-3">Datos de compra</p>
            <div class="flex mb-2 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Proveedor</p>
              <p>{{ currentMachine?.supplier }}</p>
            </div>
            <div class="flex space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Costo</p>
              <p class="text-green-600">
                ${{ currentMachine?.cost_number_format }}
              </p>
            </div>
            <!-- <div class="flex mt-7 mb-2 items-center">
              <i class="fa-solid fa-paperclip mr-2"></i>
              <p class="text-[#9A9A9A]">Archivos adjuntos</p>
            </div> -->
            <!-- <a class="hover:underline text-primary hover:text-secondary" v-for="file in currentMachine?.files" :key="file.id" :href="file.original_url" target="_blank">{{file.file_name }}</a> -->
            <p class="text-secondary col-span-2 mb-2 mt-5">Archivos adjuntos</p>
            <div v-if="currentMachine?.files?.length">
              <li v-for="file in currentMachine?.files" :key="file"
                class="flex items-center justify-between col-span-full">
                <a :href="file.original_url" target="_blank" class="flex items-center">
                  <i :class="getFileTypeIcon(file.file_name)"></i>
                  <span class="ml-2">{{ file.file_name }}</span>
                </a>
              </li>
            </div>
            <p class="text-sm text-gray-400" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos adjuntos</p>
            <div class="flex flex-col">
          </div>  
          </div>
          <!-- --------------------- Tab 1 Información general ends------------------ -->

          <!-- --------------------- Tab 2 Mantenimient starts------------------ -->
          <div v-if="tabs == 2" class="px-7 py-7 text-sm overflow-scroll h-96">
            <table v-if="currentMachine?.maintenances?.length > 0" class="border-separate border-spacing-x-8">
              <thead>
                <tr>
                  <th class="pr-4">#</th>
                  <th class="px-4">Tipo de mantenimiento</th>
                  <th class="px-4">Fecha</th>
                  <th class="px-4">Costo</th>
                  <th class="px-4">Realizó</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(maintenance, index) in currentMachine?.maintenances" :key="index"
                  class="text-[#9A9A9A] cursor-pointer mb-4">
                  <td @click="openMaintenanceModal(maintenance, index)" class="text-left pb-3">
                    {{ index + 1 }}
                  </td>
                  <td @click="openMaintenanceModal(maintenance, index)" class="text-center pb-3">
                    {{ maintenance?.maintenance_type_id }}
                  </td>
                  <td @click="openMaintenanceModal(maintenance, index)" class="text-center pb-3">
                    {{ maintenance?.created_at }}
                  </td>
                  <td @click="openMaintenanceModal(maintenance, index)" class="text-center pb-3">
                    ${{ maintenance?.cost }}
                  </td>
                  <td @click="openMaintenanceModal(maintenance, index)" class="text-center pb-3">
                    {{ maintenance?.responsible }}
                  </td>
                  <td class="text-center pb-3">
                    <div>
                      <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FFFFFF"
                        title="¿Continuar?" @confirm="deleteRow(maintenance)">
                        <template #reference>
                          <i class="fa-solid fa-trash-can text-[#9A9A9A] hover:text-red-600"></i>
                        </template>
                      </el-popconfirm>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-else class="text-center text-xs text-gray-500">No hay mantenimientos registrados</p>
          </div>
          <!-- --------------------- Tab 2 Mantenimient ends------------------ -->

          <!-- --------------------- Tab 3 refacciones starts------------------ -->
          <div v-if="tabs == 3" class="px-7 py-7 text-sm overflow-scroll h-96">
            <table v-if="currentMachine?.spare_parts?.length > 0" class="border-separate border-spacing-x-8">
              <thead>
                <tr>
                  <th class="pr-4">#</th>
                  <th class="px-4">Refacción</th>
                  <th class="px-4">Cantidad</th>
                  <th class="px-4">Costo</th>
                  <th class="px-4">Adquirida el</th>
                  <th class="px-4">Ubicación en bodega</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(spare_part, index) in currentMachine?.spare_parts" :key="index"
                  class="text-[#9A9A9A] cursor-pointer mb-4">
                  <td @click="openSparePartModal(spare_part, index)" class="text-left pb-3">
                    {{ index + 1 }}
                  </td>
                  <td @click="openSparePartModal(spare_part, index)" class="text-center pb-3">
                    {{ spare_part?.name }}
                  </td>
                  <td @click="openSparePartModal(spare_part, index)" class="text-center pb-3">
                    {{ spare_part?.quantity }}
                  </td>
                  <td @click="openSparePartModal(spare_part, index)" class="text-center pb-3">
                    ${{ spare_part?.cost }}
                  </td>
                  <td @click="openSparePartModal(spare_part, index)" class="text-center pb-3">
                    {{ spare_part?.created_at }}
                  </td>
                  <td @click="openSparePartModal(spare_part, index)" class="text-center pb-3">
                    {{ spare_part?.location }}
                  </td>
                  <td class="text-center pb-3">
                    <div>
                      <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FFFFFF"
                        title="¿Continuar?" @confirm="deleteRow(spare_part)">
                        <template #reference>
                          <i class="fa-solid fa-trash-can text-[#9A9A9A] hover:text-red-600"></i>
                        </template>
                      </el-popconfirm>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-else class="text-center text-xs text-gray-500">No hay refacciones registradas</p>
          </div>
          <!-- --------------------- Tab 3 refacciones ends------------------ -->
        </div>
      </div>

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar máquina </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div>
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>

      <!-- -------------- maintenanceModal starts----------------------- -->
      <Modal :show="maintenanceModal || sparePartModal" @close="maintenanceModal = false, sparePartModal = false">
        <div class="mx-7 my-4 space-y-4 relative">
          <section v-if="maintenanceModal">
            <div class="flex justify-center mb-4">
              <h2 class="font-bold text-center mr-2">
                {{ currentMachine?.name }}
              </h2>
              <div @click="maintenanceModal = false"
                class="cursor-pointer w-5 h-5 rounded-full border-2 border-black flex items-center justify-center absolute top-0 right-0">
                <i class="fa-solid fa-xmark"></i>
              </div>
              <span class="text-[#9A9A9A] absolute left-0 top-0">
                # {{ maintenanceIndex }}</span>
            </div>

            <div class="grid grid-cols-2">
              <div class="flex flex-col pb-7">
                <p class="text-primary">Tipo de mantenimiento</p>
                <p class="text-[#9A9A9A]">
                  {{ selectedMaintenance.maintenance_type_id }}
                </p>
              </div>
              <div class="flex flex-col pb-7">
                <div class="flex">
                  <p class="text-primary">Fecha</p>
                  <el-tooltip content="Fecha en que se realizó el mantenimiento" placement="top">
                    <i class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center"></i>
                  </el-tooltip>
                </div>
                <p class="text-[#9A9A9A]">
                  {{ selectedMaintenance.created_at }}
                </p>
              </div>
              <div class="flex flex-col pb-7">
                <p class="text-primary">Costo (MXN)</p>
                <p class="text-[#9A9A9A]">${{ selectedMaintenance.cost }}</p>
              </div>
              <div class="flex flex-col pb-7">
                <div class="flex">
                  <p class="text-primary">Realizó</p>
                  <el-tooltip content="Persona o empresa que realizó el mantenimiento" placement="top">
                    <i class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center"></i>
                  </el-tooltip>
                </div>
                <p class="text-[#9A9A9A]">
                  {{ selectedMaintenance.responsible }}
                </p>
              </div>
            </div>

            <div class="grid grid-cols-3">
              <p class="text-primary">Descripción</p>
              <p class="text-[#9A9A9A] col-span-2 mb-7">
                {{ selectedMaintenance.actions }}
              </p>

              <p class="text-primary">Evidencia</p>
              <div v-for="(media, index) in selectedMaintenance.media" :key="index" class="text-secondary hover:underline col-span-2 inline-flex  space-y-1">
                <a :href="media.original_url" target="_blank" rel="noopener noreferrer">
                
                <img :src="media.original_url" alt="">
              </a>
              </div>
            </div>
          </section>
          <!-- -------------- maintenanceModal ends----------------------- -->

          <!-- --------------------------- sparepartmodal starts ------------------------------------ -->
          <section v-if="sparePartModal">
            <div class="flex justify-center mb-7">
              <h2 class="font-bold text-center mr-2">
                {{ currentMachine?.name }}
              </h2>
              <div @click="sparePartModal = false"
                class="cursor-pointer w-5 h-5 rounded-full border-2 border-black flex items-center justify-center absolute top-0 right-0">
                <i class="fa-solid fa-xmark"></i>
              </div>
              <span class="text-[#9A9A9A] absolute left-0 top-0">
                # {{ maintenanceIndex }}</span>
            </div>

            <div class="grid grid-cols-2">
              <div class="flex flex-col mb-7">
                <p class="text-primary">Refacción</p>
                <p class="text-[#9A9A9A]">
                  {{ selectedSparePart.name }}
                </p>
              </div>
              <div class="flex flex-col mb-7">
                <p class="text-primary">Adquirida el</p>
                <p class="text-[#9A9A9A]">
                  {{ selectedSparePart.created_at }}
                </p>
              </div>
              <div class="flex flex-col mb-7">
                <p class="text-primary">Costo unitario (MXN)</p>
                <p class="text-[#9A9A9A]">${{ selectedSparePart.cost }}</p>
              </div>
              <div class="flex flex-col mb-7">
                <p class="text-primary">Cantidad</p>
                <p class="text-[#9A9A9A]">
                  {{ selectedSparePart.quantity }}
                </p>
              </div>
              <div class="flex flex-col mb-7">
                <p class="text-primary">Proveedor</p>
                <p class="text-[#9A9A9A]">
                  {{ selectedSparePart.supplier }}
                </p>
              </div>
              <div class="flex flex-col mb-7">
                <p class="text-primary">Ubicación</p>
                <p class="text-[#9A9A9A]">
                  {{ selectedSparePart.location }}
                </p>
              </div>
            </div>

            <div class="grid grid-cols-3">
              <p class="text-primary">Descripción</p>
              <p class="text-[#9A9A9A] col-span-2 mb-7">
                {{ selectedSparePart.description }}
              </p>

              <p class="text-primary">Evidencia</p>
              <div v-for="(media, index) in selectedSparePart.media" :key="index" class="text-secondary hover:underline col-span-2 inline-flex space-y-1">
                <a :href="media.original_url" target="_blank" rel="noopener noreferrer">
                <img :src="media.original_url" alt="">
                </a>
              </div>
            </div>
          </section>
          <!-- --------------------------- sparepartmodal ends ------------------------------------ -->

          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <Link
              :href="maintenanceModal ? route('maintenances.edit', selectedMaintenance) : route('spare-parts.edit', selectedSparePart)">
            <PrimaryButton>Editar</PrimaryButton>
            </Link>
          </div>
        </div>
      </Modal>

      <DialogModal :show="uploadFilesModal" @close="uploadFilesModal = false">
        <template #title>
          <p class="text-center font-bold"> Subir archivos a {{ currentMachine?.name }}</p>
        </template>
        <template #content>
          <div>
            <form @submit.prevent="uploadFiles" ref="myUploadFilesForm">
              <div class="col-span-full">
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9"
              >
                <el-tooltip content="Archivos de la máquina (Manales, instructivos, etc)" placement="left">
                  <i class="fa-solid fa-object-group"></i>
                </el-tooltip>
              </span>
              <input
                @input="form.media = $event.target.files"
                class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                aria-describedby="file_input_help"
                id="file_input"
                type="file"
                multiple
              />
            </div>
            <p
              class="mt-1 text-xs text-right text-gray-500"
              id="file_input_help"
            >
              PDF, SVG, PNG, JPG o GIF (MAX. 4 MB).
            </p>
          </div>
            </form>
          </div>
        </template>
        <template #footer>
          <CancelButton @click="uploadFilesModal = false; form.reset()"
            :disabled="form.processing">Cancelar</CancelButton>
          <PrimaryButton @click="submitUploadFilesForm" :disabled="form.processing">Subir archivos
          </PrimaryButton>
        </template>
      </DialogModal>


    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import Modal from "@/Components/Modal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      media: null,
    });
    return {
      form,
      selectedMachine: "",
      currentMachine: null,
      imageHovered: false,
      showConfirmModal: false,
      maintenanceModal: false,
      sparePartModal: false,
      uploadFilesModal: false,
      maintenanceIndex: null,
      sparePartIndex: null,
      selectedMaintenance: null,
      selectedSparePart: null,
      currentIndexMachine: null,
      tabs: 1,
    };
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    CancelButton,
    Link,
    DropdownLink,
    Dropdown,
    ConfirmationModal,
    Modal,
    DialogModal
  },
  props: {
    machine: Object,
    machines: Array,
  },
  methods: {
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
    deleteRow(obj) {
      if (this.tabs == 2) {
        this.$inertia.delete(route("maintenances.destroy", obj));

        this.$notify({
          title: "Éxito",
          message: "Se eliminó el registro de mantenimiento",
          type: "success",
        });
      } else if (this.tabs == 3) {
        this.$inertia.delete(route("spare-parts.destroy", obj));

        this.$notify({
          title: "Éxito",
          message: "Se eliminó el registro de refacción",
          type: "success",
        });
      }
      window.location.reload();
    },

    openMaintenanceModal(maintenance, index) {
      this.selectedMaintenance = maintenance;
      this.maintenanceModal = true;
      this.maintenanceIndex = index + 1;
    },

    openSparePartModal(spare_part, index) {
      this.selectedSparePart = spare_part;
      this.sparePartModal = true;
      this.sparePartIndex = index + 1;
    },

    showOverlay() {
      this.imageHovered = true;
    },
    hideOverlay() {
      this.imageHovered = false;
    },
    openImage(url) {
      window.open(url, "_blank");
    },
    submitUploadFilesForm() {
      this.$refs.myUploadFilesForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    uploadFiles(){
      this.form.post(route("machines.upload-files", this.selectedMachine), {
        _method: 'put',
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Archivos subidos",
            type: "success",
          });

          this.form.reset();
          this.uploadFilesModal = false;
        },
      });
    },
    async clone() {
      try {
        const response = await axios.post(
          route("catalog-products.clone", {
            catalog_product_id: this.currentMachine?.id,
          })
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });
          console.log(response.data.newItem);
          this.machines.data.push(response.data.newItem);
          this.selectedMachine = response.data.newItem.id;
          this.currentMachine = response.data.newItem;
        } else {
          this.$notify({
            title: "Algo salió mal",
            message: response.data.message,
            type: "error",
          });
        }
      } catch (err) {
        this.$notify({
          title: "Algo salió mal",
          message: err.message,
          type: "error",
        });
        console.log(err);
      }
    },
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("machines.destroy", this.currentMachine?.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.machines.data.findIndex(
            (item) => item.id === this.currentMachine.id
          );
          if (index !== -1) {
            this.machines.data.splice(index, 1);
            this.selectedMachine = "";
          }
        } else {
          this.$notify({
            title: "Algo salió mal",
            message: response.data.message,
            type: "error",
          });
        }
      } catch (err) {
        this.$notify({
          title: "Algo salió mal",
          message: err.message,
          type: "error",
        });
        console.log(err);
      } finally {
        this.showConfirmModal = false;
      }
    },
    previus(){
      this.currentIndexMachine -= 1;
      this.currentMachine = this.machines.data[this.currentIndexMachine];
      this.selectedMachine = this.currentMachine.id;
      },
    next(){
      this.currentIndexMachine += 1;
      this.currentMachine = this.machines.data[this.currentIndexMachine];
      this.selectedMachine = this.currentMachine.id;
    },
  },
  watch: {
    selectedMachine(newVal) {
      this.currentMachine = this.machines.data.find(
        (item) => item.id == newVal
      );
    },
  },
  mounted() {
    this.selectedMachine = this.machine.id;
  this.currentIndexMachine = this.machines.data.findIndex((obj) => obj.id == this.selectedMachine);
  },
};
</script>
