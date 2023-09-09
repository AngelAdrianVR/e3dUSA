<template>
  <div>
    <AppLayoutNoHeader title="Historial de acciones">
      <div class="flex justify-between text-lg mx-14 mt-11">
        <span>Historial de acciones</span>
      </div>

      <div class="mt-7 border-b-2">
        <!-- ------------------------Information panel tabs--------------------- -->
        <div class="border-2">
          <div class="border-b-2 px-7 py-3 flex">
            <p
              @click="tabs = 1"
              :class="
                tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base leading-none"
            >
              Creaciones
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p
              @click="tabs = 2"
              :class="
                tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
            >
              Ediciones
            </p>
            <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
            <p
              @click="tabs = 3"
              :class="
                tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
              "
              class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
            >
              Eliminaciones
            </p>
          </div>

          <!-- --------------------- Tab 1 Creaciones starts------------------ -->
          <div v-if="tabs == 1" class="px-7 py-7 text-sm">
            <!-- pagination -->
                    <!-- <div class="mt-6">
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="audits_created.data.length" />
                    </div> -->
            <table class="w-full">
              <thead>
                <tr class="text-left">
                  <th class="font-normal pb-5">
                    Módulo <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    ID Creado <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    Usuario <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    Fecha de creación
                    <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="record_created in audits_created.data"
                  :key="record_created.id"
                  class="mb-4 hover:bg-gray-200"
                >
                  <td @click="showModal = true; current_audit = record_created" class="text-left pb-3 cursor-pointer">
                    {{ translate(record_created.table_name) }}
                  </td>
                  <td @click="showModal = true; current_audit = record_created" class="text-left pb-3 cursor-pointer">
                    <span>{{ record_created.record_id }}</span>
                  </td>
                  <td @click="showModal = true; current_audit = record_created" class="text-left pb-3 cursor-pointer">
                    {{ getUserName(record_created.user_id) }}
                  </td>
                  <td @click="showModal = true; current_audit = record_created" class="text-left pb-3 cursor-pointer">
                    {{ record_created.created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- --------------------- Tab 1 Creaciones ends------------------ -->

          <!-- --------------------- Tab 2 ediciones starts------------------ -->
          <div v-if="tabs == 2" class="px-7 py-7 text-sm">
            <table class="w-full">
              <thead>
                <tr class="text-left">
                  <th class="font-normal pb-5">
                    Módulo <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    ID Editado <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    Usuario <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    Fecha de Edición
                    <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="record_edited in audits_edited.data"
                  :key="record_edited.id"
                  class="mb-4 cursor-pointer hover:bg-gray-200"
                   @click="showModal = true; current_audit = record_edited"
                >
                  <td class="text-left pb-3">
                    {{ translate(record_edited.table_name) }}
                  </td>
                  <td class="text-left pb-3">
                    <span>{{ record_edited.record_id }}</span>
                  </td>
                  <td class="text-left pb-3">
                    {{ getUserName(record_edited.user_id) }}
                  </td>
                  <td class="text-left pb-3">
                    {{ record_edited.created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- --------------------- Tab 2 ediciones ends------------------ -->

          <!-- --------------------- Tab 3 eliminaciones starts------------------ -->
          <div v-if="tabs == 3" class="px-7 py-7 text-sm">
            <table class="w-full">
              <thead>
                <tr class="text-left">
                  <th class="font-normal pb-5">
                    Módulo <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    ID Eliminado
                    <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    Usuario <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                  <th class="font-normal pb-5">
                    Fecha de Eliminación
                    <i class="fa-solid fa-arrow-down-long ml-3"></i>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="record_deleted in audits_deleted.data"
                  :key="record_deleted.id"
                  class="mb-4 cursor-pointer hover:bg-gray-200"
                   @click="showModal = true; current_audit = record_deleted"
                >
                  <td @click="showModal = true" class="text-left pb-3">
                    {{ translate(record_deleted.table_name) }}
                  </td>
                  <td class="text-left pb-3">
                    <span>{{ record_deleted.record_id }}</span>
                  </td>
                  <td class="text-left pb-3">
                    {{ getUserName(record_deleted.user_id) }}
                  </td>
                  <td class="text-left pb-3">
                    {{ record_deleted.created_at }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- --------------------- Tab 3 eliminaciones ends------------------ -->
        </div>
      </div>

      <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
          <p>Historial de registro</p>
        </template>
        <template #content>
          <div class="md:grid grid-cols-2">

          <div class="text-sm mb-4">
            <h1 class="text-center text-primary mb-2">Registro viejo</h1>
            <pre>
                {{ current_audit.old_data }}
            </pre>
          </div>
          <div class="text-sm">
            <h1 class="text-center text-primary mb-2">Registro Nuevo</h1>
            <pre>
                {{ current_audit.new_data }}
            </pre>
          </div>
            
          </div>
        </template>
        <template #footer>
          <CancelButton @click="showModal = false">Cerrar</CancelButton>
        </template>
      </DialogModal>
    </AppLayoutNoHeader>
  </div>
</template>
  
<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";

export default {
  data() {

    return {
    showModal: false,
    current_audit: null,
    tabs: 1,
    itemsPerPage: 10,
    start: 0,
    end: 10,
};
  },
  components: {
    AppLayoutNoHeader,
    PrimaryButton,
    IconInput,
    CancelButton,
    DialogModal,
  },
  props: {
    audits_created: Object,
    audits_edited: Object,
    audits_deleted: Object,
    users: Array,
  },
  methods: {
    translate(table){
        switch(table){
            case 'permissions':
                return 'Permisos';
            case 'kiosk_devices':
                return 'Kiosco';
            case 'production_costs':
                return 'Costo de producción';
            case 'samples':
                return 'Seguimiento de muestras';
            case 'meetings':
                return 'Reuniones';
            case 'spare_parts':
                return 'Piezas de refacción';
            case 'maintenances':
                return 'Mantenimiento';
            case 'machines':
                return 'Máquinas';
            case 'productions':
                return 'Ordenes de produccion';
            case 'designs':
                return 'Ordenes de diseño';
            case 'holidays':
                return 'Días festivos';
            case 'discounts':
                return 'Descuentos';
            case 'bonuses':
                return 'Bonos';
            case 'roles':
                return 'Roles';
            case 'users':
                return 'Usuarios';
            case 'additional_time_requests':
                return 'Solicitudes de tiempo adicional';
            case 'raw_materials':
                return 'Materia prima';
            case 'catalog_products':
                return 'Producto de catálogo';
            case 'sales':
                return 'Ordenes de venta';
            case 'companies':
                return 'Clientes';
            case 'quotes':
                return 'Cotizaciones';

        }
    },
    getUserName(user_id){
        
       return this.users.find((item) => item.id == user_id).name;
        // return user.name;
    },
    handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },   
  },
};
</script>
  