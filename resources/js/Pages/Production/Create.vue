<template>
  <div>
    <AppLayout title="Crear Orden de producción">
      <template #header>
        <div class="flex justify-between">
          <Back />
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Crear orden de producción
            </h2>
          </div>
        </div>
      </template>

      <!-- Form -->
      <form @submit.prevent="store">
        <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md space-y-4">
          <div class="flex items-center">
            <el-tooltip content="Órdenes de venta sin orden de producción" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-magnifying-glass"></i>
              </span>
            </el-tooltip>
            <el-select v-model="form.sale_id" clearable filterable placeholder="Busca en órdenes de venta"
              no-data-text="No hay órdenes de venta registradas" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in sales.data" :key="item.id"
                :label="item.folio + ' | ' + item.company_branch.name" :value="item.id" />
            </el-select>
          </div>

          <!-- -----------------Extra info in productions -------------------- -->
          <template v-if="form.sale_id">
            <div v-for="product in orderedProducts" :key="product"
              class="grid grid-cols-3 gap-x-3 self-start text-xs border-b border-gray-400">
              <div class="col-span-2 grid grid-cols-2 gap-x-3 self-start pb-5">
                <p>Producto</p>
                <span>{{ product?.catalog_product_company?.catalog_product.name }}</span>
                <p v-if="isSaleProduction">Cantidad usada de almacén de producto terminado</p>
                <div v-if="isSaleProduction" class="flex items-center">
                  {{ product?.finished_product_used }} unidades
                  <div
                    v-if="(product?.finished_product_used - (product?.quantity - product?.finished_product_used)) < product?.catalog_product_company?.catalog_product.min_quantity"
                    class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                      stroke="currentColor" class="w-3 h-3 ml-2 mr-1">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <strong class="text-primary mr-1">Stock mínimo</strong>
                    <el-tooltip
                      content="Al realizar la orden de producción, el stock quedará por debajo del mínimo permitido, por lo que es necesario considerar la reposición para mantener el stock mínimo "
                      placement="top">
                      <div class="rounded-full border border-primary w-2 h-2 flex items-center justify-center">
                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                      </div>
                    </el-tooltip>
                  </div>
                </div>
                <p class="text-primary">Cantidad a producir</p>
                <span class="text-black">{{ product?.quantity - product?.finished_product_used }} unidades</span>
                <p>Cantidad ordenada</p>
                <span>{{ product?.quantity }} unidades</span>
                <p>Notas</p>
                <span>{{ product?.notes ?? '--' }}</span>
              </div>
              <div>
                <figure class="pb-1 size-56">
                  <img class="rounded-md mx-auto object-contain h-full"
                    :src="product?.catalog_product_company?.catalog_product.media[currentImage].original_url">
                </figure>
                <div v-if="product?.catalog_product_company?.catalog_product.media?.length > 1"
                  class="my-3 flex items-center justify-center space-x-3">
                  <i @click="currentImage = index"
                    v-for="(image, index) in product?.catalog_product_company?.catalog_product.media?.length"
                    :key="index" :class="index == currentImage ? 'text-black' : 'text-white'"
                    class="fa-solid fa-circle text-[7px] cursor-pointer"></i>
                </div>
              </div>
            </div>
          </template>
          <!-- products ordered to generate production -->
          <div v-if="form.sale_id">
            <el-divider content-position="left" class="col-span-full">Órdenes de producción</el-divider>
            <InputError :message="errorMessage" class="col-span-full" />
            <ol v-if="form.productions.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
              <template v-for="(item, index) in form.productions" :key="index">
                <li class="flex justify-between items-center">
                  <p class="text-sm">
                    <span class="text-primary">{{ index + 1 }}.</span>
                    {{ sales.data.find(item2 => item2.id == form.sale_id).catalogProductCompanySales.find(cpcs =>
                      cpcs.id == item?.catalog_product_company_sale_id)?.catalog_product_company?.catalog_product.name }}
                    | {{ is_automatic_assignment ? 'Asignación de operdores automática' : item.tasks?.length + 'operador(es) asignado(s)' }}
                  </p>
                  <div class="flex space-x-2 items-center">
                    <el-tag v-if="editProductionIndex == index">En edición</el-tag>
                    <el-button @click="editProduction(index)" type="primary" circle>
                      <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                    </el-button>
                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                      title="¿Continuar?" @confirm="deleteProduction(index)">
                      <template #reference>
                        <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
                      </template>
                    </el-popconfirm>
                  </div>
                </li>
              </template>
            </ol>

            <div class="space-y-3 md:w-[92%] mx-auto border-2 border-[#b8b7b7] rounded-lg p-5 my-3">
              <div class="flex items-center mt-3">
                <el-tooltip content="Seleccionar producto ordenado" placement="top">
                  <span
                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                    <i class="fa-solid fa-magnifying-glass"></i>
                  </span>
                </el-tooltip>
                <el-select v-model="production.catalog_product_compan_id" clearable filterable
                  placeholder="Busca en productos ordenados" no-data-text="No hay productos registrados"
                  no-match-text="No se encontraron coincidencias">
                  <el-option v-for="item in orderedProducts" :key="item.id"
                    :label="item?.catalog_product_company?.catalog_product.name" :value="item.id" />
                </el-select>
              </div>

              <el-divider v-if="production?.catalog_product_company_sale_id" content-position="left"
                class="col-span-full">Tareas</el-divider>

              <ol v-if="tasks.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
                <template v-for="(item, index) in tasks" :key="index">
                  <li class="flex justify-between items-center">
                    <p class="text-sm">
                      <span class="text-primary">{{ index + 1 }}.</span>
                      {{ operators.find(user => user.id == item.operator_id)?.name }}
                      | estimado: {{ item.estimated_time_hours }}h {{ item.estimated_time_minutes }}m
                    </p>
                    <div class="flex space-x-2 items-center">
                      <el-tag v-if="editTaskIndex == index">En edición</el-tag>
                      <el-button @click="editTask(index)" type="primary" circle>
                        <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                      </el-button>
                      <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                        title="¿Continuar?" @confirm="deleteTask(index)">
                        <template #reference>
                          <el-button type="danger" circle><i class="fa-sharp fa-solid fa-trash"></i></el-button>
                        </template>
                      </el-popconfirm>
                    </div>
                  </li>
                </template>
              </ol>

              <div v-if="production?.catalog_product_company_sale_id"
                class="space-y-3 md:w-[92%] mx-auto border-2 border-[#b8b7b7] rounded-lg p-5 my-3">
                <div v-if="!is_automatic_assignment" class="flex items-center">
                  <el-tooltip content="Seleccionar a operador(es)" placement="top">
                    <span
                      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                      <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                  </el-tooltip>
                  <el-select v-model="task.operator_id" clearable filterable placeholder="Busca en operadores"
                    no-data-text="No hay operadores registrados" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in operators" :key="item.id" :label="item.name" :value="item.id" />
                  </el-select>
                </div>
                <div v-else class="text-xs text-secondary">
                  La asignación automatica de operadores está activa. Si quieres cambiarlo a manual, ve a
                  <Link :href="route('settings.index')" class="text-primary hover:underline">configuraciones</Link>
                </div>
                <label class="flex items-center w-28">
                  <Checkbox @change="clearVars" v-model:checked="isFreeTask" class="bg-transparent" />
                  <span class="ml-2 text-sm">Tarea libre</span>
                  <el-tooltip content="Asignas la instrucción y el tiempo manualmente" placement="top">
                    <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center ml-2">
                      <i class="fa-solid fa-info text-primary text-[7px]"></i>
                    </div>
                  </el-tooltip>
                </label>
                <!-- Seleccion de proceso -->
                <div v-if="!isFreeTask" class="flex items-center mt-3">
                  <el-tooltip content="Seleccionar proceso de producción" placement="top">
                    <span
                      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                      <i class="fa-solid fa-person-digging text-sm"></i>
                    </span>
                  </el-tooltip>
                  <el-select @change="getProductionProcess(); type_revelation = null" v-model="task.tasks" clearable
                    filterable placeholder="Selecciona el proceso de producción"
                    no-data-text="No hay opciones registradas" no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in production_processes.data" :key="item.id" :label="item.name"
                      :value="item.name" />
                  </el-select>
                </div>
                <!-- Tipo de revelado de marco -->
                <div class="flex justify-center items-start space-x-1 text-sm mb-8"
                  v-if="task.tasks && typeof task.tasks === 'string' && task.tasks.startsWith('SERIGRAFÍA')">
                  <el-radio-group @change="addRevelationTime" v-model="type_revelation" class="ml-4">
                    <el-radio label="usado" size="large">
                      Revelado de marco usado
                    </el-radio>
                    <el-radio label="virgen" size="large">
                      Revelado de marco virgen
                    </el-radio>
                  </el-radio-group>
                  <i v-if="type_revelation" @click="getProductionProcess(); type_revelation = null"
                    class="fa-solid fa-xmark TEXT-XS cursor-pointer"></i>
                </div>
                <div v-if="isFreeTask" class="flex items-center">
                  <el-tooltip content="Tareas" placement="top">
                    <span
                      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                      <i class="fa-solid fa-list-check"></i>
                    </span>
                  </el-tooltip>
                  <textarea v-model="task.tasks" class="textarea" autocomplete="off" placeholder="Tareas *"></textarea>
                  <InputError :message="form.errors.user_tasks?.tasks" />
                </div>
                <div class="flex items-center">
                  <el-tooltip content="Tiempo estimado de producción" placement="top">
                    <span
                      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                      <i class="fa-regular fa-clock"></i>
                    </span>
                  </el-tooltip>

                  <el-select class="mx-1" :disabled="!isFreeTask" v-model="task.estimated_time_hours" clearable
                    placeholder="Horas" no-data-text="No hay información"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="hour in 50" :key="hour" :label="(hour - 1)" :value="(hour - 1)" />
                  </el-select>
                  <el-select class="mx-1" :disabled="!isFreeTask" v-model="task.estimated_time_minutes" clearable
                    placeholder="Minutos" no-data-text="No hay información"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="minute in 59" :key="minute" :label="minute" :value="minute" />
                  </el-select>

                  <!-- <IconInput inputType="time" v-model="task.estimated_time" placeholder="Tiempo estimado de producción *">
                  <el-tooltip content="Tiempo estimado de producción" placement="top">
                    <i class="fa-solid fa-clock"></i>
                  </el-tooltip>
                </IconInput> -->
                  <!-- <el-time-picker v-model="task.estimated_time" placeholder="Tiempo estimado de producción"
                  format="HH:mm" /> -->
                  <!-- <InputError :message="form.errors.user_tasks?.estimated_time" /> -->
                </div>

                <div calss="col-span-full">
                  <SecondaryButton v-if="is_automatic_assignment" @click="addTask" type="button"
                    :disabled="form.processing || !task.tasks || (task.estimated_time_hours + task.estimated_time_minutes) == 0">
                    {{ editTaskIndex !== null ? 'Actualizar tareas' : 'Agregar tareas' }}
                  </SecondaryButton>
                  <SecondaryButton v-else @click="addTask" type="button"
                    :disabled="form.processing || !task.operator_id || !task.tasks || (task.estimated_time_hours + task.estimated_time_minutes) == 0">
                    {{ editTaskIndex !== null ? 'Actualizar tareas' : 'Agregar tareas' }}
                  </SecondaryButton>
                </div>
              </div>

              <div calss="col-span-full">
                <SecondaryButton @click="addProduction" type="button"
                  :disabled="form.processing || !production?.catalog_product_company_sale_id || !tasks.length">
                  {{ editProductionIndex !== null ? 'Actualizar orden' : 'Agregar orden' }}
                </SecondaryButton>
              </div>
            </div>
          </div>
          <el-divider />

          <div class="md:text-right">
            <PrimaryButton :disabled="form.processing || orderedProducts.length != 0 || form.sale_id === null">
              <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
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
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      sale_id: null,
      productions: [],
    });

    return {
      form,
      currentImage: 0, // catalog product image
      tasks: [],
      type_revelation: null,
      // saleId: null,
      editProductionIndex: null,
      editTaskIndex: null,
      errorMessage: null,
      isFreeTask: false,
      orderedProducts: [],
      isSaleProduction: null,
      production: {
        tasks: [],
        user_id: this.$page.props.auth.user.id,
        catalog_product_company_sale_id: null,
        production_cost_id: null,
      },
      task: {
        operator_id: null,
        tasks: [],
        estimated_time_hours: 0,
        estimated_time_minutes: 0,
      },
    };
  },
  components: {
    AppLayout,
    SecondaryButton,
    PrimaryButton,
    InputError,
    IconInput,
    Checkbox,
    Back,
    Link
  },
  props: {
    operators: Array,
    production_processes: Object,
    sales: Object,
    is_automatic_assignment: Boolean,
  },
  methods: {
    store() {
      this.form.post(route("productions.store"), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Órdenes de producción creadas",
            type: "success",
          });

          this.form.reset();
        },
      });
    },
    // productions
    addTask() {
      const task = { ...this.task };

      if (this.editTaskIndex !== null) {
        this.tasks[this.editTaskIndex] = task;
        this.editTaskIndex = null;
      } else {
        this.tasks.push(task);
      }

      this.task.operator_id = null;
      this.task.tasks = null;
      this.task.estimated_time_minutes = 0;
      this.task.estimated_time_hours = 0;

    },
    deleteTask(index) {
      this.tasks.splice(index, 1);
    },
    editTask(index) {
      const task = { ...this.tasks[index] };
      this.task = task;
      this.editTaskIndex = index;
    },

    // productions
    addProduction() {
      let production = { ...this.production };
      production.tasks = this.tasks;

      if (this.editProductionIndex !== null) {
        this.form.productions[this.editProductionIndex] = production;
        this.editProductionIndex = null;
      } else {
        this.form.productions.push(production);
      }

      // reset production form & list of tasks
      this.tasks = [];
      this.production.user_id = this.$page.props.auth.user.id;
      this.production.catalog_product_company_sale_id = null;

      // remove ordered product from list
      const index = this.orderedProducts.findIndex(item => item.id == production.catalog_product_company_sale_id);
      this.orderedProducts.splice(index, 1);
    },
    deleteProduction(index) {
      // add ordered product to list
      const product = sales.data.find(item => item.id == this.form.sale_id).catalogProductCompanySales.find(cpcs => cpcs.id == this.form.productions[index].catalog_product_company_sale_id).catalog_product_company.catalog_product.name
      this.orderedProducts.push(product);

      this.form.productions.splice(index, 1);
    },
    editProduction(index) {
      const production = { ...this.form.productions[index] };

      this.form.productions[index].tasks.forEach((element) => {
        const tasks = {
          operator_id: element.operator_id,
          tasks: element.tasks,
          estimated_time_hours: element.estimated_time_hours,
          estimated_time_minutes: element.estimated_time_minutes,
        };

        this.tasks.push(tasks);
      });

      this.production = production;
      this.editProductionIndex = index;
    },
    getProductionProcess() { //con selector no multiple
      this.task.estimated_time_hours = null;
      this.task.estimated_time_minutes = null;

      const productionProcess = this.production_processes.data.find(item => item.name == this.task.tasks);
      const orderedProduct = this.orderedProducts.find(item => item.id == this.production.catalog_product_company_sale_id);

      // Verificamos si productionProcess existe y tiene la propiedad "time"
      if (productionProcess && productionProcess.time) {
        const [hours, minutes, seconds] = productionProcess.time.split(':');

        // Convertimos las horas y minutos a números
        const hoursNumeric = parseInt(hours, 10);
        const minutesNumeric = parseInt(minutes, 10);
        const secondsNumeric = parseInt(seconds, 10);

        // Realizamos la multiplicación
        const totalSeconds = (hoursNumeric * 3600 + minutesNumeric * 60 + secondsNumeric) * orderedProduct.quantity;

        // Convertimos el resultado a horas y minutos
        const totalHours = Math.floor(totalSeconds / 3600);
        const remainingSeconds = totalSeconds % 3600;
        const totalMinutes = Math.floor(remainingSeconds / 60);

        // Sumamos el tiempo resultante al tiempo actual en task (si existe)
        this.task.estimated_time_hours = (this.task.estimated_time_hours || 0) + totalHours;
        this.task.estimated_time_minutes = (this.task.estimated_time_minutes || 0) + totalMinutes;

        // Ajustamos los minutos si superan 60
        this.task.estimated_time_hours += Math.floor(this.task.estimated_time_minutes / 60);
        this.task.estimated_time_minutes %= 60;
      }
    },
    // getProductionProcess() { //con selector multiple
    //   this.task.estimated_time_hours = null;
    //   this.task.estimated_time_minutes = null;

    //   // Recorremos los procesos seleccionados
    //   for (const selectedProcessName of this.task.tasks) {
    //     const productionProcess = this.production_processes.data.find(item => item.name === selectedProcessName);
    //     const orderedProduct = this.orderedProducts.find(item => item.id == this.production.catalog_product_company_sale_id);

    //     // Verificamos si productionProcess existe y tiene la propiedad "time"
    //     if (productionProcess && productionProcess.time) {
    //       const [hours, minutes, seconds] = productionProcess.time.split(':');

    //       // Convertimos las horas y minutos a números
    //       const hoursNumeric = parseInt(hours, 10);
    //       const minutesNumeric = parseInt(minutes, 10);
    //       const secondsNumeric = parseInt(seconds, 10);

    //       // Realizamos la multiplicación
    //       const totalSeconds = (hoursNumeric * 3600 + minutesNumeric * 60 + secondsNumeric) * orderedProduct.quantity;

    //       // Convertimos el resultado a horas y minutos
    //       const totalHours = Math.floor(totalSeconds / 3600);
    //       const remainingSeconds = totalSeconds % 3600;
    //       const totalMinutes = Math.floor(remainingSeconds / 60);

    //       // Sumamos el tiempo resultante al tiempo actual en task (si existe)
    //       this.task.estimated_time_hours = (this.task.estimated_time_hours || 0) + totalHours;
    //       this.task.estimated_time_minutes = (this.task.estimated_time_minutes || 0) + totalMinutes;

    //       // Ajustamos los minutos si superan 60
    //       this.task.estimated_time_hours += Math.floor(this.task.estimated_time_minutes / 60);
    //       this.task.estimated_time_minutes %= 60;
    //     }
    //   }
    // },
    addRevelationTime() {
      this.getProductionProcess();
      if (this.type_revelation === 'usado') {
        this.task.estimated_time_hours += 1;
      } else {
        // Sumar 25 minutos
        this.task.estimated_time_minutes += 25;

        // Ajustar las horas si los minutos superan los 60
        if (this.task.estimated_time_minutes >= 60) {
          const horasExtras = Math.floor(this.task.estimated_time_minutes / 60);
          const minutosRestantes = this.task.estimated_time_minutes % 60;

          // Añadir las horas extras y establecer los minutos restantes
          this.task.estimated_time_hours += horasExtras;
          this.task.estimated_time_minutes = minutosRestantes;
        }
      }
    },
    clearVars() {
      this.task.tasks = null;
      this.task.estimated_time_hours = 0;
      this.task.estimated_time_minutes = 0;
    }
  },
  watch: {
    'form.sale_id'(newVal) { // Observa el cambio de form.sale_id
      const sale = this.sales.data.find(item => item.id == newVal);

      if (sale) {
        this.orderedProducts = [...sale.catalogProductCompanySales];
        this.isSaleProduction = sale.is_sale_production;
      }
    }
  }
};
</script>
