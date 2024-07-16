<template>
    <AppLayout title="Cotizaciones">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl leading-tight">
                    Cotizaciones
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')"
                    :href="route('quotes.create')">
                <SecondaryButton>+ Nuevo</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="relative overflow-hidden min-h-[60vh]">
            <div class="flex justify-center space-x-5 mt-5">
                <p class="mr-2 text-xs text-green-500 flex items-center space-x-2">
                    <i class="fa-solid fa-circle"></i>
                    <span>Clilentes</span>
                </p>
                <p class="mr-2 text-xs text-blue-500 flex items-center space-x-2">
                    <i class="fa-solid fa-circle"></i>
                    <span>Prospectos</span>
                </p>
            </div>
            <NotificationCenter module="quote" />
            <div class="lg:w-5/6 mx-auto mt-6">

                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="quotes.length" />
                    </div>
                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar cotizaciones')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="folio" label="Folio" width="100" />
                    <el-table-column v-if="$page.props.auth.user.permissions.includes('Ver utilidades')"
                        label="Utilidad" width="140">
                        <template #default="scope">
                            <SaleProfit :profit="scope.row.profit" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="user.name" label="Creado por" />
                    <el-table-column prop="receiver" label="Receptor" />
                    <el-table-column prop="companyBranch.name" label="Cliente / Prospecto">
                        <template #default="scope">
                            <div class="flex">
                                <p class="mr-2 mt-px text-[6px]"
                                    :class="scope.row.companyBranch.name ? 'text-green-500' : 'text-blue-500'">
                                    <i class="fa-solid fa-circle"></i>
                                </p>
                                <span>{{ scope.row.companyBranch.name ?? scope.row.prospect.name }}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="authorized_user_name" label="Autorizado por" />
                    <el-table-column label="Productos" width="210">
                        <template v-slot="scope">
                            <span>{{ scope.row.catalog_products.map(product => product.name).join(', ') }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="created_at" label="Creado el" width="180" />
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id"><i
                                                class="fa-solid fa-eye"></i>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar cotizaciones')
                                            || scope.row.user.id == $page.props.auth.user.id"
                                            :command="'edit-' + scope.row.id">
                                            <i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')"
                                            :command="'clone-' + scope.row.id"><i class="fa-solid fa-clone"></i>
                                            Clonar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')"
                                            :command="'make_so-' + scope.row.id"><i
                                                class="fa-solid fa-arrows-turn-to-dots"></i>Convertir a
                                            OV</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Autorizar cotizaciones') && !scope.row.authorized_at"
                                            :command="'authorize-' + scope.row.id"><i
                                                class="fa-solid fa-check"></i>Autorizar</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </AppLayout>
</template>
<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import Checkbox from "@/Components/Checkbox.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import SaleProfit from "@/Components/MyComponents/SaleProfit.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        AppLayout,
        TextInput,
        Checkbox,
        NotificationCenter,
        SecondaryButton,
        Link,
        IndexSearchBar,
        SaleProfit,
    },
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
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
        handleSearch(search) {
            this.search = search;
        },
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
        handleRowClick(row) {
            const url = this.route('quotes.show', row);
            window.open(url, '_blank');
        },
        tableRowClassName({ row, rowIndex }) {
            return 'cursor-pointer text-xs';
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

                    this.quotes.unshift(response.data.newItem);

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
        async authorize(quote_id) {
            try {
                const response = await axios.put(route('quotes.authorize', quote_id));

                if (response.status == 200) {
                    const index = this.quotes.findIndex(item => item.id == quote_id);
                    this.quotes[index].authorized_at = response.data.item.authorized_at;
                    this.quotes[index].authorized_user_name = response.data.item.authorized_user_name;
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
            } else if (commandName == 'authorize') {
                this.authorize(rowId);
            } else {
                this.$inertia.get(route('quotes.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.quotes.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.quotes.filter(
                    (quote) =>
                        quote.folio.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.receiver.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.companyBranch.name?.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.prospect.name?.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.catalog_products.some(product => product.name.toLowerCase().includes(this.search.toLowerCase()))
                );
            }
        }
    },
}
</script>