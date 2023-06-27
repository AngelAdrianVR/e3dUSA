<template>
    <div>
        <AppLayout title="Ver catalogo de productos">
            <div class="flex justify-between text-lg mx-14 mt-11">
                <span>Catálogo de productos</span>
                <i class="fa-solid fa-xmark cursor-pointer"></i>
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
                        <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]"><i class="fa-solid fa-pen text-sm"></i></button>
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
                    <div class="border border-b-2 px-7 py-3">
                        Información general
                    </div>
                    <div class="px-7 py-7 text-sm">
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Fecha de Alta</p>
                            <p>05 enero 2020</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
                            <p>001</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Características</p>
                            <p>Llavero MG DV UK, metálico, grabado DALTON Patria, color rojo</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
                            <p>DP35</p>
                        </div>
                        <div class="flex mb-10">
                            <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
                            <p>Piezas</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad miníma permitida en almacén</p>
                            <p>0</p>
                        </div>
                        <div class="flex">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad máxima permitida en almacén</p>
                            <p>300</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
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
        AppLayout,
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
