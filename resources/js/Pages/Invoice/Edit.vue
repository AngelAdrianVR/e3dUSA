<template>
    <AppLayout title="Editar factura">
        <template #header>
            <div class="flex justify-between">
                <Back />
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Editar factura</h2>
                </div>
            </div>
        </template>

        <!-- Form -->
        <form ref="formTop" @submit.prevent="update"
            class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg p-9 shadow-md md:grid grid-cols-2 gap-4">
            <div class="col-span-full rounded-lg border border-blue-600 dark:border-gray-600 bg-blue-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 p-4 text-sm">
                <p class="font-medium">Nota:</p>
                <p>Para modificar cantidad de facturas y programarlas en calendario es necesario que sea la factura 1 de la venta en caso de tener varias facturas.</p>
            </div>
            <div>
                <InputLabel value="Orden de venta relacionada*" />
                <el-select disabled v-model="form.sale_id" @change="handleSelectedSale"
                    placeholder="Seleccionar" :fit-input-width="true">
                    <el-option v-for="item in sales" :key="item" :label="'OV-' + item.id" :value="item.id" />
                </el-select>
                <InputError :message="form.errors.sale_id" />
            </div>
            <div>
                <InputLabel value="Cliente*" />
                <el-input disabled v-model="clientName" placeholder="Selecciona una orden de venta" />
                <InputError :message="form.errors.company_branch_id" />
            </div>
            <div>
                <InputLabel value="Monto total de la OV" />
                <el-input
                    placeholder="Ingresa el monto total con IVA"
                    v-model="form.total_amount_sale"
                    :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                    <template #prepend>
                    $
                    </template>
                </el-input>
            </div>
            <div>
                <div class="flex space-x-2 items-center">
                    <InputLabel value="Cantidad de facturas*" />
                    <el-tooltip placement="top">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        <template #content>
                            <span>Específica la cantidad de facturas <br> que se harán para esta orden</span>
                        </template>
                    </el-tooltip>
                </div>
                <el-select :disabled="!form.total_amount_sale || invoice.number_of_invoice != 1" @change="handleinvoiceQuantity" v-model="form.invoice_quantity" placeholder="Seleccionar" :fit-input-width="true">
                    <el-option
                        v-for="item in 8"
                        :key="item"
                        :label="item"
                        :value="item"
                    />
                </el-select>
                <p v-if="!form.total_amount_sale" class="text-xs text-secondary">Ingresa el monto total de la ov</p>
                <InputError :message="form.errors.invoice_quantity" />
            </div>
            
            <!-- factura info -->
            <section class="col-span-full md:grid grid-cols-2 my-1 gap-4">
                <p class="font-bold text-lg col-span-full mb-1">Factura 1</p>
                <div>
                    <InputLabel value="Folio de factura (como aparece en el CFDI)*" />
                    <el-input v-model="form.folio" placeholder="Selecciona una orden de venta" />
                    <InputError :message="form.errors.folio" />
                </div>
                <div>
                    <InputLabel value="Fecha de emisión" />
                    <el-date-picker
                        v-model="form.issue_date"
                        type="date"
                        placeholder="Selecciona la fecha de emisión"
                        class="!w-full"
                    />
                    <InputError :message="form.errors.issue_date" />
                </div>
                 <div>
                    <InputLabel value="Monto de esta factura" />
                    <el-input
                        placeholder="Ingresa el monto total con IVA de esta factura"
                        v-model="form.invoice_amount"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                        <template #prepend>
                        $
                        </template>
                    </el-input>
                    <InputError :message="form.errors.invoice_amount" />
                </div>
                <div>
                    <InputLabel value="Moneda" />
                    <el-select v-model="form.currency" placeholder="Seleccionar" :fit-input-width="true">
                        <el-option
                            v-for="currency in currencies"
                            :key="currency"
                            :label="currency"
                            :value="currency"
                        />
                    </el-select>
                </div>
                <div>
                    <InputLabel value="Forma de pago" />
                    <el-select
                        v-model="form.payment_option"
                        placeholder="Seleccionar"
                        :fit-input-width="true"
                        >
                        <el-option
                            v-for="item in PaymentOptions"
                            :key="item.label"
                            :label="item.label"
                            :value="item.label"
                        >
                            <span style="float: left">{{ item.label }}</span>
                            <span
                                style="
                                float: right;
                                color: var(--el-text-color-secondary);
                                font-size: 13px;
                                "
                            >
                                {{ item.description }}
                            </span>
                        </el-option>
                    </el-select> 
                </div>
                <div>
                    <InputLabel value="Método de pago" />
                    <el-select
                        v-model="form.payment_method"
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
                </div>
                <div>
                    <div class="flex space-x-2 items-center pb-1">
                        <InputLabel value="Estado de la factura" />
                        <span v-html="statusIcon(form.status)"></span>
                    </div>
                    <el-select
                        v-model="form.status"
                        placeholder="Seleccionar"
                        :fit-input-width="true"
                        >
                        <el-option
                            v-for="item in statuses"
                            :key="item"
                            :label="item.label"
                            :value="item.label"
                        >
                            <span style="float: left">{{ item.label }}</span>
                            <span v-html="item.icon" class="pt-1"
                                style="
                                float: right;
                                "
                            >
                            </span>
                        </el-option>
                    </el-select>
                </div>
                <div v-if="invoice.media?.filter(f => f.collection_name === 'factura')?.length" class="mt-4 col-span-full">
                    <InputLabel value="Archivos adjuntos" />
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                        <FileView v-for="file in invoice.media.filter(f => f.collection_name === 'factura')" :key="file" :file="file" :deletable="true"
                            @delete-file="deleteFile($event)" />
                    </div>
                </div>
                <div class="ml-2 mt-2 col-span-full">
                    <InputLabel value="Adjuntar factura" />
                    <FileUploader :multiple="false" @files-selected="form.media = $event" />
                </div>
                <div class="col-span-full">
                    <InputLabel value="Notas" />
                    <el-input v-model="form.notes" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                        placeholder="Escribe notas adicionales" :maxlength="800" show-word-limit
                        clearable />
                    <InputError :message="form.errors.notes" />
                </div>
            </section>

            <section class="col-span-full md:grid grid-cols-2 gap-4" v-if="form.invoice_quantity > 1 && invoice.number_of_invoice == 1">
                <el-divider content-position="left" class="col-span-full">Programación de facturas</el-divider>
                <p class="text-sm col-span-full">Programa las fechas y montos de las facturas que emitirás. Recibirás un recordatorio cuando sea momento de capturarlas. 
                    Puedes consultar esta programación en el módulo de Facturas, en la pestaña “Programación de facturas”</p>

                <div class="col-span-full">
                    <div class="flex items-center space-x-2">
                        <InputLabel value="Periodicidad" />
                        <el-tooltip placement="top">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-primary">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            <template #content>
                                <span>Las fechas se asignarán de acuerdo <br> con la temporalidad que elijas.</span>
                            </template>
                        </el-tooltip>
                    </div>
                    <el-select
                        @change="handleSelectedPeriodicity"
                        class="!w-full md:!w-1/2"
                        v-model="periodicityExtraInvoices"
                        placeholder="Seleccionar"
                        :fit-input-width="true"
                        >
                        <el-option
                            v-for="item in periodicities"
                            :key="item"
                            :label="item"
                            :value="item"
                        />
                    </el-select>
                </div>

                <section class="col-span-full" v-for="(invoice, index) in form.invoice_quantity"
                        :key="index">
                    <div class="grid grid-cols-3 gap-4" v-if="index !== 0"
                        >
                        <p class="text-lg font-bold col-span-full">Factura {{ index + 1 }}</p>
                        <div>
                            <InputLabel value="Monto" />
                            <el-input
                                placeholder="Ingresa el monto total con IVA de esta factura"
                                v-model="form.invoice_amount"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                                <template #prepend>
                                $
                                </template>
                            </el-input>
                        </div>
                        <div>
                            <InputLabel value="Fecha" />
                            <el-date-picker
                                v-model="form.extra_invoices[index].reminder_date"
                                type="date"
                                placeholder="Selecciona la fecha de recordatorio"
                                class="!w-full"
                            />
                        </div>
                        <div>
                            <InputLabel value="Hora" />
                            <el-time-select
                                v-model="form.extra_invoices[index].reminder_time"
                                start="00:00"
                                step="00:30"
                                end="23:59"
                                placeholder="Selecciona la hora de recordatorio"
                                format="hh:mm A"
                                class="!w-full"
                            />
                        </div>
                    </div>
                </section>
                <el-divider content-position="left" class="col-span-full"></el-divider>
            </section>
            
            <!-- Complementos de pago -->
            <section class="col-span-full">
                <el-divider content-position="left" class="col-span-full">Complementos de pago</el-divider>
                <p v-if="!form.complements.length" class="text-sm dark:text-gray-400 text-[#373737]">Click al botón de “+” para empezar a agregar complementos de pago</p>

                <article v-else>
                    <div class="md:grid grid-cols-2 gap-4" v-for="(complement, index) in form.complements" :key="index">
                        <div class="flex justify-between col-span-full mb-2 mt-4 rounded-lg p-2 bg-gray-400 dark:bg-gray-800">
                            <p class="font-bold">Complemento de pago {{ index + 1 }}</p>
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#373737" :title="'¿Continuar?'"
                                @confirm="removeComplement(index)">
                                <template #reference>
                                <button type="button" class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                                </template>
                            </el-popconfirm>
                        </div>

                        <div>
                            <InputLabel value="Folio del complemento*" />
                            <el-input v-model="form.complements[index].folio" placeholder="Selecciona una orden de venta" />
                            <InputError :message="form.errors[`complements.${index}.folio`]" />
                        </div>
                        <div>
                            <InputLabel value="Fecha de pago" />
                            <el-date-picker
                                v-model="form.complements[index].payment_date"
                                type="date"
                                placeholder="Selecciona la fecha de pago"
                                class="!w-full"
                            />
                            <InputError :message="form.errors[`complements.${index}.payment_date`]" />
                        </div>
                        <div>
                            <InputLabel value="Monto" />
                            <el-input
                                placeholder="Ingresa el monto del complemento"
                                v-model="form.complements[index].amount"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/\$\s?|(,*)/g, '')">
                                <template #prepend>
                                $
                                </template>
                            </el-input>
                            <InputError :message="form.errors[`complements.${index}.amount`]" />
                        </div>
                        <div>
                            <InputLabel value="Método de pago" />
                            <el-select
                                v-model="form.complements[index].payment_method"
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
                            <InputError :message="form.errors[`complements.${index}.payment_method`]" />
                        </div>
                        <div v-if="invoice.media?.filter(f => f.name === `Complemento ${index + 1}`)?.length" class="mt-4 col-span-full">
                            <InputLabel value="Archivos adjuntos" />
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                <FileView v-for="file in invoice.media.filter(f => f.name === `Complemento ${index + 1}`)" :key="file" :file="file" :deletable="true"
                                    @delete-file="deleteFile($event)" />
                            </div>
                        </div>
                        <div class="ml-2 mt-2 col-span-full">
                            <InputLabel value="Adjuntar archivos" />
                            <FileUploader :multiple="true" @files-selected="form.complements[index].complementMedia = $event" />
                        </div>
                        <div class="col-span-full">
                            <InputLabel value="Notas" />
                            <el-input v-model="form.complements[index].notes" :autosize="{ minRows: 2, maxRows: 6 }" type="textarea"
                                placeholder="Escribe notas adicionales" :maxlength="800" show-word-limit
                                clearable />
                        </div>
                    </div>
                </article>
                <el-divider content-position="left" class="col-span-full"></el-divider>
                <button @click="addComplement" type="button" class="underline text-sm text-secondary">+ Agregar complemento de pago</button>
            </section>
            <div class="mt-9 mx-3 md:text-right col-span-full">
                <PrimaryButton :disabled="form.processing">
                <i v-if="form.processing" class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                 Guardar cambios
                </PrimaryButton>
            </div>
        </form>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import FileView from "@/Components/MyComponents/FileView.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { differenceInMonths, differenceInDays, format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from "axios";

export default {
data() {
    const form = useForm({
        issue_date: this.invoice.issue_date,
        folio: this.invoice.folio,
        total_amount_sale: this.invoice.total_amount_sale,
        invoice_amount: this.invoice.invoice_amount,
        currency: this.invoice.currency,
        payment_option: this.invoice.payment_option,
        payment_method: this.invoice.payment_method,
        status: this.invoice.status,
        notes: this.invoice.notes,
        company_branch_id: this.invoice.company_branch_id,
        sale_id: this.invoice.sale_id,
        media: null,
        invoice_quantity: this.invoice.invoice_quantity,
        complements: this.invoice.complements, // complementos de la factura
        extra_invoices: this.invoice.extra_invoices, // facturas extra a la misma venta (solo información de programación)
    });

    return {
        form,
        clientName: null,
        periodicityExtraInvoices: null, // periodicidad para asignacion rapida de fecha a las facturas extra
        currencies: ['MXN', 'USD'],
        periodicities: ['Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Semestral'],
        PaymentOptions: [
            {
                label: 'PUE',
                description: 'Pago en una sola exhibición'
            },
            {
                label: 'PDD',
                description: 'Pago en parcialidades o diferido'
            },
        ],
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
            {
                label: 'Cancelada',
                icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
            },
        ],
        PaymentMethods: ['Efectivo', 'Transferencia electrónica de fondos', 'Tarjeta de crédito', 'Tarjeta de débito', 'Por definir'],
    }
},
components: {
    PrimaryButton,
    FileUploader,
    InputLabel,
    InputError,
    AppLayout,
    IconInput,
    FileView,
    Back,
    Link,
},
props: {
    invoice: Object,
    sales: Array,
},
methods: {
    update() {
        this.form.post(route("invoices.update", this.invoice-id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "",
                    type: "success",
            });
            },
            onError: () => {
                // Scroll hacia el div con ref="formTop"
                this.$nextTick(() => {
                    const el = this.$refs.formTop;
                    if (el && typeof el.scrollIntoView === 'function') {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            },
        });
    },
    addComplement() {
        this.form.complements.push({
            folio: null,
            payment_date: null,
            payment_method: null,
            amount: null,
            complementMedia: null,
            notes: null,
        });
    },
    removeComplement(index) {
        this.form.complements.splice(index, 1);
    },
    handleSelectedSale() {
        this.form.company_branch_id = this.sales.find(sale => sale.id === this.form.sale_id)?.company_branch?.id;
        this.clientName = this.sales.find(sale => sale.id === this.form.sale_id).company_branch.name;
    },
    handleinvoiceQuantity() {
        if ( this.form.total_amount_sale ) {
            //Asigna el monto total de la primer factura
            this.form.invoice_amount = (this.form.total_amount_sale / this.form.invoice_quantity).toFixed(2);
            
            // Limpiar todos los elementos excepto el primero
            this.form.extra_invoices = [];
            for (let index = 0; index < this.form.invoice_quantity; index++) {
                this.form.extra_invoices.push({
                    reminder_date: null, // fecha de programación
                    reminder_time: null, // hora de programación
                    invoice_amount: (this.form.total_amount_sale / this.form.invoice_quantity).toFixed(2),
                });
            }
        }
    },
    deleteFile(fileId) {
        this.invoice.media = this.invoice.media.filter(m => m.id !== fileId);
    },
    handleSelectedPeriodicity() {
        // if (!this.form.periodicityExtraInvoices || !this.form.extra_invoices.length) return;

        const startDate = new Date(); // fecha de inicio
        let intervalDays = 0;

        switch (this.periodicityExtraInvoices) {
            case 'Semanal':
                intervalDays = 7;
                break;
            case 'Quincenal':
                intervalDays = 14;
                break;
            case 'Mensual':
                intervalDays = 30;
                break;
            case 'Bimestral':
                intervalDays = 60;
                break;
            case 'Semestral':
                intervalDays = 180;
                break;
            default:
                intervalDays = 0;
        }

        this.form.extra_invoices.forEach((invoice, index) => {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + intervalDays * index);
            invoice.reminder_date = date.toISOString().split('T')[0]; // formato YYYY-MM-DD
            invoice.reminder_time = '08:00 AM'
        });
    },
    statusIcon(status) {
        if ( status == 'Emitida' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#08688B]"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>'
        } else if ( status == 'Pendiente de pago' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#B8B30E]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
        } else if ( status == 'Parcialmente pagada' ) {
            return '<svg width="24" height="24" viewBox="0 0 24 24" class="text-[#C4620C]" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.30078 19.5173C2.8856 19.5168 3.47013 19.5249 4.05421 19.5415M19.5508 19.5173V20.5223C19.5508 21.2763 18.8248 21.8163 18.0978 21.6183C14.7336 20.7044 11.2996 20.0859 7.83503 19.7682M3.80078 5.2673V6.0173C3.80078 6.21621 3.72176 6.40698 3.58111 6.54763C3.44046 6.68828 3.24969 6.7673 3.05078 6.7673H2.30078M2.30078 6.7673V6.3923C2.30078 5.7713 2.80478 5.2673 3.42578 5.2673C8.74133 5.2673 9.35662 5.2673 14.6722 5.2673M2.30078 6.7673V15.7673M20.3008 5.2673V6.0173C20.3008 6.4313 20.6368 6.7673 21.0508 6.7673H21.8008M20.3008 5.2673H20.6758C21.2968 5.2673 21.8008 5.7713 21.8008 6.3923V16.1423C21.8008 16.7633 21.2968 17.2673 20.6758 17.2673H20.3008M20.3008 5.2673C19.6254 5.2673 19.5241 5.2673 18.8487 5.2673M2.30078 15.7673V16.1423C2.30078 16.4407 2.41931 16.7268 2.63029 16.9378C2.84126 17.1488 3.12741 17.2673 3.42578 17.2673H3.80078M2.30078 15.7673H3.05078C3.24969 15.7673 3.44046 15.8463 3.58111 15.987C3.72176 16.1276 3.80078 16.3184 3.80078 16.5173V17.2673M20.3008 17.2673V16.5173C20.3008 16.3184 20.3798 16.1276 20.5205 15.987C20.6611 15.8463 20.8519 15.7673 21.0508 15.7673H21.8008M20.3008 17.2673C20.3008 17.2673 14.9222 17.2673 9.92946 17.2673M3.80078 17.2673C3.80078 17.2673 4.41681 17.2673 5.69804 17.2673M12.0508 14.2673C12.8464 14.2673 13.6095 13.9512 14.1721 13.3886C14.7347 12.826 15.0508 12.0629 15.0508 11.2673C15.0508 11.0133 15.0186 10.7626 14.9564 10.5207M12.0508 8.2673C11.2551 8.2673 10.4921 8.58337 9.92946 9.14598C9.36685 9.70859 9.05078 10.4716 9.05078 11.2673C9.05078 11.694 9.14168 12.1113 9.31274 12.4933M19.0679 2.41113L4.43777 21.6183M18.0508 11.2673H18.0588V11.2753H18.0508V11.2673ZM6.05078 11.2673H6.05878V11.2753H6.05078V11.2673Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        } else if ( status == 'Pagada' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-green-500"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" /></svg>'
        } else if ( status == 'Cancelada' ) {
            return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>'
        }
    }
},
mounted() {
    this.handleSelectedSale();
    this.handleinvoiceQuantity();
}
}
</script>
