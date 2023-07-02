<template>
  <div>
    <AppLayout title="Maquinaria - Editar">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('machines.index')"
            class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Editar {{ machine.name }}</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-2"
        >
          <div>
            <IconInput
              v-model="form.name"
              inputPlaceholder="Nombre *"
              inputType="text"
            >
              <el-tooltip content="Nombre" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <IconInput
              v-model="form.serial_number"
              inputPlaceholder="Número de serie"
              inputType="text"
            >
              <el-tooltip content="Número de serie" placement="top">
                #
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.serial_number" />
          </div>
          <div class="grid md:grid-cols-4 grid-cols-2">
            <div>
              <IconInput
                v-model="form.weight"
                inputPlaceholder="Peso(Kg)"
                inputType="number"
              >
                <el-tooltip content="Peso(Kg)" placement="top">
                  <i class="fa-solid fa-weight-hanging"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.weight" />
            </div>
            <div>
              <IconInput
                v-model="form.width"
                inputPlaceholder="Ancho(Cm)"
                inputType="number"
              >
                <el-tooltip content="Ancho(Cm)" placement="top">
                  <i class="fa-solid fa-text-width"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.width" />
            </div>
            <div>
              <IconInput
                v-model="form.large"
                inputPlaceholder="Largo(Cm)"
                inputType="number"
              >
                <el-tooltip content="Largo(Cm)" placement="top">
                  <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.large" />
            </div>
            <div>
              <IconInput
                v-model="form.height"
                inputPlaceholder="Alto(Cm)"
                inputType="number"
              >
                <el-tooltip content="Alto(Cm)" placement="top">
                  <i class="fa-solid fa-arrows-up-down"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.height" />
            </div>
          </div>
          <div>
            <IconInput
              v-model="form.cost"
              inputPlaceholder="Costo"
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
              v-model="form.supplier"
              inputPlaceholder="Proveedor"
              inputType="text"
            >
              <el-tooltip content="Proveedor" placement="top">
                <i class="fa-solid fa-store"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.supplier" />
          </div>
          <div>
            <IconInput
              v-model="form.days_next_maintenance"
              inputPlaceholder="Mantenimiento cada (días) *"
              inputType="number"
            >
              <el-tooltip content="Mantenimiento cada (días) *" placement="top">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.days_next_maintenance" />
          </div>
          <div>
            <IconInput
              v-model="form.aquisition_date"
              inputPlaceholder="Fecha de adquisición"
              inputType="date"
            >
              <el-tooltip content="Fecha de adquisición" placement="top">
                <i class="fa-solid fa-calendar"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.aquisition_date" />
          </div>

          <div class="col-span-full">
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9"
              >
                <el-tooltip content="Imagen de la máquina" placement="top">
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
              Actualizar máquina
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";

export default {
  data() {
    const form = useForm({
      name: this.machine.name,
      serial_number: this.machine.serial_number,
      weight: this.machine.weight,
      width: this.machine.width,
      large: this.machine.large,
      height: this.machine.height,
      cost: this.machine.cost,
      supplier: this.machine.supplier,
      aquisition_date: this.machine.aquisition_date,
      days_next_maintenance: this.machine.days_next_maintenance,
    });

    return {
      form,
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    Link,
    InputError,
    IconInput,
  },
  props: {
    machine: Object
  },
  methods: {
    update() {
      this.form.put(route("machines.update", this.machine), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Máquina actualizada",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
  },
};
</script>
