<template>
    <AppLayoutNoHeader title="Envíos">
        <main class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1 text-sm">
            <section class="flex justify-between mb-5">
                <label class="text-lg">Detalles de tarifa</label>

                <Link :href="route('shipping-rates.index')"
                    class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </Link>
            </section>

            <section class="flex justify-between">
                <div class="w-1/2 md:w-1/3 mr-2">
                    <el-select @change="getShippingRates(catalogProductSelected)" v-model="catalogProductSelected"
                        filterable placeholder="Buscar registro de tarifas" no-data-text="No hay opciones registradas"
                        no-match-text="No se encontraron coincidencias">
                        <el-option v-for="item in shipping_rates" :key="item.id" :label="item.name" :value="item.id">
                            <p class="flex items-center justify-between space-x-4">
                                <span>{{ item.name }}</span>
                                <span class="text-[10px] text-gray99">({{ item.part_number }})</span>
                            </p>
                        </el-option>
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <PrimaryButton @click="$inertia.get(route('shipping-rates.create'))">Registrar tarifa</PrimaryButton>
                </div>
            </section>

            <LoadingLogo v-if="loading" />

            <section v-else class="pt-7 md:grid grid-cols-3 gap-8">
                <!-- lado izquierdo del grid -->
                <article class="col-span-1">
                    <h1 class="text-lg font-bold text-center">{{ shipping_rate.catalog_product?.name }}</h1>

                    <figure class="flex justify-center items-center mt-5 h-60 bg-gray-200 rounded-lg">
                        <img v-if="shipping_rate.catalog_product?.media?.length" class="border rounded-md object-contain h-full" :src="shipping_rate.catalog_product?.media[0]?.original_url" alt="">
                        <i v-else class="fa-regular fa-image text-gray-300 text-5xl"></i>
                    </figure>
                </article>

                <!-- lado derecho del grid -->
                <article class="col-span-2">
                    <p class="mt-2 font-bold ml-7">No. Parte: <span @click="openCatalogPoduct(shipping_rate.catalog_product?.id)" class="text-secondary underline cursor-pointer font-normal ml-5">{{ shipping_rate.catalog_product?.part_number }}</span></p>

                    <!-- tarjeta de empaque -->
                    <div v-for="(item, index) in shipping_rate.catalog_product?.shipping_rates" :key="index" class="border border-[#CCCCCC] py-4 rounded-lg mt-5">
                        <div class="flex justify-between px-7">
                            <p class="font-bold">Registro {{ (index + 1) }}</p>
                            <div class="flex items-center space-x-3">
                                <button @click="$inertia.get(route('shipping-rates.edit', item.id))" class="size-7 flex items-center justify-center rounded-full text-primary bg-[#cecccc]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button @click="showConfirmModal = true; itemToDelete = item" class="size-7 flex items-center justify-center rounded-full text-primary bg-[#cecccc]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-7 space-y-1 mt-1">
                            <p>Cantidad: <span class="ml-16">{{ item.quantity }} unidades</span></p>
                            <p>Cajas necesarias: <span class="ml-4">{{ item.boxes?.length }}</span></p>
                        </div>
                        
                        <!-- Tabla -->
                        <div class="overflow-x-auto mt-4">
                            <table class="min-w-full">
                                <thead class="bg-[#373737] text-white">
                                    <tr class="*:px-4 *:py-2 *:text-left">
                                        <th>#Caja</th>
                                        <th>Tamaño de caja</th>
                                        <th>Dimensiones</th>
                                        <th>Peso</th>
                                        <th>Piezas</th>
                                        <th>Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in item.boxes" :key="item" class="*:px-4 *:py-2">
                                        <td>{{ (index + 1) }}</td>
                                        <td>{{ item.name ?? '-' }}</td>
                                        <td>{{ item.length + 'x' + item.width + 'x' + item.height }} cm</td>
                                        <td>{{ item.weight }} kg</td>
                                        <td>{{ item.quantity }} piezas</td>
                                        <td>${{ item.cost }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            </section>
        </main>

        <ConfirmationModal :show="showConfirmModal" @close="showConfirmModal = false">
            <template #title> Eliminar registro de tarifa </template>
            <template #content> ¿Continuar con la eliminación? </template>
            <template #footer>
                <div>
                <CancelButton @click="showConfirmModal = false" class="mr-2">Cancelar</CancelButton>
                <PrimaryButton @click="deleteItem(itemToDelete)">Eliminar</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data(){
    return {
        catalogProductSelected: null,
        showConfirmModal: false,
        itemToDelete: null,
        loading: false
    }
},
components:{
    AppLayoutNoHeader,
    ConfirmationModal,
    PrimaryButton,
    CancelButton,
    LoadingLogo,
    Link
},
props:{
    shipping_rate: Object,
    shipping_rates: Array
},
methods:{
    formatDate(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
        }
    },
    openCatalogPoduct(catalogProductId) {
        const url = this.route('catalog-products.show', catalogProductId);
        window.open(url, '_blank');
    },
    deleteItem(item) {
        this.$inertia.delete(route('shipping-rates.destroy', item.id));
        this.$notify({
            title: "Éxito",
            message: "",
            type: "success",
        });
        location.reload();
    },
    async getShippingRates(catalog_product_id) {
        this.loading = true;
        try {
            const response = await axios.get(route('catalog-products.fetch-shipping-rates', catalog_product_id));
            if ( response.status === 200 ) {
                const shippingRateId = response.data.item.shipping_rates[0].id
                this.$inertia.get(route('shipping-rates.show', shippingRateId))
            }
        } catch (error) {
         console.log(error);   
        } finally {
            this.loading = true;
        }
    }
}
}
</script>
