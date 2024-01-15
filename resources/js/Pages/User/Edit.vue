<template>
  <div>
    <AppLayout title="Editar usuario">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Editar usuario "{{ user.name }}"
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="update">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg px-9 py-5 shadow-md space-y-4">
          <div class="md:grid grid-cols-2 gap-3">
            <div>
              <IconInput v-model="form.name" inputPlaceholder="Nombre completo *" inputType="text">
                <el-tooltip content="Nombre completo" placement="top">
                  A
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.name" />
            </div>
            <div>
              <IconInput v-model="form.email" inputPlaceholder="Correo electrónico" inputType="text">
                <el-tooltip content="Correo electrónico" placement="top">
                  <i class="fa-solid fa-at"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.email" />
            </div>
            <div class="flex items-center">
              <el-tooltip content="Departamento" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  <i class="fa-solid fa-users-between-lines"></i>
                </span>
              </el-tooltip>
              <el-select v-model="form.employee_properties.department" clearable placeholder="Departamento *"
                no-data-text="No hay departamentos registrados">
                <el-option v-for="(department, index) in departments" :key="index" :label="department"
                  :value="department"></el-option>
              </el-select>
              <!-- <InputError :message="form.employee_properties?.department" /> -->
            </div>
            <div>
              <IconInput v-model="form.employee_properties.job_position" inputPlaceholder="Puesto *" inputType="text">
                <el-tooltip content="Puesto" placement="top">
                  A
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.employee_properties?.job_position" />
            </div>
            <div>
              <IconInput v-model="form.employee_properties.salary.week" inputPlaceholder="Salario semanal *"
                inputType="number" inputStep="0.01">
                <el-tooltip content="Salario semanal" placement="top">
                  <i class="fa-solid fa-hand-holding-dollar"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.employee_properties?.salary?.week" />
            </div>
            <!-- <div>
              <IconInput v-model="form.employee_properties.hours_per_week" inputPlaceholder="Horas semanales *"
                inputType="number" inputStep="0.01">
                <el-tooltip content="Horas semanales" placement="top">
                  <i class="fa-solid fa-user-clock"></i>
                </el-tooltip>
              </IconInput>
              <InputError :message="form.errors.employee_properties?.hours_per_week" />
            </div> -->
            <div class="flex items-center">
              <el-tooltip content="Fecha de nacimiento" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  <i class="fa-solid fa-cake-candles"></i>
                </span>
              </el-tooltip>
              <el-date-picker v-model="form.employee_properties.birthdate" type="date" placeholder="Fecha de nacimiento *"
                format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
              <InputError :message="form.errors.employee_properties?.birthdate" />
            </div>
            <div class="flex items-center">
              <el-tooltip content="Fecha de ingreso" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  <i class="fa-solid fa-user-plus"></i>
                </span>
              </el-tooltip>
              <el-date-picker v-model="form.employee_properties.join_date" type="date" placeholder="Fecha de ingreso *"
                format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
              <InputError :message="form.errors.employee_properties?.join_date" />
            </div>
            <div class="flex items-center col-span-full">
              <el-tooltip content="Bonos" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  <i class="fa-solid fa-coins"></i>
                </span>
              </el-tooltip>
              <el-select v-model="form.employee_properties.bonuses" multiple clearable placeholder="Selecciona bonos"
                no-data-text="No hay bonos registradas" no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in bonuses" :key="item.id" :label="item.name" :value="item.id" />
              </el-select>
            </div>
            <div class="flex items-center col-span-full">
              <el-tooltip content="Descuentos" placement="top">
                <span
                  class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                  <i class="fa-solid fa-coins"></i>
                </span>
              </el-tooltip>
              <el-select v-model="form.employee_properties.discounts" multiple clearable
                placeholder="Selecciona descuentos" no-data-text="No hay descuentos registradas"
                no-match-text="No se encontraron coincidencias">
                <el-option v-for="item in discounts" :key="item.id" :label="item.name" :value="item.id" />
              </el-select>
            </div>
            <div class="col-span-full">
              <div class="flex space-x-2 mb-1">
                <IconInput v-model="newSkill" inputPlaceholder="Ingresa una habilidad (opcional)" inputType="text"
                  class="w-full">
                  <el-tooltip content="Habilidades" placement="top">
                    <i class="fa-solid fa-pen"></i>
                  </el-tooltip>
                </IconInput>
                <SecondaryButton @click="addSkill" type="button">
                  Agregar
                  <i class="fa-solid fa-arrow-down ml-2"></i>
                </SecondaryButton>
              </div>
              <el-select v-model="form.employee_properties.skills" multiple clearable placeholder="Habilidades"
                no-data-text="Agrega primero una caracteristica">
                <el-option v-for="habiliy in skills" :key="habiliy" :label="habiliy" :value="habiliy"></el-option>
              </el-select>
            </div>
          </div>

          <br><el-divider content-position="left" class="col-span-full">Días de trabajo y hora de entrada</el-divider>
          <br>

          <!-- work days-->
          <div class="border border-[#0355B5] rounded-lg px-6 py-2 relative">
            <i
              class="fa-solid fa-info text-[9px] text-secondary h-3 w-3 bg-sky-300 rounded-full text-center absolute left-2 top-3"></i>
            <p class="text-secondary text-sm">
              Dejar en blanco la hora de entrada si ese dia descansa el colaborador
            </p>
          </div>
          <table class="w-full">
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
                    <el-option v-for="(item, index) in breakTimes" :key="index" :label="item.label" :value="item.value" />
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

          <br><el-divider content-position="left" class="col-span-full">Roles</el-divider>
          <br>

          <!-- roles -->
          <div class="grid grid-cols-3 gap-2">
            <label v-for="role in roles" :key="role.id" class="flex items-center">
              <input type="checkbox" v-model="form.roles" :value="role.id"
                class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
              <span class="ml-2 text-sm">{{ role.name }}</span>
            </label>
          </div>
          <InputError :message="form.errors.roles" />

          <el-divider />

          <div class="md:text-right">
            <PrimaryButton :disabled="form.processing"> Actualizar usuario </PrimaryButton>
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      name: this.user.name,
      email: this.user.email,
      employee_properties: {
        salary: {
          hour: this.user.employee_properties?.salary.hour,
          day: this.user.employee_properties?.salary.day,
          week: this.user.employee_properties?.salary.week
        },
        hours_per_week: this.user.employee_properties?.hours_per_week,
        vacations: {
          available_days: this.user.employee_properties?.vacations?.available_days ?? 0,
          updated_date: this.user.employee_properties?.vacations?.updated_date ?? null,
        },
        birthdate: this.user.employee_properties?.birthdate.raw,
        bonuses: this.user.employee_properties?.bonuses,
        discounts: this.user.employee_properties?.discounts,
        job_position: this.user.employee_properties?.job_position,
        department: this.user.employee_properties?.department,
        work_days: [
          {
            day: 0,
            check_in: this.user.employee_properties?.work_days[0].check_in,
            check_out: this.user.employee_properties?.work_days[0].check_out,
            break: this.user.employee_properties?.work_days[0].break,
          },
          {
            day: 1,
            check_in: this.user.employee_properties?.work_days[1].check_in,
            check_out: this.user.employee_properties?.work_days[1].check_out,
            break: this.user.employee_properties?.work_days[1].break,
          },
          {
            day: 2,
            check_in: this.user.employee_properties?.work_days[2].check_in,
            check_out: this.user.employee_properties?.work_days[2].check_out,
            break: this.user.employee_properties?.work_days[2].break,
          },
          {
            day: 3,
            check_in: this.user.employee_properties?.work_days[3].check_in,
            check_out: this.user.employee_properties?.work_days[3].check_out,
            break: this.user.employee_properties?.work_days[3].break,
          },
          {
            day: 4,
            check_in: this.user.employee_properties?.work_days[4].check_in,
            check_out: this.user.employee_properties?.work_days[4].check_out,
            break: this.user.employee_properties?.work_days[4].break,
          },
          {
            day: 5,
            check_in: this.user.employee_properties?.work_days[5].check_in,
            check_out: this.user.employee_properties?.work_days[5].check_out,
            break: this.user.employee_properties?.work_days[5].break,
          },
          {
            day: 6,
            check_in: this.user.employee_properties?.work_days[6].check_in,
            check_out: this.user.employee_properties?.work_days[6].check_out,
            break: this.user.employee_properties?.work_days[6].break,
          },
        ],
        join_date: this.user.employee_properties?.join_date,
        skills: this.user.employee_properties?.skills,
      },
      roles: this.user_roles
    });

    return {
      form,
      newSkill: null,
      skills: [],
      departments: [
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
    Link
  },
  props: {
    user: Object,
    bonuses: Array,
    discounts: Array,
    roles: Array,
    user_roles: Array,
  },
  methods: {
    update() {
      this.form.put(route("users.update", this.user), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Usuario actualizado",
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
    addSkill() {
      if (this.newSkill.trim() !== '') {
        this.form.employee_properties.skills.push(this.newSkill);
        this.skills.push(this.newSkill);
        this.newSkill = '';
      }
    },
  },
};
</script>
  