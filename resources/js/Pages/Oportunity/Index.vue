<template>
  <AppLayoutNoHeader title="Oportunidades">
    <div class="relative overflow-hidden">
      <div @click="show_type_view = false" class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Oportunidades</label>
        </div>
        <div class="flex justify-between">
          <div v-if="type_view == 'Lista'" class="w-1/3 relative ">
            <input @keyup.enter="handleSearch" v-model="inputSearch" class="input outline-none pr-8"
              placeholder="Buscar proyecto" />
            <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-[#9A9A9A]"></i>
          </div>
          <span v-if="type_view == 'Kanban'"></span>
          <div class="flex items-center space-x-2">
            <div @click.stop="show_type_view = !show_type_view"
              class="flex items-center text-primary mr-7 cursor-pointer relative">
              <p class="text-sm">{{ type_view }}</p>
              <i class="fa-solid fa-angle-down text-sm ml-2"></i>
              <div v-if="show_type_view" class="text-sm absolute -bottom-10 -left-4 border rounded-md py-1 px-1">
                <p v-if="type_view == 'Lista'" @click="type_view = 'Kanban'"
                  class="cursor-pointer hover:bg-red-100 rounded-full py-1 px-3">
                  Kanban
                </p>
                <p v-if="type_view == 'Kanban'" @click="type_view = 'Lista'"
                  class="cursor-pointer hover:bg-red-100 rounded-full py-1 px-3">
                  Lista
                </p>
              </div>
            </div>
            <Link v-if="$page.props.auth.user.permissions.includes('Crear oportunidades')"
              :href="route('oportunities.create')">
            <PrimaryButton class="rounded-lg">Nueva oportunidad</PrimaryButton>
            </Link>
            <!-- <Dropdown
            align="right"
            width="48"
            v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')"
          >
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink
                @click="showConfirmModal = true"
                as="button"
                v-if="
                  $page.props.auth.user.permissions.includes('Eliminar oportunidades')
                "
              >
                Eliminar
              </DropdownLink>
            </template>
          </Dropdown> -->
          </div>
        </div>
      </div>

      <NotificationCenter module="opportunities" />
      <!-- ------------ Kanban view starts ----------------- -->
      <div v-if="type_view === 'Kanban'" class="mx-4 contenedor text-center text-sm my-16 pb-9">
        <!-- ---- Nueva --- -->
        <section class="seccion">
          <h2 class="text-[#9A9A9A] bg-[#D9D9D9] border border-[#9A9A9A] py-1">Nueva</h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-secondary text-xl my-2">
              ${{ newTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00" }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="newOportunitiesLocal"
              :animation="300" item-key="id" tag="ul" group="oportunities" id="new"
              :class="(drag && !newOportunitiesLocal?.length) ? 'h-40' : ''">
              <template #item="{ element: oportunity }">
                <li>
                  <OportunityCard class="my-3" :oportunity="oportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!newOportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay oportunidades en este estatus</p>
            </div>
          </div>
        </section>

        <!-- ---- Pendiente de aprobación --- -->
        <section class="seccion">
          <h2 class="text-[#C88C3C] bg-[#F3FD85] border border-[#9A9A9A] py-1">
            Pendiente de aprobación
          </h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-secondary text-xl my-2">
              ${{
                pendingTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00"
              }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false"
              v-model="pendingOportunitiesLocal" :animation="300" item-key="id" tag="ul" group="oportunities" id="pending"
              :class="(drag && !pendingOportunitiesLocal?.length) ? 'h-40' : ''">
              <template #item="{ element: oportunity }">
                <li>
                  <OportunityCard class="my-3" :oportunity="oportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!pendingOportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay oportunidades en este estatus</p>
            </div>
          </div>
        </section>

        <!-- ---- Cerrada --- -->
        <section class="seccion">
          <h2 class="text-[#FD8827] bg-[#FEDBBD] border border-[#9A9A9A] py-1">
            Cerrada
          </h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-secondary text-xl my-2">
              ${{
                closedTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00"
              }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="closedOportunitiesLocal"
              :animation="300" item-key="id" tag="ul" group="oportunities" id="closed"
              :class="(drag && !closedOportunitiesLocal?.length) ? 'h-40' : ''">
              <template #item="{ element: oportunity }">
                <li>
                  <OportunityCard class="my-3" :oportunity="oportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!closedOportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay oportunidades en este estatus</p>
            </div>
          </div>
        </section>

        <!-- ---- Pagado --- -->
        <section class="seccion">
          <h2 class="text-[#37951F] bg-[#AFFDB2] border border-[#9A9A9A] py-1">Pagado</h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-secondary text-xl my-2">
              ${{ paidTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00" }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="paidOportunitiesLocal"
              :animation="300" item-key="id" tag="ul" group="oportunities" id="paid"
              :class="(drag && !paidOportunitiesLocal?.length) ? 'h-40' : ''">
              <template #item="{ element: oportunity }">
                <li>
                  <OportunityCard class="my-3" :oportunity="oportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!paidOportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay oportunidades en este estatus</p>
            </div>
          </div>
        </section>

        <!-- ---- Perdidas --- -->
        <section class="seccion">
          <h2 class="text-[#9E0FA9] bg-[#F7B7FC] border border-[#9A9A9A] py-1">Perdidas</h2>
          <div class="border border-[#9A9A9A] p-2 min-h-full">
            <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
            <p class="text-secondary text-xl my-2">
              ${{ lostTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00" }}
            </p>
            <draggable @start="handleStartDrag" @add="handleAddDrag" @end="drag = false" v-model="lostOportunitiesLocal"
              :animation="300" item-key="id" tag="ul" group="oportunities" id="lost"
              :class="(drag && !lostOportunitiesLocal?.length) ? 'h-40' : ''">
              <template #item="{ element: oportunity }">
                <li>
                  <OportunityCard class="my-3" :oportunity="oportunity" />
                </li>
              </template>
            </draggable>
            <div class="text-center" v-if="!lostOportunitiesLocal?.length">
              <p class="text-xs text-gray-500 mt-6">No hay oportunidades en este estatus</p>
            </div>
          </div>
        </section>
      </div>
      <!-- ------------ Kanban view ends ----------------- -->

      <!-- ------------ Lista view starts ----------------- -->
      <div v-if="type_view === 'Lista'" class="w-11/12 mx-auto my-16">
        <table v-if="oportunities.data.length > 0" class="lg:w-[90%] w-full mx-auto">
          <thead>
            <tr class="text-left">
              <th class="font-bold pb-5">
                Nombre <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Estatus <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Fecha inicio <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Estimación de cierre <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th class="font-bold pb-5">
                Cerrada el <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="oportunity in filteredTableData" :key="oportunity.id"
              class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
              @click="$inertia.get(route('oportunities.show', oportunity.id))">
              <td class="text-left py-2 px-2 rounded-l-full">
                {{ oportunity.name }}
              </td>
              <td class="text-left py-2 px-2">
                <span class="py-1 px-4 rounded-full" :class="getStatusStyles(oportunity)">{{ oportunity.status }}</span>
              </td>
              <td class="text-left py-2 px-2">
                <span class="py-1 px-2 rounded-full">{{ oportunity.created_at.isoFormat }}</span>
              </td>
              <td class="text-left py-2 px-2">
                {{ oportunity.estimated_finish_date }}
              </td>
              <td class="text-left py-2 px-2">
                {{ oportunity.finished_at ?? "--" }}
              </td>
              <td v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')"
                class="text-left py-2 px-2 rounded-r-full">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537" title="¿Eliminar?"
                  @confirm="deleteOportunity(oportunity)">
                  <template #reference>
                    <i @click.stop="" class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
                  </template>
                </el-popconfirm>
              </td>
            </tr>
          </tbody>
        </table>
        <p class="text-center text-sm text-gray-600" v-else>No hay oportunidades</p>
      </div>
      <!-- ------------ Lista view ends ----------------- -->
    </div>

<!-- ----------------- Lost modal ----------- -->
    <Modal :show="showLostOportunityModal || showCreateSaleModal"
      @close="showLostOportunityModal = false; showCreateSaleModal = false">
      <section v-if="showLostOportunityModal" class="mx-7 my-4 space-y-4 relative">
        <div>
          <label>Causa oportunidad perdida
            <el-tooltip content="Escribe la causa por la cual se PERDIÓ esta oportunidad" placement="top">
              <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
            </el-tooltip>
          </label>
          <textarea v-model="lost_oportunity_razon" required class="textarea mt-3"></textarea>
        </div>
        <div class="flex justify-end space-x-3 pt-5 pb-1">
          <CancelButton @click="showLostOportunityModal = false">Cancelar</CancelButton>
          <PrimaryButton @click="updateOpportunityStatus('Perdida')">Actualizar estatus</PrimaryButton>
        </div>
      </section>

      <section v-if="showCreateSaleModal" class="mx-7 my-4 space-y-4 relative">
        <div>
          <h2 class="font bold text-center font-bold mb-5">Paso clave - Crear Orden de Venta</h2>
          <p class="px-5">Es necesario crear una orden de venta al haber marcado como <span class="text-[#FD8827]">”cerrada”</span>  
          o <span class="text-[#37951F]">”Pagada”</span> la oportunidad para llevar un correcto seguimiento y flujo de trabajo. 
          </p>
        </div>
        <div class="flex justify-end space-x-3 pt-5 pb-1">
          <CancelButton @click="cancelUpdating">Cancelar</CancelButton>  
          <PrimaryButton @click="CreateSale">Continuar</PrimaryButton>
        </div>
      </section>
    </Modal>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import OportunityCard from "@/Components/MyComponents/OportunityCard.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import draggable from 'vuedraggable';
import Modal from "@/Components/Modal.vue";
import { Link } from "@inertiajs/vue3";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";

export default {
  data() {
    return {
      search: "",
      inputSearch: "",
      show_type_view: false,
      showLostOportunityModal: false,
      showCreateSaleModal: false,
      type_view: "Kanban",
      localStatus: null,
      lost_oportunity_razon: null,
      newTotal: null,
      pendingTotal: null,
      closedTotal: null,
      paidTotal: null,
      lostTotal: null,
      newOportunitiesLocal: [],
      pendingOportunitiesLocal: [],
      closedOportunitiesLocal: [],
      paidOportunitiesLocal: [],
      lostOportunitiesLocal: [],
      drag: false,
      draggingOpportunityId: null,
      oportunitiesLocal: null,
    };
  },
  components: {
    AppLayoutNoHeader,
    NotificationCenter,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    SecondaryButton,
    CancelButton,
    OportunityCard,
    draggable,
    Modal,
    Link,
  },
  props: {
    oportunities: Object,
  },
  methods: {
    cancelUpdating() {
      window.location.reload()
    },
    handleStartDrag(evt) {
      this.draggingOpportunityId = evt.item.__draggable_context.element.id;
      this.drag = true;
    },
    handleAddDrag(evt) {
      let status = 'Perdida';
      if (evt.to.id === 'new') {
        status = 'Nueva';
      } else if (evt.to.id === 'pending') {
        status = 'Pendiente';
      } else if (evt.to.id === 'closed') {
        status = 'Cerrada';
      } else if (evt.to.id === 'paid') {
        status = 'Pagado';
      } else if (evt.to.id === 'lost') {
        status = 'Perdida';
      }

       if (evt.to.id === "lost") {
        this.showLostOportunityModal = true;
      }else if (evt.to.id === "closed" || evt.to.id === "paid") {
        this.showCreateSaleModal = true;
        this.localStatus = status;
      } else {
        this.updateOpportunityStatus(status);
      }
       
      this.drag = false;
    },
    async updateOpportunityStatus(status) {
      //cierra los modales antes de actualizar el estado
        this.showLostOportunityModal = false;
        this.showCreateSaleModal = false;
      try {
        const response = await axios.put(route('oportunities.update-status', this.draggingOpportunityId), { status: status, lost_oportunity_razon: this.lost_oportunity_razon });

        if (response.status === 200) {
          const OpportunityIndex = this.oportunitiesLocal.findIndex(item => item.id === this.draggingOpportunityId);
          this.oportunitiesLocal[OpportunityIndex].status = status;
          this.updateLists();
          this.calculateTotals();
        }
      } catch (error) {
        console.log(error);
      }
    },
    async CreateSale() {
      try {
        const response = await axios.put(route('oportunities.create-sale', this.draggingOpportunityId));
        if (response.status === 200) {
          if (response.data.message) {
            this.$notify({
              title: "Éxito",
              message: response.data.message,
              type: "success",
            });
            this.showCreateSaleModal = false;
            this.updateStatus();
          } else {
            this.updateOpportunityStatus(this.localStatus);
            this.$inertia.get(route('sales.create'), { opportunityId: this.draggingOpportunityId });
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    getStatusStyles(oportunity) {
      if (oportunity.status === 'Nueva') {
        return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
      } else if (oportunity.status === 'Pendiente') {
        return 'text-[#C88C3C] bg-[#F3FD85]';
      } else if (oportunity.status === 'Cerrada') {
        return 'text-[#FD8827] bg-[#FEDBBD]';
      } else if (oportunity.status === 'Pagado') {
        return 'text-[#37951F] bg-[#ADFEB5]';
      } else if (oportunity.status === 'Perdida') {
        return 'text-[#9E0FA9] bg-[#F7B7FC]';
      }
    },
    handleSearch() {
      this.search = this.inputSearch;
    },
    deleteOportunity(oportunity) {
      this.$inertia.delete(route('oportunities.destroy', oportunity));
      this.$notify({
        title: "Éxito",
        message: "Oportunidad eliminado",
        type: "success",
      });
    },
    calculateTotals() {
      this.newTotal = this.newOportunitiesLocal.reduce(
        (total, oportunity) => total + oportunity.amount,
        0
      );
      this.pendingTotal = this.pendingOportunitiesLocal.reduce(
        (total, oportunity) => total + oportunity.amount,
        0
      );
      this.closedTotal = this.closedOportunitiesLocal.reduce(
        (total, oportunity) => total + oportunity.amount,
        0
      );
      this.paidTotal = this.paidOportunitiesLocal.reduce(
        (total, oportunity) => total + oportunity.amount,
        0
      );
      this.lostTotal = this.lostOportunitiesLocal.reduce(
        (total, oportunity) => total + oportunity.amount,
        0
      );
    },
    updateLists() {
      this.newOportunitiesLocal = this.oportunitiesLocal.filter(
        (oportunity) => oportunity.status === "Nueva"
      );
      this.pendingOportunitiesLocal = this.oportunitiesLocal.filter(
        (oportunity) => oportunity.status === "Pendiente"
      );
      this.closedOportunitiesLocal = this.oportunitiesLocal.filter(
        (oportunity) => oportunity.status === "Cerrada"
      );
      this.paidOportunitiesLocal = this.oportunitiesLocal.filter(
        (oportunity) => oportunity.status === "Pagado"
      );
      this.lostOportunitiesLocal = this.oportunitiesLocal.filter(
        (oportunity) => oportunity.status === "Perdida"
      );
    },

  },
  mounted() {
    this.oportunitiesLocal = this.oportunities.data;
    this.updateLists();
    // Calcula el dinero total de cada sección
    this.calculateTotals();
  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.oportunities.data;
      } else {
        return this.oportunities.data.filter((oportunity) =>
          oportunity.name.toLowerCase().includes(this.search.toLowerCase()) ||
          oportunity.status.toLowerCase().includes(this.search.toLowerCase())
        );
      }
    },
  },
};
</script>

<style>
.contenedor {
  display: flex;
  overflow-x: scroll;
  /* Permite el desplazamiento horizontal */
  white-space: nowrap;
  /* Evita el salto de línea de las secciones */
}

.seccion {
  flex: 0 0 22%;
  /* Establece el ancho de cada sección al 25% */
}
</style>