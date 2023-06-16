<template>
    <AppLayout title="Cotizaciones">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Cotizaciones
                </h2>
                <Link :href="route('quotes.create')">
                    <SecondaryButton>+ Nuevo</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="lg:w-5/6 mx-auto mt-6">
            <div class="flex space-x-2 justify-end">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
                    @confirm="deleteSelections">
                    <template #reference>
                        <el-button type="danger" plain class="mb-3" :disabled="disableMassiveActions">Eliminar</el-button>
                    </template>
                </el-popconfirm>
            </div>

            <el-table :data="filteredTableData" max-height="450" style="width: 100%"
                @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="45" />
                <el-table-column prop="folio" label="Folio" width="85" />
                <el-table-column prop="user.name" label="Creado por" />
                <el-table-column prop="receiver" label="Receptor" />
                <el-table-column prop="company_branch.name" label="Cliente" />
                <el-table-column prop="authorized_user_name" label="Autorizado por" />
                <el-table-column prop="created_at" label="Enviado el" />
                <el-table-column align="right" fixed="right">
                    <template #header>
                        <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
                    </template>
                    <template #default="scope">
                        <el-button size="small" type="primary"
                            @click="createQuote(scope.$index, scope.row)">Clonar</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </AppLayout>
</template>
<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        AppLayout,
        TextInput,
        SecondaryButton,
        Link,
    },
    data() {
        return {
            disableMassiveActions: true,
            search: '',
        };
    },
    props: {
        quotes: {
            type: Array,
            default: [
                {
                    id: '1',
                    folio: 'COT-001',
                    receiver: 'Alexis Llanos',
                    department: 'Mercadotecnia',
                    tooling_cost: 800.00,
                    freight_cost: 350.00,
                    first_production_days: '12 días laborales',
                    notes: 'Notas adicionales para la cotización en general',
                    currency: '$MXN',
                    authorized_user_name: 'Maribel Ortíz',
                    authorized_at: '11 Jun., 2023 05:16 pm.',
                    is_spanish_template: true,
                    company_branch: {
                        name: 'Dalton Honda'
                    },
                    user: {
                        name: 'Miguel Vázquez'
                    },
                    created_at: '11 Jun., 2023 01:11 pm.',
                    sale: null,
                    products: [
                        {
                            name: 'Dalton honda diseño 2',
                            cost: 21.55,
                            features:
                            {
                                family: 'Porta placas',
                                material: 'ABS',
                            },
                        },
                    ],
                },
                {
                    id: '2',
                    folio: 'COT-002',
                    receiver: 'Anguel Vazquez',
                    department: 'Ventas',
                    tooling_cost: 700.00,
                    freight_cost: 350.00,
                    first_production_days: '10 días laborales',
                    notes: 'Notas adicionales para la cotización en general',
                    currency: '$MXN',
                    authorized_user_name: 'Maribel Ortíz',
                    authorized_at: '11 Jun., 2023 05:16 pm.',
                    is_spanish_template: true,
                    company_branch: {
                        name: 'Tesla Nuevo León'
                    },
                    user: {
                        name: 'Miguel Vázquez'
                    },
                    created_at: '11 Jun., 2023 01:11 pm.',
                    sale: null,
                    products: [
                        {
                            name: 'Metalico tesla',
                            cost: 41.55,
                            features:
                            {
                                family: 'Llavero',
                                material: 'Metal cromado',
                            },
                        },
                    ],
                },
            ]
        },
    },
    methods: {
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('quotes.massive-delete', {
                    quotes: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.quotes.forEach((quote, index) => {
                        if (this.$refs.multipleTableRef.value.includes(quote)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.quotes.splice(index, 1);
                    }

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                        title: 'Algo salió mal',
                        message: err.message,
                        type: 'error'
                    });
                console.log(err);
            }
        },
        tableRowClassName({ row, rowIndex }) {
            if (row.status === 1) {
                return 'text-green-600';
            }

            return '';
        },
        createQuote(index, message) {
            console.log(message)
        }
    },
    computed: {
        filteredTableData() {
            return this.quotes.filter(
                (quote) =>
                    !this.search ||
                    quote.folio.toLowerCase().includes(this.search.toLowerCase()) ||
                    quote.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    quote.receiver.toLowerCase().includes(this.search.toLowerCase()) ||
                    quote.company_branch.name.toLowerCase().includes(this.search.toLowerCase())

            )
        }
    },
}
</script>