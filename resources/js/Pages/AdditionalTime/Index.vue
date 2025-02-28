<template>
  <div class="dark:text-white">
    <AppLayout title="Solicitud de tiempo adicional">
      <template #header>
        <div class="flex justify-between">
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Solicitudes de tiempo adicional
            </h2>
          </div>
          <div>
            <SecondaryButton @click="createRequestModal = true; form.reset(); editFlag = false">+ Nuevo
            </SecondaryButton>
          </div>
        </div>
      </template>

      <div class="flex space-x-6 items-center justify-center text-xs mt-2">
        <p> <i class="fa-regular fa-clock mr-1 text-amber-500"></i>Esperando autorización </p>
        <p><i class="fa-regular fa-circle-check mr-1 text-green-500"></i>Autorizado</p>
      </div>

      <!-- tabla -->
      <div class="lg:w-5/6 mx-auto mt-6">
        <div class="flex justify-between">
          <!-- pagination -->
          <div>
            <el-pagination @current-change="handlePagination" layout="prev, pager, next"
              :total="more_additional_times.data.length" />
          </div>
          <!-- buttons -->
          <div>
            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
              @confirm="deleteSelections">
              <template #reference>
                <el-button type="danger" plain class="mb-3" :disabled="disableMassiveActions">Eliminar</el-button>
              </template>
            </el-popconfirm>
          </div>
          <!-- buscador -->
          <IndexSearchBar @search="handleSearch" />
        </div>


        <el-table :data="filteredTableData" max-height="450" style="width: 100%"
          @selection-change="handleSelectionChange" @row-click="handleRowClick" ref="multipleTableRef"
          :row-class-name="tableRowClassName">

          <el-table-column type="selection" width="30" />
          <el-table-column prop="id" label="ID" width="60" />
          <el-table-column prop="created_at" label="Solicitado el" width="120" />
          <el-table-column prop="time_requested" label="Tiempo solicitado" width="140" />
          <el-table-column prop="status" label="Estatus" width="140">
            <template #default="scope">
              <div class="flex">
                <p class="mr-2 mt-px text-[10px]">
                  <i :class="getStatusColor(scope.row)"></i>
                </p>
                <span>{{ scope.row.status }}</span>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="authorized_user_name" label="Autorizado por" width="130" />
          <el-table-column prop="authorized_at" label="Autorizado el" width="120" />
          <el-table-column prop="justification" label="Justificación" width="200" />
          <el-table-column align="right">
            <template #default="scope">
              <el-dropdown v-if="scope.row.authorized_at == 'No autorizado'" trigger="click">
                <button @click.stop
                  class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <template #dropdown>
                  <el-dropdown-menu>
                    <el-dropdown-item @click="edit(scope.row)" v-if="scope.row.authorized_at == 'No autorizado'"><i
                        class="fa-solid fa-pen"></i> Editar</el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <!-- tabla -->

      <!-- -------------- Modal starts----------------------- -->
      <Modal :show="createRequestModal" @close="createRequestModal = false">
        <form @submit.prevent="editFlag == true ? update() : store()">
          <div class="p-3 relative">
            <i @click="
              createRequestModal = false;
            form.reset();
            editFlag = false;
            "
              class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"></i>
            <i v-if="!helpDialog" @click="helpDialog = true"
              class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-3 top-3 cursor-pointer"></i>

            <p class="font-bold text-center">
              Solicitar autorización de timepo adicional
            </p>

            <p class="ml-7 my-4 text-secondary">Nómina en curso</p>
            <div class="mb-2">
              <div class="flex items-center">
                <el-tooltip content="Día de la semana laboral" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-magnifying-glass"></i>
                  </span>
                </el-tooltip>
                <el-select @change="validateDay()" v-model="form.payroll_user_id" clearable
                  placeholder="Selecciona día de la semana laboral" no-data-text="No hay dias registrados"
                  no-match-text="No se encontraron coincidencias">
                  <el-option v-for="item in payrolls_user" :key="item.pivot.id" :label="formatDate(item.pivot.date)"
                    :value="item.pivot.id" />
                </el-select> <br>
              </div>
              <p v-if="invalidDay" class="text-xs text-red-500 ml-7">Este día ya tiene solicitud. Selecciona otro</p>
            </div>
            <div class="ml-7 flex space-x-3 items-center">
              <div class="w-1/3 mr-2">
                <IconInput v-model="form.hours" inputPlaceholder="Horas *" inputType="number">
                  <el-tooltip content="horas" placement="top">
                    <i class="fa-regular fa-clock"></i>
                  </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.hours" />
              </div>
              hrs
              <div class="w-1/3">
                <IconInput v-model="form.minutes" inputPlaceholder="Minutos *" inputType="number">
                </IconInput>
                <InputError :message="form.errors.minutes" />
              </div>
              <span class="ml-1">min</span>
            </div>
            <div class="flex ml-7 mt-2 w-3/4">
              <el-tooltip content="Justificación" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                  <i class="fa-solid fa-grip-lines"></i>
                </span>
              </el-tooltip>
              <textarea v-model="form.justification" class="textarea w-full" autocomplete="off"
                placeholder="Justificación"></textarea>
              <InputError :message="form.errors.justification" />
            </div>
            <div v-if="helpDialog" class="border border-[#0355B5] rounded-lg px-6 py-2 mt-5 mx-7 relative">
              <i
                class="fa-solid fa-question text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"></i>
              <i @click="helpDialog = false"
                class="fa-solid fa-xmark cursor-pointer w-3 h-3 rounded-full text-secondary flex items-center justify-center absolute right-3 top-3 text-xs"></i>
              <p class="text-secondary text-sm">
                Es necesario describir las actividades que justifiquen el tiempo
                adicional que estás solicitando. Sólo se podrá realizar una
                solicitud por dia, por lo que debes de ingresar las horas
                adicionales a tu jornada normal.
              </p>
            </div>

            <div class="flex justify-start space-x-3 pt-5 pb-1">
              <PrimaryButton :disabled="invalidDay">{{ editFlag == true ? 'Actualizar' : 'Enviar' }}</PrimaryButton>
              <CancelButton @click="
              createRequestModal = false;
            form.reset();
            editFlag = false
              ">Cancelar</CancelButton>
            </div>
          </div>
        </form>
      </Modal>
      <!-- --------------------------- Modal ends ------------------------------------ -->
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import axios from "axios";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
  data() {
    const form = useForm({
      hours: null,
      minutes: null,
      justification: null,
      payroll_user_id: null,
    });

    return {
      form,
      disableMassiveActions: true,
      inputSearch: '',
      search: "",
      createRequestModal: false,
      helpDialog: false,
      editFlag: false,
      more_additional_time: null,
      invalidDay: false,
      // pagination
      itemsPerPage: 10,
      start: 0,
      end: 10,
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    CancelButton,
    Link,
    TextInput,
    Modal,
    InputError,
    IconInput,
    IndexSearchBar,
  },
  props: {
    more_additional_times: Array,
    payrolls_user: Array,
    requested_payrolls_user: Array,
  },
  methods: {
    getStatusColor(row) {
      if (row.status == 'Autorizado') {
        return 'text-green-500 fa-regular fa-circle-check';
      } else {
        return 'text-amber-500 fa-regular fa-clock';
      }
    },
    validateDay() {
      if (this.requested_payrolls_user?.some(item => item.pivot.id === this.form.payroll_user_id)) {
        this.invalidDay = true;
      } else {
        this.invalidDay = false;
      }
    },
    formatDate(date) {
      const parsedDate = new Date(date);
      return format(parsedDate, 'EEEE d \'de\' MMMM', { locale: es }); // Formato personalizado
    },
    handleSearch(search) {
      this.search = search;
    },
    store() {
      this.form.post(route("more-additional-times.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Solicitud de tiempo adicional enviada",
            type: "success",
          });

          this.form.reset();
          this.createRequestModal = false;
        },
      });
    },
    update() {
      this.form.put(route("more-additional-times.update", this.more_additional_time), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Solicitud de tiempo adicional editada",
            type: "success",
          });

          this.form.reset();
          this.createRequestModal = false;
          this.editFlag = false;
        },
      });
    },
    edit(obj) {
      var parts = obj.time_requested.split(":");
      this.editFlag = true;
      this.more_additional_time = obj;
      this.createRequestModal = true;
      this.form.hours = parts[0];
      this.form.minutes = parts[1];
      this.form.justification = obj.justification;
      this.form.payroll_user_id = obj.payroll_user_id;
      this.invalidDay = false;
    },

    handleSelectionChange(val) {
      this.$refs.multipleTableRef.value = val;

      if (!this.$refs.multipleTableRef.value.length) {
        this.disableMassiveActions = true;
      } else {
        this.disableMassiveActions = false;
      }
    },
    tableRowClassName({ row, rowIndex }) {
      return 'cursor-pointer text-xs';
    },
    handleRowClick(row) {
      if (row.status != 'Autorizado')
        this.edit(row);
      else {
        return
      }
    },

    handlePagination(val) {
      this.start = (val - 1) * this.itemsPerPage;
      this.end = val * this.itemsPerPage;
    },

    async deleteSelections() {
      try {
        const response = await axios.post(
          route("more-additional-times.massive-delete", {
            more_additional_times: this.$refs.multipleTableRef.value,
          })
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          // update list of companies
          let deletedIndexes = [];
          this.more_additional_times.data.forEach((more_additional_time, index) => {
            if (this.$refs.multipleTableRef.value.includes(more_additional_time)) {
              deletedIndexes.push(index);
            }
          });

          // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
          deletedIndexes.sort((a, b) => b - a);

          // Eliminar clientes por índice
          for (const index of deletedIndexes) {
            this.more_additional_times.data.splice(index, 1);
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
      }
    },

  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.more_additional_times.data.filter(
          (item, index) => index >= this.start && index < this.end
        );
      } else {
        return this.more_additional_times.data.filter(
          (more_additional_time) =>
            more_additional_time.status
              .toLowerCase()
              .includes(this.search.toLowerCase()) ||
            more_additional_time.id
              .toString()
              .toLowerCase()
              .includes(this.search.toLowerCase()) ||
            more_additional_time.justification
              .toLowerCase()
              .includes(this.search.toLowerCase())
        );
      }
    },
  },
};
</script>
