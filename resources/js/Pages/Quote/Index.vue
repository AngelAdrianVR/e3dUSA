<template>
    <AppLayout title="Cotizaciones">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl leading-tight">
                    Cotizaciones
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')" :href="route('quotes.create')">
                <SecondaryButton>+ Nuevo</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="lg:w-5/6 mx-auto mt-6">
            <div class="flex justify-between">
                <!-- pagination -->
                <div>
                    <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                        :total="quotes.data.length" />
                </div>

                <!-- buttons -->
                <div>
                    <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar cotizaciones')" confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
                        @confirm="deleteSelections">
                        <template #reference>
                            <el-button type="danger" plain class="mb-3"
                                :disabled="disableMassiveActions">Eliminar</el-button>
                        </template>
                    </el-popconfirm>
                </div>
            </div>
            <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="450" style="width: 100%"
                @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="45" />
                <el-table-column prop="folio" label="Folio" width="100" />
                <el-table-column prop="user.name" label="Creado por" />
                <el-table-column prop="receiver" label="Receptor" />
                <el-table-column prop="companyBranch.name" label="Cliente" />
                <el-table-column prop="authorized_user_name" label="Autorizado por" />
                <el-table-column prop="created_at" label="Creado el" width="180" />
                <el-table-column align="right" fixed="right" >
                    <template #header>
                        <TextInput v-model="search" type="search" class="w-full text-gray-600" placeholder="Buscar" />
                    </template>
                    <template #default="scope">
                        <el-dropdown trigger="click" @command="handleCommand">
                            <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </span>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item :command="'show-' + scope.row.id"><i class="fa-solid fa-eye"></i>
                                        Ver</el-dropdown-item>
                                    <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar cotizaciones')" :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                        Editar</el-dropdown-item>
                                    <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')" :command="'clone-' + scope.row.id"><i class="fa-solid fa-clone"></i>
                                        Clonar</el-dropdown-item>
                                    <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')" :command="'make_so-' + scope.row.id"><i
                                            class="fa-solid fa-arrows-turn-to-dots"></i>Convertir a OV</el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
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
            // pagination
            itemsPerPage: 10,
            start: 0,
            end: 10,
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
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
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

        handleRowClick(row) {
            this.$inertia.get(route('quotes.show', row));
        },

        tableRowClassName({ row, rowIndex }) {
            if (row.status === 1) {
                return 'text-green-600 cursor-pointer';
            }

            return 'cursor-pointer';
        },
        async clone(quote_id) {
            try {
                const response = await axios.post(route('quotes.clone', {
                    quote_id: quote_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.quotes.data.unshift(response.data.newItem);

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
        async createSO(quote_id) {
            try {
                const response = await axios.post(route('quotes.create-so', {
                    quote_id: quote_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
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
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                this.createSO(rowId);
            } else {
                this.$inertia.get(route('quotes.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.quotes.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.quotes.data.filter(
                    (quote) =>
                        quote.folio.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.receiver.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.companyBranch.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
    },
}
</script>