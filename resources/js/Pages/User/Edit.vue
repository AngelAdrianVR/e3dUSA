<template>
    <div>
      <AppLayout title="Editar usuario">
        <template #header>
          <div class="flex justify-between">
            <Link :href="route('users.index')"
              class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
            <i class="fa-solid fa-chevron-left"></i>
            </Link>
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
                  inputType="number">
                  <el-tooltip content="Salario semanal" placement="top">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                  </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.employee_properties?.salary?.week" />
              </div>
              <div>
                <IconInput v-model="form.employee_properties.hours_per_week" inputPlaceholder="Horas semanales *"
                  inputType="number">
                  <el-tooltip content="Horas semanales" placement="top">
                    <i class="fa-solid fa-user-clock"></i>
                  </el-tooltip>
                </IconInput>
                <InputError :message="form.errors.employee_properties?.hours_per_week" />
              </div>
              <div class="flex items-center">
                <el-tooltip content="Fecha de nacimiento" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-cake-candles"></i>
                  </span>
                </el-tooltip>
                <el-date-picker v-model="form.employee_properties.birthdate" type="date" placeholder="Fecha de nacimiento *"
                  format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
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
                  format="YYYY/MM/DD" value-format="YYYY-MM-DD" />
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
            <div class="lg:grid grid-cols-2 gap-y-2">
              <div v-for="(day, index) in weekDays" :key="index" class="flex items-center">
                <span class="w-28 text-sm">{{ day }}</span>
                <div class="col-span-3">
                  <IconInput v-model="form.employee_properties.work_days[index].check_in"
                    inputPlaceholder="Hora de entrada *" inputType="time">
                  </IconInput>
                </div>
              </div>
            </div>
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
  import { Link, useForm } from "@inertiajs/vue3";
  import InputError from "@/Components/InputError.vue";
  import IconInput from "@/Components/MyComponents/IconInput.vue";
  
  export default {
    data() {
      const form = useForm({
        name: this.user.name,
        email: this.user.email,
        employee_properties: {
          salary: {
            hour: this.user.employee_properties.salary.hour,
            day: this.user.employee_properties.salary.day,
            week: this.user.employee_properties.salary.week
          },
          hours_per_week: this.user.employee_properties.hours_per_week,
        //   vacations: {
        //     available_days: 0.00,
        //     updated_date: null,
        //   },
          birthdate: this.user.employee_properties.birthdate,
          bonuses: this.user.employee_properties.bonuses,
          job_position: this.user.employee_properties.job_position,
          department: this.user.employee_properties.department,
          work_days: [
            {
              day: 0,
              check_in: this.user.employee_properties.work_days[0].check_in,
            },
            {
              day: 1,
              check_in: this.user.employee_properties.work_days[1].check_in,
            },
            {
              day: 2,
              check_in: this.user.employee_properties.work_days[2].check_in,
            },
            {
              day: 3,
              check_in: this.user.employee_properties.work_days[3].check_in,
            },
            {
              day: 4,
              check_in: this.user.employee_properties.work_days[4].check_in,
            },
            {
              day: 5,
              check_in: this.user.employee_properties.work_days[5].check_in,
            },
            {
              day: 6,
              check_in: this.user.employee_properties.work_days[6].check_in,
            },
          ],
          join_date: this.user.employee_properties.join_date,
        },
      });
  
      return {
        form,
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
        weekDays: [
          'Viernes',
          'Sábado',
          'Domingo',
          'Lunes',
          'Martes',
          'Miércoles',
          'Jueves',
        ],
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
      user: Object,
      bonuses: Array,
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
    },
  };
  </script>
  