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
                    <th class="w-1/6 text-start"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in company_branches" :key="index">
                    <td>{{ item.name }}</td>
                    <td>
                        <div class="flex items-center space-x-2 pb-2" v-for="contact in item.contacts" :key="contact">
                            <!-- Input para editar y texto original -->
                            <p v-if="indexEdit != index">{{ contact.name }}</p>
                            <el-input v-else v-model="form.name" class="!w-48" placeholder="Ingresa el telefono" clearable />
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2 pb-2" v-for="contact in item.contacts" :key="contact">
                            <!-- Input para editar y texto original -->
                            <p v-if="indexEdit != index">{{ contact.phone }}</p>
                            <el-input v-else v-model="form.phone" class="!w-36" placeholder="Ingresa el telefono" clearable />                            
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2 pb-2" v-for="contact in item.contacts" :key="contact">
                            <!-- Input para editar y texto original -->
                            <p v-if="indexEdit != index">{{ contact.email }}</p>
                            <el-input v-else v-model="form.email" class="!w-52" placeholder="Ingresa el email" clearable />
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center space-x-2 pb-2" v-for="contact in item.contacts" :key="contact">
                            <!-- Input para editar y texto original -->
                            <p v-if="indexEdit != index">{{ contact.charge }}</p>
                            <el-input v-else v-model="form.charge" class="!w-52" placeholder="Ingresa el cargo" clearable />
                        </div>
                    </td>
                    <td>
                        <div v-for="contact in item.contacts" :key="contact">
                            <p v-if="indexEdit != index">{{ contact.birthdate_day }} {{ getMonthName(contact.birthdate_month) }}</p>

                            <div v-else class="grid grid-cols-2 gap-2 w-full">
                                <el-select v-model="form.birthdate_day" placeholder="Dia">
                                    <el-option v-for="day in 31" :key="day" :label="day" :value="day" />
                                </el-select>

                                <el-select v-model="form.birthdate_month" placeholder="Mes">
                                    <el-option v-for="(month, index) in months" :key="index" :label="month" :value="index" :disabled="index == 0" />
                                </el-select>
                            </div>
                            {{ form.birthdate_month }}
                        </div>
                    </td>
                    <td>
                        <div v-for="contact in item.contacts" :key="contact">

                            <!-- Boton para editar -->
                            <button v-if="indexEdit != index" @click="handleEdit(index, contact)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </button>

                            <!-- Botones de confirmacion y cancelar edicion -->
                            <div v-else class="flex items-center space-x-2">
                                <button @click="updateContact(contact)" class="rounded-full size-6 flex items-center justify-center text-green-500 border border-gray-400">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </button>
                                <button @click="handleCancelEdit()" class="rounded-full size-6 flex items-center justify-center text-red-500 border border-gray-400">
                                    <i class="fa-solid fa-x text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        name: null,
        phone: null,
        email: null,
        charge: null,
        birthdate_day: null,
        birthdate_month: null,
    });
    return {
        form,
        indexEdit: null,
        months: [
        "-- Selecciona --",
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre",
      ],
    }
},
components: {
    Head,
},
props: {
    company_branches: Array,
},
methods:{
    getMonthName(month) {
        switch (month) {
            case 0: return '';
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
    },
    updateContact(contact) {
        this.form.put(route('contacts.update', contact.id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "",
                    type: "success",
                });
                this.indexEdit = null;
                this.form.reset();
            }
        });
    },
    handleCancelEdit() {
        this.indexEdit = null;
    },
    handleEdit(index, contact) {
        this.form.name = contact.name;
        this.form.phone = contact.phone;
        this.form.email = contact.email;
        this.form.charge = contact.charge;
        this.form.birthdate_day = contact.birthdate_day;
        this.form.birthdate_month = contact.birthdate_month;
        this.indexEdit = index;
    },
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
