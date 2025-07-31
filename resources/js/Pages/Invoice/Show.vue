<template>
    <AppLayoutNoHeader :title="invoice.folio">
        <main class="dark:text-white p-2 md:p-5 lg:p-7">
            <div class="flex justify-between">
                <label class="text-lg">Detalles de factura</label>
                <Link :href="route('invoices.index')"
                    class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </Link>
            </div>

            <div class="flex justify-between my-2">
                <div class="md:w-1/3">
                    <el-select @change="$inertia.get(route('invoices.show', selectedInvoice))" v-model="selectedInvoice"
                        filterable placeholder="Buscar factura" no-data-text="No hay registros"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in invoices" :key="item.id" :label="item.folio" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar facturas')" content="Editar"
                    placement="top">
                        <Link :href="route('invoices.edit', selectedInvoice)">
                            <button v-if="invoice.status != 'Cancelada' && invoice.status != 'Pagada'" class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                        </Link>
                    </el-tooltip>

                    <PrimaryButton v-if="invoice.payment_option == 'PDD'" :disabled="handleDisable()" @click="complementModal = true" class="rounded-md">Agregar complemento</PrimaryButton>

                    <Dropdown align="right" width="48"
                        v-if="$page.props.auth.user.permissions.includes('Crear facturas') && $page.props.auth.user.permissions.includes('Eliminar facturas')">
                        <template #trigger>
                            <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] dark:bg-[#202020] dark:text-white flex items-center justify-center text-sm">
                                Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
                            </button>
                        </template>
                        <template #content>
                        <DropdownLink :href="route('invoices.create')"
                        v-if="$page.props.auth.user.permissions.includes('Crear facturas')">
                        Crear nueva factura
                        </DropdownLink>
                        
                        <DropdownLink @click="showConfirmModal = true" as="button"
                        v-if="$page.props.auth.user.permissions.includes('Eliminar facturas')">
                        Eliminar factura
                        </DropdownLink>
                    </template>
                    </Dropdown>
                </div>
            </div>

            <p class="text-center font-bold text-lg my-3">
                Folio {{ invoice.folio }}
            </p>

            <div v-if="invoice.status != 'Pagada' && invoice.status != 'Cancelada'" class="w-72 flex space-x-2 items-center ml-4">
              <p class="text-gray-700">Estatus:</p>
              <el-select @change="updateStatus"
                        v-model="status"
                        placeholder="Seleccionar"
                        :fit-input-width="true"
              >
                <el-option
                  v-for="item in filteredStatuses"
                  :key="item.label"
                  :label="item.label"
                  :value="item.label"
                >
                  <span style="float: left">{{ item.label }}</span>
                  <span v-html="item.icon" class="pt-1" style="float: right;"></span>
                </el-option>
              </el-select>

            </div>

            <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClick">
                <el-tab-pane label="Datos de la factura" name="1">
                    <General :invoice="invoice" :extra_invoices="extra_invoices" />
                </el-tab-pane>
            </el-tabs>
        </main>

        <!-- -------------- Complements Modal starts----------------------- -->
        <Modal :show="complementModal" @close="complementModal = false">
          <div class="p-5 relative">
              <h2 class="font-bold mb-5">Complemento de pago</h2>
              <i @click="complementModal = false"
              class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3 top-3"></i>
              <form @submit.prevent="storeComplement" class="md:grid grid-cols-2 gap-4">
                 <div>
                      <InputLabel value="Folio del complemento*" />
                      <el-input v-model="form.complements[0].folio" placeholder="Selecciona una orden de venta" />
                      <InputError :message="form.errors[`complements.${0}.folio`]" />
                  </div>
                  <div>
                      <InputLabel value="Fecha de pago" />
                      <el-date-picker
                          v-model="form.complements[0].payment_date"
                          type="date"
                          placeholder="Selecciona la fecha de pago"
                          class="!w-full"
                      />
                      <InputError :message="form.errors[`complements.${0}.payment_date`]" />
                  </div>
                  <div>
                      <InputLabel value="Monto" />
                      <!-- <el-input
                          placeholder="Ingresa el monto del complemento"
                          v-model="form.complements[0].amount"
                          :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                          :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                          <template #prepend>
                          $
                          </template>
                      </el-input> -->
                      <el-input
                        placeholder="Ingresa el monto del complemento"
                        v-model="form.complements[0].amount"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')"
                        @input="handleAmountInput(0)">
                        <template #prepend>$</template>
                      </el-input>
                      <p v-if="showMaxAmountComplement" class="text-xs text-primary">el monto restante es de {{ remainingAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                      <InputError :message="form.errors[`complements.${0}.amount`]" />
                  </div>
                  <div>
                      <InputLabel value="Método de pago" />
                      <el-select
                          v-model="form.complements[0].payment_method"
                          placeholder="Seleccionar"
                          :fit-input-width="true"
                          >
                          <el-option
                              v-for="item in PaymentMethods"
                              :key="item"
                              :label="item"
                              :value="item"
                          />
                      </el-select>
                      <InputError :message="form.errors[`complements.${0}.payment_method`]" />
                  </div>
                  <div class="ml-2 mt-2 col-span-full">
                      <InputLabel value="Adjuntar archivos" />
                      <FileUploader :multiple="true" @files-selected="form.complements[0].complementMedia = $event" />
                  </div>
                  <div class="col-span-full">
                      <InputLabel value="Notas" />
                      <el-input v-model="form.complements[0].notes" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                          placeholder="Escribe notas adicionales" :maxlength="800" show-word-limit
                          clearable />
                  </div>
                  <div class="mt-5 mx-3 md:text-right col-span-full">
                      <PrimaryButton :disabled="form.processing">
                      <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                      Crear complemento
                      </PrimaryButton>
                  </div>
              </form>
          </div>
        </Modal>

        <!-- Confirmacion para eliminar la factura -->
        <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
          <template #title> Eliminar factura </template>
          <template #content> ¿Continuar con la eliminación? </template>
          <template #footer>
            <div>
              <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
              <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
            </div>
          </template>
        </ConfirmationModal>
    </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Modal from "@/Components/Modal.vue";
import General from "./Tabs/General.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import { differenceInMonths, differenceInDays, format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Link, useForm } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
  const form = useForm({
        complements: [
          {
            folio: null,
            payment_date: format(new Date(), "yyyy-MM-dd"), // Establece la fecha de hoy por defecto,
            amount: null,
            payment_method: 'Transferencia electrónica de fondos',
            notes: null,
            complementMedia: null
          }
        ], // complementos de la factura
    });

    return {
      form,
      showConfirmModal: false, // modal para confirmar eliminación de factura
      selectedInvoice: this.invoice.id,
      complementModal: false,
      showMaxAmountComplement: false, // muestra el monto maximo para un complemento
      activeTab: '1',
      PaymentMethods: ['Efectivo', 'Transferencia electrónica de fondos', 'Tarjeta de crédito', 'Tarjeta de débito', 'Por definir'],
      status: this.invoice.status,
      statuses: [
          // {
          //     label: 'Emitida',
          //     icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#08688B]"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>'
          // },
          {
              label: 'Pendiente de pago',
              icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#B8B30E]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
          },
          {
              label: 'Parcialmente pagada',
              icon: '<svg width="24" height="24" viewBox="0 0 24 24" class="text-[#C4620C]" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.30078 19.5173C2.8856 19.5168 3.47013 19.5249 4.05421 19.5415M19.5508 19.5173V20.5223C19.5508 21.2763 18.8248 21.8163 18.0978 21.6183C14.7336 20.7044 11.2996 20.0859 7.83503 19.7682M3.80078 5.2673V6.0173C3.80078 6.21621 3.72176 6.40698 3.58111 6.54763C3.44046 6.68828 3.24969 6.7673 3.05078 6.7673H2.30078M2.30078 6.7673V6.3923C2.30078 5.7713 2.80478 5.2673 3.42578 5.2673C8.74133 5.2673 9.35662 5.2673 14.6722 5.2673M2.30078 6.7673V15.7673M20.3008 5.2673V6.0173C20.3008 6.4313 20.6368 6.7673 21.0508 6.7673H21.8008M20.3008 5.2673H20.6758C21.2968 5.2673 21.8008 5.7713 21.8008 6.3923V16.1423C21.8008 16.7633 21.2968 17.2673 20.6758 17.2673H20.3008M20.3008 5.2673C19.6254 5.2673 19.5241 5.2673 18.8487 5.2673M2.30078 15.7673V16.1423C2.30078 16.4407 2.41931 16.7268 2.63029 16.9378C2.84126 17.1488 3.12741 17.2673 3.42578 17.2673H3.80078M2.30078 15.7673H3.05078C3.24969 15.7673 3.44046 15.8463 3.58111 15.987C3.72176 16.1276 3.80078 16.3184 3.80078 16.5173V17.2673M20.3008 17.2673V16.5173C20.3008 16.3184 20.3798 16.1276 20.5205 15.987C20.6611 15.8463 20.8519 15.7673 21.0508 15.7673H21.8008M20.3008 17.2673C20.3008 17.2673 14.9222 17.2673 9.92946 17.2673M3.80078 17.2673C3.80078 17.2673 4.41681 17.2673 5.69804 17.2673M12.0508 14.2673C12.8464 14.2673 13.6095 13.9512 14.1721 13.3886C14.7347 12.826 15.0508 12.0629 15.0508 11.2673C15.0508 11.0133 15.0186 10.7626 14.9564 10.5207M12.0508 8.2673C11.2551 8.2673 10.4921 8.58337 9.92946 9.14598C9.36685 9.70859 9.05078 10.4716 9.05078 11.2673C9.05078 11.694 9.14168 12.1113 9.31274 12.4933M19.0679 2.41113L4.43777 21.6183M18.0508 11.2673H18.0588V11.2753H18.0508V11.2673ZM6.05078 11.2673H6.05878V11.2753H6.05078V11.2673Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
          },
          {
              label: 'Pagada',
              icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-green-500"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" /></svg>'
          },
          // {
          //     label: 'Cancelada',
          //     icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
          // },
      ],
    }
},
components:{
    AppLayoutNoHeader,
    ConfirmationModal,
    PrimaryButton,
    FileUploader,
    DropdownLink,
    CancelButton,
    InputLabel,
    InputError,
    Dropdown,
    General,
    Modal,
    Link
},
props:{
    invoice: Object, //la factura en curso
    invoices: Array, //para la busqueda en el select
    extra_invoices: Array //las facturas relacionadas a esa misma ov
},
computed: {
  remainingAmount() {
    const invoiceAmount = this.invoice.invoice_amount || 0;

    // Sumamos todos los montos de los complementos (excepto el que se está editando si quieres prevenir doble conteo)
    const complements = Array.isArray(this.invoice.complements)
      ? this.invoice.complements.reduce((sum, comp) => {
          return sum + (parseFloat(comp.amount) || 0);
        }, 0)
      : 0;

    // Retornamos el restante (evitamos negativos)
    return Math.max(invoiceAmount - complements, 0);
  },
  filteredStatuses() {
    return this.statuses.filter(item => {
      if (item.label === 'Parcialmente pagada') {
        return this.invoice.payment_option === 'PDD';
      }
      return true;
    });
  }
},
methods:{
    handleClick(tab) {
      // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
      const currentURL = new URL(window.location.href);
      currentURL.searchParams.set('currentTab', tab.props.name);
      // Actualiza la URL
      window.history.replaceState({}, document.title, currentURL.href);
    },
    async updateStatus() {
      try {
        const response = await axios.post(route('invoices.change-status', this.invoice.id), { status: this.status });
        if ( response.status === 200 ) {
          this.invoice.status = response.data.invoice.status;
          this.$notify({
              title: "Correcto",
              message: "",
              type: "success",
          });
        }
      } catch (error) {
        console.log(error);
      }
    },
    handleDisable() {
      if ( (this.invoice.complements?.length && this.remainingAmount == 0) || (this.invoice.status == 'Pagada' || this.invoice.status == 'Cancelada') ) {
        return true;
      } else {
        return false;
      }
    },
    handleAmountInput(index) {
      let val = parseFloat(this.form.complements[index].amount || 0);
      const remaining = this.remainingAmount;

      if (val > remaining) {
        this.form.complements[index].amount = remaining.toFixed(2);
        this.showMaxAmountComplement = true;
      }
    },
    storeComplement() {
      this.form.post(route("invoices.store-complement", this.invoice.id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "",
                    type: "success",
                });
              this.form.reset();
              this.status = this.invoice.status; // Reset status to current invoice status
              this.showMaxAmountComplement = false;
              this.complementModal = false;
            },
            
        });
    },
    async deleteItem() {
      try {
        const response = await axios.delete(
          route("invoices.destroy", this.invoice.id)
        );

        if (response.status == 200) {
          this.$notify({
            title: "Correcto",
            message: '',
            type: "success",
          });
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
          message: err.message + "Actualiza la página",
          type: "error",
        });
        console.log(err);
      } finally {
        this.showConfirmModal = false;
        this.$inertia.get(route('invoices.index'));
      }
    },
  },
  mounted() {
    // Obtener la URL actual
    const currentURL = new URL(window.location.href);
    // Extraer el valor de 'currentTab' de los parámetros de búsqueda
    const currentTabFromURL = currentURL.searchParams.get('currentTab');

    if (currentTabFromURL) {
      this.activeTab = currentTabFromURL;
    }
  },
}
</script>