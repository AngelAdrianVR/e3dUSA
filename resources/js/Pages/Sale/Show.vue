<template>
  <div>
    <AppLayoutNoHeader title="Órdenes de venta / stock">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de venta / stock</label>
          <Link :href="route('sales.index')"
            class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>

        <div class="flex justify-between">
          <div class="md:w-1/3 mr-2">
            <el-select @change="$inertia.get(route('sales.show', saleSelected))" v-model="saleSelected" clearable
              filterable placeholder="Buscar órden de venta / stock" no-data-text="No hay órdenes registradas"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in sales" :key="item.id" :label="'OV-' + item.id" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Imprimir" placement="top">
              <button @click="openPrintPage" class="size-9 flex items-center justify-center rounded-lg bg-[#D9D9D9]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
              </button>
            </el-tooltip>
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
              sale.data.user_id == $page.props.auth.user.id" content="Editar" placement="top">
              <Link :href="route('sales.edit', sale.data.id)">
              <button class="size-9 flex items-center justify-center rounded-lg bg-[#D9D9D9]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
              </button>
              </Link>
            </el-tooltip>

            <!-- ----------------------- botones para super admin starts------------------------ -->

            <el-popconfirm
              v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de venta') && !sale.data.authorized_at"
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
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center justify-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink v-if="$page.props.auth.user.permissions.includes(
                  'Crear ordenes de venta'
                )
                  " :href="route('sales.create')">
                  Crear nueva orden
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
      <h1 class="font-bold text-lg mb-4 flex items-center justify-center space-x-3">
        <el-tooltip v-if="sale.data.is_sale_production" content="Orden de venta" placement="top">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6 text-purple-500">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
          </svg>
        </el-tooltip>
        <el-tooltip v-else content="Orden de stock" placement="top">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6 text-rose-500">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
          </svg>
        </el-tooltip>
        <span>{{ sale.data.folio }}</span>
      </h1>

      <!-- Tabs -->
      <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClick">
        <el-tab-pane label="Datos de la orden" name="1">
          <General :sale="sale.data" />
        </el-tab-pane>
        <el-tab-pane label="Productos" name="2">
          <p class="text-secondary mb-2">Productos Ordenados</p>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-7">
            <ProductSaleCard is_view_for_seller v-for="productSale in sale.data.catalogProductCompanySales"
              :key="productSale.id" :catalog_product_company_sale="productSale" />
          </div>
        </el-tab-pane>
        <el-tab-pane v-if="sale.data?.catalogProductCompanySales?.some(item => item?.catalog_product_company?.catalog_product?.part_number.includes('EM'))"
                     label="Certificado de calidad" name="3">
          <a class="inline-block" :href="route('sales.quality-certificate', sale.id)" target="_blank">
            <p class="text-secondary underline mb-2 cursor-pointer">Ver certificado de calidad</p>
          </a>
        </el-tab-pane>
      </el-tabs>

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar Órden </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div class="">
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>

      <Modal :show="productionOrderModal" @close="productionOrderModal = false">
        <div class="py-3 px-5">
          <p class="text-secondary text-center">Crear órden de producción</p>

          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <PrimaryButton>Crear órden</PrimaryButton>
          </div>
        </div>
      </Modal>
    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import General from "./Tabs/General.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import RawMaterialCard from "@/Components/MyComponents/RawMaterialCard.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ProductSaleCard from "@/Components/MyComponents/ProductSaleCard.vue";
import axios from "axios";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      activeTab: '1',
      saleSelected: "",
      showConfirmModal: false,
      productionOrderModal: false,
    };
  },
  props: {
    sale: Object,
    sales: Array,
  },
  components: {
    AppLayoutNoHeader,
    ConfirmationModal,
    RawMaterialCard,
    ProductSaleCard,
    PrimaryButton,
    DropdownLink,
    CancelButton,
    Dropdown,
    General,
    Modal,
    Link,
  },
  methods: {
    openPrintPage() {
      const url = route('sales.print', this.sale.data.id);
      window.open(url, '_blank');
    },
    handleClick(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    getFileTypeIcon(fileName) {
      // Asocia extensiones de archivo a iconos
      const fileExtension = fileName.split('.').pop().toLowerCase();
      switch (fileExtension) {
        case 'pdf':
          return 'fa-regular fa-file-pdf text-red-700';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fa-regular fa-image text-blue-300';
        default:
          return 'fa-regular fa-file-lines';
      }
    },
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("sales.destroy", this.sale.dataid)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: "Orden eliminada",
            type: "success",
          });

          // const index = this.sales.data.findIndex(
          //   (item) => item.id === this.sale.dataid
          // );
          // if (index !== -1) {
          //   this.sales.data.splice(index, 1);
          //   this.saleSelected = "";
          // }
        } else {
          this.$notify({
            title: "Algo salió mal",
            message: response.data.message,
            type: "error",
          });
        }
      } catch (err) {
        this.$notify({
          title: "Algo salió mal",
          message: err.message,
          type: "error",
        });
        console.log(err);
      } finally {
        this.showConfirmModal = false;
        this.$inertia.get(route('sales.index'));
      }
    },
    // saleSelection() {
    //   this.currentSale = this.sales.data.find(
    //     (item) => item.id == this.saleSelected
    //   );
    // },
    async authorizeOrder() {
      try {
        const response = await axios.put(route("sales.authorize", this.sale.dataid));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Orden autorizada",
            type: "success",
          });

          locatoin.reload();
          //this.$inertia.get(route('sales.index'));
          // this.sale.data.authorized_at = response.data.item.authorized_at;
          // this.sale.data.status = response.data.item.status;
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
  },

  mounted() {
    this.saleSelected = this.sale.data.id;
    
    // Obtener la URL actual
    const currentURL = new URL(window.location.href);
    // Extraer el valor de 'currentTab' de los parámetros de búsqueda
    const currentTabFromURL = currentURL.searchParams.get('currentTab');

    if (currentTabFromURL) {
      this.activeTab = currentTabFromURL;
    }
  },
};
</script>