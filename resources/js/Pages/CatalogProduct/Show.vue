<template>
    <div class="dark:text-white">
        <AppLayoutNoHeader title="Ver catalogo de productos">
            <div class="flex justify-between text-lg mx-14 mt-11">
                <span>Catálogo de productos</span>
                <Link :href="route('catalog-products.index')"
                    class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] dark:hover:bg-[#191919] hover:!text-primary dark:text-white flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
                </Link>
            </div>
            <div class="flex justify-between mt-5 mx-14">
                <div class="md:w-1/3">
                    <el-select @change="$inertia.get(route('catalog-products.show', selectedCatalogProduct))"
                        v-model="selectedCatalogProduct" clearable filterable placeholder="Buscar producto"
                        no-data-text="No hay productos en el catalogo" no-match-text="No se encontraron coincidencias">
                        <el-option class="w-[700px] text-sm" v-for="item in catalog_products" :key="item.id"
                            :label="item.name" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar catalogo de productos')"
                        content="Editar" placement="top">
                        <Link :href="route('catalog-products.edit', selectedCatalogProduct)">
                        <button
                            class="size-9 flex items-center justify-center rounded-[10px] bg-[#D9D9D9] dark:bg-[#202020] dark:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        </Link>
                    </el-tooltip>
                    <!-- <PrimaryButton class="rounded-lg">Ajustar existencias</PrimaryButton> -->
                    <Dropdown align="right" width="48" v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')
                        && $page.props.auth.user.permissions.includes('Eliminar catalogo de productos')">
                        <template #trigger>
                            <button
                                class="h-9 px-3 rounded-lg bg-[#D9D9D9] dark:bg-[#202020] dark:text-white flex items-center justify-center text-sm">
                                Más <i class="fa-solid fa-chevron-down text-[10px] ml-2 pb-[2px]"></i>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('catalog-products.create')"
                                v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')">
                                Crear nuevo artículo
                            </DropdownLink>
                            <DropdownLink @click="clone" as="button"
                                v-if="$page.props.auth.user.permissions.includes('Crear catalogo de productos')">
                                Clonar artículo
                            </DropdownLink>
                            <!-- <DropdownLink as="button">
                                Marcar como inactivo
                            </DropdownLink> -->
                            <DropdownLink @click="showConfirmModal = true" as="button"
                                v-if="$page.props.auth.user.permissions.includes('Eliminar catalogo de productos')">
                                Eliminar
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
            <div class="lg:grid grid-cols-3 mt-12 border-b-2">
                <div class="px-14">
                    <h2 class="text-xl font-bold text-center mb-6">{{ catalog_product.data.name }}</h2>
                    <div class="flex items-center">
                        <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
                            class="w-full h-60 bg-[#D9D9D9] dark:bg-[#333333] rounded-lg relative flex items-center justify-center">
                            <section v-if="catalog_product.data.media?.length">
                                <img class="object-contain h-60"
                                    :src="catalog_product.data.media[currentImage]?.original_url" alt="">
                                <div v-if="imageHovered" @click="openImage(catalog_product.data.media[0]?.original_url)"
                                    class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
                                    <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
                                </div>
                            </section>
                            <i v-else class="fa-regular fa-image text-gray-400 text-5xl"></i>
                        </figure>
                    </div>
                    <div v-if="catalog_product.data.media?.length > 1"
                        class="mt-3 flex items-center justify-center space-x-3">
                        <i @click="currentImage = index" v-for="(image, index) in catalog_product.data.media?.length"
                            :key="index" :class="index == currentImage ? 'text-black' : 'text-gray-300'"
                            class="fa-solid fa-circle text-xs cursor-pointer"></i>
                    </div>
                    <div class="mt-8 ml-6 text-sm">
                        <div class="flex mb-2">
                            <p class="w-1/3 text-primary">Existencias</p>
                            <p>{{ catalog_product.data.storages[0]?.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                                ",") ?? '0'
                                }} {{ catalog_product.data.measure_unit }}</p>
                        </div>
                        <div class="flex mb-3">
                            <p class="w-1/3 text-primary">Ubicación</p>
                            <p>{{ catalog_product.data.storages[0]?.location ?? '--' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 border-2">
                    <el-tabs v-model="activeTab" class="mx-5 mt-3" @tab-click="handleClickInTab">
                        <el-tab-pane label="Información general" name="1">
                            <General :product="catalog_product.data" />
                        </el-tab-pane>
                        <el-tab-pane label="Precios registrados" name="2">
                            <Prices :product="catalog_product.data" />
                        </el-tab-pane>
                    </el-tabs>
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
                    <div>
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
import Prices from "./Tabs/Prices.vue";
import General from "./Tabs/General.vue";

export default {
    data() {
        return {
            selectedCatalogProduct: '',
            imageHovered: false,
            showConfirmModal: false,
            currentIndexProduct: null,
            currentImage: 0,
            activeTab: '1',
        };
    },
    components: {
        AppLayoutNoHeader,
        ConfirmationModal,
        PrimaryButton,
        CancelButton,
        DropdownLink,
        Dropdown,
        Link,
        General,
        Prices,
    },
    props: {
        catalog_product: Object,
        catalog_products: Object,
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
                    catalog_product_id: this.catalog_product.data.id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.$inertia.get(route('catalog-products.index'));
                    // console.log(response.data.newItem);
                    // this.catalog_products.data.push(response.data.newItem);
                    // this.selectedCatalogProduct = response.data.newItem.id;
                    // this.catalog_product.data = response.data.newItem

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
                    message: err.message + "Actualiza la página",
                    type: 'error'
                });
                console.log(err);
            }
        },
        async deleteItem() {
            try {
                const response = await axios.delete(route('catalog-products.destroy', this.catalog_product.data.id));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.$inertia.get(route('catalog-products.index'));

                    // const index = this.catalog_products.data.findIndex(item => item.id === this.catalog_product.data.id);
                    // if (index !== -1) {
                    //     this.catalog_products.data.splice(index, 1);
                    //     this.selectedCatalogProduct = '';
                    // }

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
        //     previus(){
        //   this.currentIndexProduct -= 1;
        //   this.currentCatalogProduct = this.catalog_products.data[this.currentIndexProduct];
        //   this.selectedCatalogProduct = this.currentCatalogProduct.id;
        // },
        // next(){
        //   this.currentIndexProduct += 1;
        //   this.currentCatalogProduct = this.catalog_products.data[this.currentIndexProduct];
        //   this.selectedCatalogProduct = this.currentCatalogProduct.id;
        // },
    },
    // watch: {
    //     selectedCatalogProduct(newVal) {
    //         this.currentCatalogProduct = this.catalog_products.data.find(item => item.id == newVal);
    //     }
    // },
    mounted() {
        this.selectedCatalogProduct = this.catalog_product.data.id;
        this.setTabInUrl();
        // this.currentIndexProduct = this.catalog_products.data.findIndex((obj) => obj.id == this.selectedCatalogProduct); //para carga dinamica
    }
};
</script>
