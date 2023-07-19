<template>
  <div>
    <AppLayout title="Mantenimiento - Editar">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('machines.show', maintenance.machine_id)"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar mantenimiento
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-2"
        >
          <div style="margin-top: 20px">
            <el-radio-group v-model="form.maintenance_type">
              <el-radio-button label="Preventivo" />
              <el-radio-button label="Correctivo" />
            </el-radio-group>
          </div>
          <div v-if="form.maintenance_type == 'Correctivo'">
            <div class="flex col-span-full">
              <el-tooltip
                content="Problemas o causas del mantenimiento"
                placement="top"
              >
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
                >
                  ...
                </span>
              </el-tooltip>
              <textarea
                v-model="form.problems"
                class="textarea"
                autocomplete="off"
                placeholder="Problemas *"
              ></textarea>
              <InputError :message="form.errors.problems" />
            </div>
          </div>
          <div>
            <div class="flex col-span-full">
            <el-tooltip
              content="Descripción de lo que se le hizo a la máquina"
              placement="top"
            >
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
              >
                ...
              </span>
            </el-tooltip>
            <textarea
              v-model="form.actions"
              class="textarea"
              autocomplete="off"
              placeholder="Acciones *"
            ></textarea>
              <InputError :message="form.errors.actions" />
            </div>
          </div>
          <div>
            <IconInput
              v-model="form.cost"
              inputPlaceholder="Costo *"
              inputType="number"
            >
              <el-tooltip content="Costo" placement="top">
                <i class="fa-solid fa-sack-dollar"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.cost" />
          </div>
          <div>
            <IconInput
              v-model="form.responsible"
              inputPlaceholder="Responsable *"
              inputType="text"
            >
              <el-tooltip
                content="Persona o empresa que realizó el mantenimiento"
                placement="top"
              >
                <i class="fa-solid fa-user"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.responsible" />
          </div>

          <div class="col-span-full">
            <label class="text-[#747474]">Imágenes de evidencia</label>
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9"
              >
                <el-tooltip content="Imágenes de evidencia " placement="top">
                  <i class="fa-solid fa-file-invoice"></i>
                </el-tooltip>
              </span>
              <input
                @input="form.media = $event.target.files[0]"
                class="input h-12 rounded-lg file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white file:cursor-pointer hover:file:bg-red-600"
                aria-describedby="file_input_help"
                id="file_input"
                type="file"
              />
            </div>
            <p
              class="mt-1 text-xs text-right text-gray-500"
              id="file_input_help"
            >
              SVG, PNG, JPG o GIF (MAX. 4 MB).
            </p>
          </div>

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Actualizar mantenimiento
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
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";

export default {
  data() {
    const form = useForm({
      maintenance_type: 'Correctivo',
      problems: this.maintenance.problems,
      actions: this.maintenance.actions,
      cost: this.maintenance.cost,
      responsible: this.maintenance.responsible,
      machine_id: this.maintenance.machine_id,
      media: null
    });

    return {
      form,
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    Link,
    InputError,
    IconInput,
  },
  props: {
    maintenance: Object,
    media: Object,
  },
  methods: {
    update() {
      this.form.put(route("maintenances.update", this.maintenance), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Mantenimiento Actualizado",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
  },
};
</script>
