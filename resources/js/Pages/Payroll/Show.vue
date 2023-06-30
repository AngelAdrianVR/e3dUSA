<template>
  <div>
    <AppLayout title="Nóminas">
      <template #header>
        <div class="flex justify-between">
          <div class="flex items-center space-x-2">
            <Link
              :href="route('payrolls.index')"
              class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center"
            >
              <i class="fa-solid fa-chevron-left"></i>
            </Link>
            <h2 class="font-semibold text-xl leading-tight">
              Nómina-{{ payroll.data.week }}
            </h2>
          </div>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              {{ payroll.data.start_date }} - {{ payroll.data.end_date }}
            </h2>
          </div>
        </div>
      </template>

      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div>
          <label>Nóminas</label>
        </div>
        <div>
          <el-select
            v-model="payroll_selected_id"
            multiple
            filterable
            allow-create
            default-first-option
            :reserve-keyword="false"
            placeholder="Buscar nóminas"
          >
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
        </div>

        <div class="flex justify-center items-center">
          <p>
            <strong class="mr-4">Nómina semanal {{ payroll.data.week }}</strong>
            {{ payroll.data.start_date }} | {{ payroll.data.end_date }}
          </p>
          <div class="flex items-center">
            <ThirthButton @click="incidentModal = true" class="ml-9">
              Registrar incidencia
            </ThirthButton>
            <el-popconfirm
              confirm-button-text="Si"
              cancel-button-text="No"
              icon-color="#FF0000"
              title="Seguro que deseas eliminar?"
              @confirm="deleteIncident"
            >
              <template #reference>
                <i
                  class="fa-regular fa-trash-can text-red-600 hover:text-red-500 ml-3 cursor-pointer"
                ></i>
              </template>
            </el-popconfirm>
          </div>
        </div>
      </div>

      <!-- ------------- tabs section starts ------------- -->
      <div
        class="border-y-2 border-[#cccccc] flex justify-between items-center py-2"
      >
        <div class="flex">
          <p
            @click="incidentsTab = false"
            :class="
              !incidentsTab ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="h-10 p-2 cursor-pointer ml-5 transition duration-300 ease-in-out"
          >
            Imprimir nóminas
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="incidentsTab = true"
            :class="
              incidentsTab ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out"
          >
            Ver incidencias
          </p>
        </div>

        <div v-if="!incidentsTab" class="text-right mr-9 flex items-center">
          <PrimaryButton>Imprimir</PrimaryButton>
          <DropdownNoClose align="right" width="60">
            <template #trigger>
              <i class="fa-solid fa-filter text-gray-600 text-lg ml-5 cursor-pointer"></i>
            </template>
            <template #content>
              <div class="block px-4 py-2 text-xs text-gray-400 text-center">
                Por colaborador
              </div>
              <div class="flex flex-col">
                <div v-for="user in users" :key="user.id" class="flex items-center space-x-2 mx-3">
                    <Checkbox v-model:checked="users_payroll_filtered_id" :name="user.name" :value="user.id" />
                    <label class="text-gray-600 text-sm" :for="user.name">{{ user.name }}</label>
                </div>
                <footer class="grid grid-cols-2 border-t-2 border-[#cccccc] mt-2 py-1">
                      <span @click="console.log('Aplicar filtro')" class="text-primary text-center border-r-2 border-[#cccccc] cursor-pointer">Aplicar</span>
                        <span class="text-center cursor-pointer">cancelar</span>
                </footer>
              </div>
            </template>
          </DropdownNoClose>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- -------------- Incidents starts----------------------- -->
      <div v-if="incidentsTab" class="md:mx-9 md:my-7 space-y-3 m-1 h-48">
        <div class="flex items-center">
          <label class="mr-10">Colaborador</label>
          <el-select v-model="user_selected" class="m-2" placeholder="Select">
            <el-option
              v-for="item in users"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            />
          </el-select>
        </div>
        <div v-if="user_selected" class="mt-5">
          <IncidentTable @closeIncidentTable="console.log('sdoifh')" :justifications="justifications" />
        </div>
      </div>
      <!-- -------------- Incidents ends----------------------- -->

      <!-- -------------- IncidentModal starts----------------------- -->
      <Modal :show="incidentModal" @close="incidentModal = false">
        <div class="mx-7 my-4 space-y-4">
          <div class="flex justify-center mb-7">
            <h2 class="font-bold text-center mr-2">Registrar incidencias</h2>
            <i
              class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center"
            ></i>
          </div>
          <div class="flex items-center">
            <label class="text-gray-600">Colaborador</label>
            <el-select
              v-model="form.user_selected"
              class="mx-2"
              placeholder="Select"
            >
              <el-option
                v-for="item in users"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
            <InputError :message="form.errors.user_selected" />
          </div>
          <div class="flex items-center">
            <label class="text-gray-600">Incidencia</label>
            <el-select
              v-model="form.incident"
              class="mx-2"
              placeholder="Select"
            >
              <el-option
                v-for="item in incidents"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              />
            </el-select>
            <InputError :message="form.errors.incident" />
          </div>
          <div class="flex items-center">
            <label class="text-gray-600">Fecha</label>
            <el-date-picker
              class="mx-2"
              v-model="form.rangeDate"
              type="daterange"
              range-separator="-"
              start-placeholder="Desde"
              end-placeholder="Hasta"
            />
            <InputError :message="form.errors.rangeDate" />
          </div>
          <div class="flex justify-center">
            <IconInput
              v-model="form.days"
              inputPlaceholder="Total"
              inputType="text"
            >
              .
            </IconInput>
            <InputError :message="form.errors.days" />

            <IconInput
              v-model="form.hours"
              inputPlaceholder="Horas"
              inputType="text"
            >
              .
            </IconInput>
            <InputError :message="form.errors.hours" />
          </div>
          <div class="flex">
            <label class="text-gray-600">Descripción</label>
            <textarea
              v-model="form.description"
              class="textarea mx-2"
              autocomplete="off"
            ></textarea>
            <InputError :message="form.errors.description" />
          </div>
          <div class="flex space-x-3 pt-3">
            <PrimaryButton>Guardar</PrimaryButton>
            <CancelButton @click="incidentModal = false">Cancelar</CancelButton>
          </div>
        </div>
      </Modal>
      <!-- -------------- IncidentModal ends----------------------- -->
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import IncidentTable from "@/Components/MyComponents/IncidentTable.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import Checkbox from "@/Components/Checkbox.vue";
import DropdownNoClose from "@/Components/DropdownNoClose.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      user_selected: null,
      incident: null,
      rangeDate: null,
      description: null,
      days: null,
      hours: null,
    });

    return {
      form,
      user_selected: null,
      incidentsTab: true,
      incidentModal: false,
      payroll_selected_id: null,
      users_payroll_filtered_id: [],

      incidents: [
        {
          label: "Permiso sin goce",
          value: "PSG",
        },
        {
          label: "Permiso con goce",
          value: "PCG",
        },
        {
          label: "Vacaciones",
          value: "V",
        },
        {
          label: "Falta justificada",
          value: "FJ",
        },
        {
          label: "Falta injustificada",
          value: "FI",
        },
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    CancelButton,
    ThirthButton,
    DropdownNoClose,
    Modal,
    Link,
    IconInput,
    Checkbox,
    IncidentTable,
  },
  props: {
    payroll: Object,
    payrolls: Object,
    users: Array,
    justifications: Array,
  },
  methods: {
    deleteIncident() {
      console.log("Elimidado");
    },
  },

  computed: {},
};
</script>
