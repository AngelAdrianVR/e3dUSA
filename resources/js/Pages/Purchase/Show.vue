<template>
  <AppLayoutNoHeader title="Órdenes de compra">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1 dark:text-white">
      <div class="flex justify-between">
        <label class="text-lg">Órdenes de compra</label>
        <Link :href="route('purchases.index')"
          class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
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
            @click="authorize" class="rounded-lg bg-primary text-white p-2 text-sm" :disabled="authorizing">
            <i v-if="authorizing" class="fa-solid fa-circle-notch fa-spin mr-1"></i>
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
            <button class="w-9 h-9 rounded-lg bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
              <i class="fa-solid fa-pen text-sm"></i>
            </button>
            </Link>
          </el-tooltip>
          <Dropdown align="right" width="48"
            v-if="$page.props.auth.user.permissions.includes('Crear ordenes de compra') && $page.props.auth.user.permissions.includes('Eliminar ordenes de compra')">
            <template #trigger>
              <button
                class="h-9 px-3 rounded-lg bg-[#D9D9D9] dark:bg-[#202020] dark:text-white flex items-center text-sm">
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
    <el-steps :active="getCurrentStep" finish-status="success" class="w-2/3 mx-auto">
      <el-step title="Autorizado.Compra no realizada" />
      <el-step title="Compra realizada" />
      <el-step title="Producto/servicio recibido" />
    </el-steps>
    <p class="justify-center flex items-center space-x-3 font-bold text-lg dark:text-white mb-4 mt-5">
      <el-tooltip v-if="purchase.data.is_for_production" content="Para producción" placement="top">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="size-6 text-purple-500">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
        </svg>
      </el-tooltip>
      <el-tooltip v-else content="Para muestras" placement="top">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="size-6 text-rose-500">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
        </svg>
      </el-tooltip>
      <span>{{ purchase.data.supplier.name }}</span>
    </p>
    <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
      <el-tab-pane label="Datos de la órden" name="1">
        <General :purchase="purchase.data" />
      </el-tab-pane>
      <el-tab-pane label="Productos" name="2">
        <Products :purchase="purchase.data" />
      </el-tab-pane>
    </el-tabs>
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
        <h1 class="font-bold">
          Recepción de compra
        </h1>
      </template>
      <template #content>
        <div class="grid grid-cols-2 gap-3 mt-3">
          <div>
            <InputLabel value="Paqueteria que llevó la mercancía" />
            <el-select v-model="ratingForm.carrier" no-data-text="No hay opciones por mostrar"
              no-match-text="No se encontraron coincidencias" filterable placeholder="Seleccionar">
              <el-option v-for="item in carriers" :key="item" :label="item" :value="item" />
            </el-select>
          </div>
          <div>
            <InputLabel value="Folio de factura" />
            <el-input v-model="ratingForm.invoice_folio" type="text">
            </el-input>
          </div>
        </div>
        <h1 class="flex items-center justify-between font-bold text-lg mt-6">
          <p>Evaluación de proveedor</p>
          <p>REG-CO-07</p>
        </h1>
        <p class="text-[#999999]">Por favor, completa la siguiente evaluación del proveedor.</p>
        <form @submit.prevent="storeRating" class="mt-5">
          <section>
            <h2 class="text-[#373737] dark:text-gray-500 font-bold mb-2">¿Cumplió con el tiempo de entrega?</h2>
            <div class="flex items-center space-x-2 mx-3">
              <input type="radio" id="1.1" value="Si" v-model="ratingForm.q1"
                class="bg-transparent text-primary focus:ring-0" />
              <label for="1.1">Si</label>
            </div>
            <div class="flex items-center space-x-2 mx-3">
              <input type="radio" id="1.2" :value="`No, hubo un retraso de ${ratingForm.q1_days} días(s)`"
                v-model="ratingForm.q1" class="bg-transparent text-primary focus:ring-0" />
              <label for="1.2">
                No, hubo un retraso de
                <el-input @change="handleChangeLateDays" @click="handleInputClick" v-model="ratingForm.q1_days"
                  type="text" class="!w-10" :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                  :parser="(value) => value.replace(/[^\d.]/g, '')">
                </el-input>
                día(s)
              </label>
            </div>
          </section>
          <section class="mt-3">
            <h2 class="text-[#373737] dark:text-gray-500 font-bold mb-2">¿Las características solicitadas de los
              productos o servicio
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
            <h2 class="text-[#373737] dark:text-gray-500 font-bold mb-2">¿Cumplió con el apoyo técnico ofrecido?</h2>
            <div>
              <InputLabel value="Tipo de soporte brindado" />
              <el-select @change="handleQ31Change" v-model="ratingForm.q3_1" no-data-text="No hay opciones por mostrar"
                no-match-text="No se encontraron coincidencias" filterable placeholder="Seleccionar" class="!w-1/2">
                <el-option v-for="item in a3_1" :key="item" :label="item" :value="item" />
              </el-select>
            </div>
            <div v-if="ratingForm.q3_1 != 'No se requirió soporte'" class="mt-2">
              <InputLabel value="Tiempo de atención al soporte" />
              <el-select v-model="ratingForm.q3_2" no-data-text="No hay opciones por mostrar"
                no-match-text="No se encontraron coincidencias" filterable placeholder="Seleccionar" class="!w-1/2">
                <el-option v-for="item in a3_2" :key="item" :label="item" :value="item" />
              </el-select>
            </div>
          </section>
          <section class="mt-3">
            <h2 class="text-[#373737] dark:text-gray-500 font-bold mb-2">Ante alguna urgencia, ¿se ofreció apoyo en la
              entrega?</h2>
            <div>
              <InputLabel value="Días de atraso en la urgencia" />
              <el-select v-model="ratingForm.q4" no-data-text="No hay opciones por mostrar"
                no-match-text="No se encontraron coincidencias" filterable placeholder="Seleccionar" class="!w-1/2">
                <el-option v-for="item in a4" :key="item" :label="item" :value="item" />
              </el-select>
            </div>
          </section>
          <section class="mt-3">
            <h2 class="text-[#373737] dark:text-gray-500 font-bold mb-2">¿Hubo alguna incidencia?</h2>
            <div>
              <InputLabel value="Avisos de rechazo" />
              <el-select v-model="ratingForm.q5" no-data-text="No hay opciones por mostrar"
                no-match-text="No se encontraron coincidencias" filterable placeholder="Seleccionar" class="!w-1/2">
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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import General from "./Tabs/General.vue";
import Products from "./Tabs/Products.vue";

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
      carrier: null,
      invoice_folio: null,
    });

    return {
      ratingForm,
      selectedPurchase: "",
      activeTab: '1',
      showConfirmModal: false,
      showRatingModal: false,
      // cargas
      authorizing: false,
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
      carriers: [
        'Paquetexpress',
        'DHL',
        'FEDEX',
        'Tres guerras',
        'El proveedor trajo la mercancía',
        'Emblems3d fue a recoger la mercancía',
        'Otro',
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
    Link,
    InputLabel,
    General,
    Products,
  },
  computed: {
    getCurrentStep() {
      const statuses = [
        'Pendiente',
        'Autorizado',
        'Emitido',
        'Recibido',
      ];

      return statuses.findIndex(i => i == this.purchase.data.status);
    },
  },
  methods: {
    handleClickInTab(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    setTabInUrl() {
      // Obtener la URL actual
      const currentURL = new URL(window.location.href);
      // Extraer el valor de 'currentTab' de los parámetros de búsqueda
      const currentTabFromURL = currentURL.searchParams.get('currentTab');

      if (currentTabFromURL) {
        this.activeTab = currentTabFromURL;
      }
    },
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
    handleInputClick() {
      this.ratingForm.q1 = `No, hubo un retraso de ${this.ratingForm.q1_days} días(s)`;
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
    async authorize() {
      this.authorizing = true;
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
    this.setTabInUrl();
  },
};
</script>