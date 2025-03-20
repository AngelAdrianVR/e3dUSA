<template>
    <div v-if="loading" class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
    </div>
    <div v-if="loading"
        class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
        <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
    </div>
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

        <div class="relative overflow-hidden min-h-[60vh] dark:text-white">
            <div class="flex justify-center space-x-5 mt-5">
                <p class="mr-2 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle text-green-500"></i>
                    <span>Clilentes</span>
                </p>
                <p class="mr-2 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle text-blue-500"></i>
                    <span>Prospectos</span>
                </p>
            </div>
            <NotificationCenter module="quote" />
            <div class="lg:w-5/6 mx-auto mt-6">

                <div class="flex justify-between">
                    <!-- pagination -->
                    <div v-if="!search" class="overflow-auto mb-2">
                        <PaginationWithNoMeta :pagination="quotes" class="py-2" />
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
                <el-table :data="filteredQuotes" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="folio" label="Folio" width="100">
                        <template #default="scope">
                            <el-tooltip v-if="scope.row.quote_acepted" placement="top">
                                <template #content>
                                    <p>
                                        El cliente firmó la cotización <br>
                                        el {{ scope.row.responded_at }}
                                    </p>
                                </template>
                                <i class="fa-solid fa-check text-[9px] text-green-700 mr-1"></i>
                            </el-tooltip>
                            <el-tooltip v-else-if="scope.row.rejected_razon" placement="top">
                                <template #content>
                                    <p>
                                        El cliente rechazó la cotización <br>
                                        el {{ scope.row.responded_at }} <br>
                                        Motivo: {{ scope.row.rejected_razon }}
                                    </p>
                                </template>
                                <i class="fa-solid fa-check text-[9px] text-green-700 mr-1"></i>
                            </el-tooltip>
                            <span>{{ scope.row.folio }}</span>
                        </template>
                    </el-table-column>
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
                            <span>{{scope.row.catalog_products.map(product => product.name).join(', ')}}</span>
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
                                        <el-dropdown-item :command="'show-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar cotizaciones')
                                            || scope.row.user.id == $page.props.auth.user.id"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')"
                                            :command="'clone-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                            </svg>
                                            Clonar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')"
                                            :command="'make_so-' + scope.row.id"
                                            :disabled="!scope.row.authorized_at">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                                            </svg>
                                            Convertir a OV 
                                        </el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Autorizar cotizaciones') && !scope.row.authorized_at"
                                            :command="'authorize-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            Autorizar
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>

        <ConfirmationModal :show="showConversionConfirm" @close="showConversionConfirm = true">
            <template #title>
                <h1>Convertir cotización a orden de venta</h1>
            </template>
            <template #content>
                <p>
                    Al hacer la conversión se te redireccionará a la OV creada ya que
                    quedarán varios campos sin llenar. <br>
                    Es importante llenar toda la información correspondiente, para evitar
                    futuros problemas.
                </p>
                <div class="flex mt-3">
                    <p class="mr-2 mt-px text-[6px] text-blue-500">
                        <i class="fa-solid fa-circle"></i>
                    </p>
                    <p>
                        Si la cotización fue creada para un prospecto, se convertirá a cliente en automático, lo cual
                        dejará también información pendiente del cliente por llenar.
                    </p>
                </div>
            </template>
            <template #footer>
                <div class="flex items-center justify-end space-x-1">
                    <CancelButton @click="showConversionConfirm = false" :disabled="converting">Cancelar</CancelButton>
                    <PrimaryButton @click="createSO" :disabled="converting">Convertir</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import SaleProfit from "@/Components/MyComponents/SaleProfit.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    components: {
        PaginationWithNoMeta,
        NotificationCenter,
        SecondaryButton,
        PrimaryButton,
        CancelButton,
        IndexSearchBar,
        LoadingLogo,
        SaleProfit,
        AppLayout,
        Link,
        ConfirmationModal,
    },
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            loading: false,
            pagination: null,
            filteredQuotes: this.quotes.data,
            showConversionConfirm: false,
            converting: false,
            selectedQuoteId: null,
        };
    },
    props: {
        quotes: {
            type: Object,
            default: []
        },
    },
    methods: {
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMM, Y', { locale: es }); // Formato personalizado
        },
        async handleSearch(search) {
            this.search = search;
            this.loading = true;
            try {
                if (!this.search) {
                    this.filteredQuotes = this.quotes.data;
                } else {
                    const response = await axios.post(route('quotes.get-matches', { query: this.search }));

                    if (response.status === 200) {
                        this.filteredQuotes = response.data.items;
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
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
        async createSO() {
            try {
                this.converting = true;
                const response = await axios.post(route('quotes.create-so', {
                    quote_id: this.selectedQuoteId
                }));

                if (response.status == 200) {
                    this.$inertia.visit(route('sales.edit', response.data.sale_id));
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
                    const index = this.quotes.data.findIndex(item => item.id == quote_id);
                    this.quotes.data[index].authorized_at = response.data.item.authorized_at;
                    this.quotes.data[index].authorized_user_name = response.data.item.authorized_user_name;
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
                this.selectedQuoteId = rowId;
                this.showConversionConfirm = true;
            } else if (commandName == 'authorize') {
                this.authorize(rowId);
            } else {
                this.$inertia.get(route('quotes.' + commandName, rowId));
            }
        },
    },
}
</script>