<template>
  <div>
    <AppLayout title="Crear usuarios">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Crear nuevo usuario
            </h2>
          </div>
        </div>
      </template>
      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 grid grid-cols-2 gap-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg px-9 py-5 shadow-md">
          <div>
            <InputLabel value="Nombre completo*" />
            <el-input v-model="form.name" placeholder="Ej. Miguel Gonzalez Rivera" />
            <InputError :message="form.errors.name" />
          </div>
          <div>
            <InputLabel value="Correo electrónico*" />
            <el-input v-model="form.email" placeholder="Ej. miguel@gmail.com" />
            <InputError :message="form.errors.email" />
          </div>
          <div>
            <InputLabel value="Departamento*" />
            <el-select v-model="form.employee_properties.department" clearable placeholder="Selecciona"
              no-data-text="No hay departamentos registrados">
              <el-option v-for="(department, index) in departments" :key="index" :label="department"
                :value="department"></el-option>
            </el-select>
            <InputError :message="form.errors['employee_properties.department']" />
          </div>
          <div>
            <InputLabel value="Puesto*" />
            <el-input v-model="form.employee_properties.job_position" placeholder="Ej. miguel@gmail.com" />
            <InputError :message="form.errors['employee_properties.job_position']" />
          </div>
          <div>
            <InputLabel value="Salario semanal*" />
            <el-input v-model="form.employee_properties.salary.week" type="text"
            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
            :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 2,500" />
            <InputError :message="form.errors['employee_properties.salary.week']" />
          </div>
          <div>
            <InputLabel value="Fecha de nacimiento*" />
            <el-date-picker v-model="form.employee_properties.birthdate" type="date" placeholder="Selecciona fecha"
            class="!w-full" format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
            <InputError :message="form.errors['employee_properties.birthdate']" />
          </div>
          <div>
            <InputLabel value="Fecha de ingreso*" />
            <el-date-picker v-model="form.employee_properties.join_date" type="date" placeholder="Selecciona fecha"
            class="!w-full" format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
            <InputError :message="form.errors['employee_properties.join_date']" />
          </div>
          <div class="col-span-full">
            <InputLabel value="Bonos" />
            <el-select v-model="form.employee_properties.bonuses" multiple clearable placeholder="Selecciona bonos"
              no-data-text="No hay bonos registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in bonuses" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div class="col-span-full">
            <InputLabel value="Descuentos" />
            <el-select v-model="form.employee_properties.discounts" multiple clearable
              placeholder="Selecciona descuentos" no-data-text="No hay descuentos registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in discounts" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div class="col-span-full">
            <InputLabel value="Habilidades" />
            <div class="flex mb-1">
              <el-input v-model="newSkill" type="text" placeholder="Ej. Serigrafia rapidamente" />
              <SecondaryButton @click="addSkill" type="button" class="!rounded-r-full !rounded-l-none"
                :disabled="!newSkill">
                Agregar
                <i class="fa-solid fa-arrow-down ml-2"></i>
              </SecondaryButton>
            </div>
            <el-select v-model="form.employee_properties.skills" multiple clearable
              placeholder="Ninguna habilidad agregada" no-data-text="Agrega primero una habilidad">
              <el-option v-for="habiliy in skills" :key="habiliy" :label="habiliy" :value="habiliy"></el-option>
            </el-select>
          </div>
          <el-divider content-position="left" class="col-span-full">Días de trabajo y hora de entrada</el-divider>
          <!-- work days-->
          <div class="border border-[#0355B5] rounded-lg px-6 py-2 relative col-span-full">
            <i
              class="fa-solid fa-info text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"></i>
            <p class="text-secondary text-sm">
              Dejar en blanco la hora de entrada si ese dia descansa el colaborador
            </p>
          </div>
          <table class="w-full col-span-full">
            <thead>
              <tr>
                <th>Dia</th>
                <th>Entrada</th>
                <th>T. de comida</th>
                <th>Salida</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(day, index) in weekDays" :key="index">
                <td>{{ day }}</td>
                <td>
                  <IconInput v-model="form.employee_properties.work_days[index].check_in"
                    inputPlaceholder="Hora de entrada *" inputType="time" class="w-28 md:w-full">
                  </IconInput>
                </td>
                <td>
                  <el-select v-model="form.employee_properties.work_days[index].break"
                    placeholder="Selecciona el tiempo de comida" no-data-text="No hay elementos"
                    class="w-28 md:w-full mb-1">
                    <el-option v-for="(item, index) in breakTimes" :key="index" :label="item.label"
                      :value="item.value" />
                  </el-select>
                </td>
                <td>
                  <IconInput v-model="form.employee_properties.work_days[index].check_out"
                    inputPlaceholder="Hora de salida *" inputType="time" class="w-28 md:w-full">
                  </IconInput>
                </td>
              </tr>
            </tbody>
          </table>
          <el-divider content-position="left" class="col-span-full">Roles</el-divider>
          <!-- roles -->
          <div class="col-span-full grid grid-cols-3 gap-2">
            <label v-for="role in roles" :key="role.id" class="flex items-center">
              <input type="checkbox" v-model="form.roles" :value="role.id"
                class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
              <span class="ml-2 text-sm">{{ role.name }}</span>
            </label>
          </div>
          <InputError :message="form.errors.roles" />

          <div class="md:text-right col-span-full mt-6">
            <PrimaryButton :disabled="form.processing">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
              Crear
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
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const form = useForm({
      name: null,
      email: null,
      password: null,
      employee_properties: {
        salary: {
          hour: null,
          day: null,
          week: null,
        },
        skills: [],
        hours_per_week: null,
        vacations: {
          available_days: 0.00,
          updated_date: null,
        },
        birthdate: null,
        bonuses: [],
        discounts: [],
        job_position: null,
        department: null,
        password: null,
        work_days: [
          {
            day: 0,
            check_in: 0,
            check_out: 0,
            break: 15,
          },
          {
            day: 1,
            check_in: 0,
            check_out: 0,
            break: 15,
          },
          {
            day: 2,
            check_in: 0,
            check_out: 0,
            break: 15,
          },
          {
            day: 3,
            check_in: 0,
            check_out: 0,
            break: 15,
          },
          {
            day: 4,
            check_in: 0,
            check_out: 0,
            break: 15,
          },
          {
            day: 5,
            check_in: 0,
            check_out: 0,
            break: 15,
          },
          {
            day: 6,
            check_in: 0,
            check_out: 0,
            break: 15,
          },
        ],
        join_date: null,
      },
      roles: []
    });

    return {
      form,
      newSkill: null,
      skills: [],
      breakTimes: [
        {
          label: '15 minutos', value: 15,
        },
        {
          label: '30 minutos', value: 30,
        },
        {
          label: '45 minutos', value: 45,
        },
        {
          label: '1 hora', value: 60,
        },
        {
          label: '1 hora 15 minutos', value: 75,
        },
        {
          label: '1 hora 30 minutos', value: 90,
        },
        {
          label: '2 horas', value: 120,
        },
      ],
      departments: [
        'Administración',
        'Almacén',
        'Calidad',
        'Compras',
        'Cotizaciones',
        'Dirección',
        'Diseño',
        'Finanzas',
        'Inovación y desarrollo',
        'Ingeniería',
        'Mantenimiento',
        'Mercadotecnia',
        'Producción',
        'Recursos humanos',
        'Ventas',
      ],
      weekDays: [
        'Domingo',
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
        'Sábado',
      ],
    };
  },
  components: {
    AppLayout,
    PrimaryButton,
    SecondaryButton,
    InputError,
    IconInput,
    Back,
    Link,
    InputLabel,
  },
  props: {
    employee_number: Number,
    bonuses: Array,
    discounts: Array,
    roles: Array,
  },
  methods: {
    store() {
      this.form.post(route("users.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Usuario creado",
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
    getRandomString() {
      var char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
      var length = 8;
      var randomString = '';

      for (var i = 0; i < length; i++) {
        var randomIndex = Math.floor(Math.random() * char.length);
        randomString += char.charAt(randomIndex);
      }

      return randomString;
    },
    addSkill() {
      if (this.newSkill.trim() !== '') {
        this.form.employee_properties.skills.push(this.newSkill);
        this.skills.push(this.newSkill);
        this.newSkill = '';
      }
    },
  },
  mounted() {
    this.form.employee_properties.password = this.getRandomString();
  },
};
</script>
