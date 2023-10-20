<template>
  <AppLayoutNoHeader title="Oportunidades|">
    <div
      @click="show_type_view = false"
      class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1"
    >
      <div class="flex justify-between">
        <label class="text-lg">Oportunidades</label>
      </div>
      <div class="flex justify-between">
        <div v-if="type_view == 'Lista'" class="flex items-center space-x-2 w-1/3">
          <input
          :disabled="type_view == 'Kanban'"
          @keyup.enter="handleSearch"
          v-model="inputSearch"
          type="search"
          class="input"
          placeholder="Buscar"
          />
          <SecondaryButton :disabled="type_view == 'Kanban'" @click="handleSearch" type="submit" class="rounded-lg">
            <i class="fa-solid fa-magnifying-glass"></i>
          </SecondaryButton>
        </div>
        <span v-if="type_view == 'Kanban'"></span>
        <div class="flex items-center space-x-2">
          <div
            @click.stop="show_type_view = !show_type_view"
            class="flex items-center text-primary mr-7 cursor-pointer relative"
          >
            <p class="text-sm">{{ type_view }}</p>
            <i class="fa-solid fa-angle-down text-sm ml-2"></i>
            <div
              v-if="show_type_view"
              class="text-sm absolute -bottom-16 border rounded-md py-1 px-1"
            >
              <p
                @click="type_view = 'Kanban'"
                class="cursor-pointer hover:bg-red-100 rounded-full py-1 px-3"
              >
                Kanban
              </p>
              <p
                @click="type_view = 'Lista'"
                class="cursor-pointer hover:bg-red-100 rounded-full py-1 px-3"
              >
                Lista
              </p>
            </div>
          </div>
            <Link v-if="$page.props.auth.user.permissions.includes('Crear oportunidades')" :href="route('oportunities.create')">
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

    <!-- ------------ Kanban view starts ----------------- -->
    <div
      v-if="type_view === 'Kanban'"
      class="mx-4 contenedor text-center text-sm my-16 pb-9"
    >
      <!-- ---- Nueva --- -->
      <section class="seccion">
        <h2 class="text-[#9A9A9A] bg-[#D9D9D9] border border-[#9A9A9A] py-1">Nueva</h2>
        <div class="border border-[#9A9A9A] p-2 min-h-full">
          <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
          <p class="text-secondary text-xl my-2">
            ${{ newTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00" }}
          </p>

          <OportunityCard
            class="my-3"
            v-for="oportunity in newOportunitiesLocal"
            :key="oportunity"
            :oportunity="oportunity"
          />
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

          <OportunityCard
            class="my-3"
            v-for="oportunity in pendingOportunitiesLocal"
            :key="oportunity"
            :oportunity="oportunity"
          />
        </div>
      </section>

      <!-- ---- En progreso --- -->
      <section class="seccion">
        <h2 class="text-[#FD8827] bg-[#FEDBBD] border border-[#9A9A9A] py-1">
          En progreso
        </h2>
        <div class="border border-[#9A9A9A] p-2 min-h-full">
          <!-- <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p> -->
          <p class="text-secondary text-xl my-2">
            ${{
              inProgressTotal?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? "0.00"
            }}
          </p>

          <OportunityCard
            class="my-3"
            v-for="oportunity in progressOportunitiesLocal"
            :key="oportunity"
            :oportunity="oportunity"
          />
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

          <OportunityCard
            class="my-3"
            v-for="oportunity in paidOportunitiesLocal"
            :key="oportunity"
            :oportunity="oportunity"
          />
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

          <OportunityCard
            class="my-3"
            v-for="oportunity in lostOportunitiesLocal"
            :key="oportunity"
            :oportunity="oportunity"
          />
        </div>
      </section>
    </div>
    <!-- ------------ Kanban view ends ----------------- -->

    <!-- ------------ Lista view starts ----------------- -->
    <div v-if="type_view === 'Lista'" class="w-11/12 mx-auto my-16">
      <table class="lg:w-[80%] w-full mx-auto">
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
          <tr
            v-for="oportunity in filteredTableData"
            :key="oportunity.id"
            class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
            @click="$inertia.get(route('oportunities.show', oportunity.id))"
          >
            <td class="text-left py-2 px-2 rounded-l-full">
              {{ oportunity.name }}
            </td>
            <td class="text-left py-2 px-2">
              <span
                class="py-1 px-4 rounded-full"
                :class="getStatusStyles(oportunity)"
                >{{ oportunity.status }}</span
              >
            </td>
            <td class="text-left py-2 px-2">
              <span
                class="py-1 px-2 rounded-full"
                >{{ oportunity.created_at.isoFormat }}</span
              >
            </td>
            <td class="text-left py-2 px-2">
              {{ oportunity.estimated_finish_date }}
            </td>
            <td class="text-left py-2 px-2">
              {{ oportunity.finished_at ?? "--" }}
            </td>
            <td
              v-if="$page.props.auth.user.permissions.includes('Eliminar oportunidades')"
              class="text-left py-2 px-2 rounded-r-full"
            >
              <el-popconfirm
                confirm-button-text="Si"
                cancel-button-text="No"
                icon-color="#D90537"
                title="¿Eliminar?"
                @confirm="deleteOportunity(oportunity)"
              >
                <template #reference>
                  <i
                    @click.stop=""
                    class="fa-regular fa-trash-can text-primary cursor-pointer p-2"
                  ></i>
                </template>
              </el-popconfirm>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- ------------ Lista view ends ----------------- -->
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import OportunityCard from "@/Components/MyComponents/OportunityCard.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      search: "",
      inputSearch: "",
      show_type_view: false,
      type_view: "Kanban",
      newTotal: null,
      pendingTotal: null,
      inProgressTotal: null,
      paidTotal: null,
      lostTotal: null,
      newOportunitiesLocal: [],
      pendingOportunitiesLocal: [],
      progressOportunitiesLocal: [],
      paidOportunitiesLocal: [],
      lostOportunitiesLocal: [],
    };
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    SecondaryButton,
    OportunityCard,
    Link,
  },
  props: {
    oportunities: Object,
  },
  methods: {
    getStatusStyles(oportunity){
        if (oportunity.status === 'Nueva') {
            return 'text-[#9A9A9A] bg-[#CCCCCCCC]';
        } else if (oportunity.status === 'Pendiente') {
             return 'text-[#C88C3C] bg-[#F3FD85]';
        } else if (oportunity.status === 'En proceso') {
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
    
  },
  mounted() {
    this.newOportunitiesLocal = this.oportunities?.data.filter(
      (oportunity) => oportunity.status === "Nueva"
    );
    this.pendingOportunitiesLocal = this.oportunities?.data.filter(
      (oportunity) => oportunity.status === "Pendiente"
    );
    this.progressOportunitiesLocal = this.oportunities?.data.filter(
      (oportunity) => oportunity.status === "En proceso"
    );
    this.paidOportunitiesLocal = this.oportunities?.data.filter(
      (oportunity) => oportunity.status === "Pagado"
    );
    this.lostOportunitiesLocal = this.oportunities?.data.filter(
      (oportunity) => oportunity.status === "Perdida"
    );

    // Calcula el dinero total de cada sección
    this.newTotal = this.newOportunitiesLocal.reduce(
      (total, oportunity) => total + oportunity.amount,
      0
    );
    this.pendingTotal = this.pendingOportunitiesLocal.reduce(
      (total, oportunity) => total + oportunity.amount,
      0
    );
    this.inProgressTotal = this.progressOportunitiesLocal.reduce(
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
  overflow-x: scroll; /* Permite el desplazamiento horizontal */
  white-space: nowrap; /* Evita el salto de línea de las secciones */
}

.seccion {
  flex: 0 0 25%; /* Establece el ancho de cada sección al 25% */
}
</style>