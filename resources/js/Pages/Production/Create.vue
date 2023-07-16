<template>
  <div>
    <AppLayout title="Órden de producción - Crear">
      <template #header>
        <div class="flex justify-between">
          <Link
            :href="route('productions.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Crear órden de producción
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div
          class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4"
        >
          <div class="">
            <el-select
              v-model="form.user_tasks.user_id"
              clearable
              filterable
              placeholder="Seleccionar operador"
              no-data-text="No hay operadores registrados"
              no-match-text="No se encontraron coincidencias"
            >
              <el-option
                v-for="item in production_users"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </div>
          <div class="">
            <el-select
              v-model="form.user_tasks.user_id"
              clearable
              filterable
              placeholder="Seleccionar operador"
              no-data-text="No hay operadores registrados"
              no-match-text="No se encontraron coincidencias"
            >
              <el-option
                v-for="item in production_users"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </div>
          <div class="flex ml-3">
                <el-tooltip content="Tareas asignadas" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
                  >
                    <i class="fa-solid fa-person-digging"></i>
                  </span>
                </el-tooltip>
                <textarea
                  v-model="form.user_tasks.tasks"
                  class="textarea"
                  autocomplete="off"
                  placeholder="Tareas"
                ></textarea>
                <InputError :message="form.errors.user_tasks?.tasks" />
              </div>
          <div>
              <el-time-picker
                v-model="form.user_tasks.estimated_time"
                placeholder="Tiempo estimado"
                format="HH:mm"
              />
              <InputError :message="form.errors.user_tasks?.estimated_time" />
            </div>

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Crear órden de producción
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
      user_tasks:{
        user_id: null,
        tasks: null,
        estimated_time: null
      },

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
    production_users: Array
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
  },
};
</script>
