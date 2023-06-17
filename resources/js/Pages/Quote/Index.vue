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
                <el-table-column prop="companyBranch.name" label="Cliente" />
                <el-table-column prop="authorized_user_name" label="Autorizado por" />
                <el-table-column prop="created_at" label="Creado el" />
                <el-table-column align="right" fixed="right">
                    <template #header>
                        <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
                    </template>
                    <template #default="scope">
                        <el-button size="small" type="primary"
                            @click="$inertia.get(route('quotes.show', scope.row.id))">Ver</el-button>
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
            type: Object,
            default: []
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
                    this.quotes.data.forEach((quote, index) => {
                        if (this.$refs.multipleTableRef.value.includes(quote)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.quotes.data.splice(index, 1);
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
            return this.quotes.data.filter(
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