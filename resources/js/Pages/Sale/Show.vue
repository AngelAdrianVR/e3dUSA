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
              <el-option v-for="item in sales" :key="item.id" :label="item.folio" :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Imprimir" placement="top">
              <Link :href="route('sales.print', saleSelected)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-print text-sm"></i>
              </button>
              </Link>
            </el-tooltip>
            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
              sale.data.user.id == $page.props.auth.user.id" content="Editar" placement="top">
              <Link :href="route('sales.edit', saleSelected)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>

            <!-- ----------------------- botones para super admin starts------------------------ -->

            <el-popconfirm
              v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de venta') && sale.data.authorized_at == 'No autorizado'"
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
                  Crear nueva orden
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
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p v-if="sale.data.catalogProductCompanySales.some(item => item?.catalog_product_company?.catalog_product?.part_number.includes('EM'))" @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Certificado de calidad
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <p class="text-secondary col-span-2 mb-2">Logística</p>
          <span class="text-gray-500">Paquetería</span>
          <span>{{ sale.data.shipping_company }}</span>
          <span class="text-gray-500 my-1">Guía</span>
          <span>{{ sale.data.traking_guide }}</span>
          <span class="text-gray-500 my-1">Costo de envío</span>
          <span>$ {{ sale.data.freight_cost }}</span>
          <span v-if="sale.data.promise_date" class="text-gray-500 my-1">Fecha de entrega</span>
          <span v-if="sale.data.promise_date" class="text-red-600 bg-red-200 px-2 py-1">{{ sale.data.promise_date
          }}</span>
          <div v-if="sale.data.partialities" class="col-span-full">
            <article v-for="(item, index) in sale.data.partialities" :key="index" class="grid grid-cols-2">
              <span class="col-span-full font-bold my-2">Parcialidad {{ (index + 2) }}</span>
              <span class="text-gray-500">Paquetería</span>
              <span>{{ item.shipping_company }}</span>
              <span class="text-gray-500 my-1">Guía</span>
              <span>{{ item.traking_guide }}</span>
              <span class="text-gray-500 my-1">Costo de envío</span>
              <span>$ {{ item.freight_cost }}</span>
              <span v-if="item.promise_date" class="text-gray-500 my-1">Fecha de entrega</span>
              <span v-if="item.promise_date" class="text-red-600 bg-red-200 px-2 py-1">{{ dateFormat(item.promise_date)
              }}</span>
            </article>
          </div>

          <p class="text-secondary col-span-2 mb-2 mt-8">Datos de la órden</p>

          <span class="text-gray-500">ID</span>
          <span>{{ sale.data.id }}</span>
          <span class="text-gray-500 my-1">Solicitada por</span>
          <span>{{ sale.data.user.name }}</span>
          <span class="text-gray-500 my-1">Solicitada el</span>
          <span>{{ sale.data.created_at }}</span>
          <span class="text-gray-500 my-1">Medio de petición</span>
          <span>{{ sale.data.order_via }}</span>
          <span class="text-gray-500 my-1">Es prioridad alta</span>
          <span>
            <i v-if="currentSale?.is_high_priority" class="fa-solid fa-check text-red-500"></i>
            <i v-else class="fa-solid fa-minus"></i>
          </span>
          <span class="text-gray-500 my-1">OCE</span>
          <span>{{ sale.data.oce_name }}</span>
          <span class="text-gray-500 my-1">Factura</span>
          <span>{{ sale.data.invoice }}</span>
          <span class="text-gray-500 my-1">Estatus</span>
          <span :class="sale.data.status['text-color'] +
            ' ' +
            sale.data.status['border-color']
            " class="rounded-full border text-center">{{ sale.data.status["label"] }}</span>
          <span class="text-gray-500 my-1">Notas</span>
          <span>{{ sale.data.notes }}</span>
        </div>
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Datos del cliente</p>

          <span class="text-gray-500 mb-6">Razón solcial</span>
          <span class="mb-6">{{ sale.data.company_branch?.company?.business_name }}</span>
          <span class="text-gray-500">ID</span>
          <span>{{ sale.data.company_branch?.id }}</span>
          <span class="text-gray-500 my-1">Sucursal</span>
          <span>{{ sale.data.company_branch?.name }}</span>
          <span class="text-gray-500 my-1">Dirección</span>
          <span>{{ sale.data.company_branch?.address }}</span>
          <span class="text-gray-500 my-1">Código postal</span>
          <span>{{ sale.data.company_branch?.post_code }}</span>
          <span class="text-gray-500 my-1">Teléfono</span>
          <span>{{ sale.data.company_branch?.phone }}</span>

          <p class="text-secondary col-span-2 mt-7">Contacto</p>

          <span class="text-gray-500 my-1">Nombre</span>
          <span>{{ sale.data.contact?.name }}</span>
          <span class="text-gray-500 my-1">Correo(s) electrónico(s)</span>
          <span>{{ sale.data.contact?.email }}, {{ sale.data.contact?.additional_emails?.join(', ') }}</span>
          <span class="text-gray-500 my-1">telefono(s)</span>
          <span>{{ sale.data.contact?.phone }}, {{ sale.data.contact?.additional_phones?.join(', ') }}</span>
          <span class="text-gray-500 my-1">Cargo</span>
          <span>{{ sale.data.contact?.charge }}</span>
        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 products starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <p class="text-secondary mb-2">Productos Ordenados</p>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-7">
          <ProductSaleCard is_view_for_seller v-for="productSale in sale.data.catalogProductCompanySales"
            :key="productSale.id" :catalog_product_company_sale="productSale" />
        </div>
      </div>

      <!-- ------------- tab 2 products ends ------------ -->

      <!-- -------------tab 3 history starts ------------- -->

      <div v-if="tabs == 3" class="p-7">
        <a class="inline-block" :href="route('sales.quality-certificate', sale.data.id)" target="_blank">
          <p class="text-secondary underline mb-2 cursor-pointer">Ver certificado de calidad</p>
        </a>
      </div>

      <!-- ------------- tab 3 history ends ------------ -->

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
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
  data() {
    return {
      saleSelected: "",
      tabs: 1,
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
    dateFormat(date) {
      const formattedDate = format(new Date(date), 'dd MMMM yyyy', { locale: es });

      return formattedDate;
    },
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("sales.destroy", this.sale.data.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: "Orden eliminada",
            type: "success",
          });

          // const index = this.sales.data.findIndex(
          //   (item) => item.id === this.sale.data.id
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
        const response = await axios.put(route("sales.authorize", this.saleSelected));

        if (response.status === 200) {
          this.$notify({
            title: "Éxito",
            message: "Orden autorizada",
            type: "success",
          });

          //this.$inertia.get(route('sales.index'));
          this.sale.data.authorized_at = response.data.item.authorized_at;
          this.sale.data.status = response.data.item.status;
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

  // watch: {
  //   selectedSale(newVal) {
  //     this.currentSale = this.sales.data.find((item) => item.id == newVal);
  //     console.log(this);
  //   },
  // },

  mounted() {
    this.saleSelected = this.sale.data.id;
    // this.currentSale = this.sales.data.find(
    //   (item) => item.id == this.saleSelected
    // );
  },
};
</script>