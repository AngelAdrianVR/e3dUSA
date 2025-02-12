<template>
  <div>
    <AppLayout title="Maquinaria - Agregar">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Agregar máquina</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Nombre *" inputType="text">
              <el-tooltip content="Nombre" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <IconInput v-model="form.serial_number" inputPlaceholder="Número de serie" inputType="text">
              <el-tooltip content="Número de serie" placement="top">
                #
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.serial_number" />
          </div>
          <div class="grid md:grid-cols-4 grid-cols-2">
            <div>
              <IconInput v-model="form.weight" inputPlaceholder="Peso(Kg)" inputType="number" inputStep="0.01">
                <el-tooltip content="Peso(Kg)" placement="top">
                  <i class="fa-solid fa-weight-hanging"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.weight" />
            </div>
            <div>
              <IconInput v-model="form.width" inputPlaceholder="Ancho(Cm)" inputType="number" inputStep="0.01">
                <el-tooltip content="Ancho(Cm)" placement="top">
                  <i class="fa-solid fa-text-width"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.width" />
            </div>
            <div>
              <IconInput v-model="form.large" inputPlaceholder="Largo(Cm)" inputType="number" inputStep="0.01">
                <el-tooltip content="Largo(Cm)" placement="top">
                  <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.large" />
            </div>
            <div>
              <IconInput v-model="form.height" inputPlaceholder="Alto(Cm)" inputType="number" inputStep="0.01">
                <el-tooltip content="Alto(Cm)" placement="top">
                  <i class="fa-solid fa-arrows-up-down"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.height" />
            </div>
          </div>
          <div>
            <IconInput v-model="form.cost" inputPlaceholder="Costo" inputType="number" inputStep="0.01">
              <el-tooltip content="Costo" placement="top">
                <i class="fa-solid fa-sack-dollar"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.cost" />
          </div>
          <div>
            <IconInput v-model="form.supplier" inputPlaceholder="Proveedor" inputType="text">
              <el-tooltip content="Proveedor" placement="top">
                <i class="fa-solid fa-store"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.supplier" />
          </div>
          <div>
            <IconInput v-model="form.days_next_maintenance" inputPlaceholder="Mantenimiento cada (días) *"
              inputType="number">
              <el-tooltip content="Mantenimiento cada (días) *" placement="top">
                <i class="fa-solid fa-screwdriver-wrench"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.days_next_maintenance" />
          </div>
          <div class="flex items-center">
            <el-tooltip content="Fecha de adquisición *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
            </el-tooltip>
            <el-date-picker v-model="form.aquisition_date" type="date" placeholder="Fecha de adquisición * *"
              format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
            <InputError :message="form.errors.aquisition_date" :disabled-date="disabledDate" />
          </div>
          <div class="col-span-full">
            <div class="flex items-center">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                <el-tooltip content="Imagen del producto" placement="top">
                  <i class="fa-solid fa-images"></i>
                </el-tooltip>
              </span>
              <input @input="form.media = $event.target.files[0]" class="input h-12 rounded-lg
                            file:mr-4 file:py-1 file:px-2
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary file:text-white
                            file:cursor-pointer
                            hover:file:bg-red-600" aria-describedby="file_input_help" id="file_input" type="file">
            </div>
            <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">SVG, PNG, JPG o
              GIF (MAX. 4 MB).</p>
          </div>

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Agregar máquina
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
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: null,
      serial_number: null,
      weight: null,
      width: null,
      large: null,
      height: null,
      cost: null,
      supplier: null,
      aquisition_date: null,
      days_next_maintenance: null,
      media: null,
    });

    return {
      form,
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Back,
    Link
  },
  props: {

  },
  methods: {
    store() {
      this.form.post(route("machines.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Nueva máquina agregada",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() > today.getTime();
    },
  },
};
</script>
