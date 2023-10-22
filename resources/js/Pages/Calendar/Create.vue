<template>
  <AppLayout title="Agendar tarea |">
    <template #header>
      <div class="flex justify-between">
        <Link :href="route('calendars.index')"
          class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
        <i class="fa-solid fa-chevron-left"></i>
        </Link>
        <div class="flex items-center space-x-2">
          <h2 class="font-semibold text-xl leading-tight">Agendar nueva tarea/evento</h2>
        </div>
      </div>
    </template>

    <!-- Form -->
    <form @submit.prevent="store">
      <div class="md:w-1/2 md:mx-auto my-5 bg-[#D9D9D9] rounded-lg lg:p-9 p-4 shadow-md space-y-4">
        <div class="flex justify-center items-center space-x-12">
          <div class="flex items-center">
            <input v-model="form.type" value="Evento"
              class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-black bg-transparent" type="radio"
              name="task_type" />
            <p class="ml-3">Evento</p>
          </div>
          <div class="flex items-center">
            <input v-model="form.type" value="Tarea"
              class="checked:bg-primary focus:text-primary focus:ring-[#D90537] border-black bg-transparent" type="radio"
              name="task_type" />
            <p class="ml-3">Tarea</p>
          </div>
          <InputError :message="form.errors.type" />
        </div>

        <!-- --------------- Evento -------------- -->
        <section class="space-y-3" v-if="form.type == 'Evento'">
          <div>
            <label class="block" for="">Título del evento *</label>
            <input v-model="form.title" class="input" type="text" placeholder="Agregar título" />
            <InputError :message="form.errors.title" />
          </div>
          <div>
            <label class="block" for="">Participante(s) *</label>
            <el-select class="w-full mt-2" v-model="form.participants" clearable filterable multiple
              placeholder="Seleccionar participantes" no-data-text="No hay usuarios registrados"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id" />
            </el-select>
            <InputError :message="form.errors.participants" />
          </div>
          <div class="flex items-center">
            <div class="mt-2 lg:mt-0">
              <label class="block">Fecha *</label>
              <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha *" :disabled-date="disabledDate" />
              <InputError :message="form.errors.start_date" />
            </div>
            <label class="flex items-center mt-5">
              <Checkbox v-model:checked="form.is_full_day" class="bg-transparent disabled:border-gray-400" />
              <span class="ml-2 text-xs">Todo el día</span>
            </label>
          </div>
          <div v-if="!form.is_full_day">
            <label>Horario</label>
            <div class="demo-range">
              <el-time-picker v-model="form.time" is-range range-separator="-" start-placeholder="Hora inicio"
                end-placeholder="Hora final" />
            </div>
            <InputError :message="form.errors.time" />
          </div>
          <!-- <div>
            <label class="block">Repetir</label>
            <el-select
              class="w-full mt-2"
              v-model="form.repeater"
              clearable
              placeholder="Seleccionar"
              no-data-text="No hay opciones registradas"
              no-match-text="No se encontraron coincidencias"
            >
              <el-option
                v-for="repeater in repeaters"
                :key="repeater"
                :label="repeater"
                :value="repeater"
              />
            </el-select>
            <InputError :message="form.errors.repeater" />
          </div> -->
          <div>
            <label class="block">Ubicación</label>
            <input v-model="form.location" class="input" type="text" placeholder="Agregar ubicación" />
            <InputError :message="form.errors.location" />
          </div>
          <div>
            <label>Descripción</label>
            <textarea class="textarea"> </textarea>
            <InputError :message="form.errors.description" />
          </div>
          <!-- <div>
            <label class="block">Recordatorio</label>
            <div class="flex items-center">
              <el-select
                class="w-1/2 mt-2"
                v-model="form.reminder"
                clearable
                placeholder="Seleccionar"
                no-data-text="No hay opciones registradas"
                no-match-text="No se encontraron coincidencias"
              >
                <el-option
                  v-for="reminder in reminders"
                  :key="reminder"
                  :label="reminder"
                  :value="reminder"
                />
              </el-select>
              <p class="text-sm text-primary ml-7 cursor-pointer w-1/2">
                + Agregar recordatorio
              </p>
            </div>
            <InputError :message="form.errors.reminder" />
          </div> -->
        </section>

        <!-- ------------- Tarea .............. -->
        <section class="space-y-3" v-else>
          <div>
            <label class="block" for="">Título de la tarea *</label>
            <input v-model="form.title" class="input" type="text" placeholder="Agregar título" />
            <InputError :message="form.errors.title" />
          </div>
          <div class="flex items-center mt-2">
            <div class="mt-2 lg:mt-0">
              <label class="block">Fecha *</label>
              <el-date-picker v-model="form.start_date" type="date" placeholder="Fecha *" :disabled-date="disabledDate" />
              <InputError :message="form.errors.start_date" />
            </div>
            <label class="flex items-center mt-5">
              <Checkbox v-model:checked="form.is_full_day" class="bg-transparent disabled:border-gray-400" />
              <span class="ml-2 text-xs">Todo el día</span>
            </label>
          </div>
          <div v-if="!form.is_full_day">
            <label>Horario</label>
            <div class="demo-range">
              <el-time-picker v-model="form.time" is-range range-separator="-" start-placeholder="Hora inicio"
                end-placeholder="Hora final" />
            </div>
            <InputError :message="form.errors.time" />
          </div>
          <!-- <div>
            <label class="block">Repetir</label>
            <el-select class="w-full mt-2" v-model="form.repeater" clearable placeholder="Seleccionar"
              no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="repeater in repeaters" :key="repeater" :label="repeater" :value="repeater" />
            </el-select>
            <InputError :message="form.errors.repeater" />
          </div> -->
          <div>
            <label>Descripción</label>
            <textarea v-model="form.description" class="textarea"> </textarea>
            <InputError :message="form.errors.description" />
          </div>
          <!-- <div>
            <label class="block">Recordatorio</label>
            <div class="flex items-center">
              <el-select class="w-1/2 mt-2" v-model="form.reminder" clearable placeholder="Seleccionar"
                no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="reminder in reminders" :key="reminder" :label="reminder" :value="reminder" />
              </el-select>
              <p class="text-sm text-primary ml-7 cursor-pointer w-1/2">
                + Agregar recordatorio
              </p>
            </div>
            <InputError :message="form.errors.reminder" />
          </div> -->
        </section>

        <div class="flex md:text-left items-center">
          <PrimaryButton :disabled="form.processing"> Guardar </PrimaryButton>
        </div>
      </div>
    </form>
  </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";

export default {
  data() {
    const form = useForm({
      type: "Evento",
      title: null,
      participants: [],
      repeater: "No se repite",
      location: null,
      description: null,
      reminder: null,
      is_full_day: false,
      time: null,
      start_date: null,
    });

    return {
      form,
      repeaters: [
        "No se repite",
        "Todos los días",
        "Cada semana, el lunes",
        "Personalizado",
      ],
      reminders: [
        "5 minutos antes",
        "10 minutos antes",
        "1 hora antes",
        "1 día antes",
        "Personalizado",
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    Link,
    InputError,
    Checkbox,
  },
  props: {
    users: Array,
  },
  methods: {
    store() {
      this.form.post(route("calendars.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Se ha agendado correctamente!",
            type: "success",
          });
        },
      });
    },
    disabledDate(time) {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return time.getTime() < today.getTime();
    },
  },
};
</script>

<style scoped>
/* Estilo para el hover de las opciones */
.el-select-dropdown .el-select-dropdown__item:hover {
  background-color: #d90537;
  /* Color de fondo al hacer hover */
  color: white;
  /* Color del texto al hacer hover */
  border-radius: 20px;
  /* Redondeo */
}
</style>
