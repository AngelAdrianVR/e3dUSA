<template>
  <div>
    <AppLayoutNoHeader title="Usuarios">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Usuarios</label>
          <Link
            :href="route('users.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
          >
            <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <el-select
            v-model="userSelected"
            clearable
            filterable
            placeholder="Buscar órden de diseño"
            no-data-text="No hay órdenes registradas"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in users"
              :key="item.id"
              :label="item.id + '-' + item.name"
              :value="item.id"
            />
          </el-select>
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
              <PrimaryButton>
                + Nuevo
              </PrimaryButton>
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
              <DropdownLink :href="route('users.create')">
                Geberar recibo de vacaciones
              </DropdownLink>
              <DropdownLink
                :href="route('users.create')"
              >
                Reporte de actividades
              </DropdownLink>
              <DropdownLink
                :href="route('users.create')"
              >
                Desactivar usuario
              </DropdownLink>
              <DropdownLink
                :href="route('users.create')"
              >
                Reseetear contraseña
              </DropdownLink>
              <DropdownLink @click="showConfirmModal = true" as="button">
                Eliminar
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ currentUser?.name }}
      </p>
      {{userSelected}}
      {{currentUser}}
      <!-- ------------- tabs section starts ------------- -->
      <div
        class="border-y-2 border-[#cccccc] flex justify-between items-center py-2"
      >
        <!-- <div class="flex">
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
        </div> -->
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div
        v-if="tabs == 1"
        class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm"
      >
        <div
          class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center"
        >

        <p class="text-secondary col-span-2 mt-4 mb-2">Datos personales</p>

          <span class="text-gray-500">Nombre</span>
          <span>{{ currentUser?.id }}</span>
          <span class="text-gray-500 my-2">Fecha de nacimiento</span>
          <span>{{ currentUser?.user.name }}</span>
          <span class="text-gray-500 my-2">Dependientes económicos</span>
          <span>{{ currentUser?.created_at }}</span>
          <span class="text-gray-500 my-2">Dirección</span>
          <span
            >{{ currentUser?.authorized_user_name }} 
            {{ currentUser?.authorized_at }}</span
          >
          <span class="text-gray-500 my-2">Teléfono</span>
          <span>{{ currentUser?.designer.name }}</span>
          <span class="text-gray-500 my-2">Correo personal</span>
          <span>{{ currentUser?.expected_end_at }}</span>

          <p class="text-secondary col-span-2 mt-7">Datos laborales</p>

          <span class="text-gray-500 my-2">ID</span>
          <span>{{ currentUser?.user.name }}</span>
          <span class="text-gray-500 my-2">Fecha de ingreso</span>
          <span>{{ currentUser?.user.name }}</span>
          <span class="text-gray-500 my-2">Correo corporativo</span>
          <span>{{ currentUser?.user.name }}</span>
          <span class="text-gray-500 my-2">Departamento</span>
          <span>{{ currentUser?.user.name }}</span>
          

        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
        <span class="text-gray-500 my-2">Puesto</span>
          <span>{{ currentUser?.user.name }}</span>
          <span class="text-gray-500">NSS</span>
          <span>{{ currentUser?.company_branch_name }}</span>
          <span class="text-gray-500 my-2">RFC</span>
          <span>{{ currentUser?.contact_name }}</span>
          <span class="text-gray-500 my-2">Curp</span>
          <span>{{ currentUser?.name }}</span>
          <span class="text-gray-500 my-2">Nivel académico</span>
          <span>{{ currentUser?.design_type.name }}</span>
          <span class="text-gray-500 my-2">Salario semanal</span>
          <span>{{ currentUser?.design_type.name }}</span>
          <span class="text-gray-500 my-2">Horas laborales por semana</span>
          <span>{{ currentUser?.design_type.name }}</span>

          <p class="text-secondary col-span-2 mt-7">Contacto de emergencia</p>

          <span class="text-gray-500 my-2">Nombre</span>
          <span>{{ currentUser?.specifications }}</span>

          <span class="text-gray-500 my-2">Parentezco</span>
          <span>{{ currentUser?.measure_unit }}</span>
          <span class="text-gray-500 my-2">Teléfono</span>
          <span>{{ currentUser?.dimensions }}</span>
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      
      <!-- -------------tab 2 modifications starts ------------- -->

      <!-- <div v-if="tabs == 2" class="p-7">
        <p class="text-secondary">Modificaciones</p>
        <div>

        </div>
      </div> -->

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
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    // const form = useForm({
    //   expected_end_at: null,
    // });

    return {
    //   form,
      userSelected: "",
      currentUser: null,
    //   startOrderModal: false,
    //   helpDialog: false,
      tabs: 1,
    };
  },
  props: {
    user: Object,
    users: Array,
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

  },

  watch: {
    selectedUser(newVal) {
      this.currentUser = this.users.find((item) => item.id == newVal);
    },
  },

  mounted() {
    this.userSelected = this.user.id;
  },
};
</script>