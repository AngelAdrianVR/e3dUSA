<template>
    <Head :title="'Reporte pdf'" />
    <div class="mx-auto bg-gray-100">
        <table class="styled-table">
            <caption class="text-sm my-2 font-bold">
                Información de envíos
            </caption>
            <caption class="text-sm">
                <p>Total gastos cobrados: ${{ getTotalMoneyCollected?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                <p>Total gastos reales: ${{ getTotalMoneySpent?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            </caption>
            <thead>
                <tr>
                    <th class="w-[13%] text-start">folio de venta</th>
                    <th class="w-[13%] text-start">Sucursal</th>
                    <th class="w-[13%] text-start">Gastos de envío cobrado</th>
                    <th class="w-[13%] text-start">Gastos de envío reales</th>
                    <th class="w-[13%] text-start">Paquetería</th>
                    <th class="w-[13%] text-start">Fecha promesa de embarque</th>
                    <th class="w-[13%] text-start">Fecha real de embarque</th>
                    <th class="w-[13%] text-start">status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in sales" :key="index">
                    <td>OV-{{ item.id.toString().padStart(4, '0') }}</td>
                    <td>
                        <p class="pb-2">{{ item.company_branch?.name }}</p>
                    </td>
                    <td>
                        <p class="pb-2">${{ item.freight_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }} {{ item.freight_cost_charged_in_product ? '(Cargado a producto)' : '' }}</p>
                    </td>
                    <td>
                        <div v-for="partiality in item.partialities" :key="partiality">
                            <p class="pb-2">${{ partiality.shipping_cost ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <div v-for="partiality in item.partialities" :key="partiality">
                            <p class="pb-2">{{ partiality.shipping_company ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <div v-for="partiality in item.partialities" :key="partiality">
                            <p class="pb-2">{{ formatDate(partiality.promise_date) ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <div v-for="partiality in item.partialities" :key="partiality">
                            <p class="pb-2">{{ formatDate(partiality.sent_at) ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <p class="pb-2">{{ item.status }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
components: {
    Head,
},
props: {
    sales: Array,
},
methods:{
    formatDate(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
        }
    },  
},
computed:{
    getTotalMoneyCollected() {
        return this.sales.reduce((total, sale) => {
            return total + (sale.freight_cost || 0);
        }, 0);
    },
    getTotalMoneySpent() {
    return this.sales.reduce((total, sale) => {
        if (sale.partialities && sale.partialities.length) {
            return total + sale.partialities.reduce((subtotal, partiality) => {
                return subtotal + (partiality.shipping_cost || 0);
            }, 0);
        }
        return total;
    }, 0);
}
}
}
</script>

<style>
    body {
        font-family: sans-serif;
    }

    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 0.7em;
        font-family: sans-serif;
        width: 100%;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .styled-table thead tr {
        background-color: #d90537;
        color: #ffffff;
        text-align: left;
    }

    .footer {
        background-color: #d90537;
        text-align: right;
        font-weight: bold;
        color: white;
    }

    .text-end {
        text-align: right;
    }

    .text-start {
        text-align: left;
    }

    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .styled-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #d90537;
    }

    .styled-table tbody tr.active-row {
        font-weight: bold;
        color: #d90537;
    }

    .text-red {
        color: red;
    }

    .text-orange {
        color: orange;
    }
</style>
