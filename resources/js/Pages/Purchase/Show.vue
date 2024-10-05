<template>
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
          <el-select @change="$inertia.get(route('purchases.show', selectedPurchase))" v-model="selectedPurchase"
            clearable filterable placeholder="Buscar órden de compra" no-data-text="No hay órdenes en el catálogo"
            no-match-text="No se encontraron coincidencias">
            <el-option v-for="item in purchases" :key="item.id" :label="item.folio" :value="item.id" />
          </el-select>
        </div>
        <div class="flex items-center space-x-2">

          <button @click="showTemplate()" class="rounded-lg bg-primary text-white p-2 text-sm">
            Imprimir
          </button>

          <button
            v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de compra') && purchase.data.status === 'Pendiente'"
            @click="authorize" class="rounded-lg bg-primary text-white p-2 text-sm">
            Autorizar
          </button>

          <el-tooltip
            content="Una vez realizada la compra marcar como compra realizada para cambiar estatus y dar seguimiento"
            placement="top">
            <button v-if="purchase.data.status === 'Autorizado'"
              @click="$inertia.put(route('purchases.done', purchase.data.id))"
              class="rounded-lg bg-primary text-white p-2 text-sm">
              Compra realizada
            </button>
          </el-tooltip>

          <el-tooltip content="Se indica al sistema que el producto o servicio ya fue recibido" placement="top">
            <button v-if="purchase.data.status === 'Emitido'" @click="showRatingModal = true"
              class="rounded-lg bg-green-500 text-white p-2 text-sm">
              Marcar como recibido
            </button>
          </el-tooltip>

          <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar ordenes de compra') &&
            purchase.data.user.id == $page.props.auth.user.id" content="Editar" placement="top">
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
          : purchase.data.status == 'Emitido' ? 'text-blue-600 bg-blue-200' : 'text-green-600 bg-green-200'">{{
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
        <span class="text-gray-500 my-2">Moneda</span>
        <span>{{ purchase.data.currency ?? '-' }}</span>
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
        <span>{{ purchase.data.supplier.banks[purchase.data.bank_information]?.beneficiary_name ?? '--' }}</span>
        <span class="text-gray-500 my-2">Número de cuenta</span>
        <span>{{ purchase.data.supplier.banks[purchase.data.bank_information]?.accountNumber ?? '--' }}</span>
        <span class="text-gray-500 my-2">Clabe</span>
        <span>{{ purchase.data.supplier.banks[purchase.data.bank_information]?.clabe ?? '--' }}</span>
        <span class="text-gray-500 my-2">Banco</span>
        <span>{{ purchase.data.supplier.banks[purchase.data.bank_information]?.bank_name ?? '--' }}</span>

      </div>
    </div>
    <!-- ------------- Informacion general ends 1 ------------- -->

    <!-- -------------tab 2 products starts ------------- -->

    <div v-if="tabs == 2" class="p-7">
      <div v-if="!loading">
        <div v-if="rawMaterials?.length > 0" class="lg:grid grid-cols-4 mt-7 gap-5">
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

    <DialogModal :show="showRatingModal" @close="showRatingModal = false" maxWidth="3xl">
      <template #title>
        <h1 class="flex items-center justify-between font-bold mt-3">
          <p>Evaluación de proveedor</p>
          <p>REG-CO-07</p>
        </h1>
      </template>
      <template #content>
        <p class="text-[#999999]">Por favor, completa la siguiente evaluación del proveedor.</p>
        <form @submit.prevent="storeRating" class="mt-5">
          <section>
            <h2 class="text-[#373737] font-bold mb-2">¿Cumplió con el tiempo de entrega?</h2>
            <div class="flex items-center space-x-2 mx-3">
              <input type="radio" id="1.1" value="Si" v-model="ratingForm.q1"
                class="bg-transparent text-primary focus:ring-0" />
              <label for="1.1">Si</label>
            </div>
            <div class="flex items-center space-x-2 mx-3">
              <input type="radio" id="1.2" :value="`No, hubo un retraso de ${ratingForm.q1_days} días(s)`" v-model="ratingForm.q1"
                class="bg-transparent text-primary focus:ring-0" />
              <label for="1.2">
                No, hubo un retraso de
                <el-input @change="handleChangeLateDays" v-model="ratingForm.q1_days" type="text" class="!w-10"
                  :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  :parser="(value) => value.replace(/[^\d.]/g, '')">
                </el-input>
                día(s)
              </label>
            </div>
          </section>
          <section class="mt-3">
            <h2 class="text-[#373737] font-bold mb-2">¿Las características solicitadas de los productos o servicio
              fueron cubiertos?</h2>
            <div class="flex items-center space-x-2 mx-3">
              <input type="radio" id="2.1" value="Sí, cumplió con todo" v-model="ratingForm.q2"
                class="bg-transparent text-primary focus:ring-0" />
              <label for="2.1">Sí, cumplió con todo</label>
            </div>
            <div class="flex items-center space-x-2 mx-3">
              <input type="radio" id="2.2" value="No, no se cumplieron las especificaciones" v-model="ratingForm.q2"
                class="bg-transparent text-primary focus:ring-0" />
              <label for="2.2">
                No, no se cumplieron las especificaciones
              </label>
            </div>
          </section>
          <section class="mt-3">
            <h2 class="text-[#373737] font-bold mb-2">¿Cumplió con el apoyo técnico ofrecido?</h2>
            <div>
              <InputLabel value="Tipo de soporte brindado" />
              <el-select @change="handleQ31Change" v-model="ratingForm.q3_1"
                no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias"
                filterable placeholder="Seleccionar" class="!w-1/2">
                <el-option v-for="item in a3_1" :key="item" :label="item" :value="item" />
              </el-select>
            </div>
            <div v-if="ratingForm.q3_1 != 'No se requirió soporte'" class="mt-2">
              <InputLabel value="Tiempo de atención al soporte" />
              <el-select v-model="ratingForm.q3_2"
                no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias"
                filterable placeholder="Seleccionar" class="!w-1/2">
                <el-option v-for="item in a3_2" :key="item" :label="item" :value="item" />
              </el-select>
            </div>
          </section>
          <section class="mt-3">
            <h2 class="text-[#373737] font-bold mb-2">Ante alguna urgencia, ¿se ofreció apoyo en la entrega?</h2>
            <div>
              <InputLabel value="Días de atraso en la urgencia" />
              <el-select v-model="ratingForm.q4"
                no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias"
                filterable placeholder="Seleccionar" class="!w-1/2">
                <el-option v-for="item in a4" :key="item" :label="item" :value="item" />
              </el-select>
            </div>
          </section>
          <section class="mt-3">
            <h2 class="text-[#373737] font-bold mb-2">¿Hubo alguna incidencia?</h2>
            <div>
              <InputLabel value="Avisos de rechazo" />
              <el-select v-model="ratingForm.q5"
                no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias"
                filterable placeholder="Seleccionar" class="!w-1/2">
                <el-option v-for="item in a5" :key="item" :label="item" :value="item" />
              </el-select>
            </div>
          </section>
        </form>
      </template>
      <template #footer>
        <PrimaryButton @click="storeRating" :disabled="ratingForm.processing">Continuar</PrimaryButton>
      </template>
    </DialogModal>
  </AppLayoutNoHeader>
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
import { Link, useForm } from "@inertiajs/vue3";
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";

export default {
  data() {
    const ratingForm = useForm({
      q1: 'Si',
      q1_days: 1,
      q2: 'Sí, cumplió con todo',
      q3_1: 'No se requirió soporte',
      q3_2: null,
      q4: 'No se presentó ninguna urgencia',
      q5: '0 avisos de rechazo',
    });

    return {
      ratingForm,
      selectedPurchase: "",
      currentPurchase: null,
      tabs: 1,
      showConfirmModal: false,
      loading: false,
      showRatingModal: false,
      rawMaterials: [],
      // respuestas de seleccion
      a3_1: [
        'No se requirió soporte',
        'Soporte por defecto del producto',
        'Soporte de acuerdo al desarrollo del material',
      ],
      a3_2: [
        'Atención inmediata',
        'Atención tardía (2 o más días para atender)',
        'No brindó soporte',
      ],
      a4: [
        'No se presentó ninguna urgencia',
        '1 día de atraso',
        '2 a 3 días de atraso',
        '4 a 5 días de atraso',
        'Más de 6 días de atraso',
      ],
      a5: [
        '0 avisos de rechazo',
        '1 aviso de rechazo',
        '2 o más avisos de rechazo',
      ],
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
    DialogModal,
    CancelButton,
    PrimaryButton,
    RawMaterialCard,
    SupplierProductCard,
    Link,
    InputLabel,
  },
  methods: {
    handleQ31Change() {
      if (this.ratingForm.q3_1 == 'No se requirió soporte') {
        this.ratingForm.q3_2 = null;
      }
    },
    handleChangeLateDays() {
      if (this.ratingForm.q1 != 'Si' && this.ratingForm.q1 != 'No, el pedido fue cancelado') {
        this.ratingForm.q1 = `No, hubo un retraso de ${this.ratingForm.q1_days} días(s)`;
      }
    },
    storeRating() {
      this.ratingForm.put(route('purchases.store-rating', this.purchase.data.id), {
        onSuccess: () => {
          this.$notify({
            title: "Evaluación registrada",
            type: "success",
          });

          this.showRatingModal = false;
          // this.selectedPurchase.recieved_at = new Date().toISOString();
        },
        onerror: error => {
          console.log(error)
        },
      });
    },
    showTemplate() {
      const url = route('purchases.show-template', {
        purchase_id: this.purchase.data.id,
      });

      window.open(url, '_blank');
    },
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
      if (this.rawMaterials.length <= 0) { //solo hace la peticion si no se han cargado
        this.loading = true;
        try {
          const response = await axios.get(route('raw-materials.fetch-supplier-items', {
            raw_materials_ids: this.purchase.data.products.map(item => item.id).join(',')
          }));

          if (response.status === 200) {
            this.rawMaterials = response.data.items;
            //Agrega a los productos la cantidad comprada
            this.rawMaterials = this.rawMaterials.map((item, index) => {
              return {
                ...item,
                quantity: this.purchase.data.products[index].quantity,
                currency: this.purchase.data.currency,
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