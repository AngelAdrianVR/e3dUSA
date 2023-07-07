<template>
  <div>
    <AppLayoutNoHeader title="Órdenes de compra">
      <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Órdenes de compra</label>
          <Link
            :href="route('purchases.index')"
            class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center"
          >
            <i class="fa-solid fa-xmark"></i>
          </Link>
        </div>
        <div class="flex justify-between">
          <el-select
            v-model="selectedPurchase"
            clearable
            filterable
            placeholder="Buscar órden de compra"
            no-data-text="No hay órdenes en el catálogo"
            no-match-text="No se encontraron coincidencias"
          >
            <el-option
              v-for="item in purchases.data"
              :key="item.id"
              :label="item.id + '-' + item.created_at"
              :value="item.id"
            />
          </el-select>
          <div class="flex items-center space-x-2">
            <el-tooltip content="Editar" placement="top">
              <Link :href="route('purchases.edit', selectedPurchase)">
                <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]">
                  <i class="fa-solid fa-pen text-sm"></i>
                </button>
              </Link>
            </el-tooltip>

            <Dropdown align="right" width="48">
              <template #trigger>
                <button
                  class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm"
                >
                  Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('purchases.create')">
                  Crear nueva órden
                </DropdownLink>
                <DropdownLink v-if="!currentPurchase?.emited_at" @click="$inertia.put(route('purchases.done', currentPurchase?.id))">
                  Marcar como órden pedida
                </DropdownLink>
                <DropdownLink v-else @click="$inertia.put(route('purchases.recieved', currentPurchase?.id))">
                  Marcar como órden recibida
                </DropdownLink>
                <DropdownLink @click="showConfirmModal = true" as="button">
                  Eliminar
                </DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
      <!-- <p class="text-center font-bold text-lg mb-4">
        {{ supplier.name }}
      </p> -->

      <!-- ------------- tabs section starts ------------- -->
      <div
        class="border-y-2 border-[#cccccc] flex justify-between items-center py-2 "
      >
        <div class="flex">
          <p
            @click="tabs = 1"
            :class="
              tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base"
          >
            Datos de la órden
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="tabs = 2"
            :class="
              tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base"
          >
            Productos
          </p>
        </div>
      </div>
      <!-- ------------- tabs section ends ------------- -->

      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc]">
        <div
            class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center"
        >
            <span class="text-gray-500">ID</span>
            <span>{{ currentPurchase?.id }}</span>
            <span class="text-gray-500 my-2">Creado por</span>
            <span>{{ currentPurchase?.user.name }}</span>
            <span class="text-gray-500 my-2">Proveedor</span>
            <span>{{ currentPurchase?.supplier.name }}</span>
            <span class="text-gray-500 my-2">Creado el</span>
            <span>{{ currentPurchase?.created_at }}</span>
            <span class="text-gray-500 my-2">Estatus</span>
            <span :class="currentPurchase?.status == 'Pendiente' ? 'text-amber-600' : 'text-green-600'">{{ currentPurchase?.status }}</span>
            <span class="text-gray-500 my-2">Autorizado el</span>
            <span>{{ currentPurchase?.authorized_at }}</span>
            <span class="text-gray-500 my-2">Autorizado por</span>
            <span>{{ currentPurchase?.authorized_user_name }}</span>
            <span class="text-gray-500 my-2">Fecha de entrega esperada</span>
            <span>{{ currentPurchase?.expected_delivery_date }}</span>
            <span class="text-gray-500 my-2">Fecha de realización de pedido</span>
            <span>{{ currentPurchase?.emited_at }}</span>
            <span class="text-gray-500 my-2">Fecha de recibido</span>
            <span>{{ currentPurchase?.recieved_at }}</span>
            <span class="text-gray-500 my-2">Notas</span>
            <span>{{ currentPurchase?.notes }}</span>
        </div>
        <div
            class="grid grid-cols-2 text-left p-4 md:ml-10 items-center"
        >
            <p class="text-secondary col-span-2 mb-2">Datos del proveedor</p>

            <span class="text-gray-500">ID</span>
            <span>{{ currentPurchase?.supplier.id }}</span>
            <span class="text-gray-500 my-2">Nombre</span>
            <span>{{ currentPurchase?.supplier.name }}</span>
            <span class="text-gray-500 my-2">Dirección</span>
            <span>{{ currentPurchase?.supplier.address }}</span>
            <span class="text-gray-500 my-2">Código postal</span>
            <span>{{ currentPurchase?.supplier.post_code }}</span>
            <span class="text-gray-500 my-2">Teléfono</span>
            <span>{{ currentPurchase?.supplier.phone }}</span>

            <p class="text-secondary col-span-2 mt-7">Datos Bancarios</p>

            <span class="text-gray-500 my-2">Nombre del beneficiario</span>
            <span>{{ currentPurchase?.bank_information.beneficiary_name }}</span>
            <span class="text-gray-500 my-2">Número de cuenta</span>
            <span>{{ currentPurchase?.bank_information.accountNumber }}</span>
            <span class="text-gray-500 my-2">Clabe</span>
            <span>{{ currentPurchase?.bank_information.clabe }}</span>
            <span class="text-gray-500 my-2">Banco</span>
            <span>{{ currentPurchase?.bank_information.bank_name }}</span>

        </div>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->

      <!-- -------------tab 2 products starts ------------- -->

      <div v-if="tabs == 2" class="p-7">
        <p class="text-secondary">Productos Ordenados</p>
        <div class="grid lg:grid-cols-3 md:grid-cols-2 mt-7 gap-10">
          <RawMaterialCard
            v-for="product in currentPurchase?.products"
            :key="product.id"
            :raw_material="product"
          />
        </div>
      </div>

      <!-- ------------- tab 2 products ends ------------ -->

      <ConfirmationModal
        :show="showConfirmModal"
        @close="showConfirmModal = false"
      >
        <template #title> Eliminar Órden de compra </template>
        <template #content> Continuar con la eliminación? </template>
        <template #footer>
          <div class="">
            <CancelButton @click="showConfirmModal = false" class="mr-2"
              >Cancelar</CancelButton
            >
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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      selectedPurchase: "",
      currentPurchase: null,
      tabs: 1,
      showConfirmModal: false,
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
    Link,
    ConfirmationModal,
    CancelButton,
    PrimaryButton,
    RawMaterialCard
  },
  methods: {
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("purchases.destroy", this.currentPurchase?.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          const index = this.purchases.data.findIndex(
            (item) => item.id === this.currentPurchase.id
          );
          if (index !== -1) {
            this.purchases.data.splice(index, 1);
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
        this.showConfirmModal = false;
      }
    },
  },

  watch: {
    selectedPurchase(newVal) {
      this.currentPurchase = this.purchases.data.find((item) => item.id == newVal);
    },
  },

  mounted() {
    this.selectedPurchase = this.purchase.id;
  },
};
</script>