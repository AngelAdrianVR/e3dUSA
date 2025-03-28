<template>
    <div v-if="loading"
        class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
    </div>
    <div v-if="loading"
        class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
        <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
    </div>
    <AppLayout title="Tarifas">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl leading-tight">
                    Tarifas
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('Crear logistica')"
                    :href="route('shipping-rates.create')">
                <SecondaryButton>Agregar tarifa</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="relative overflow-hidden min-h-[60vh]">
            <div class="lg:w-5/6 mx-auto mt-6">

                <div class="flex justify-between">
                    <!-- pagination -->
                    <div v-if="!search" class="overflow-auto mb-2">
                        <PaginationWithNoMeta :pagination="shipping_rates" class="py-2" />
                    </div>

                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar logistica')"
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
                <el-table :data="filteredShippingRates" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="id" label="ID" width="100" />
                    <el-table-column prop="part_number" label="Numero de parte" width="200" />
                    <el-table-column prop="name" label="Producto" />
                    <el-table-column label="Registro">
                        <template #default="scope">
                            <p>{{ scope.row.shipping_rates?.length }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" >
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item @click="$inertia.get(route('shipping-rates.show', scope.row.shipping_rates[0].id))">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
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
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    return {
        disableMassiveActions: true,
        inputSearch: '',
        search: '',
        loading: false,
        pagination: null,
        filteredShippingRates: this.shipping_rates.data,
    }
},
components:{
    PaginationWithNoMeta,
    SecondaryButton,
    IndexSearchBar,
    AppLayout,
    Link,
},
props:{
    shipping_rates: Object
},
methods:{
    async handleSearch(search) {
        this.search = search;
        this.loading = true;
        try {
            if (!this.search) {
                this.filteredShippingRates = this.shipping_rates.data;
            } else {
                const response = await axios.post(route('shipping-rates.get-matches', { query: this.search }));

                if (response.status === 200) {
                    this.filteredShippingRates = response.data.items.data;
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
            const response = await axios.post(route('shipping-rates.massive-delete', {
                shipping_rates: this.$refs.multipleTableRef.value
            }));

            if (response.status == 200) {
                this.$notify({
                    title: 'Éxito',
                    message: response.data.message,
                    type: 'success'
                });

                // update list of shipping-rates
                let deletedIndexes = [];
                this.shipping_rates.data.forEach((quote, index) => {
                    if (this.$refs.multipleTableRef.value.includes(quote)) {
                        deletedIndexes.push(index);
                    }
                });

                // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                deletedIndexes.sort((a, b) => b - a);

                // Eliminar cotizaciones por índice
                for (const index of deletedIndexes) {
                    this.shipping_rates.data.splice(index, 1);
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
        // console.log(row)
        this.$inertia.get(route('shipping-rates.show', row.shipping_rates[0].id));

        //abrir en otra pestaña
        // const url = this.route('shipping-rates.show', row);
        // window.open(url, '_blank');
    },
    tableRowClassName() {
        return 'cursor-pointer text-xs';
    },
    handleCommand(command) {
        const commandName = command.split('-')[0];
        const rowId = command.split('-')[1];

        this.$inertia.get(route('shipping-rates.' + commandName, rowId));
    },
}
}
</script>