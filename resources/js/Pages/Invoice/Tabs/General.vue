<template>
    <main class="grid grid-cols-2">    
        <section class="grid grid-cols-2 gap-2 text-left p-4 text-sm items-center dark:text-white border-r border-[#D9D9D9]">
            <p class="text-secondary mb-2 col-span-full">Datos de la factura</p>
            <span class="text-gray-500">Folio de factura</span>
            <span>{{ invoice.folio }}</span>
            <span class="text-gray-500">Fecha de emisión</span>
            <span>{{ dateFormat(invoice.issue_date) }}</span>
            <span class="text-gray-500">Monto de esta factura</span>
            <span>${{ invoice.invoice_amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            <span class="text-gray-500">Pendiente de pago</span>
            <span>${{ invoice.status == 'Pagada' ? '0.00' :  remainingInvoiceAmount }}</span>
            <span class="text-gray-500">Forma de pago</span>
            <span>{{ invoice.payment_option === 'PUE' ? invoice.payment_option + ' - Pago en una sola exhibición' : invoice.payment_option + ' - Pago en parcialidades o diferido' }}</span>
            <span class="text-gray-500">Método de pago</span>
            <span>{{ invoice.payment_method }}</span>
            <span class="text-gray-500">Estado de factura</span>
            <p class="flex items-center space-x-1 rounded-full px-3 py-1 border-2" :class="colorStatus(invoice.status)">
                <span v-html="statusIcon(invoice.status)"></span>
                <span>{{ invoice.status }}</span>
            </p>
            <span class="text-gray-500">Notas</span>
            <span>{{ invoice.notes ?? '-' }}</span>
            <span class="text-gray-500">Archivos adjuntos</span>
            <div v-if="invoice.media.filter(f => f.collection_name === 'factura')?.length" class="col-span-full">
                <li
                    v-for="file in invoice.media.filter(f => f.collection_name === 'factura')"
                    :key="file.id"
                    class="flex justify-between"
                >
                    <a :href="file.original_url" target="_blank" class="flex space-x-2">
                        <i :class="getFileTypeIcon(file.file_name)" class="mt-1"></i>
                        <span>{{ file.file_name }}</span>
                    </a>
                </li>
            </div>
            <p class="text-sm text-gray-400" v-else>
                <i class="fa-regular fa-file-excel mr-3"></i>
                No hay archivos adjuntos en la factura
            </p>

            <!-- Complementos de pago -->
            <section v-if="invoice.payment_option == 'PDD'" class="col-span-full grid grid-cols-2 gap-2">
                <el-divider class="col-span-full" />
                <p class="text-secondary mb-4 col-span-full">Complementos de pago</p>
                <section class="col-span-full" v-if="invoice.complements?.length">
                    <div class="grid grid-cols-2 gap-2 space-y-0 mb-7" v-for="(item, index) in invoice.complements" :key="item">
                        <p class="col-span-full font-semibold">Complemento {{ index + 1 }}</p>
                        <span class="text-gray-500">Folio de complemento</span>
                        <span>{{ item.folio }}</span>
                        <span class="text-gray-500">Fecha de pago</span>
                        <span>{{ dateFormat(item.payment_date) }}</span>
                        <span class="text-gray-500">Monto pagado</span>
                        <span>${{ Number(item.amount)?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                        <span class="text-gray-500">Notas</span>
                        <span>{{ item.notes ?? '-' }}</span>
                        <span class="text-gray-500">Archivos adjuntos</span>
                        <div v-if="invoice.media.filter(f => f.collection_name === 'complementos') 
                            && invoice.media.filter(f => f.name === `Complemento ${index + 1}`)" class="col-span-full">
                            <li
                                v-for="file in invoice.media.filter(f => f.collection_name === 'complementos' && f.name === `Complemento ${index + 1}`)"
                                :key="file.id"
                                class="flex justify-between"
                            >
                                <a :href="file.original_url" target="_blank" class="flex space-x-2">
                                    <i :class="getFileTypeIcon(file.file_name)" class="mt-1"></i>
                                    <span>{{ file.file_name }}</span>
                                </a>
                            </li>
                        </div>
                        <p class="text-sm text-gray-400" v-else>
                            <i class="fa-regular fa-file-excel mr-3"></i>
                            No hay archivos adjuntos
                        </p>
                    </div>
                </section>
                <p v-else class="text-[#9A9A9A]">Sin complementos de pago aún</p>
            </section>

            
        </section>

        <!-- Lado derecho de la información -->
        <section class="grid grid-cols-2 gap-2 text-left p-4 text-sm items-center dark:text-white self-start">
            <p class="text-secondary mb-2 col-span-full">Datos del cliente</p>
            <span class="text-gray-500">Razón social</span>
            <span>{{ invoice.company_branch?.company?.business_name }}</span>
            <span class="text-gray-500">RFC</span>
            <span>{{ invoice.company_branch?.company?.rfc }}</span>
            <span class="text-gray-500">Cliente/sucursal</span>
            <span>{{ invoice.company_branch?.name }}</span>
            <span class="text-gray-500">Dirección</span>
            <span>{{ invoice.company_branch?.address }}. C.P. {{ invoice.company_branch?.post_code }}</span>

            <p class="text-black mb-2 col-span-full">Contacto</p>
            <div v-for="contact in invoice.company_branch?.contacts" :key="contact" class="col-span-full grid grid-cols-2 space-y-1 mb-7">
                <span class="text-gray-500">Nombre</span>
                <span>{{ contact.name }}</span>
                <span class="text-gray-500">Correo electrónico</span>
                <span>{{ contact.email }}</span>
                <span class="text-gray-500">Teléfono</span>
                <span>{{ contact.phone }}</span>
            </div>

            <!-- Informaciond e la ov -->
            <p class="text-secondary mb-2 col-span-full">Datos de la OV</p>
            <span class="text-gray-500">OV relacionada</span>
            <span @click="$inertia.visit(route('sales.show', invoice.sale_id))" class="underline text-secondary cursor-pointer">{{ invoice.sale_id }}</span>
            <span class="text-gray-500">Cantidad de facturas</span>
            <span>{{ invoice.invoice_quantity }}</span>
            <span class="text-gray-500">Monto total</span>
            <span>${{ invoice.total_amount_sale?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            <span class="text-gray-500">Pendiente de pago</span>
            <span>${{ pendingSaleAmount.replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>

            <!-- Facturas relacionadas -->
            <p class="text-black dark:text-white mb-2 mt-5 col-span-full font-semibold">Facturas relacionadas a la orden</p>
            <div class="col-span-full" v-if="invoice.invoice_quantity > 1">
            <div
                class="grid grid-cols-2 gap-y-2"
                v-for="index in invoice.invoice_quantity"
                :key="index"
            >
                <span class="text-gray-500">Factura {{ index }} </span>

                <!-- Mostrar "Es esta factura" si coincide con el número actual -->
                <div v-if="invoice.number_of_invoice === index" class="flex items-center space-x-2">
                    <p class="text-green-600">Es esta factura</p>
                </div>

                <!-- Si ya existe otra factura con ese número -->
                <Link
                    v-else-if="extra_invoices.find(i => i.number_of_invoice === index)"
                    :href="route('invoices.show', extra_invoices.find(i => i.number_of_invoice === index)?.id)"
                >
                    <p>
                        <span class="text-secondary underline cursor-pointer">
                            {{ extra_invoices.find(i => i.number_of_invoice === index)?.folio }}
                        </span>
                        <span class="text-gray-500">- {{ extra_invoices.find(i => i.number_of_invoice === index)?.status }}</span>
                    </p>
                </Link>

                <!-- Si no existe ninguna -->
                <div v-else class="flex items-center space-x-2">
                    <p>Sin registro aún</p>
                    <SecondaryButton
                        @click="$inertia.visit(route('invoices.create', { 
                            sale_id: invoice.sale_id, 
                            total_amount_sale: invoice.total_amount_sale, 
                            invoice_quantity: invoice.invoice_quantity 
                        }))"
                        class="!py-0"
                        >
                        Crear factura
                    </SecondaryButton>
                </div>
            </div>
        </div>

        <p class="text-sm text-[#9A9A9A] col-span-full" v-else>
            No hay más facturas relacionadas a la orden de venta
        </p>
        </section>
    </main>
</template>

<script>
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data() {
    return {

    }
},
components:{
    SecondaryButton,
    Link
},
props:{
    invoice: Object,
    extra_invoices: Array
},
computed: {
    pendingSaleAmount() {
        const invoice = this.invoice;
        const total = invoice.total_amount_sale || 0;

        // Calcular lo pagado del invoice principal según su estado
        let principalPaid = 0;
        
        if (invoice.status === "Pagada") {
            principalPaid = invoice.invoice_amount || 0;
        } else if (invoice.status === "Parcialmente pagada") {
            const complements = Array.isArray(invoice.complements) ? invoice.complements : [];
            principalPaid = complements.reduce((sum, c) => sum + (Number(c.amount) || 0), 0);
        }

        // Verificar extras
        const extras = Array.isArray(this.extra_invoices) ? this.extra_invoices : [];

        const extraPaid = extras.reduce((sum, extra) => {
            if (extra.status === "Pagada") {
            return sum + (extra.invoice_amount || 0);
            }

            if (extra.status === "Parcialmente pagada") {
            const complements = Array.isArray(extra.complements) ? extra.complements : [];
            const complementSum = complements.reduce((cSum, c) => cSum + (Number(c.amount) || 0), 0);
            return sum + complementSum;
            }

            return sum; // Pendiente de pago u otro estado
        }, 0);

        const pending = total - principalPaid - extraPaid;

        return Math.max(pending, 0).toFixed(2);
    },
    remainingInvoiceAmount() {
        const invoiceAmount = this.invoice.invoice_amount || 0;

        const complements = Array.isArray(this.invoice.complements)
            ? this.invoice.complements.reduce((sum, comp) => {
                return sum + (parseFloat(comp.amount) || 0);
            }, 0)
            : 0;

        const remaining = Math.max(invoiceAmount - complements, 0);

        // Formatea con comas (ej: 12,345.67)
        return remaining.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
},
methods:{
    dateFormat(date) {
        if (!date) return;

        const formattedDate = format(new Date(date), 'dd MMM yyyy', { locale: es });
        return formattedDate;
    },
    colorStatus(status){
      if ( status == 'Emitida' ) {
            return 'border-[#08688B] text-[#08688B]'
        } else if ( status == 'Pendiente de pago' ) {
            return 'border-[#B8B30E] text-[#B8B30E]'
        } else if ( status == 'Parcialmente pagada' ) {
            return 'border-[#C4620C] text-[#C4620C]'
        } else if ( status == 'Pagada' ) {
            return 'border-green-500 text-green-500'
        } else if ( status == 'Cancelada' ) {
            return 'border-red-500 text-red-500'
        }  
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
}
}
</script>