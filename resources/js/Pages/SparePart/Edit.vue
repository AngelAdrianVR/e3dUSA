<template>
  <div>
    <AppLayout title="Refacciones - Editar">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('machines.show', spare_part.machine_id)"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar refacción
            </h2>
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
              <el-tooltip content="Nombre de refacción" placement="top">
                A
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
        <div>
            <IconInput
              v-model="form.supplier"
              inputPlaceholder="Proveedor"
              inputType="text"
            >
              <el-tooltip content="Proveedor" placement="top">
                 <i class="fa-solid fa-user"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.supplier" />
          </div>
          <div>
            <IconInput
              v-model="form.quantity"
              inputPlaceholder="Cantidad *"
              inputType="number"
            >
              <el-tooltip content="Cantidad de refacciones en stock" placement="top">
                123
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.quantity" />
          </div>
          <div>
            <IconInput
              v-model="form.location"
              inputPlaceholder="Ubicación *"
              inputType="text"
            >
              <el-tooltip content="Ubicación" placement="top">
                 <i class="fa-solid fa-map-pin"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.location" />
          </div>
          <div>
            <IconInput
              v-model="form.cost"
              inputPlaceholder="Costo *"
              inputType="number"
            >
              <el-tooltip content="Costo unitario" placement="top">
                <i class="fa-solid fa-sack-dollar"></i>
              </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.cost" />
          </div>
            <div class="flex col-span-full">
              <el-tooltip
                content="Descripción de la refacción"
                placement="top"
              >
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
                >
                  ...
                </span>
              </el-tooltip>
              <textarea
                v-model="form.description"
                class="textarea"
                autocomplete="off"
                placeholder="Descripción"
              ></textarea>
              <InputError :message="form.errors.description" />
            </div>

          <div class="col-span-full">
            <label class="text-[#747474]">Imágenes de Refacción</label>
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

          <div class="mx-3 text-right">
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
      name: this.spare_part.name,
      supplier: this.spare_part.supplier,
      quantity: this.spare_part.quantity,
      cost: this.spare_part.cost,
      location: this.spare_part.location,
      description: this.spare_part.description,
      machine_id: this.spare_part.machine_id,
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
    spare_part: Object
  },
  methods: {
    update() {
      this.form.put(route("spare-parts.update", this.spare_part), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Refacción Actualizada",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
  },
};
</script>
