<template>
  <div>
    <AppLayoutNoHeader title="Órdenes de venta">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de venta</label>
          <Link :href="route('sales.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>

        <div class="flex justify-between">
          <div class="md:w-1/3 mr-2">
            <el-select @change="saleSelection" v-model="saleSelected" clearable filterable
              placeholder="Buscar órden de venta" no-data-text="No hay órdenes en el catálogo"
              no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in sales.data" :key="item.id" :label="item.folio" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
                       currentSale?.user.id == $page.props.auth.user.id" content="Editar" placement="top">
              <Link :href="route('sales.edit', saleSelected)">
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
                <!-- <DropdownLink @click="productionOrderModal = true" as="button">
                  Crear orden de producción
                </DropdownLink> -->
                <!-- <DropdownLink :href="route('sales.create')">
                  Certificado de calidad
                </DropdownLink>
                <DropdownLink :href="route('sales.create')">
                  Formato órden de ventas
                </DropdownLink>
                <DropdownLink :href="route('sales.create')">
                  Paquetes
                </DropdownLink> -->
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
      <p class="text-center font-bold text-lg mb-4">
        {{ currentSale?.folio }}
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Datos de la órden
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Productos
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <p class="text-secondary col-span-2 mb-2">Logística</p>

          <span class="text-gray-500">Paquetería</span>
          <span>{{ currentSale?.shipping_company }}</span>
          <span class="text-gray-500 my-2">Guía</span>
          <span>{{ currentSale?.traking_guide }}</span>
          <span class="text-gray-500 my-2">Costo de envío</span>
          <span>{{ currentSale?.freight_cost }}</span>

          <p class="text-secondary col-span-2 mb-2 mt-8">Datos de la órden</p>

          <span class="text-gray-500">ID</span>
          <span>{{ currentSale?.id }}</span>
          <span class="text-gray-500 my-2">Solicitada por</span>
          <span>{{ currentSale?.user.name }}</span>
          <span class="text-gray-500 my-2">Solicitada el</span>
          <span>{{ currentSale?.created_at }}</span>
          <span class="text-gray-500 my-2">Medio de petición</span>
          <span>{{ currentSale?.order_via }}</span>
          <!-- <span class="text-gray-500 my-2">Prioridad</span>
          <span>{{ "currentSale?.status" }}</span>
          <span class="text-gray-500 my-2">Operadores</span>
          <span>{{ "currentSale?.authorized_at" }}</span> -->
          <span class="text-gray-500 my-2">OCE</span>
          <span>{{ currentSale?.oce_name }}</span>
          <span class="text-gray-500 my-2">Factura</span>
          <span>{{ currentSale?.invoice }}</span>
          <span class="text-gray-500 my-2">Estatus</span>
          <span :class="currentSale?.status['text-color'] +
            ' ' +
            currentSale?.status['border-color']
            " class="rounded-full border text-center">{{ currentSale?.status["label"] }}</span>
          <span class="text-gray-500 my-2">Notas</span>
          <span>{{ currentSale?.notes }}</span>
        </div>
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Datos del cliente</p>

          <span class="text-gray-500 mb-6">Razón solcial</span>
          <span class="mb-6">{{ currentSale?.company_branch?.company?.business_name }}</span>
          <span class="text-gray-500">ID</span>
          <span>{{ currentSale?.company_branch?.id }}</span>
          <span class="text-gray-500 my-2">Sucursal</span>
          <span>{{ currentSale?.company_branch?.name }}</span>
          <span class="text-gray-500 my-2">Dirección</span>
          <span>{{ currentSale?.company_branch?.address }}</span>
          <span class="text-gray-500 my-2">Código postal</span>
          <span>{{ currentSale?.company_branch?.post_code }}</span>
          <span class="text-gray-500 my-2">Teléfono</span>
          <span>{{ currentSale?.company_branch?.phone }}</span>

          <p class="text-secondary col-span-2 mt-7">Contacto</p>

          <span class="text-gray-500 my-2">Nombre</span>
          <span>{{ currentSale?.contact?.name }}</span>
          <span class="text-gray-500 my-2">Correo electrónico</span>
          <span>{{ currentSale?.contact?.email }}</span>
          <span class="text-gray-500 my-2">telefono</span>
          <span>{{ currentSale?.contact?.phone }}</span>
          <span class="text-gray-500 my-2">Cargo</span>
          <span>{{ currentSale?.contact?.charge }}</span>
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 products starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <p class="text-secondary mb-2">Productos Ordenados</p>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-7">
          <ProductSaleCard is_view_for_seller
            v-for="productSale in currentSale?.catalogProductCompanySales" :key="productSale.id"
            :catalog_product_company_sale="productSale" />
        </div>
      </div>

      <!-- ------------- tab 2 products ends ------------ -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar Órden de venta </template>
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
            <!-- <CancelButton @click="productionOrderModal = false" class="mr-2">Cancelar</CancelButton> -->
          </div>
        </div>
      </Modal>
    </AppLayoutNoHeader>
  </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import RawMaterialCard from "@/Components/MyComponents/RawMaterialCard.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ProductSaleCard from "@/Components/MyComponents/ProductSaleCard.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

export default {
  data() {
    return {
      saleSelected: "",
      currentSale: null,
      tabs: 1,
      showConfirmModal: false,
      productionOrderModal: false,
    };
  },
  props: {
    sale: Object,
    sales: Object,
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    Link,
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    RawMaterialCard,
    Modal,
    ProductSaleCard
  },
  methods: {
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("sales.destroy", this.currentSale?.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.sales.data.findIndex(
            (item) => item.id === this.currentSale.id
          );
          if (index !== -1) {
            this.sales.data.splice(index, 1);
            this.saleSelected = "";
          }
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
      }
    },
    saleSelection() {
      this.currentSale = this.sales.data.find(
        (item) => item.id == this.saleSelected
      );
    },
    async authorizeOrder() {
      try {
        const response = await axios.put(route("sales.authorize", this.saleSelected));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Orden de venta autorizada",
            type: "success",
          });

          this.currentSale.authorized_at = response.data.item.authorized_at;
          this.currentSale.status = response.data.item.status;
        }
      } catch (error) {
        this.$notify({
          title: "Error",
          message: error.message,
          type: "error",
        });
      }
    },
    productionOrder() {

    },
  },

  // watch: {
  //   selectedSale(newVal) {
  //     this.currentSale = this.sales.data.find((item) => item.id == newVal);
  //     console.log(this);
  //   },
  // },

  mounted() {
    this.saleSelected = this.sale.id;
    this.currentSale = this.sales.data.find(
      (item) => item.id == this.saleSelected
    );
  },
};
</script>