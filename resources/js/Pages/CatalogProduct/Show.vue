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
                <div class="md:w-1/3">
                    <el-select v-model="selectedCatalogProduct" clearable filterable placeholder="Buscar producto"
                        no-data-text="No hay productos en el catalogo" no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in catalog_products.data" :key="item.id" :label="item.name"
                            :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar catalogo de productos')"
                        content="Editar" placement="top">
                        <Link :href="route('catalog-products.edit', selectedCatalogProduct)">
                        <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]"><i class="fa-solid fa-pen text-sm"></i></button>
                        </Link>
                    </el-tooltip>
                    <!-- <PrimaryButton class="rounded-lg">Ajustar existencias</PrimaryButton> -->
                    <Dropdown align="right" width="48"
                        v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos') 
                        && $page.props.auth.user.permissions.includes('Eliminar catalogo de productos')">
                        <template #trigger>
                            <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">Más <i
                                    class="fa-solid fa-chevron-down text-[11px] ml-2"></i></button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('catalog-products.create')" v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')">
                                Crear nuevo artículo
                            </DropdownLink>
                            <DropdownLink @click="clone" as="button" v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')">
                                Clonar artículo
                            </DropdownLink>
                            <!-- <DropdownLink as="button">
                                Marcar como inactivo
                            </DropdownLink> -->
                            <DropdownLink @click="showConfirmModal = true" as="button" v-if="$page.props.auth.user.permissions.includes('Eliminar catalogo de productos')">
                                Eliminar
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
            <div class="lg:grid grid-cols-3 mt-12 border-b-2">
                <div class="px-14">
                    <h2 class="text-xl font-bold text-center mb-6">{{ currentCatalogProduct?.name }}</h2>
                    <div class="flex items-center">
                    <i :class="currentIndexProduct == 0 ? 'hidden' : 'block'" @click="previus" class="fa-solid fa-chevron-left mr-4 text-lg text-gray-600 cursor-pointer p-1 rounded-full"></i>
                    <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
                        class="w-full h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center">
                        <el-image style="height: 100%; " :src="currentCatalogProduct?.media[0]?.original_url" fit="fit">
                            <template #error>
                                <div class="flex justify-center items-center text-[#ababab]">
                                    <i class="fa-solid fa-image text-6xl"></i>
                                </div>
                            </template>
                        </el-image>
                        <div v-if="imageHovered" @click="openImage(currentCatalogProduct?.media[0]?.original_url)"
                            class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
                            <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
                        </div>
                    </figure>
                    <i :class="currentIndexProduct == catalog_products.data.length - 1 ? 'hidden' : 'block'" @click="next" class="fa-solid fa-chevron-right ml-4 text-lg text-gray-600 cursor-pointer p-1 mb-2 rounded-full"></i>
                    </div>
                    <div class="mt-8 ml-6 text-sm">
                        <div class="flex mb-2">
                            <p class="w-1/3 text-primary">Existencias</p>
                            <p>{{ currentCatalogProduct?.storages[0]?.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '0' }} {{ currentCatalogProduct?.measure_unit }}</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-primary">Ubicación</p>
                            <p>{{ currentCatalogProduct?.storages[0]?.location ?? '--' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 border-2">
                    <div class="border-b-2 px-7 py-3">
                        Información general
                    </div>
                    <div class="px-7 py-7 text-sm">
                        <div class="flex space-x-2 mb-6">
                            <p class="w-1/3 text-[#9A9A9A]">Fecha de Alta</p>
                            <p>{{ currentCatalogProduct?.created_at }}</p>
                        </div>
                        <div class="flex mb-2 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
                            <p>{{ currentCatalogProduct?.id }}</p>
                        </div>
                        <div class="flex mb-6 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Características</p>
                            <!-- <p>{{ currentCatalogProduct?.features?.raw.join(', ') }}</p> -->
                        </div>
                        <div class="flex mb-2 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
                            <p>{{ currentCatalogProduct?.part_number }}</p>
                        </div>
                        <div class="flex mb-6 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
                            <p>{{ currentCatalogProduct?.measure_unit }}</p>
                        </div>
                        <div class="flex mb-6 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Costo de producción</p>
                            <p class="text-[#4FC03D]">{{ currentCatalogProduct?.cost.number_format }}</p>
                        </div>
                        <div class="flex mb-2 space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad miníma permitida en almacén</p>
                            <p>{{ currentCatalogProduct?.min_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ currentCatalogProduct?.measure_unit }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <p class="w-1/3 text-[#9A9A9A]">Cantidad máxima permitida en almacén</p>
                            <p>{{ currentCatalogProduct?.max_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ currentCatalogProduct?.measure_unit }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
                <template #title>
                    Eliminar producto de catálogo
                </template>
                <template #content>
                    Continuar con la eliminación?
                </template>
                <template #footer>
                    <div class="">
                        <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
                        <PrimaryButton @click="deleteItem">Eliminar</PrimaryButton>
                    </div>
                </template>
            </ConfirmationModal>
        </AppLayoutNoHeader>
    </div>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { Link } from "@inertiajs/vue3";

export default {
    data() {
        return {
            selectedCatalogProduct: '',
            currentCatalogProduct: null,
            imageHovered: false,
            showConfirmModal: false,
            currentIndexProduct: null,
        };
    },
    components: {
        AppLayoutNoHeader,
        PrimaryButton,
        CancelButton,
        Link,
        DropdownLink,
        Dropdown,
        ConfirmationModal,
    },
    props: {
        catalog_product: Object,
        catalog_products: Object,
    },
    methods: {
        showOverlay() {
            this.imageHovered = true;
        },
        hideOverlay() {
            this.imageHovered = false;
        },
        openImage(url) {
            window.open(url, '_blank');
        },
        async clone() {
            try {
                const response = await axios.post(route('catalog-products.clone', {
                    catalog_product_id: this.currentCatalogProduct?.id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                    console.log(response.data.newItem);
                    this.catalog_products.data.push(response.data.newItem);
                    this.selectedCatalogProduct = response.data.newItem.id;
                    this.currentCatalogProduct = response.data.newItem

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
        async deleteItem() {
            try {
                const response = await axios.delete(route('catalog-products.destroy', this.currentCatalogProduct?.id));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    const index = this.catalog_products.data.findIndex(item => item.id === this.currentCatalogProduct.id);
                    if (index !== -1) {
                        this.catalog_products.data.splice(index, 1);
                        this.selectedCatalogProduct = '';
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
            } finally {
                this.showConfirmModal = false;
            }
        },
        previus(){
      this.currentIndexProduct -= 1;
      this.currentCatalogProduct = this.catalog_products.data[this.currentIndexProduct];
      this.selectedCatalogProduct = this.currentCatalogProduct.id;
    },
    next(){
      this.currentIndexProduct += 1;
      this.currentCatalogProduct = this.catalog_products.data[this.currentIndexProduct];
      this.selectedCatalogProduct = this.currentCatalogProduct.id;
    },
    },
    watch: {
        selectedCatalogProduct(newVal) {
            this.currentCatalogProduct = this.catalog_products.data.find(item => item.id == newVal);
        }
    },
    mounted() {
        this.selectedCatalogProduct = this.catalog_product.id;
        this.currentIndexProduct = this.catalog_products.data.findIndex((obj) => obj.id == this.selectedCatalogProduct);
    }
};
</script>
