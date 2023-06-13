<template>
    <AppLayout title="Cotizaciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Cotizaciones
            </h2>
        </template>

        <div class="lg:w-5/6 mx-auto mt-6">
            <div class="flex space-x-2 justify-end">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                    title="Continuar con la eliminacion?" @confirm="deleteSelections">
                    <template #reference>
                        <el-button type="danger" plain class="mb-3" :disabled="disableMassiveActions">Eliminar</el-button>
                    </template>
                </el-popconfirm>
            </div>

            <el-table :data="filteredTableData" max-height="450" style="width: 100%" @selection-change="handleSelectionChange"
                ref="multipleTableRef" :row-class-name="tableRowClassName">
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

export default {
    components: {
        AppLayout,
        TextInput,
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
        /*  async markAsDispatched() {
             try {
                 const response = await axios.post(route('messages.mark-as-dispatched', {
                     messages: this.$refs.multipleTableRef.value
                 }));
 
                 if (response.status == 200) {
                     this.toast.success(response.data.message);
 
                     // change status to selected items
                     this.$refs.multipleTableRef.value.forEach(element => {
                         element.status = 1;
                     });
 
                 } else {
                     this.toast.error(response.data.message);
                 }
 
             } catch (err) {
                 this.toast.error(err);
                 console.log(err);
             }
         }, */
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
                const response = await axios.post(route('messages.massive-delete', {
                    messages: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    // let indexes = [];
                    this.toast.success(response.data.message);

                    // update list of messages
                    let deletedIndexes = [];
                    this.messages.forEach((message, index) => {
                        if (this.$refs.multipleTableRef.value.includes(message)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar mensajes por índice
                    for (const index of deletedIndexes) {
                        this.messages.splice(index, 1);
                    }

                } else {
                    this.toast.error(response.data.message);
                }

            } catch (err) {
                this.toast.error(err);
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