<template>
  <AppLayoutNoHeader title="Oportunidades|">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
      <div class="flex justify-between">
        <label class="text-lg">Oportunidades</label>
        <Link
          :href="route('dashboard')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
        >
          <i class="fa-solid fa-xmark"></i>
        </Link>
      </div>
      <div class="flex justify-between">
        <div class="md:w-1/3">
          <el-select
            v-model="selectedOportunity"
            clearable
            filterable
            placeholder="Buscar oporunidad"
            no-data-text="No hay oportunidades registradas"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in companies"
              :key="item.id"
              :label="item.business_name"
              :value="item.id"
            />
          </el-select>
        </div>
        <div class="flex items-center space-x-2">
          <div class="flex items-center text-primary mr-5 cursor-pointer">
            <p class="text-sm">{{ type_view }}</p>
            <i class="fa-solid fa-angle-down text-sm ml-2"></i>
          </div>
          <el-tooltip
            v-if="$page.props.auth.user.permissions.includes('Crear oportunidades')"
            content="Crear oportunidad"
            placement="top"
          >
            <Link :href="route('oportunities.create')">
              <PrimaryButton class="rounded-md">Crear</PrimaryButton>
            </Link>
          </el-tooltip>

          <Dropdown
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
          </Dropdown>
        </div>
      </div>
    </div>

    <!-- ------------ Kanban view ----------------- -->
    <div class="w-11/12 mx-auto md:grid grid-cols-4 text-center text-sm my-16">
      <!-- ---- Nueva --- -->
      <section>
        <h2 class="text-[#9A9A9A] bg-[#D9D9D9] border border-[#9A9A9A] py-1">Nueva</h2>
        <div class="border border-[#9A9A9A] p-2">
          <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p>
          <p class="text-secondary text-xl my-2">
            ${{ newTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
          </p>

          <!-- --- oportunity card -- -->
          <div
            class="border border-[#D9D9D9] text-left rounded-md py-2 lg:px-8 shadow-md shadow-gray-400/100 h-24 relative"
          >
            <div class="flex items-center absolute top-2 left-3">
              <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
              <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
            </div>
            <i class="fa-solid fa-circle text-[9px] absolute top-3 right-3"></i>
            <p>{{ "BOSH Porta Placas" }}</p>
            <p>{{ "Martin Gallegos" }}</p>
            <p>${{ "65,945.32" }}</p>
            <p class="text-right">{{ "Hace 5 días" }}</p>
          </div>
        </div>
      </section>

      <!-- ---- Pendiente de aprobación --- -->
      <section>
        <h2 class="text-[#C88C3C] bg-[#F3FD85] border border-[#9A9A9A] py-1">
          Pendiente de aprobación
        </h2>
        <div class="border border-[#9A9A9A]">
          <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p>
          <p class="text-secondary text-xl my-2">
            ${{ pendentTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
          </p>
        </div>
      </section>

      <!-- ---- En progreso --- -->
      <section>
        <h2 class="text-[#FD8827] bg-[#FEDBBD] border border-[#9A9A9A] py-1">
          En progreso
        </h2>
        <div class="border border-[#9A9A9A]">
          <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p>
          <p class="text-secondary text-xl my-2">
            ${{ inProgressTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
          </p>
        </div>
      </section>

      <!-- ---- Pagado --- -->
      <section>
        <h2 class="text-[#37951F] bg-[#AFFDB2] border border-[#9A9A9A] py-1">Pagado</h2>
        <div class="border border-[#9A9A9A]">
          <p class="text-[#9A9A9A] cursor-pointer mt-1">+ Agregar</p>
          <p class="text-secondary text-xl my-2">
            ${{ payedTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
          </p>
        </div>
      </section>
    </div>
  </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedOportunity: "",
      currentCompany: null,
      type_view: "Kanban",
      newTotal: 15477,
      pendentTotal: 4579,
      inProgressTotal: 1777,
      payedTotal: 78943,
    };
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    Link,
  },
  props: {},
  methods: {},
};
</script>

<style></style>
