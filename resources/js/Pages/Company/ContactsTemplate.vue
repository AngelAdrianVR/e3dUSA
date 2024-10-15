<template>
    <Head :title="'Reporte pdf'" />
    <div class="mx-auto">
        <table class="styled-table">
            <caption class="text-sm">
                Información de contactos
            </caption>
            <thead>
                <tr>
                    <th class="w-1/6 text-start">Sucursal</th>
                    <th class="w-1/6 text-start">Nombre de contactacto</th>
                    <th class="w-1/6 text-start">Teléfono de contacto</th>
                    <th class="w-1/6 text-start">Correo de contacto</th>
                    <th class="w-1/6 text-start">Cargo</th>
                    <th class="w-1/6 text-start">cumpleaños</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in company_branches" :key="index">
                    <td>{{ item.name }}</td>
                    <td>
                        <div v-for="contact in item.contacts" :key="contact">
                            <p class="pb-2">{{ contact.name }}</p>
                        </div>
                    </td>
                    <td>
                        <div v-for="contact in item.contacts" :key="contact">
                            <p class="pb-2">{{ contact.phone }}</p>
                        </div>
                    </td>
                    <td>
                        <div v-for="contact in item.contacts" :key="contact">
                            <p class="pb-2">{{ contact.email }}</p>
                        </div>
                    </td>
                    <td>
                        <div v-for="contact in item.contacts" :key="contact">
                            <p class="pb-2">{{ contact.charge }}</p>
                        </div>
                    </td>
                    <td>
                        <div v-for="contact in item.contacts" :key="contact">
                            <p>{{ contact.birthdate_day }} {{ getMonthName(contact.birthdate_day) }}</p>
                        </div>
                    </td>
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
    company_branches: Array,
},
methods:{
    getMonthName(month) {
        switch (month) {
            case 1: return 'Enero';
            case 2: return 'Febrero';
            case 3: return 'Marzo';
            case 4: return 'Abril';
            case 5: return 'Mayo';
            case 6: return 'Junio';
            case 7: return 'Julio';
            case 8: return 'Agosto';
            case 9: return 'Septiembre';
            case 10: return 'Octubre';
            case 11: return 'Noviembre';
            case 12: return 'Diciembre';
        }
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
