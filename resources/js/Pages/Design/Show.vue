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
            <el-select @change="$inertia.get(route('designs.show', selectedDesign))" v-model="selectedDesign" clearable filterable placeholder="Buscar órden de diseño"
              no-data-text="No hay órdenes registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in designs" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div v-if="design.data" class="flex items-center space-x-2">
            <el-tooltip content="Editar" placement="top">
                  <Link :href="route('designs.edit', selectedDesign)">
                    <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>
                  </Link>
              </el-tooltip>
            <el-popconfirm
              v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de diseño') && design.data.authorized_at == 'No autorizado'"
              confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
              @confirm="authorizeOrder">
              <template #reference>
                <button class="rounded-lg bg-primary text-white py-1 px-2">
                  Autorizar
                </button>
              </template>
            </el-popconfirm>

            <el-tooltip v-if="design.data.finished_at && design.data?.user.id == $page.props.auth.user.id"
              content="Solicitar modificaciones de resultados obtenidos" placement="top">
              <button @click="showModificationsModal = true"
                class="rounded-lg bg-primary text-sm text-white py-[6px] px-2 disabled:cursor-not-allowed disabled:opacity-50">
                Solicitar modificaciones
              </button>
            </el-tooltip>

            <el-tooltip
              v-if="design.data.expected_end_at == '--' && this.design.data.designer.id == this.$page.props.auth.user.id"
              content="Marcar orden como iniciada" placement="top">
              <button :disabled="design.data.authorized_at == 'No autorizado'" @click="startOrderModal = true"
                class="rounded-lg bg-primary text-sm text-white py-[6px] px-2 disabled:cursor-not-allowed disabled:opacity-50">
                Iniciar
              </button>
            </el-tooltip>

            <el-tooltip
              v-else-if="this.design.data.designer.id == this.$page.props.auth.user.id"
              content="Marcar como orden terminada y subir resultados" placement="top">
              <button @click="finishOrderModal = true" class="rounded-lg bg-green-600 text-sm text-white p-2">
                Subir resultados
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
          <span>{{ design.data.id }}</span>
          <span class="text-gray-500 my-2">Solicitada por</span>
          <span>{{ design.data.user.name }}</span>
          <span class="text-gray-500 my-2">Solicitada el</span>
          <span>{{ design.data.created_at }}</span>
          <span class="text-gray-500 my-2">Autorizado</span>
          <span>{{ design.data.authorized_user_name }} -
            {{ design.data.authorized_at }}</span>
          <span class="text-gray-500 my-2">Diseñador(a)</span>
          <span>{{ design.data.designer.name }}</span>
          <span class="text-gray-500 my-2">Estimado de entrega</span>
          <span>{{ design.data.expected_end_at }}</span>
          <span class="text-gray-500 my-2">Iniciado el</span>
          <span>{{ design.data.started_at }}</span>
          <span class="text-gray-500 my-2">Estatus</span>
          <span class="rounded-full border text-center" :class="design.data.status['text-color'] +
            ' ' +
            design.data.status['border-color']
            ">{{ design.data.status["label"] }}</span>
          <span v-if="design.data.has_priority" class="text-gray-500 my-2">Prioridad</span>
          <span v-if="design.data.has_priority" class="text-primary"><i class="fa-solid fa-triangle-exclamation mr-2"></i>{{ 'Alta' }}</span>
          <span v-if="design.data.media.length" class="text-gray-500 my-2">Archivos de resultados</span>
          <div v-if="design.data.media.length" class="flex flex-col">
            <a class="hover:underline text-primary hover:text-secondary" v-for="file in design.data.media"
              :key="file.id" :href="file.original_url" target="_blank">{{ file.file_name }}</a>
          </div>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <span class="text-gray-500">Cliente</span>
          <span>{{ design.data.company_branch_name }}</span>
          <span class="text-gray-500 my-2">Contacto</span>
          <span>{{ design.data.contact_name }}</span>
          <span class="text-gray-500 my-2">Nombre del diseño</span>
          <span>{{ design.data.name }}</span>
          <span class="text-gray-500 my-2">Clasificación</span>
          <span>{{ design.data.design_type.name }}</span>

          <p class="text-secondary col-span-2 mt-7">Especificaciones</p>

          <span class="text-gray-500 my-2">Requerimientos</span>
          <span>{{ design.data.specifications }}</span>

          <span class="text-gray-500 my-2">Unidad</span>
          <span>{{ design.data.measure_unit }}</span>
          <span class="text-gray-500 my-2">Dimensiones</span>
          <span>{{ design.data.dimensions }}</span>
          <span class="text-gray-500 my-2">Pantones</span>
          <span>{{ design.data.pantones }}</span>
          <span class="text-gray-500 my-2">Plano</span>
          <a class="text-primary hover:text-secondary hover:underline" target="_blank"
            :href="design.data.media_plano[0]?.original_url">{{ design.data.media_plano[0]?.file_name }}</a>
          <span class="text-gray-500 my-2">Logo</span>
          <a class="text-primary hover:text-secondary hover:underline" target="_blank"
            :href="design.data.media_logo[0]?.original_url">{{ design.data.media_logo[0]?.file_name }}</a>
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 modifications starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <table class="border-separate border-spacing-x-8">
          <thead>
            <tr>
              <th class="text-left ">#</th>
              <th class="text-left ">Modificaciones</th>
              <th class="text-left w-40">Fecha</th>
              <th class="text-left w-64">Resultados</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(modification, index) in modifications" :key="index" class="text-[#9A9A9A] text-xs">
              <td class="pb-6">
                {{ index + 1 }}
              </td>
              <td @click="handleOpenModification(modification.id)" class="pb-6 hover:underline cursor-pointer">
                {{ modification.modifications }}
              </td>
              <td class="pb-6 w-40">
                {{ getDateFormtted(modification.created_at) }}
              </td>
              <td class="pb-6 w-64">
                <template v-if="modification?.media?.length">
                  <p v-if="modification?.media?.length" v-for="file in modification.media" :key="file.id">
                    <a class="hover:underline text-primary hover:text-secondary" :href="file.original_url"
                      target="_blank">{{ file.file_name }}</a>
                  </p>
                </template>
                <p v-else> No se han subido archivos </p>
              </td>
            </tr>
          </tbody>
        </table>
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
              <el-date-picker v-model="form.expected_end_at" type="datetime" placeholder="Selecciona fecha y hora"
                :disabled-date="disabledDate" />
              <InputError :message="form.errors.expected_end_at" />
            </div>

            <div v-if="helpDialog" class="border border-[#0355B5] rounded-lg px-6 py-2 mt-5 mb-3 mx-7 relative">
              <i
                class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"></i>
              <i @click="helpDialog = false"
                class="fa-solid fa-xmark cursor-pointer w-3 h-3 rounded-full text-secondary flex items-center justify-center absolute right-3 top-3 text-xs"></i>

              <p v-if="startOrderModal" class="text-secondary text-sm">
                Una vez dando una fecha y hora estimada de finalización, se entenderá que
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
              <PrimaryButton>Subir</PrimaryButton>
              <CancelButton @click="
                finishOrderModal = false;
              form.reset();
              ">Cancelar</CancelButton>
            </div>
          </form>

        </div>
      </Modal>
      <!-- --------------------------- Modal ends ------------------------------------ -->

      <DialogModal :show="showModificationsModal || showModificationsResultsModal"
        @close="showModificationsModal = false; showModificationsResultsModal = false;">
        <template #title>
          <p v-if="showModificationsModal">Solicitar modificaciones</p>
          <p v-if="showModificationsResultsModal">Resultados de modificaciones</p>
        </template>
        <template #content>
          <form @submit.prevent="showModificationsModal ? storeModifications() : storeModificationsResults()" ref="myForm">
            <div v-if="showModificationsModal" class="flex">
              <el-tooltip content="Modificaciones *" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                  <i class="fa-solid fa-grip-lines"></i>
                </span>
              </el-tooltip>
              <textarea v-model="modificationsForm.modifications" rows="4" class="textarea" autocomplete="off"
                placeholder="Modificaciones *"></textarea>
              <InputError :message="modificationsForm.errors.modifications" />
            </div>
            <div v-else>
              <p v-for="file in modifications.find(item => item.id == selectedModification)?.media" :key="file.id">
                - <a class="hover:underline text-primary hover:text-secondary" :href="file.original_url"
                  target="_blank">{{ file.file_name }}</a>
              </p>
              <div v-if="design.data.designer.id == $page.props.auth.user.id" class="col-span-full">
                <div class="flex items-center">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9">
                    <el-tooltip content="Archivos de resultado" placement="left">
                      <i class="fa-solid fa-object-group"></i>
                    </el-tooltip>
                  </span>
                  <input @input="handleFileSelection"
                    class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                    aria-describedby="file_input_help" id="file_input" type="file" multiple />
                </div>
                <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                  PDF, SVG, PNG, JPG o GIF (MAX. 4 MB).
                </p>
              </div>
            </div>
          </form>
        </template>
        <template #footer>
          <CancelButton
            @click="showModificationsModal = false; showModificationsResultsModal = false; modificationsForm.reset();"
            :disabled="modificationsForm.processing">
            Cerrar
          </CancelButton>
          <PrimaryButton v-if="showModificationsResultsModal && design.data.designer.id == $page.props.auth.user.id"
            @click="storeModificationsResults()" :disabled="modificationsForm.processing">
            Subir resultados
          </PrimaryButton>
          <PrimaryButton v-if="showModificationsModal" @click="submitModificationsForm"
            :disabled="modificationsForm.processing">
            Solicitar
          </PrimaryButton>
        </template>
      </DialogModal>
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
import DialogModal from "@/Components/DialogModal.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from "axios";
import moment from "moment";

export default {
  data() {
    const form = useForm({
      expected_end_at: null,
      media: null,
    });

    const modificationsForm = useForm({
      modifications: null,
    });

    return {
      form,
      modificationsForm,
      selectedDesign: "",
      selectedModification: null,
      // currentDesign: null,
      startOrderModal: false,
      finishOrderModal: false,
      showModificationsModal: false,
      showModificationsResultsModal: false,
      helpDialog: true,
      tabs: 1,
      modificationsMedia: null,
      modifications: null,
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
    CancelButton,
    PrimaryButton,
    CancelButton,
    InputError,
    DialogModal,
    Modal,
    Link,
  },
  methods: {
    async startOrder() {
      try {
        const response = await axios.put(route("designs.start-order", this.selectedDesign), {
          expected_end_at: this.form.expected_end_at
        });

        if (response.status === 200) {
          this.$notify({
            title: "Exito",
            message: "Orden iniciada",
            type: "success",
          });

          this.design.data.started_at = response.data.item.started_at;
          this.form.reset();
          this.startOrderModal = false;
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
    handleFileSelection(event) {
      this.modificationsMedia = event.target.files;
    },
    handleOpenModification(modificationId) {
      this.showModificationsResultsModal = true;
      this.selectedModification = modificationId;
    },
    async storeModifications() {
      try {
        const response = await axios.post(route('design-modifications.store'), {
          modifications: this.modificationsForm.modifications,
          design_id: this.selectedDesign,
        });

        if (response.status === 200) {
          this.design.data.modifications.unshift(response.data.item);
          this.showModificationsModal = false;
          this.$notify({
            title: "Éxito",
            message: "Solicitud de modificacion creada",
            type: "success",
          });
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
    async storeModificationsResults() {
      try {
        const response = await axios.post(route('design-modifications.upload-results'), {
          modification_id: this.selectedModification,
          media: this.modificationsMedia
        }, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        if (response.status === 200) {
          const index = this.modifications.findIndex(item => item.id == this.selectedModification);
          this.modifications[index] = response.data.item;
          this.showModificationsResultsModal = false;
          this.$notify({
            title: "Éxito",
            message: "Archivos subidos",
            type: "success",
          });
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
    getDateFormtted(dateTime) {
      if (!dateTime) return null;
      return moment(dateTime).format("DD MMM YYYY, hh:mmA");
    },
    submitModificationsForm() {
      this.$refs.myForm.dispatchEvent(new Event('submit', { cancelable: false }));
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
          console.log(response);
          this.design.data.authorized_at = response.data.item.authorized_at;
          this.design.data.status = response.data.item.status;
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
    async finishOrder() {
      try {
        const response = await axios.post(route('designs.finish-order'), {
          expected_end_at: this.form.expected_end_at,
          media: this.form.media,
          design_id: this.selectedDesign,
        }, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        if (response.status === 200) {
          this.design.data.media = response.data.item;
          this.$notify({
            title: "Éxito",
            message: "Órden terminada",
            type: "success",
          });

          this.form.reset();
          this.finishOrderModal = false;
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
  },

  // watch: {
  //   selectedDesign(newVal) {
  //     this.currentDesign = this.designs.data.find((item) => item.id == newVal);
  //     this.modifications = this.currentDesign?.modifications.reverse();
  //   },
  // },

  mounted() {
    this.selectedDesign = this.design.data.id;
  },
};
</script>