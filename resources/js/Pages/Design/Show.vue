<template>
  <div>
    <AppLayoutNoHeader title="Órdenes de diseño">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de diseño</label>
          <Link :href="route('designs.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="w-1/3">
            <el-select v-model="selectedDesign" clearable filterable placeholder="Buscar órden de diseño"
              no-data-text="No hay órdenes registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in designs.data" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div v-if="currentDesign" class="flex items-center space-x-2">
            <el-popconfirm
              v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de diseño') && currentDesign?.authorized_at == 'No autorizado'"
              confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
              @confirm="authorizeOrder">
              <template #reference>
                <button class="rounded-lg bg-primary text-white py-1 px-2">
                  Autorizar
                </button>
              </template>
            </el-popconfirm>

            <el-tooltip v-if="currentDesign?.expected_end_at == '--'" content="Marcar orden como iniciada"
              placement="top">
              <button :disabled="currentDesign?.authorized_at == 'No autorizado'" @click="startOrderModal = true"
                class="rounded-lg bg-primary text-sm text-white py-[6px] px-2 disabled:cursor-not-allowed disabled:opacity-50">
                Iniciar
              </button>
            </el-tooltip>

            <el-tooltip v-else-if="!currentDesign?.finished_at" content="Marcar como orden terminada" placement="top">
              <button @click="finishOrderModal = true" class="rounded-lg bg-green-600 text-sm text-white p-2">
                Terminada
              </button>
            </el-tooltip>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ selectedDesign.name }}
      </p>
      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Datos de la órden
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Modificaciones
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <span class="text-gray-500">ID</span>
          <span>{{ currentDesign?.id }}</span>
          <span class="text-gray-500 my-2">Solicitada por</span>
          <span>{{ currentDesign?.user.name }}</span>
          <span class="text-gray-500 my-2">Solicitada el</span>
          <span>{{ currentDesign?.created_at }}</span>
          <span class="text-gray-500 my-2">Autorizado</span>
          <span>{{ currentDesign?.authorized_user_name }} -
            {{ currentDesign?.authorized_at }}</span>
          <span class="text-gray-500 my-2">Diseñador(a)</span>
          <span>{{ currentDesign?.designer.name }}</span>
          <span class="text-gray-500 my-2">Estimado de entrega</span>
          <span>{{ currentDesign?.expected_end_at }}</span>
          <span class="text-gray-500 my-2">Estatus</span>
          <span class="rounded-full border text-center" :class="currentDesign?.status['text-color'] +
            ' ' +
            currentDesign?.status['border-color']
            ">{{ currentDesign?.status["label"] }}</span>
          <span class="text-gray-500 my-2">Archivos de resultados</span>
          <div class="flex flex-col">
            <a class="hover:underline text-primary hover:text-secondary" v-for="file in currentDesign?.media"
              :key="file.id" :href="file.original_url" target="_blank">{{ file.file_name }}</a>
          </div>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <span class="text-gray-500">Cliente</span>
          <span>{{ currentDesign?.company_branch_name }}</span>
          <span class="text-gray-500 my-2">Contacto</span>
          <span>{{ currentDesign?.contact_name }}</span>
          <span class="text-gray-500 my-2">Nombre del diseño</span>
          <span>{{ currentDesign?.name }}</span>
          <span class="text-gray-500 my-2">Clasificación</span>
          <span>{{ currentDesign?.design_type.name }}</span>

          <p class="text-secondary col-span-2 mt-7">Especificaciones</p>

          <span class="text-gray-500 my-2">Requerimientos</span>
          <span>{{ currentDesign?.specifications }}</span>

          <span class="text-gray-500 my-2">Unidad</span>
          <span>{{ currentDesign?.measure_unit }}</span>
          <span class="text-gray-500 my-2">Dimensiones</span>
          <span>{{ currentDesign?.dimensions }}</span>
          <span class="text-gray-500 my-2">Pantones</span>
          <span>{{ currentDesign?.pantones }}</span>
          <span class="text-gray-500 my-2">Plano</span>
          <a class="text-primary hover:text-secondary hover:underline" target="_blank"
            :href="currentDesign?.media_plano[0]?.original_url">{{ currentDesign?.media_plano[0]?.file_name }}</a>
          <span class="text-gray-500 my-2">Logo</span>
          <a class="text-primary hover:text-secondary hover:underline" target="_blank"
            :href="currentDesign?.media_logo[0]?.original_url">{{ currentDesign?.media_logo[0]?.file_name }}</a>
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 modifications starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <p class="text-secondary">Modificaciones</p>
        <div>

        </div>
      </div>

      <!-- ------------- tab 2 modifications ends ------------ -->

      <!-- -------------- Modal starts----------------------- -->
      <Modal :show="startOrderModal || finishOrderModal" @close="startOrderModal = false; finishOrderModal = false">
        <div class="p-3 relative">
          <i @click="startOrderModal = false; finishOrderModal = false"
            class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>
          <el-tooltip content="Ayuda" placement="left">
            <i v-if="!helpDialog" @click="helpDialog = true"
              class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-3 top-3 cursor-pointer"></i>
          </el-tooltip>

          <form v-if="startOrderModal" class="text-center mt-5 mb-2" @submit.prevent="startOrder">
            <div class="felx items-center mb-3">
              <el-tooltip content="Fecha estimada de finalización" placement="top">
                <i class="fa-solid fa-calendar-days mr-3"></i>
              </el-tooltip>
              <el-date-picker v-model="form.expected_end_at" type="date" placeholder="Selecciona una fecha"
                :disabled-date="disabledDate" />
              <InputError :message="form.errors.expected_end_at" />
            </div>

            <div v-if="helpDialog" class="border border-[#0355B5] rounded-lg px-6 py-2 mt-5 mx-7 relative">
              <i
                class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"></i>
              <i @click="helpDialog = false"
                class="fa-solid fa-xmark cursor-pointer w-3 h-3 rounded-full text-secondary flex items-center justify-center absolute right-3 top-3 text-xs"></i>

              <p v-if="startOrderModal" class="text-secondary text-sm">
                Una vez dando una fecha estimada de finalización, se entenderá que
                el trabajo estará en proceso.
              </p>
              <p v-if="finishOrderModal" class="text-secondary text-sm">
                Puedes subir todos los archivos que necesites para dar por terminada la orden. No es obligatorio.
              </p>
            </div>

            <div class="flex justify-start space-x-3 pt-2 pb-1 border-t border-[#9a9a9a] py-2">
              <PrimaryButton>Iniciar órden</PrimaryButton>
              <CancelButton @click="
                startOrderModal = false;
              form.reset();
              ">Cancelar</CancelButton>
            </div>
          </form>

          <!-- ---------------------------------------FinishOrderModal------------------------------------------- -->
          <form v-if="finishOrderModal" class="text-center mt-5 mb-2" @submit.prevent="finishOrder">
            <div class="col-span-full">
              <div class="flex items-center">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9">
                  <el-tooltip content="Archivos de resultado" placement="left">
                    <i class="fa-solid fa-object-group"></i>
                  </el-tooltip>
                </span>
                <input @input="form.media = $event.target.files"
                  class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                  aria-describedby="file_input_help" id="file_input" type="file" multiple />
              </div>
              <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                PDF, SVG, PNG, JPG o GIF (MAX. 4 MB).
              </p>
            </div>

            <div v-if="helpDialog" class="border border-[#0355B5] rounded-lg px-6 py-2 mt-5 mx-7 relative">
              <i
                class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"></i>
              <i @click="helpDialog = false"
                class="fa-solid fa-xmark cursor-pointer w-3 h-3 rounded-full text-secondary flex items-center justify-center absolute right-3 top-3 text-xs"></i>

              <p v-if="startOrderModal" class="text-secondary text-sm">
                Una vez dando una fecha estimada de finalización, se entenderá que
                el trabajo estará en proceso.
              </p>
              <p v-if="finishOrderModal" class="text-secondary text-sm">
                Puedes subir todos los archivos que necesites para dar por terminada la orden. No es obligatorio.
              </p>
            </div>

            <div class="flex justify-start space-x-3 pt-2 pb-1 border-t border-[#9a9a9a] py-2">
              <PrimaryButton>Terminar orden</PrimaryButton>
              <CancelButton @click="
                finishOrderModal = false;
              form.reset();
              ">Cancelar</CancelButton>
            </div>
          </form>

        </div>
      </Modal>
      <!-- --------------------------- Modal ends ------------------------------------ -->
    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      expected_end_at: null,
      media: null,
    });

    return {
      form,
      selectedDesign: "",
      currentDesign: null,
      startOrderModal: false,
      finishOrderModal: false,
      helpDialog: false,
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
    Modal,
    CancelButton,
    InputError,
  },
  methods: {
    startOrder() {
      this.form.put(route("designs.start-order", this.selectedDesign), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Órden en proceso",
            type: "success",
          });

          this.form.reset();
          this.startOrderModal = false;
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    async authorizeOrder() {
      try {
        const response = await axios.put(route("designs.authorize", this.selectedDesign));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Orden de diseño autorizada",
            type: "success",
          });

          this.currentDesign.authorized_at = response.data.item.authorized_at;
          this.currentDesign.status = response.data.item.status;
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
    finishOrder() {
      this.form.post(route("designs.finish-order", this.selectedDesign), {
        _method: 'put',
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Órden terminada",
            type: "success",
          });

          this.form.reset();
          this.finishOrderModal = false;
        },
      });
    },
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