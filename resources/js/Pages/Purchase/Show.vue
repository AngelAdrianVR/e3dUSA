<template>
  <div>
    <AppLayoutNoHeader title="Órdenes de compra">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de compra</label>
          <Link :href="route('purchases.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
          <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <div class="md:w-1/3">
            <el-select @change="$inertia.get(route('purchases.show', selectedPurchase))" v-model="selectedPurchase" clearable filterable placeholder="Buscar órden de compra"
              no-data-text="No hay órdenes en el catálogo" no-match-text="No se encontraron coincidencias">
              <el-option v-for="item in purchases" :key="item.id" :label="item.folio"
                :value="item.id" />
            </el-select>
          </div>
          <div class="flex items-center space-x-2">

            <button
              v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de compra') && purchase.data.status === 'Pendiente'"
              @click="authorize"
              class="rounded-lg bg-primary text-white p-2 text-sm"
            >
              Autorizar
            </button>

            <el-tooltip
              content="Una vez realizada la compra marcar como compra realizada para cambiar estatus y dar seguimiento"
              placement="top"
            >
            <button
              v-if="purchase.data.status === 'Autorizado'"
              @click="$inertia.put(route('purchases.done', purchase.data.id))"
              class="rounded-lg bg-primary text-white p-2 text-sm"
            >
              Compra realizada
            </button>
            </el-tooltip>

            <el-tooltip
              content="Una vez realizada la compra marcar como compra realizada para cambiar estatus y dar seguimiento"
              placement="top"
            >
            <button
              v-if="purchase.data.status === 'Emitido'"
              @click="$inertia.put(route('purchases.recieved', purchase.data.id))"
              class="rounded-lg bg-green-500 text-white p-2 text-sm"
            >
              Marcar como recibido
            </button>
            </el-tooltip>

            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar ordenes de compra') &&
                       purchase.data.user.id == $page.props.auth.user.id" content="Editar"
              placement="top">
              <Link :href="route('purchases.edit', selectedPurchase)">
              <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                <i class="fa-solid fa-pen text-sm"></i>
              </button>
              </Link>
            </el-tooltip>

            <Dropdown align="right" width="48"
              v-if="$page.props.auth.user.permissions.includes('Crear ordenes de compra') && $page.props.auth.user.permissions.includes('Eliminar ordenes de compra')">
              <template #trigger>
                <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('purchases.create')"
                  v-if="$page.props.auth.user.permissions.includes('Crear ordenes de compra')">
                  Crear nueva órden
                </DropdownLink>
                <DropdownLink @click="showConfirmModal = true" as="button"
                  v-if="$page.props.auth.user.permissions.includes('Eliminar ordenes de compra')">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">
        {{ purchase.data.supplier.name }} - 
        <span class="py-1 p-2" :class="purchase.data.status == 'Pendiente' ? 'text-red-600 bg-red-200' 
        : purchase.data.status == 'Autorizado' ? 'text-yellow-600 bg-yellow-200' 
        : purchase.data.status == 'Emitido' ? 'text-blue-600 bg-blue-200' : 'text-green-600 bg-green-200' ">{{
            purchase.data.status }}</span>
      </p>

      <!-- ------------- tabs section starts ------------- -->
      <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 ">
        <div class="flex">
          <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
            Datos de la órden
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p @click="fetchSupplierItems(); tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            " class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
            Productos
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
          <span class="text-gray-500">ID</span>
          <span>{{ purchase.data.id }}</span>
          <span class="text-gray-500 my-2">Creado por</span>
          <span>{{ purchase.data.user.name }}</span>
          <span class="text-gray-500 my-2">Proveedor</span>
          <span>{{ purchase.data.supplier.name }}</span>
          <span class="text-gray-500 my-2">Creado el</span>
          <span>{{ purchase.data.created_at }}</span>
          <!-- <span class="text-gray-500 my-2">Estatus</span>
          <span class="py-1 p-2" :class="purchase.data.status == 'Pendiente' ? 'text-amber-600 bg-amber-200' : 'text-green-600 bg-green-200'">{{
            purchase.data.status }}</span> -->
          <span class="text-gray-500 my-2">Autorizado el</span>
          <span>{{ purchase.data.authorized_at }}</span>
          <span class="text-gray-500 my-2">Autorizado por</span>
          <span>{{ purchase.data.authorized_user_name }}</span>
          <span class="text-gray-500 my-2">Fecha de entrega esperada</span>
          <span>{{ purchase.data.expected_delivery_date }}</span>
          <span class="text-gray-500 my-2">Fecha de realización de pedido</span>
          <span>{{ purchase.data.emited_at }}</span>
          <span class="text-gray-500 my-2">Fecha de recibido</span>
          <span>{{ purchase.data.recieved_at }}</span>
          <span class="text-gray-500 my-2">Notas</span>
          <span>{{ purchase.data.notes }}</span>
        </div>
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
          <p class="text-secondary col-span-2 mb-2">Datos del proveedor</p>

          <span class="text-gray-500">ID</span>
          <span>{{ purchase.data.supplier.id }}</span>
          <span class="text-gray-500 my-2">Nombre</span>
          <span>{{ purchase.data.supplier.name }}</span>
          <span class="text-gray-500 my-2">Dirección</span>
          <span>{{ purchase.data.supplier.address }}</span>
          <span class="text-gray-500 my-2">Código postal</span>
          <span>{{ purchase.data.supplier.post_code }}</span>
          <span class="text-gray-500 my-2">Teléfono</span>
          <span>{{ purchase.data.supplier.phone }}</span>

          <p class="text-secondary col-span-2 mt-7">Datos Bancarios</p>

          <span class="text-gray-500 my-2">Nombre del beneficiario</span>
          <span>{{ purchase.data.bank_information.beneficiary_name }}</span>
          <span class="text-gray-500 my-2">Número de cuenta</span>
          <span>{{ purchase.data.bank_information.accountNumber }}</span>
          <span class="text-gray-500 my-2">Clabe</span>
          <span>{{ purchase.data.bank_information.clabe }}</span>
          <span class="text-gray-500 my-2">Banco</span>
          <span>{{ purchase.data.bank_information.bank_name }}</span>

        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 products starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <div v-if="!loading">
          <div v-if="rawMaterials?.length > 0" class="lg:grid grid-cols-2 mt-7 gap-5">
            <SupplierProductCard v-for="product in rawMaterials" :key="product" :product="product" />
          </div>
            <p v-else class="text-gray-500 text-center text-sm">No hay productos registrados a este proveedor</p>
        </div>
        <div v-else class="flex justify-center items-center pt-10">
          <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
        </div>
      </div>

      <!-- ------------- tab 2 products ends ------------ -->

      <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
        <template #title> Eliminar Órden de compra </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div>
            <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
            <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
          </div>
        </template>
      </ConfirmationModal>
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
import SupplierProductCard from "@/Components/MyComponents/SupplierProductCard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedPurchase: "",
      currentPurchase: null,
      tabs: 1,
      showConfirmModal: false,
      loading: false,
      rawMaterials: [],
    };
  },
  props: {
    purchase: Object,
    purchases: Array,
  },
  components: {
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    RawMaterialCard,
    SupplierProductCard,
    Link,
  },
  methods: {
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("purchases.destroy", this.purchase.data.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.purchases.data.findIndex(
            (item) => item.id === this.purchase.data.id
          );
          if (index !== -1) {
            this.purchases.splice(index, 1);
            this.selectedPurchase = "";
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
        this.$inertia.get(route('purchases.index'));
        this.showConfirmModal = false;
      }
    },
    async fetchSupplierItems() {
      if ( this.rawMaterials.length <= 0) { //solo hace la peticion si no se han cargado
          this.loading = true;
            try {
              const response = await axios.get(route('raw-materials.fetch-supplier-items', {
                raw_materials_ids: this.purchase.data.products.map(item => item.id).join(',')
              }));
              
              if (response.status === 200) {
                console.log(response.data.items);
                  this.rawMaterials = response.data.items;
                  //Agrega a los productos la cantidad comprada
                  this.rawMaterials = this.rawMaterials.map((item, index) => {
                    return {
                      ...item,
                      quantity: this.purchase.data.products[index].quantity,
                    };
                  });
              }
          } catch (error) {
            console.log(error);
          } finally {
            this.loading = false;
          }
        }
    },
    async authorize() {
        try {
            const response = await axios.put(route('purchases.authorize', this.purchase.data.id));

            if (response.status === 200) {

              this.$notify({
                title: 'Éxito',
                    message: response.data.message,
                    type: 'success'
                });

                // window.location.reload();
                this.$inertia.get(route('purchases.index'));

            } else {
                this.$notify({
                    title: 'Algo salió mal',
                    message: response.data.message,
                    type: 'error'
                });
            }
        } catch (err) {
            this.$notify({
                title: 'Algo salió mal',
                message: err.message,
                type: 'error'
            });
            console.log(err);
        }
    },
  },

  mounted() {
    this.selectedPurchase = this.purchase.data.id;
  },
};
</script>