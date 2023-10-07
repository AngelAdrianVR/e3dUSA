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

            <!-- ----------------------- botones para super admin starts------------------------ -->

            <el-popconfirm
              v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de venta') && currentSale?.authorized_at == 'No autorizado'"
              confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
              @confirm="authorizeOrder">
              <template #reference>
                <button class="rounded-lg bg-primary text-white py-1 px-2">
                  Autorizar
                </button>
              </template>
            </el-popconfirm>
            <!-- ----------------------- botones para super admin ends------------------------ -->

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
                <DropdownLink v-if="$page.props.auth.user.permissions.includes(
                  'Crear ordenes de venta'
                )
                  " :href="route('sales.create')">
                  Crear nueva orden de venta
                </DropdownLink>

                <DropdownLink v-if="$page.props.auth.user.permissions.includes(
                  'Eliminar ordenes de venta'
                )
                  " @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
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

 },
}
</script>

<style>

</style>