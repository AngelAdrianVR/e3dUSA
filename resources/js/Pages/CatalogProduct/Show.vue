<template>
    <div>
        <AppLayoutNoHeader title="Ver catalogo de productos">
            <div class="flex justify-between text-lg mx-14 mt-11">
                <span>Catálogo de productos</span>
                <Link :href="route('catalog-products.index')"
                    class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
                </Link>
            </div>

            <div class="flex justify-between mt-5 mx-14">
                <div class="w-1/3">
                    <el-select v-model="item_search" clearable filterable placeholder="Buscar producto"
                        no-data-text="No hay productos en el catalogo" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in catalog_products" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <el-tooltip content="Editar" placement="top">
                        <Link :href="route('catalog-products.edit', catalog_product)">
                        <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]"><i class="fa-solid fa-pen text-sm"></i></button>
                        </Link>
                    </el-tooltip>
                    <PrimaryButton class="rounded-lg">Ajustar existencias</PrimaryButton>
                    <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">Más <i
                            class="fa-solid fa-chevron-down text-[11px] ml-2"></i></button>
                </div>
            </div>
            <div class="lg:grid grid-cols-3 mt-12 border-b-2">
                <div class="px-14">
                    <h2 class="text-xl font-bold text-center mb-6">{{ catalog_product.name }}</h2>
                    <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
                        class="w-full h-60 bg-[#D9D9D9] rounded-lg relative">
                        <div v-if="imageHovered"
                            class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
                            <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
                        </div>
                    </figure>
                    <div class="mt-8 ml-6">
                        <div class="flex mb-2">
                            <p class="w-1/3 text-primary">Existencias</p>
                            <p>250</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-primary">Ubicación</p>
                            <p>Almacén</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 border-2">
                    <div class="border-b-2 px-7 py-3">
                        Información general
                    </div>
                    <div class="px-7 py-7 text-sm">
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Fecha de Alta</p>
                            <p>{{ catalog_product.created_at }}</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
                            <p>{{ catalog_product.id }}</p>
                        </div>
                        <div v-if="catalog_product.features !== null" class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Características</p>
                            <p>{{ catalog_product.features }}</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
                            <p>{{ catalog_product.part_number }}</p>
                        </div>
                        <div class="flex mb-10">
                            <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
                            <p>{{ catalog_product.measure_unit }}</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad miníma permitida en almacén</p>
                            <p>{{ catalog_product.min_quantity }}</p>
                        </div>
                        <div class="flex">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad máxima permitida en almacén</p>
                            <p>{{ catalog_product.max_quantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayoutNoHeader>
    </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
    data() {
        return {
            item_search: '',
            imageHovered: false,
        };
    },
    components: {
        AppLayoutNoHeader,
        PrimaryButton,
        Link,
    },
    props: {
        catalog_product: Object,
        catalog_products: Array,
    },
    methods: {
        showOverlay() {
            this.imageHovered = true;
        },
        hideOverlay() {
            this.imageHovered = false;
        }
    },
};
</script>
