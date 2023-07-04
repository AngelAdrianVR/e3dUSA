<template>
  <div>
    <AppLayoutNoHeader title="Ver maquinaria">
      <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Maquinaria</span>
        <Link
          :href="route('machines.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
        >
          <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>
      <div class="flex justify-between mt-5 mx-14">
        <div class="w-1/3">
          <el-select
            v-model="selectedMachine"
            clearable
            filterable
            placeholder="Buscar máquina"
            no-data-text="No hay maquinaria registrada"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in machines.data"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </div>
        <div class="flex items-center space-x-2">
          <el-tooltip content="Editar" placement="top">
            <Link :href="route('machines.edit', selectedMachine)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
            </Link>
          </el-tooltip>
          <Dropdown align="right" width="48">
            <template #trigger>
              <button
                class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm"
              >
                Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink :href="route('machines.create')">
                Agregar nueva máquina
              </DropdownLink>
              <DropdownLink
                :href="route('maintenances.create', selectedMachine)"
              >
                Registrar mantenimiento
              </DropdownLink>
              <DropdownLink
                :href="route('spare-parts.create', selectedMachine)"
              >
                Registrar refacción
              </DropdownLink>
              <DropdownLink @click="showConfirmModal = true" as="button">
                Eliminar
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>
      <div class="lg:grid grid-cols-3 mt-12 border-b-2">
        <div class="px-14">
          <h2 class="text-xl font-bold text-center mb-6">
            {{ currentMachine?.name }}
          </h2>
          <figure
            @mouseover="showOverlay"
            @mouseleave="hideOverlay"
            class="w-full h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center"
          >
            <!-- <el-image
              style="height: 100%"
              :src="currentMachine?.media[0]?.original_url"
              fit="fit"
            >
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image> -->
            <!-- <img :src="currentMachine?.media[0]?.original_url" :alt="currentMachine?.name"
                            class="object-contain w-full h-full rounded-lg"> -->
            <!-- <div
              v-if="imageHovered"
              @click="openImage(currentMachine?.media[0]?.original_url)"
              class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in"
            >
              <i
                class="fa-solid fa-magnifying-glass-plus text-white text-4xl"
              ></i>
            </div> -->
          </figure>
        </div>

        <!-- ------------------------Information panel tabs--------------------- -->
        <div class="col-span-2 border-2">
          <div class="border-b-2 px-7 py-3 flex">
            <p
              @click="tabs = 1"
              :class="
                tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base leading-none"
            >
              Información general
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p
              @click="tabs = 2"
              :class="
                tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
            >
              Mantenimiento
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p
              @click="tabs = 3"
              :class="
                tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
            >
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
              <p class="w-1/3 text-[#9A9A9A]">Peso (kg)</p>
              <p>{{ currentMachine?.weight }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Ancho (cm)</p>
              <p>{{ currentMachine?.width }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Largo (cm)</p>
              <p>{{ currentMachine?.large }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
              <p class="w-1/3 text-[#9A9A9A]">Alto (cm)</p>
              <p>{{ currentMachine?.height }}</p>
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
            <div class="flex mt-7 mb-2 items-center">
              <i class="fa-solid fa-paperclip mr-2"></i>
              <p class="text-[#9A9A9A]">No hay archivos adjuntos</p>
            </div>
          </div>
          <!-- --------------------- Tab 1 Información general ends------------------ -->

          <!-- --------------------- Tab 2 Mantenimient starts------------------ -->
          <div v-if="tabs == 2" class="px-7 py-7 text-sm overflow-scroll">
            <table class="border-separate border-spacing-x-8">
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
                <tr
                  v-for="(maintenance, index) in currentMachine?.maintenances"
                  :key="index"
                  class="text-[#9A9A9A] cursor-pointer mb-4"
                >
                  <td
                    @click="openMaintenanceModal(maintenance, index)"
                    class="text-left pb-3"
                  >
                    {{ index + 1 }}
                  </td>
                  <td
                    @click="openMaintenanceModal(maintenance, index)"
                    class="text-center pb-3"
                  >
                    {{ maintenance.manteinance_type_id }}
                  </td>
                  <td
                    @click="openMaintenanceModal(maintenance, index)"
                    class="text-center pb-3"
                  >
                    {{ maintenance.created_at }}
                  </td>
                  <td
                    @click="openMaintenanceModal(maintenance, index)"
                    class="text-center pb-3"
                  >
                    ${{ maintenance.cost }}
                  </td>
                  <td
                    @click="openMaintenanceModal(maintenance, index)"
                    class="text-center pb-3"
                  >
                    {{ maintenance.responsible }}
                  </td>
                  <td class="text-center pb-3">
                    <div>
                      <el-popconfirm
                        confirm-button-text="Si"
                        cancel-button-text="No"
                        icon-color="#FFFFFF"
                        title="¿Continuar?"
                        @confirm="deleteRow(maintenance)"
                      >
                        <template #reference>
                          <i
                            class="fa-solid fa-trash-can text-[#9A9A9A] hover:text-red-600"
                          ></i>
                        </template>
                      </el-popconfirm>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- --------------------- Tab 2 Mantenimient ends------------------ -->

          <!-- --------------------- Tab 3 refacciones starts------------------ -->
          <div v-if="tabs == 3" class="px-7 py-7 text-sm overflow-scroll">
            <table class="border-separate border-spacing-x-8">
              <thead>
                <tr>
                  <th class="pr-4">#</th>
                  <th class="px-4">Refacción</th>
                  <th class="px-4">Cantidad</th>
                  <th class="px-4">Adquirida el</th>
                  <th class="px-4">Ubicación</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(spare_part, index) in currentMachine?.spare_parts"
                  :key="index"
                  class="text-[#9A9A9A] cursor-pointer mb-4"
                >
                  <td
                    @click="openSparePartModal(spare_part, index)"
                    class="text-left pb-3"
                  >
                    {{ index + 1 }}
                  </td>
                  <td
                    @click="openSparePartModal(spare_part, index)"
                    class="text-center pb-3"
                  >
                    {{ spare_part.name }}
                  </td>
                  <td
                    @click="openSparePartModal(spare_part, index)"
                    class="text-center pb-3"
                  >
                    {{ spare_part.quantity }}
                  </td>
                  <td
                    @click="openSparePartModal(spare_part, index)"
                    class="text-center pb-3"
                  >
                    {{ spare_part.created_at }}
                  </td>
                  <td
                    @click="openSparePartModal(spare_part, index)"
                    class="text-center pb-3"
                  >
                    {{ spare_part.location }}
                  </td>
                  <td class="text-center pb-3">
                    <div>
                      <el-popconfirm
                        confirm-button-text="Si"
                        cancel-button-text="No"
                        icon-color="#FFFFFF"
                        title="¿Continuar?"
                        @confirm="deleteRow(spare_part)"
                      >
                        <template #reference>
                          <i
                            class="fa-solid fa-trash-can text-[#9A9A9A] hover:text-red-600"
                          ></i>
                        </template>
                      </el-popconfirm>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- --------------------- Tab 3 refacciones ends------------------ -->
        </div>
      </div>

      <ConfirmationModal
        :show="showConfirmModal"
        @close="showConfirmModal = false"
      >
        <template #title> Eliminar máquina </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div class="">
            <CancelButton @click="showConfirmModal = false" class="mr-2"
              >Cancelar</CancelButton
            >
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
                {{ currentMachine.name }}
              </h2>
              <div
                @click="maintenanceModal = false"
                class="cursor-pointer w-5 h-5 rounded-full border-2 border-black flex items-center justify-center absolute top-0 right-0"
              >
                <i class="fa-solid fa-xmark"></i>
              </div>
              <span class="text-[#9A9A9A] absolute left-0 top-0">
                # {{ maintenanceIndex }}</span
              >
            </div>

            <div class="grid grid-cols-2">
              <div class="flex flex-col pb-7">
                <p class="text-primary">Tipo de mantenimiento</p>
                <p class="text-[#9A9A9A]">
                  {{ selectedMaintenance.manteinance_type_id }}
                </p>
              </div>
              <div class="flex flex-col pb-7">
                <div class="flex">
                  <p class="text-primary">Fecha</p>
                  <el-tooltip
                    content="Fecha en que se realizó el mantenimiento"
                    placement="top"
                  >
                    <i
                      class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center"
                    ></i>
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
                  <el-tooltip
                    content="Persona o empresa que realizó el mantenimiento"
                    placement="top"
                  >
                    <i
                      class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center"
                    ></i>
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

              <p class="text-primary">Evidencias</p>
              <p class="text-[#9A9A9A] col-span-2">
                {{ "Algunas fotografias de evidencias" }}
              </p>
            </div>
          </section>
      <!-- -------------- maintenanceModal ends----------------------- -->



   <!-- --------------------------- sparepartmodal starts ------------------------------------ -->
          <section v-if="sparePartModal">
            <div class="flex justify-center mb-7">
              <h2 class="font-bold text-center mr-2">
                {{ currentMachine.name }}
              </h2>
              <div
                @click="sparePartModal = false"
                class="cursor-pointer w-5 h-5 rounded-full border-2 border-black flex items-center justify-center absolute top-0 right-0"
              >
                <i class="fa-solid fa-xmark"></i>
              </div>
              <span class="text-[#9A9A9A] absolute left-0 top-0">
                # {{ maintenanceIndex }}</span
              >
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

              <p class="text-primary">Evidencias</p>
              <p class="text-[#9A9A9A] col-span-2">
                {{ "Algunas fotografias de evidencias" }}
              </p>
            </div>
          </section>
   <!-- --------------------------- sparepartmodal ends ------------------------------------ -->

            <div class="flex justify-end space-x-3 py-5">
              <Link :href="maintenanceModal ? route('maintenances.edit', selectedMaintenance) : route('spare-parts.edit', selectedSparePart)">
                <PrimaryButton>Editar</PrimaryButton>
              </Link>
            </div>
        </div>
      </Modal>
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
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedMachine: "",
      currentMachine: null,
      imageHovered: false,
      showConfirmModal: false,
      maintenanceModal: false,
      sparePartModal: false,
      maintenanceIndex: null,
      sparePartIndex: null,
      selectedMaintenance: null,
      selectedSparePart: null,
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
  },
  props: {
    machine: Object,
    machines: Array,
  },
  methods: {
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
  },
};
</script>
