<template>
  <div>
    <AppLayout title="Mantenimiento - Editar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar mantenimiento
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-2">
          <h1 class="font-bold text-end">{{ maintenance.machine.name }}</h1>
          <div style="margin-top: 20px">
            <p class="text-[#999999] mb-3 text-sm">Selecciona el tipo de mantenimiento que se va a realizar</p>
            <el-radio-group v-model="form.maintenance_type" class="mb-4">
              <el-radio-button label="Preventivo" />
              <el-radio-button label="Correctivo" />
              <el-radio-button label="Limpieza" />
            </el-radio-group>
          </div>
          <div>
            <InputLabel value="Fecha de realización*" />
            <el-date-picker v-model="form.start_date" type="date" placeholder="Selecciona un día" format="DD/MM/YYYY"
              value-format="YYYY-MM-DD" />
            <InputError :message="form.errors.start_date" />
          </div>
          <div v-if="form.maintenance_type == 'Limpieza'">
            <p class="text-sm font-bold">Acciones realizadas</p>
            <p class="text-sm text-[#999999]">Seleccione las acciones realizadas a la máquina</p>
            <label v-for="(action, index) in cleaningActions" :key="index"
              class="lg:w-1/3 flex items-center cursor-pointer">
              <input type="checkbox" :value="action" v-model="actions"
                class="bg-transparent text-primary focus:ring-primary rounded-sm" />
              <span class="ml-2 text-sm">{{ action }}</span>
            </label>
            <InputError :message="form.errors.actions" />
          </div>
          <div v-if="form.maintenance_type == 'Correctivo'">
            <InputLabel value="Problemas detectados*" />
            <textarea v-model="form.problems" class="textarea" autocomplete="off"
              placeholder="Ej. Banda transportadora atascada"></textarea>
            <InputError :message="form.errors.problems" />
          </div>
          <div v-if="form.maintenance_type != 'Limpieza'">
            <InputLabel value="Acciones realizadas*" />
            <textarea v-model="form.actions" class="textarea" autocomplete="off"
              placeholder="Ej. Sintonización de servo para la banda"></textarea>
            <InputError :message="form.errors.actions" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <InputLabel value="Costo" />
              <el-input v-model="form.cost" type="text"
                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 1,200">
                <template #prefix>
                  <i class="fa-solid fa-dollar-sign"></i>
                </template>
              </el-input>
              <InputError :message="form.errors.cost" />
            </div>
            <div>
              <InputLabel value="Responsable*" />
              <el-input v-model="form.responsible" type="text" placeholder="Ej. Ing. Marcelino" />
              <InputError :message="form.errors.responsible" />
            </div>
          </div>
          <div v-if="form.maintenance_type != 'Limpieza'" class="col-span-full">
            <InputLabel value="Imágenes de evidencia" />
            <FileUploader @files-selected="this.form.media = $event" acceptedFormat="imagen"
              :multiple="false" />
            <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
              SVG, PNG, JPG o GIF (MAX. 4 MB).
            </p>
          </div>
          <div class="pt-8 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Guardar cambios
            </PrimaryButton>
          </div>
        </div>
      </form>
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";

export default {
  data() {
    const form = useForm({
      maintenance_type: null,
      start_date: this.maintenance.start_date,
      problems: this.maintenance.problems,
      actions: this.maintenance.actions,
      cost: this.maintenance.cost,
      responsible: this.maintenance.responsible,
      machine_id: this.maintenance.machine_id,
      media: null
    });

    return {
      form,
      cleaningActions: [
        'Limpieza externa',
        'Limpieza interna',
        'Lubricación',
        'Inspección general',
      ],
      actions: [],
      maintenanceTypes: [
        'Preventivo',
        'Correctivo',
        'Limpieza',
      ]
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    InputError,
    IconInput,
    Back,
    Link,
    InputLabel,
    FileUploader,
  },
  props: {
    maintenance: Object,
    media: Object,
  },
  methods: {
    update() {
      if (this.form.media !== null) {
        this.form.post(route("maintenances.update-with-media", this.maintenance), {
          method: '_put',
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se actualizó correctamente",
              type: "success",
            });
          },
        });
      } else {
        this.form.put(route("maintenances.update", this.maintenance), {
          onSuccess: () => {
            this.$notify({
              title: "Éxito",
              message: "Se actualizó correctamente",
              type: "success",
            });
          },
        });
      }
    },
    setMaintenanceType() {
      if (this.maintenance.maintenance_type_id == '0') {
        this.form.maintenance_type = 'Preventivo';
      } else if (this.maintenance.maintenance_type_id == '1') {
        this.form.maintenance_type = 'Correctivo';
      } else if (this.maintenance.maintenance_type_id == '2') {
        this.form.maintenance_type = 'Limpieza';
        this.actions = this.maintenance.actions.split(', ');
      }
    },
  },
  mounted() {
    this.setMaintenanceType();
  },
};
</script>
