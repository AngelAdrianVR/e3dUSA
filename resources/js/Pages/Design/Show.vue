<template>
  <div class="dark:text-white">
    <AppLayoutNoHeader title="Órdenes de diseño">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de diseño</label>
          <Link :href="route('designs.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="w-1/3">
            <el-select @change="$inertia.get(route('designs.show', selectedDesign))" v-model="selectedDesign" clearable
              filterable placeholder="Buscar órden de diseño" no-data-text="No hay órdenes registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in designs" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div v-if="design.data" class="flex items-center space-x-2">
            <el-tooltip content="Editar" placement="top">
              <Link :href="route('designs.edit', selectedDesign)">
              <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
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
              v-if="design.data.expected_end_at == '--' && design.data.designer.id == $page.props.auth.user.id"
              content="Marcar orden como iniciada" placement="top">
              <button :disabled="design.data.authorized_at == 'No autorizado'" @click="startOrderModal = true"
                class="rounded-lg bg-primary text-sm text-white py-[6px] px-2 disabled:cursor-not-allowed disabled:opacity-50">
                Iniciar
              </button>
            </el-tooltip>

            <el-tooltip v-else-if="design.data.designer.id == $page.props.auth.user.id"
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

      <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
        <el-tab-pane label="Información general" name="1">
          <General :design="design.data" />
        </el-tab-pane>
        <el-tab-pane label="Modificaciones" name="2">
          <Modifications :design="design.data" />
        </el-tab-pane>
      </el-tabs>

      <DialogModal :show="startOrderModal || finishOrderModal" maxWidth="md"
        @close="startOrderModal = false; finishOrderModal = false">
        <template #title>
          <h1 v-if="startOrderModal" class="font-bold">Iniciar orden</h1>
          <h1 v-else class="font-bold">Finalizar orden</h1>
        </template>
        <template #content>
          <form v-if="startOrderModal" @submit.prevent="startOrder">
            <div>
              <InputLabel>
                <div class="flex items-center space-x-2">
                  <span>Fecha estimada de finalización*</span>
                  <el-tooltip placement="top">
                    <template #content>
                      <p>
                        Una vez dando una fecha y hora estimada de finalización, <br>
                        se marcará que el trabajo está en proceso.
                      </p>
                    </template>
                    <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                </div>
              </InputLabel>
              <el-date-picker v-model="form.expected_end_at" type="datetime" placeholder="Selecciona fecha y hora"
                class="!w-full" :disabled-date="disabledDate" />
              <InputError :message="form.errors.expected_end_at" />
            </div>
          </form>
          <!-- ---------------------------------------FinishOrderModal------------------------------------------- -->
          <form v-if="finishOrderModal" @submit.prevent="finishOrder">
            <div>
              <InputLabel>
                <div class="flex items-center space-x-2">
                  <span>Archivos de resultado</span>
                  <el-tooltip placement="top">
                    <template #content>
                      <p>
                        Puedes subir todos los archivos que necesites <br>
                        para dar por terminada la orden. <br> 
                        En caso de no requerir subir archivos solo da clic <br>
                        En "Subir" para indicar que la orden se a completado.
                      </p>
                    </template>
                    <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                </div>
              </InputLabel>
              <FileUploader @files-selected="form.media = $event" :multiple="true" />
              <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                PDF, SVG, PNG, JPG, GIF (MAX. 4 MB).
              </p>
            </div>
          </form>
        </template>
        <template #footer>
          <div v-if="startOrderModal" class="flex justify-end space-x-1">
            <CancelButton @click="startOrderModal = false; form.reset()" :disabled="loading">Cancelar</CancelButton>
            <PrimaryButton @click="startOrder" :disabled="loading">Iniciar órden</PrimaryButton>
          </div>
          <div v-else class="flex justify-end space-x-1">
            <CancelButton @click="finishOrderModal = false; form.reset()" :disabled="loading">Cancelar</CancelButton>
            <PrimaryButton @click="finishOrder" :disabled="loading">Subir</PrimaryButton>
          </div>
        </template>
      </DialogModal>

      <DialogModal :show="showModificationsModal" @close="showModificationsModal = false">
        <template #title>
          <p>Solicitar modificaciones</p>
        </template>
        <template #content>
          <form @submit.prevent="storeModifications" ref="myForm">
            <div>
              <InputLabel value="Modificaciones*" />
              <el-input v-model="modificationsForm.modifications" :rows="3" maxlength="800" placeholder="..."
                show-word-limit type="textarea" />
              <InputError :message="modificationsForm.errors.modifications" />
            </div>
          </form>
        </template>
        <template #footer>
          <CancelButton @click="showModificationsModal = false; modificationsForm.reset();"
            :disabled="creatingModification">
            Cerrar
          </CancelButton>
          <PrimaryButton @click="submitModificationsForm" :disabled="creatingModification">
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
import General from "./Tabs/General.vue";
import Modifications from "./Tabs/Modifications.vue";
import InputLabel from "@/Components/InputLabel.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";

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
      //cargas
      creatingModification: false,
      loading: false,
      // generales
      selectedDesign: "",
      startOrderModal: false,
      finishOrderModal: false,
      showModificationsModal: false,
      helpDialog: true,
      activeTab: '1',
      // modifications: null,
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
    General,
    Modifications,
    InputLabel,
    FileUploader,
  },
  methods: {
    handleClickInTab(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    setTabInUrl() {
      // Obtener la URL actual
      const currentURL = new URL(window.location.href);
      // Extraer el valor de 'currentTab' de los parámetros de búsqueda
      const currentTabFromURL = currentURL.searchParams.get('currentTab');

      if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
      }
    },
    submitModificationsForm() {
      this.$refs.myForm.dispatchEvent(new Event('submit', { cancelable: false }));
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
    async startOrder() {
      this.loading = true;
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

          this.design.data.expected_end_at = response.data.item.expected_end_at;
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
      } finally {
        this.loading = false;
      }
    },
    async finishOrder() {
      this.loading = true;
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
      } finally {
        this.loading = false;
      }
    },
    async storeModifications() {
      this.creatingModification = true;
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
      } finally {
        this.creatingModification = true;
      }
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
  },
  mounted() {
    this.selectedDesign = this.design.data.id;
    this.setTabInUrl();
  },
};
</script>