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
                <p>Balance total de envíos: <span>{{ ( getTotalMoneyCollected - getTotalMoneySpent) > 0 ? '+' : '' }}</span> ${{ ( getTotalMoneyCollected - getTotalMoneySpent).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            </caption>
            <thead>
                <tr>
                    <th class="w-[9%] text-start">folio de venta</th>
                    <th class="w-[11%] text-start">Sucursal</th>
                    <th class="w-[11%] text-start">Gastos de envío cobrado</th>
                    <th class="w-[11%] text-start">Gastos de envío reales</th>
                    <th class="w-[11%] text-start">Forma de envío</th>
                    <th class="w-[11%] text-start">Guía</th>
                    <th class="w-[11%] text-start">Paquetería</th>
                    <th class="w-[11%] text-start">Fecha promesa de embarque</th>
                    <th class="w-[11%] text-start">Fecha real de embarque</th>
                    <th class="w-[11%] text-start">status</th>
                    <th class="w-[17%] text-start">balance total</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in sales" :key="index">
                    <td>
                        OV-{{ item.id.toString().padStart(4, '0') }} 
                        <i @click="collapseIndex[index] == true ? collapseIndex[index] = false : collapseIndex[index] = true" v-if="item.partialities?.length > 1" 
                            class="fa-solid cursor-pointer ml-2 p-1" :class="collapseIndex[index] == true ? 'fa-angle-down' : 'fa-angle-up'">
                        </i>
                    </td>
                    <td>
                        <p class="pb-2">{{ item.company_branch?.name }}</p>
                    </td>
                    <td>
                        <p class="pb-2">${{ item.freight_cost?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }} {{ item.freight_cost_charged_in_product ? '(Cargado a producto)' : '' }}</p>
                    </td>
                    <td>
                        <p v-if="collapseIndex[index] !== true" class="pb-2">${{ item.partialities?.length ? item.partialities[0].shipping_cost : '-' }}</p>
                        <div v-else v-for="(partiality, index) in item.partialities" :key="index">
                            <p class="pb-2">${{ partiality.shipping_cost ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <p class="pb-2">{{ item.freight_option ?? '-' }}</p>
                    </td>
                    <td>
                        <p v-if="collapseIndex[index] !== true" class="pb-2">{{ item.partialities?.length ? item.partialities[0].tracking_guide : '-' }}</p>
                        <div v-else v-for="(partiality, index) in item.partialities" :key="index">
                            <p class="pb-2">{{ partiality.tracking_guide ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <p v-if="collapseIndex[index] !== true" class="pb-2">{{ item.partialities?.length ? item.partialities[0].shipping_company : '-' }}</p>
                        <div v-else v-for="(partiality, index) in item.partialities" :key="index">
                            <p class="pb-2">{{ partiality.shipping_company ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <p v-if="collapseIndex[index] !== true" class="pb-2">{{ item.partialities?.length ? formatDate(item.partialities[0].promise_date) : '-' }}</p>
                        <div v-else v-for="(partiality, index) in item.partialities" :key="index">
                            <p class="pb-2">{{ formatDate(partiality.promise_date) ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <p v-if="collapseIndex[index] !== true" class="pb-2">{{ item.partialities?.length ? formatDate(item.partialities[0].sent_at) : '-' }}</p>
                        <div v-else v-for="(partiality, index) in item.partialities" :key="index">
                            <p class="pb-2">{{ formatDate(partiality.sent_at) ?? '-' }}</p>
                        </div>
                    </td>
                    <td>
                        <p class="pb-2">{{ item.status }}</p>
                    </td>
                    <td>
                        <p class="pb-2 flex"><span>{{ ( item.freight_cost - realShippingCost(item)) > 0 ? '+' : '' }}</span> ${{ ( item.freight_cost - realShippingCost(item))?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
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
data() {
    return {
        collapseIndex: [],
    }
},
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
            return format(parsedDate, 'dd MMM yyyy', { locale: es }); // Formato personalizado
        }
    },
    realShippingCost(sale) {
    return sale.partialities?.reduce((total, partiality) => {
        return total + (partiality.shipping_cost || 0);
    }, 0);
}
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
