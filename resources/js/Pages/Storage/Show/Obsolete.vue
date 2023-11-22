<template>
    <div>
      <AppLayoutNoHeader title="Almacén de obsoletos">
  
        <div class="flex justify-between text-lg mx-14 mt-11">
          <span>Almacén de obsoletos</span>
  
          <Link :href="route('storages.obsolete.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between mt-5 mx-14">
          <div class="md:w-1/3 mr-2">
            <el-select v-model="selectedStorage" clearable filterable placeholder="Buscar producto obsoleto"
              no-data-text="No hay productos en el almacén" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in storages" :key="item.id" :label="item.storageable.name" :value="item.id" />
            </el-select>
          </div>
          <div class="flex flex-col md:flex-row items-center space-x-2">
            <div class="flex gap-1 mb-2 md:mb-[1px]">
              <el-tooltip v-if="$page.props.auth.user.permissions.includes('Crear entradas')"
                content="Dar entrada a almacén" placement="top">
                <button @click="
                  is_add = true;
                showDialogModal = true;
                " class="rounded-lg bg-green-600 text-white py-2 px-2 text-sm">
                  Entrada
                </button>
              </el-tooltip>
  
              <el-tooltip v-if="$page.props.auth.user.permissions.includes('Crear salidas')" content="Dar salida de almacén"
                placement="top">
                <button @click="
                  is_add = false;
                showDialogModal = true;
                " class="rounded-lg bg-primary text-white py-2 px-2 text-sm">
                  Salida
                </button>
              </el-tooltip>
            </div>
  
            <div class="flex gap-1">
              <el-tooltip
                v-if="$page.props.auth.user.permissions.includes('Editar materia prima') && currentStorage?.type != 'producto-terminado'"
                content="Editar" placement="top">
                <Link :href="route('raw-materials.edit', selectedRawMaterial)">
                <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                  <i class="fa-solid fa-pen text-sm"></i>
                </button>
                </Link>
              </el-tooltip>
              <!-- <el-tooltip
                v-if="$page.props.auth.user.permissions.includes('Editar materia prima') && currentStorage?.type != 'producto-terminado' && currentStorage"
                content="Editar" placement="top">
                <Link :href="route('raw-materials.edit', currentStorage?.id)">
                <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                  <i class="fa-solid fa-pen text-sm"></i>
                </button>
                </Link>
              </el-tooltip> -->
  
              <Dropdown align="right" width="48" v-if="$page.props.auth.user.permissions.includes('Crear scrap') &&
                $page.props.auth.user.permissions.includes(
                  'Eliminar materia prima'
                )
                ">
                <template #trigger>
                  <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                    Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                  </button>
                </template>
                <template #content>
                <DropdownLink :href="route('raw-materials.create')"
                  v-if="$page.props.auth.user.permissions.includes('Crear materia prima')">
                  Agregar nueva materia prima
                </DropdownLink>
                  <DropdownLink @click="scrapModal = true" as="button"
                    v-if="$page.props.auth.user.permissions.includes('Crear scrap')">
                    Mandar a scrap
                  </DropdownLink>
                  <DropdownLink @click="showConfirmModal = true" as="button" v-if="$page.props.auth.user.permissions.includes(
                    'Eliminar materia prima'
                  )
                    ">
                    Eliminar
                  </DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
        <div class="lg:grid grid-cols-3 mt-12 border-b-2">
          <div class="px-6">
            <h2 class="text-xl font-bold text-center mb-6">
              {{ currentStorage?.storageable.name }}
            </h2>
            <div class="flex items-center">
            <i :class="currentIndexStorage == 0 ? 'hidden' : 'block'" @click="previus" class="fa-solid fa-chevron-left mr-4 text-lg text-gray-600 cursor-pointer p-1 rounded-full"></i>
            <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
            :class="currentStorage?.storageable?.media.length ? 'bg-transparent' : 'bg-[#D9D9D9]'"
              class="w-full h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center">
              <el-image style="height: 100%" :src="currentStorage?.storageable?.media[0]?.original_url" fit="fit">
                <template #error>
                  <div class="flex justify-center items-center text-[#ababab]">
                    <i class="fa-solid fa-image text-6xl"></i>
                  </div>
                </template>
              </el-image>
              <div v-if="imageHovered" @click="
                openImage(currentStorage?.storageable?.media[0]?.original_url)
                "
                class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
                <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
              </div>
            </figure>
            <i :class="currentIndexStorage == storages.length - 1 ? 'hidden' : 'block'" @click="next" class="fa-solid fa-chevron-right ml-4 text-lg text-gray-600 cursor-pointer p-1 mb-2 rounded-full"></i>
            </div>
            <div class="mt-8 ml-6 text-sm">
              <div class="flex mb-2">
                <p class="w-1/3 text-primary">Existencias</p>
                <p>
                  {{ currentStorage?.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0" }}
                  {{ currentStorage?.storageable.measure_unit ?? "" }}
                </p>
              </div>
              <div class="flex mb-3">
                <p class="w-1/3 text-primary">Ubicación</p>
                <p>{{ currentStorage?.location ?? "--" }}</p>
              </div>
            </div>
          </div>
  
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
                Historial de movimientos
              </p>
            </div>
            <!-- --------------------- Tab 1 Información general starts------------------ -->
            <div v-if="tabs == 1" class="text-sm">
              <div class="col-span-2 px-7 py-3">
                <div class="flex space-x-2 mb-6">
                  <p class="w-1/3 text-[#9A9A9A]">Tipo:</p>
                  <p>{{ currentStorage?.type }}</p>
                </div>
                <div class="flex space-x-2 mb-6">
                  <p class="w-1/3 text-[#9A9A9A]">Fecha de Alta</p>
                  <el-tooltip content="Año-Mes-Día" placement="top">
                    <p>{{ currentStorage?.created_at.split("T")[0] }}</p>
                  </el-tooltip>
                </div>
                <div class="flex mb-2 space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
                  <p>{{ selectedRawMaterial }}</p>
                </div>
                <div class="flex mb-6 space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">Características</p>
                  <p>{{ currentStorage?.storageable?.features?.join(", ") }}</p>
                </div>
                <div class="flex mb-2 space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
                  <p>{{ currentStorage?.storageable.part_number }}</p>
                </div>
                <div class="flex mb-6 space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
                  <p>{{ currentStorage?.storageable.measure_unit }}</p>
                </div>
                <div class="flex mb-3 space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">Costo de aquisición</p>
                  <p class="text-[#4FC03D]">
                    {{ currentStorage?.storageable.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '--' }} $MXN
                  </p>
                </div>
                <div class="flex mb-6 space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">Costo total de este producto en almacén</p>
                  <p class="text-[#4FC03D]">
                    {{ (currentStorage?.storageable.cost *
                      currentStorage?.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} $MXN
                  </p>
                </div>
                <div class="flex mb-2 space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">
                    Cantidad miníma permitida en almacén
                  </p>
                  <p>{{ currentStorage?.storageable.min_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                    currentStorage?.storageable.measure_unit }}</p>
                </div>
                <div class="flex space-x-2">
                  <p class="w-1/3 text-[#9A9A9A]">
                    Cantidad máxima permitida en almacén
                  </p>
                  <p>{{ currentStorage?.storageable.max_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                    currentStorage?.storageable.measure_unit }}</p>
                </div>
              </div>
            </div>
            <!-- --------------------- Tab 1 Información general ends------------------ -->
  
            <!-- --------------------- Tab 2 historial de movimientos starts------------------ -->
            <div v-if="tabs == 2" class="px-7 py-7 text-sm h-96 overflow-y-auto">
              <table class="border-separate border-spacing-x-8">
                <thead>
                  <tr>
                    <th class="pr-4">#</th>
                    <th class="px-4">Creador</th>
                    <th class="px-4">Fecha</th>
                    <th class="px-4">Cantidad</th>
                    <th class="px-4">Tipo</th>
                    <th class="px-4">Notas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(movement, index) in reversedMovements" :key="index"
                    class="text-[#9A9A9A] mb-4 text-xs">
                    <td class="text-left pb-3">
                      {{ index + 1 }}
                    </td>
                    <td class="text-center pb-3">
                      {{ movement.user?.name }}
                    </td>
                    <td class="text-center pb-3">
                      {{ getDateFormtted(movement.created_at) }}
                    </td>
                    <td class="text-center pb-3">
                      {{ movement.quantity }}
                    </td>
                    <td class="text-center pb-3">
                      {{ movement.type }}
                    </td>
                    <td class="text-center pb-3">
                      {{ movement.notes ?? '--' }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- --------------------- Tab 2 historial de movimientos ends------------------ -->
          </div>
        </div>
        <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
          <template #title> Eliminar producto de Almacén </template>
          <template #content> Continuar con la eliminación? </template>
          <template #footer>
            <div class="">
              <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
              <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
            </div>
          </template>
        </ConfirmationModal>
  
        <!-- -------------- Dialog Modal starts----------------------- -->
        <DialogModal :show="showDialogModal" @close="
          showDialogModal = false;
        is_add = null;
        form.reset();
        ">
          <template #title>
            <p>Ingresa la cantidad</p>
          </template>
          <template #content>
            <form ref="myForm" @submit.prevent="is_add ? addStorage() : subStorage()">
              <div>
                <IconInput v-model="form.quantity" inputPlaceholder="Cantidad" inputType="number" inputStep="0.01">
                  <el-tooltip content="Cantidad" placement="top">
                    123
                  </el-tooltip>
                </IconInput>
                <p v-if="errorMessage" class="text-red-600 text-xs">
                  {{ errorMessage }}
                </p>
                <InputError :message="form.errors.quantity" />
              </div>
              <div class="flex">
                <el-tooltip content="Notas" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                    ...
                  </span>
                </el-tooltip>
                <textarea v-model="form.notes" class="textarea" autocomplete="off" placeholder="Notas"></textarea>
                <InputError :message="form.errors.notes" />
              </div>
            </form>
          </template>
          <template #footer>
            <CancelButton @click="
              showDialogModal = false;
            form.reset();
            is_add = null;
            " :disabled="form.processing">
              Cancelar</CancelButton>
            <PrimaryButton @click="submitForm" :disabled="form.processing">{{ is_add ? "Dar entrada" : "Dar salida" }}
            </PrimaryButton>
          </template>
        </DialogModal>
        <!-- --------------------------- Dialog Modal ends ------------------------------------ -->
  
        <!-- -------------- scrapModal starts----------------------- -->
        <Modal :show="scrapModal" @close="scrapModal = false">
        <form @submit.prevent="sentToScrap">
          <div class="mx-7 my-4 space-y-4 relative">
            <section v-if="scrapModal">
                <h2 class="font-bold text-center mr-2">
                  Mandar {{ currentStorage?.storageable.name }} a scrap
                </h2>
              <div class="flex flex-col justify-center mt-7">
                <div @click="scrapModal = false"
                  class="cursor-pointer w-5 h-5 rounded-full border-2 border-black flex items-center justify-center absolute top-0 right-0">
                  <i class="fa-solid fa-xmark"></i>
                </div>
                <div>
                <IconInput v-model="form.quantity" inputPlaceholder="Cantidad" inputType="number" inputStep="0.01">
                  <el-tooltip content="Cantidad" placement="top">
                    123
                  </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.quantity" />
              </div>
                <div>
                <IconInput v-model="form.location" inputPlaceholder="Ubicación" inputType="text">
                  <el-tooltip content="Ubicación" placement="top">
                    U
                  </el-tooltip>
                  <InputError :message="form.errors.location" />
                </IconInput>
              </div>
              </div>
            </section>
            <!-- -------------- scrapModal ends----------------------- -->
  
            <div class="flex justify-end space-x-3 pt-5 pb-1">
              <PrimaryButton>Mandar a scrap</PrimaryButton>
            </div>
            </div>
        </form>
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
  import DialogModal from "@/Components/DialogModal.vue";
  import IconInput from "@/Components/MyComponents/IconInput.vue";
  import InputError from "@/Components/InputError.vue";
  import moment from "moment";
  import { Link, useForm } from "@inertiajs/vue3";
  import Modal from "@/Components/Modal.vue";
  
  export default {
    data() {
      const form = useForm({
        quantity: null,
        notes: null,
        location: null,
        type: 'scrap',
        storage_id: null,
  
      });
      return {
        form,
        selectedStorage: "",
        selectedRawMaterial: "",
        currentStorage: null,
        imageHovered: false,
        showConfirmModal: false,
        showDialogModal: false,
        scrapModal: false,
        is_add: null,
        errorMessage: null,
        currentIndexStorage: null,
        tabs: 1,
        reversedMovements: null,
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
      DialogModal,
      IconInput,
      InputError,
      Modal,
    },
    props: {
      storage: Object,
      storages: Array,
      totalStorageMoney: Number
    },
    methods: {
      sentToScrap() {
        this.form.storage_id = this.selectedRawMaterial;
        this.form.post(route("storages.scraps.store"), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Producto mandado a scrap",
              type: "success",
            });
          },
        });
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
      getDateFormtted(dateTime) {
        if (!dateTime) return null;
        return moment(dateTime).format("DD MMM YYYY, hh:mmA");
      },
      async deleteItem() {
        try {
          const response = await axios.delete(
            route("storages.destroy", this.currentStorage?.id)
          );
  
          if (response.status == 200) {
            this.$notify({
              title: "Éxito",
              message: response.data.message,
              type: "success",
            });
  
            const index = this.storages.findIndex(
              (item) => item.id === this.currentStorage.id
            );
            if (index !== -1) {
              this.storages.splice(index, 1);
              this.selectedStorage = "";
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
      submitForm() {
        this.$refs.myForm.dispatchEvent(
          new Event("submit", { cancelable: true })
        );
      },
      async addStorage() {
        try {
          const response = await axios.post(
            route("storages.add", this.selectedStorage),
            {
              quantity: this.form.quantity,
              notes: this.form.notes,
              type: 'Entrada',
            }
          );
          if (response.status === 200) {
            this.$notify({
              title: "Éxito",
              message: "Entrada exitosa",
              type: "success",
            });
            this.currentStorage.movements.push(response.data.movement);
            this.form.reset();
            this.showDialogModal = false;
            this.is_add = null;
            this.currentStorage.quantity = response.data.item.quantity;
            this.errorMessage = null;
          }
        } catch (error) {
          if (error.response.status === 422) {
            console.log(error);
            this.errorMessage = error.response.data.message;
  
            this.$notify({
              title: "Error",
              message: "Formulario incompleto o formato invalido",
              type: "error",
            });
          } else {
            this.$notify({
              title: "Error",
              message: error.message,
              type: "error",
            });
          }
        }
      },
      async subStorage() {
        try {
          const response = await axios.post(
            route("storages.sub", this.selectedStorage),
            {
              quantity: this.form.quantity,
              notes: this.form.notes,
              type: 'Salida',
            }
          );
          if (response.status === 200) {
            this.$notify({
              title: "Éxito",
              message: "Salida exitosa",
              type: "success",
            });
            this.currentStorage.movements.push(response.data.movement);
            this.form.reset();
            this.showDialogModal = false;
            this.is_add = null;
            this.currentStorage.quantity = response.data.item.quantity;
            this.errorMessage = null;
          }
        } catch (error) {
          if (error.response.status === 422) {
            console.log(error);
            this.errorMessage = error.response.data.message;
  
            this.$notify({
              title: "Error",
              message: "Formulario incompleto o formato invalido",
              type: "error",
            });
          } else {
            this.$notify({
              title: "Error",
              message: error.message,
              type: "error",
            });
          }
        }
            },
      previus(){
        this.currentIndexStorage -= 1;
        this.currentStorage = this.storages[this.currentIndexStorage];
        this.selectedStorage = this.currentStorage.id;
      },
      next(){
        this.currentIndexStorage += 1;
        this.currentStorage = this.storages[this.currentIndexStorage];
        this.selectedStorage = this.currentStorage.id;
      },
    },
    watch: {
      selectedStorage(newVal) {
        this.currentStorage = this.storages.find((item) => item.id == newVal);
        this.reversedMovements = this.currentStorage.movements.reverse();
      },
    },
    mounted() {
      this.selectedStorage = this.storage.id;
      this.selectedRawMaterial = this.storage.storageable.id;
      this.currentIndexStorage = this.storages.findIndex((obj) => obj.id == this.selectedStorage);
  
    },
  };
  </script>
  