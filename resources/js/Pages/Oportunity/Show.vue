<template>
   <AppLayoutNoHeader title="Oportunidades">
        <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Oportunidades</label>
          <Link :href="route('oportunities.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>

        <div class="flex justify-between">
          <div class="md:w-1/3 mr-2">
            <el-select @change="saleSelection" v-model="oportunitySelected" clearable filterable
              placeholder="Buscar oportunidades" no-data-text="No hay oportuindades registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in oportunities.data" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar oportunidades')" content="Editar" placement="top">
              <Link :href="route('oportunities.edit', oportunitySelected)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <el-tooltip
            v-if="$page.props.auth.user.permissions.includes('Crear oportunidades')"
            content="Crear oportunidad"
            placement="top"
          >
            <Link :href="route('oportunities.create')">
              <PrimaryButton class="rounded-md">Crear</PrimaryButton>
            </Link>
          </el-tooltip>

            <Dropdown align="right" width="48" v-if="$page.props.auth.user.permissions.includes(
              'Crear ordenes de venta'
            ) &&
              $page.props.auth.user.permissions.includes(
                'Eliminar ordenes de venta'
              )
              ">
              <template #trigger>
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')">
                  Enviar correo
                </DropdownLink>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')">
                  Registrar cita
                </DropdownLink>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')">
                  Registrar pago
                </DropdownLink>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')">
                  Crear nueva actividad
                </DropdownLink>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes(
                  'Eliminar oportunidades'
                )
                  " @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ currentOportunity?.name }}
      </p>

      
      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Info de oportunidad
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Actividades
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Seguimiento integral
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 4" :class="tabs == 4 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Historial
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>

          <span class="text-gray-500">Folio</span>
          <span>{{ currentOportunity?.folio }}</span>
          <span class="text-gray-500 my-2">Nombre de la oportunidad</span>
          <span>{{ currentOportunity?.name }}</span>
          <span class="text-gray-500 my-2">Descripción</span>
          <span>{{ currentOportunity?.description }}</span>
          <span class="text-gray-500 my-2">Creado por</span>
          <span>{{ currentOportunity?.description }}</span>
          <span class="text-gray-500 my-2">Estatus</span>
          <p :class="getStatusStyles()" class="rounded-full px-2 py-1 w-1/2 text-center">{{ currentOportunity?.status }}</p>
          <span class="text-gray-500 my-2">Probabilidad</span>
          <span>{{ currentOportunity?.probability }}</span>
          <span class="text-gray-500 my-2">Fecha de inicio</span>
          <span>{{ currentOportunity?.start_date }}</span>
          <span class="text-gray-500 my-2">Fecha estimada de cierre</span>
          <span>{{ currentOportunity?.estimated_finish_date }}</span>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Usuarios</p>

          <span v-for="asignedName in uniqueAsignedNames" :key="asignedName" class="text-gray-500">{{ asignedName }}</span>
          <span>{{ currentOportunity?.company_branch }}</span>

          <p class="text-secondary col-span-2 mt-7">Documentos adjuntos</p>

          <span class="text-gray-500 my-2">Nombre</span>
          <span>{{ currentOportunity?.contact?.name }}</span>

          <div class="flex items-center justify-end space-x-2 col-span-2 mr-4">
            <i class="fa-regular fa-calendar text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
            <i class="fa-solid fa-money-bill text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
            <i class="fa-regular fa-envelope text-primary cursor-pointer text-lg px-3"></i>
          </div>

        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 atividades starts ------------- -->

      <div v-if="tabs == 2" class="md:grid grid-cols-3 text-left p-4 text-sm">
      <!-- -- TERMINAR HOY -- -->
      <div class="lg:border-r lg:mb-0 mb-16 border-[#9A9A9A] h-auto lg:pr-7">
        <h2 class="font-bold mb-10">
          TERMINAR HOY <span class="font-normal ml-7">{{ currentOportunity?.oportunityTasks?.length }}</span>
        </h2>
        <!-- <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="currentOportunity" :animation="300" item-key="id" tag="ul" group="tasks" id="pendent" :class="(drag && !currentOportunity?.length) ? 'h-40' : ''">
          <template #item="{ element: task }">
            <li>
              <ProjectTaskCard @updated-status="updateTask($event)" :taskComponent="task" :users="users" :id="task.id" />
            </li>
          </template>
        </draggable> -->
        <div class="text-center" v-if="!currentOportunity?.oportunityTasks.length">
          <p class="text-xs text-gray-500">No hay tareas para mostrar</p>
          <i class="fa-regular fa-folder-open text-9xl text-gray-300/50 mt-16"></i>
        </div>
      </div>

    </div>

      <!-- ------------- tab 2 atividades ends ------------ -->
   </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
 data(){
    return{
    oportunitySelected: "",
    currentOportunity: null,
    tabs: 1,
    }
 },
 components:{
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    Link,
 },
 props:{
    oportunity: Object,
    oportunities: Object,
 },
 methods:{
    getStatusStyles(){
        if (this.currentOportunity?.status === 'Nueva') {
            return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
        } else if (this.currentOportunity?.status === 'Pendiente') {
             return 'text-[#C88C3C] bg-[#F3FD85]';
        } else if (this.currentOportunity?.status === 'En progreso') {
             return 'text-[#FD8827] bg-[#FEDBBD]';
        } else if (this.currentOportunity?.status === 'Pagado') {
             return 'text-[#37951F] bg-[#ADFEB5]';
        } else if (this.currentOportunity?.status === 'Perdida') {
             return 'text-[#9E0FA9] bg-[#F7B7FC]';
        }
 },
 },
 watch: {
    oportunitySelected(newVal) {
      this.currentOportunity = this.oportunities.data.find((item) => item.id == newVal);
    },
  },
 mounted() {
    this.oportunitySelected = this.oportunity.id;
    this.currentOportunity = this.oportunities.data.find(
      (item) => item.id == this.oportunitySelected
    );
  },
  computed: {
    uniqueAsignedNames() {
      const asignedNamesSet = new Set(); // Usamos un Set para nombres únicos.

        if (this.currentOportunity?.oportunityTasks.length) {

            // Recorremos las tareas y agregamos los nombres de los asignados al conjunto.
      this.currentOportunity?.oportunityTasks?.forEach((task) => {
          asignedNamesSet.add(task.asigned.name);
      });

      // Convertimos el conjunto a un array para mostrarlo en la plantilla.
      return Array.from(asignedNamesSet);
          }
    },
  },
};
</script>

<style>

</style>