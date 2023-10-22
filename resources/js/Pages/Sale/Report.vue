<template>
    <Head :title="'Clientes con ventas'" />
    <div>
        <table class="styled-table">
            <caption>
               Clientes con ventas del último año a hoy
            </caption>
            <thead>
                <tr>
                    <th class="w-1/6 text-start">Cliente</th>
                    <th class="w-1/6 text-start">Número de OVs</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in data" :key="index">
                    <td>{{ item.name }}</td>
                    <td>{{ item.sales_count }}</td>
                </tr>
            </tbody>
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
      
    },
    methods: {
    
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