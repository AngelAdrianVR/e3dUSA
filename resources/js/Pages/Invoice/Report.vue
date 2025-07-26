<template>
    <Head :title="'Reporte'" />
    <main class="p-4 mx-auto" :class="printing ? 'w-full' : 'w-[70%]'">
        
        <!-- header -->
        <section class="flex justify-between">
            <figure class="w-72">
                <img src="@/../../public/images/logo.png" alt="Logo">
            </figure>
            <PrintButton v-if="!printing" @click="print" />
        </section>

            <h2 class="font-bold text-xl">REPORTE DE FACTURAS</h2>
        <!-- body -->
        <section class="mt-8 text-sm">

            <div class="space-y-10">
                <div v-for="(data, clientName) in grouped_data" :key="clientName" class=" p-4 rounded-md">
                    <h2 class="text-xl font-bold mb-3">{{ clientName }}</h2>

                    <!-- Facturas registradas -->
                    <div v-if="data.invoices.length">
                        <h3 class="text-lg font-semibold mb-2 text-secondary">Facturas registradas</h3>
                        <table class="min-w-full table-auto border mb-6">
                        <thead class="bg-gray-200 text-left">
                            <tr>
                            <th class="px-4 py-2">Folio</th>
                            <th class="px-4 py-2">Fecha de emisión</th>
                            <th class="px-4 py-2">Orden de venta</th>
                            <th class="px-4 py-2">Importe</th>
                            <th class="px-4 py-2">Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="invoice in data.invoices" :key="invoice.id" class="border-t">
                            <td class="px-4 py-2">{{ invoice.folio }}</td>
                            <td class="px-4 py-2">{{ formatDate(invoice.issue_date) }}</td>
                            <td class="px-4 py-2">OV-{{ invoice.sale_id }}</td>
                            <td class="px-4 py-2">${{ invoice.invoice_amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                            <td class="px-4 py-2">{{ invoice.status }}</td>
                            </tr>
                        </tbody>
                        </table>
                    </div>

                    <!-- OV sin facturar -->
                    <div v-if="data.ovs_no_invoices.length">
                        <h3 class="text-lg text-secondary font-semibold mb-2">OV terminadas sin facturas</h3>
                        <table class="min-w-full table-auto border mb-6">
                        <thead class="bg-gray-200 text-left">
                            <tr>
                            <th class="px-4 py-2">Folio</th>
                            <th class="px-4 py-2">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="ov in data.ovs_no_invoices" :key="ov.id" class="border-t">
                            <td class="px-4 py-2">OV-{{ ov.id }}</td>
                            <td class="px-4 py-2">{{ formatDate(ov.created_at) }}</td>
                            </tr>
                        </tbody>
                        </table>
                    </div>

                    <!-- Facturas programadas -->
                    <div v-if="data.programed_invoices.length">
                        <h3 class="text-lg text-secondary font-semibold mb-2">Facturas programadas</h3>
                        <table class="min-w-full table-auto border">
                        <thead class="bg-gray-200 text-left">
                            <tr>
                            <th class="px-4 py-2">Folio</th>
                            <th class="px-4 py-2">Orden de venta</th>
                            <th class="px-4 py-2">Fecha programada</th>
                            <th class="px-4 py-2">N° de factura</th>
                            <th class="px-4 py-2">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="pi in data.programed_invoices" :key="pi.id" class="border-t">
                            <td class="px-4 py-2">P-{{ pi.id }}</td>
                            <td class="px-4 py-2">OV-{{ pi.sale_id }}</td>
                            <td class="px-4 py-2">{{ formatDate(pi.reminder_date) }}</td>
                            <td class="px-4 py-2">{{ pi.number_of_invoice }} de {{ pi.invoice_quantity }}</td>
                            <td class="px-4 py-2">${{ pi.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import PrintButton from "@/Components/MyComponents/PrintButton.vue";
import { format, parseISO } from 'date-fns';
import es from 'date-fns/locale/es';
import { Head } from "@inertiajs/vue3";

export default {
data() {
    return {
        printing: false,
    }
},
components:{
    PrintButton,
    Head
},
props:{
    grouped_data: Object
    // invoices: Array,
    // ovs_no_invoices: Array,
    // programmed_invoices: Array
},
methods:{
    formatDate(dateTimeString) {
        if (!dateTimeString) return '';
        return format(parseISO(dateTimeString), 'dd MMM yyy', { locale: es });
    },
    print() {
      this.printing = true;
      setTimeout(() => {
        window.print();
      }, 200);
    },
    handleAfterPrint() {
      this.printing = false;
    },
},
mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
},
beforeDestroy() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
}
}
</script>

<style scoped>
table {
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ddd;
}
</style>
