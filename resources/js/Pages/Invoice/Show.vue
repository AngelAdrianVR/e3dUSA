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
                            <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                        </Link>
                    </el-tooltip>

                    <PrimaryButton :disabled="handleDisable()" @click="complementModal = true" class="rounded-md">Agregar complemento</PrimaryButton>

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
    </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
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
import { Axios } from 'axios';

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
          }
        ], // complementos de la factura
    });

    return {
      form,
      selectedInvoice: this.invoice.id,
      complementModal: false,
      showMaxAmountComplement: false, // muestra el monto maximo para un complemento
      activeTab: '1',
      PaymentMethods: ['Efectivo', 'Transferencia electrónica de fondos', 'Tarjeta de crédito', 'Tarjeta de débito', 'Por definir'],
    }
},
components:{
    AppLayoutNoHeader,
    PrimaryButton,
    FileUploader,
    DropdownLink,
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
    handleDisable() {
      if ( this.invoice.complements?.length && this.remainingAmount == 0 ) {
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
              this.form.reset;
              this.showMaxAmountComplement = false;
              this.complementModal = false;
            },
            
        });
    },
    async deleteItem() {
      try {
        const response = await Axios.delete(
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