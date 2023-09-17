<template>
  <div>
    <AppLayout title="Crear proyecto |">
      <template #header>
        <div class="flex justify-between">
          <Link :href="route('projects.index')"
            class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
          <i class="fa-solid fa-chevron-left"></i>
          </Link>
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Crear proyecto</h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div>
            <IconInput v-model="form.name" inputPlaceholder="Título del proyecto *" inputType="text">
              <el-tooltip content="Título del proyecto" placement="top"> A </el-tooltip>
            </IconInput>
            <InputError :message="form.errors.name" />
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

          <div class="mt-9 mx-3 md:text-right">
            <PrimaryButton :disabled="form.processing">
              Crear proyecto
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
      name: null,

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

  },
  methods: {
    store() {
      this.form.post(route("projects.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha creado un nuevo proyecto",
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
