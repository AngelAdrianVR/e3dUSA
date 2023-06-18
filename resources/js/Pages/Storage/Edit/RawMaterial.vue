<template>
  <div>
    <AppLayout title="Editar Materia prima">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('storages.raw-materials.index')"
            class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar "{{ raw_material.name }}""
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div>
            <IconInput
              v-model="form.name"
              inputPlaceholder="Nombre *"
              inputType="text"
            >
              A
            </IconInput>
            <InputError :message="form.errors.name" />
          </div>
          <div class="md:grid gap-6 mb-6 grid-cols-2">
            <div>
              <IconInput
                v-model="form.part_number"
                inputPlaceholder="Número de parte *"
                inputType="text"
              >
                #
              </IconInput>
              <InputError :message="form.errors.part_number" />
            </div>
            <div>
              <IconInput
                v-model="form.min_quantity"
                inputPlaceholder="Stock mínimo"
                inputType="number"
              >
                <i class="fa-solid fa-minus"></i>
              </IconInput>
              <InputError :message="form.errors.min_quantity" />
            </div>
            <div>
              <IconInput
                v-model="form.max_quantity"
                inputPlaceholder="Stock máximo"
                inputType="number"
              >
                <i class="fa-solid fa-plus"></i>
              </IconInput>
              <InputError :message="form.errors.max_quantity" />
            </div>
            <div>
              <IconInput
                v-model="form.initial_stock"
                inputPlaceholder="Stock de apertura"
                inputType="number"
              >
                123
              </IconInput>
              <InputError :message="form.errors.initial_stock" />
            </div>
            <div>
              <IconInput
                v-model="form.measure_unit"
                inputPlaceholder="Unidad de medida"
                inputType="text"
              >
                <i class="fa-solid fa-ruler-vertical"></i>
              </IconInput>
              <InputError :message="form.errors.measure_unit" />
            </div>
            <div>
              <IconInput
                v-model="form.cost"
                inputPlaceholder="Costo *"
                inputType="number"
              >
                <i class="fa-solid fa-dollar"></i>
              </IconInput>
              <InputError :message="form.errors.cost" />
            </div>
          </div>

          <div class="flex">
            <span
              class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
            >
              ...
            </span>
            <textarea
              v-model="form.description"
              class="textarea"
              autocomplete="off"
              placeholder="Descripción *"
              required
            ></textarea>
            <InputError :message="form.errors.description" />
          </div>

          <div class="mt-2 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Actualizar
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
import { ref } from "vue";

export default {
  data() {
    const form = useForm({
      name: this.raw_material.name,
      part_number: this.raw_material.part_number,
      measure_unit: this.raw_material.measure_unit,
      min_quantity: this.raw_material.min_quantity,
      max_quantity: this.raw_material.max_quantity,
      cost: this.raw_material.cost,
      initial_stock: null,
      type: 'materia-prima',
      description: this.raw_material.description,
      features: this.raw_material.features,
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
    raw_material: Object,
  },
  methods: {
    update() {
      this.form.put(route("raw-materials.update", this.raw_material), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "¡Se actualizó correctamente!",
            type: "success",
          });
        },
      });
    },
  },
};
</script>
