<template>
    <div class="dark:text-white">
        <AppLayoutNoHeader title="Órdenes de producción">
            <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
                <div class="flex justify-between">
                    <label class="text-lg">Órdenes de producción</label>
                    <Link :href="route('productions.index')"
                        class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                    </Link>
                </div>
                <div class="flex justify-between">
                    <div class="w-1/3">
                        <el-select @change="$inertia.get(route('productions.show', selectedSale))"
                            v-model="selectedSale" clearable filterable placeholder="Buscar orden de producción"
                            no-data-text="No hay órdenes registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in sales" :key="item.id"
                                :label="'OP-' + item.id + ' | ' + item.company_branch.name" :value="item.id" />
                        </el-select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <el-tooltip v-if="$page.props.auth.user?.permissions.includes('Ordenes de produccion todas')"
                            content="Editar" placement="top">
                            <Link :href="route('productions.edit', selectedSale)">
                            <button class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                            </Link>
                        </el-tooltip>
                    </div>
                </div>
            </div>
            <h1 class="font-bold text-lg mb-4 flex items-center justify-center space-x-3">
                <el-tooltip v-if="sale.data.is_sale_production" content="Orden de venta" placement="top">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </el-tooltip>
                <el-tooltip v-else content="Orden de stock" placement="top">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-rose-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                </el-tooltip>
                <span> {{ sale.data?.folio.replace('OV', 'OP') + ' | ' + sale.data?.company_branch?.name }}</span>
            </h1>

            <!-- Tabs -->
            <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
                <el-tab-pane label="Datos de la órden" name="1">
                    <General :sale="sale.data" />
                </el-tab-pane>
                <el-tab-pane label="Productos" name="2">
                    <Products :sale="sale.data" :qualities="qualities" />
                </el-tab-pane>
                <el-tab-pane label="Hojas viajeras" name="3">
                    <Traveler :sale="sale.data" />
                </el-tab-pane>
            </el-tabs>
        </AppLayoutNoHeader>
    </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import Traveler from "./Tabs/Traveler.vue";
import { Link, useForm } from "@inertiajs/vue3";
import General from "./Tabs/General.vue";
import Products from "./Tabs/Products.vue";

export default {
    data() {
        const form = useForm({
            expected_end_at: null,
        });

        return {
            form,
            selectedSale: "",
            activeTab: '1',
        };
    },
    props: {
        sale: Object,
        sales: Object,
        qualities: Object,
    },
    components: {
        AppLayoutNoHeader,
        Dropdown,
        Traveler,
        Link,
        General,
        Products,
    },
    methods: {
        handleClickInTab(tab) {
            // Agrega la variable currentTab=tab.props.name a la URL para mejorar la navegacion al actalizar o cambiar de pagina
            const currentURL = new URL(window.location.href);
            currentURL.searchParams.set('currentTab', tab.props.name);
            // Actualiza la URL
            window.history.replaceState({}, document.title, currentURL.href);
        },
        setTabInUrl() {
            // Obtener la URL actual
            const currentURL = new URL(window.location.href);
            // Extraer el valor de 'currentTab' de los parámetros de búsqueda
            const currentTabFromURL = currentURL.searchParams.get('currentTab');

            if (currentTabFromURL) {
                this.activeTab = currentTabFromURL;
            }
        },
    },
    mounted() {
        this.selectedSale = this.sale.data.id;
        this.setTabInUrl();
    },
};
</script>