<template>

    <Head :title="'Reporte pdf'" />
    <div>
        <table class="styled-table">
            <caption>
                Inventario materia prima {{ getCurrentDate() }}
            </caption>
            <thead>
                <tr>
                    <th class="w-1/6 text-start">Número de parte</th>
                    <th class="w-1/6 text-start">Producto</th>
                    <th class="w-1/6 text-start">Cantidad actual</th>
                    <th class="w-1/6 text-start">Cantidad mínima</th>
                    <th class="w-1/6 text-start">Cantidad máxima</th>
                    <th class="w-1/6 text-start">Cantidad a pedir</th>
                    <th class="w-1/6 text-start">Costo por unidad</th>
                    <th class="w-1/6 text-start">$ por pedir</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data" :key="index">
                    <td>{{ item.storageable?.part_number }}</td>
                    <td>{{ item.storageable?.name }}</td>
                    <td :class="{ 'text-red': isQuantityLow(item) }">
                        {{ formatQuantity(item.quantity) }}
                    </td>
                    <td>{{ formatQuantity(item.storageable?.min_quantity) }}</td>
                    <td>{{ formatQuantity(item.storageable?.max_quantity) }}</td>
                    <td>{{ calculateSupplyQuantity(item) }}</td>
                    <td>${{ item.storageable?.cost }}</td>
                    <td>${{ calculateCost(item) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <!-- <td colspan="6" class="footer">TOTAL: ${{ totalCost }}</td> -->
                </tr>
            </tfoot>
        </table>
    </div>
</template>
<script>
import { Head } from '@inertiajs/vue3';

export default {
    components: {
        Head,
    },
    props: {
        data: Array,
    },
    computed: {
        totalCost() {
            let totalCost = 0;
            for (const item of this.data) {
                totalCost += this.calculateCost(item);
            }
            return totalCost;
        },
    },
    methods: {
        getCurrentDate() {
            return new Date().toLocaleDateString('es-ES');
        },
        isQuantityLow(item) {
            return item.quantity <= item.storageable?.min_quantity;
        },
        formatQuantity(quantity) {
            return quantity?.toFixed(2);
        },
        calculateSupplyQuantity(item) {
            return this.isQuantityLow(item) ? item.storageable?.max_quantity - item.quantity : 0;
        },
        calculateCost(item) {
            return (this.calculateSupplyQuantity(item) * item.storageable?.cost).toFixed(2);
        },
        formatTotalCost(totalCost) {
            return totalCost.toFixed(2);
        },
    },
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