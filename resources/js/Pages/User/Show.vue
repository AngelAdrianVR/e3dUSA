<template>
  <div>
    <AppLayoutNoHeader title="Usuarios">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Usuarios</label>

          <Link :href="route('users.index')"
            class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="w-1/3">
            <el-select @change="$inertia.get(route('users.show', userSelected))" v-model="userSelected" clearable
              filterable placeholder="Buscar órden de diseño" no-data-text="No hay órdenes registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in users" :key="item.id" :label="item.id + '. ' + item.name" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Editar" placement="top">
              <Link :href="route('users.edit', userSelected)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <el-tooltip content="Crear nuevo usuario" placement="top">
              <Link :href="route('users.create')">
              <button class="rounded-lg bg-primary py-2 px-3 text-sm text-white">
                +
              </button>
              </Link>
            </el-tooltip>
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <!-- <DropdownLink :href="route('users.create')">
                  Generar recibo de vacaciones
                </DropdownLink>
                <DropdownLink :href="route('users.create')">
                  Reporte de actividades
                </DropdownLink> -->
                <DropdownLink @click="changeUserStatus" as="button">
                  {{
                    user.data.is_active.bool
                    ? "Desactivar usuario"
                    : "Activar usuario"
                  }}
                </DropdownLink>

                <DropdownLink @click="resetPassword" as="button">
                  Resetear contraseña
                </DropdownLink>
                <DropdownLink @click="showVacationsModal = true" as="button">
                  Pagar vacaciones
                </DropdownLink>
                <DropdownLink @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>

      <div class="flex flex-col items-center justify-center mb-4">
        <img v-if="user.data" :class="user.data.is_active.bool ? 'border-green-600' : 'border-red-600'
          " class="h-32 w-32 rounded-full object-cover hidden md:block border-2" :src="user.data.profile_photo_url"
          :alt="user.data.name" />
        <p class="font-bold text-lg">{{ user.data.name }}</p>
        <p v-if="user.data.is_active.bool">Experiencia. {{ user.data.experience }}</p>
      </div>
      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Información general
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Desempeño
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <p class="text-secondary col-span-2 mt-4 mb-2">Datos personales</p>

          <span class="text-gray-500">Nombre</span>
          <span>{{ user.data.name }}</span>
          <span class="text-gray-500 my-2">Fecha de nacimiento</span>
          <span>{{ user.data.employee_properties?.birthdate.raw }}</span>
          <!-- <span class="text-gray-500 my-2">Dependientes económicos</span>
          <span>{{ "--" }}</span>
          <span class="text-gray-500 my-2">Dirección</span>
          <span>{{ "--" }}</span>
          <span class="text-gray-500 my-2">Teléfono</span>
          <span>{{ "--" }}</span> -->
          <span class="text-gray-500 my-2">Correo personal</span>
          <span>{{ user.data.email }}</span>

          <p class="text-secondary col-span-2 mt-7">Datos laborales</p>

          <span class="text-gray-500 my-2">ID</span>
          <span>{{ user.data.id }}</span>
          <span class="text-gray-500 my-2">Fecha de ingreso</span>
          <span>{{ user.data.created_at }}</span>
          <span class="text-gray-500 my-2">Correo corporativo</span>
          <span>{{ "--" }}</span>
          <span class="text-gray-500 my-2">Departamento</span>
          <span>{{ user.data.employee_properties?.department }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center self-start">
          <span class="text-gray-500 my-2">Puesto</span>
          <span>{{ user.data.employee_properties?.job_position }}</span>
          <!-- <span class="text-gray-500">NSS</span>
          <span>{{ "--" }}</span>
          <span class="text-gray-500 my-2">RFC</span>
          <span>{{ "--" }}</span>
          <span class="text-gray-500 my-2">Curp</span>
          <span>{{ "--" }}</span>
          <span class="text-gray-500 my-2">Nivel académico</span>
          <span>{{ "--" }}</span> -->
          <span class="text-gray-500 my-2">Salario semanal</span>
          <span>${{
            user.data.employee_properties?.salary.week
              .toFixed(2)
              .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
          }}</span>
          <span class="text-gray-500 my-2">Horas laborales por semana</span>
          <span>{{ user.data.employee_properties?.hours_per_week }}</span>
          <span class="text-gray-500 my-2">Vacaciones disponibles</span>
          <span>{{ user.data.employee_properties?.vacations.available_days.toFixed(3) }} dias</span>

          <!-- <p class="text-secondary col-span-2 mt-7">Contacto de emergencia</p>

          <span class="text-gray-500 my-2">Nombre</span>
          <span>{{ "--" }}</span>

          <span class="text-gray-500 my-2">Parentezco</span>
          <span>{{ "--" }}</span>
          <span class="text-gray-500 my-2">Teléfono</span>
          <span>{{ "--" }}</span> -->
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 desempeño starts ------------- -->
      <div v-if="tabs == 2" class="border-b-2 border-[#cccccc] text-sm">
        <div v-if="user.data.is_active?.bool" class="px-12 pb-12 pt-6">
          <!-- --------------------------- performance tab starts --------------------------- -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h2 class="text-secondary">General</h2>
              <div class="px-6">
                <li v-for="(item, index) in personal" :key="index">
                  {{ item.label }}: {{ item.value }}
                </li>
              </div>
            </div>
            <div>
              <h2 class="text-secondary">Producción</h2>
              <div class="px-6">
                <li v-for="(item, index) in production_performances" :key="index">
                  {{ item.label }}: {{ item.value }}
                </li>
              </div>
            </div>
            <div>
              <h2 class="text-secondary">Diseño</h2>
              <div class="px-6">
                <li v-for="(item, index) in design_performances" :key="index">
                  {{ item.label }}: {{ item.value }}
                </li>
              </div>
            </div>
            <div>
              <h2 class="text-secondary">Ventas</h2>
              <div class="px-6">
                <li v-for="(item, index) in sale_performances" :key="index">
                  {{ item.label }}: {{ item.value }}
                </li>
              </div>
            </div>
            <div>
              <h2 class="text-secondary">Habilidades</h2>
              <div v-if="user.data.employee_properties?.skills?.length > 0" class="px-6">
                <li v-for="(item, index) in user.data.employee_properties?.skills" :key="index">
                  {{ item }}
                </li>
              </div>
              <p v-else class="text-xs text-gray-500">No hay habilidades registradas en este usuario</p>
            </div>
          </div>

          <!-- --------------------------- performance tab ends --------------------------- -->

          <!-- ------------------------------ Performance table ------------------------ -->
          <!-- <p class="text-secondary col-span-2 mt-4 mb-4 text-center">
            Tabla de desempeño
          </p>
          <div class="lg:grid grid-cols-3 px-5 overflow-auto pb-12">
            <div class="col-span-2">
              <PerformanceTable />
            </div>

            <div class="text-center">
              <p class="mb-5">Tabla de puntuaciones</p>

              <div
                class="grid grid-cols-2 rounded-xl lg:mx-10 border-2 border-[#cccccc]"
              >
                <div class="py-1 rounded-xl">70 - 100</div>
                <div
                  class="py-1 bg-green-500 rounded-xl border-2 border-[#cccccc]"
                >
                  Sobresaliente
                </div>
                <div class="py-1 rounded-xl">40 - 69</div>
                <div
                  class="py-1 bg-orange-500 rounded-xl border-2 border-[#cccccc]"
                >
                  Falta mejorar
                </div>
                <div class="py-1 rounded-xl">0 - 39</div>
                <div
                  class="py-1 bg-red-500 rounded-xl border-2 border-[#cccccc]"
                >
                  Deficiente
                </div>
              </div>
            </div>
          </div> -->
        </div>
        <div v-else>
          <p class="text-primary text-center text-xl">El usuario se encuentra inactivo actualmente</p>
        </div>
      </div>
      <!-- ------------- tab 2 desempeño ends ------------ -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar usuario </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div>
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>

      <DialogModal :show="showVacationsModal" @close="showVacationsModal = false">
        <template #title> Pagar vacaciones </template>
        <template #content>
          <div class="grid grid-cols-3 gap-y-1 gap-x-6">
            <span class="col-span-2 text-right">Días de vacaciones disponibles hasta el momento</span>
            <b>{{ user.data.employee_properties.vacations.available_days.toFixed(2) }}</b>
            <span class="col-span-2 text-right">Días a pagar</span>
            <input v-model="vacationsToPay" type="number" min="1"
              :max="user.data.employee_properties.vacations.available_days.toFixed(2)" step="0.01" class="input">
            <span class="col-span-2 text-right">Promedio de horas trabajadas por día</span>
            <b>{{ user.data.employee_properties.hours_per_week /
              user.data.employee_properties.work_days.filter(day => day.check_in !== null).length }}</b>
            <span class="col-span-2 text-right">Promedio de sueldo por día</span>
            <b>${{ (user.data.employee_properties.hours_per_week /
              user.data.employee_properties.work_days.filter(day => day.check_in !== null).length) *
              user.data.employee_properties.salary.hour }}</b>
            <span class="col-span-2 text-right">Monto por días de vacaciones</span>
            <b>
              ${{ getVacationsAmount().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
            </b>
            <span class="col-span-2 text-right">Monto por prima vacacional</span>
            <b>
              ${{ (getVacationsAmount() * 0.25).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
            </b>
            <span class="col-span-2 text-right">Total a pagar</span>
            <b>
              ${{ (getVacationsAmount() * 1.25).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
            </b>
          </div>
          <p class="text-center text-sm mt-6 text-secondary">
            Al dar click en el botón "Pagar", se rebajarán los días especificados en el campo númerico de arriba.
          </p>
        </template>
        <template #footer>
          <div>
            <CancelButton @click="showVacationsModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="clearVacations" :disabled="!vacationsToPay || vacationsToPay <= 0">Pagar
            </PrimaryButton>
          </div>
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
import PerformanceTable from "@/Components/MyComponents/PerformanceTable.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      userSelected: "",
      // currentUser: null,
      employeeExperience: "",
      showConfirmModal: false,
      showVacationsModal: false,
      tabs: 1,
      vacationsToPay: this.user.data.employee_properties.vacations.available_days.toFixed(2),
    };
  },
  props: {
    user: Object,
    users: Array,
    personal: Array,
    production_performances: Array,
    design_performances: Array,
    sale_performances: Array,
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    CancelButton,
    PrimaryButton,
    Modal,
    CancelButton,
    InputError,
    ConfirmationModal,
    PerformanceTable,
    Link,
    DialogModal,
  },
  methods: {
    async clearVacations() {
      try {
        const response = await axios.put(route('users.update-vacations', this.user.data), { operation: '-', days: this.vacationsToPay });

        if (response.status === 200) {
          this.user.data.employee_properties.vacations.available_days = response.data.available_days;
          this.showVacationsModal = false;
          this.$notify({
            title: "Éxito",
            message: "Se ha actualizado la cantidad de vacaciones disponibles para este empleado",
            type: "success",
          });
        }

      } catch (error) {
        console.log(error);
        this.$notify({
          title: "Algo inesperado ha sucedio en el servidor",
          message: "El servidor no pudo procesar la solicitud. Favor de intentarlo más tarde",
          type: "error",
        });
      }
    },
    getVacationsAmount() {
      return ((this.user.data.employee_properties.hours_per_week /
        this.user.data.employee_properties.work_days.filter(day => day.check_in !== null).length) *
        this.user.data.employee_properties.salary.hour *
        this.vacationsToPay);
    },
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("users.destroy", this.user.data.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          // const index = this.users.findIndex(
          //   (item) => item.id === this.user.data.id
          // );
          // if (index !== -1) {
          //   this.users.splice(index, 1);
          //   this.userSelected = "";
          // }
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
        this.$inertia.get(route('users.index'));
      }
    },

    resetPassword() {
      this.$inertia.put(route("users.reset-pass", this.user.data.id));
      this.$notify({
        title: "Éxito",
        message: "Contraseña reseteada a 'e3d'",
        type: "success",
      });
    },

    changeUserStatus() {
      this.$inertia.put(route("users.change-status", this.user.data.id));
      this.$notify({
        title: "Éxito",
        message: this.user.data.is_active.bool
          ? "Usuario activado"
          : "Usuario desactivado",
        type: "success",
      });
    },
    userSelection() {
      this.user.data = this.users.data.find(
        (item) => item.id == this.userSelected
      );
      this.$inertia.visit("/users/" + this.user.data.id);
    },
  },

  // watch: {
  //   selectedUser(newVal) {
  //     this.currentUser = this.users.data.find((item) => item.id == newVal);
  //   },
  // },

  mounted() {
    this.userSelected = this.user.data.id;
    // this.currentUser = this.users.data.find(
    //   (item) => item.id == this.userSelected
    // );
  },
};
</script>