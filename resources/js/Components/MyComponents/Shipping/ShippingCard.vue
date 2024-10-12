<template>
    <div v-if="loading" class="flex justify-center border border-[#999999] rounded-2xl py-4">
        <i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i>
    </div>
    <main v-else class="border border-[#999999] rounded-2xl py-4 mt-1">
        <section class="grid grid-cols-2 gap-3 px-4 mt-3">
            <article class="space-y-1">
                <div class="flex space-x-2">
                    <p class="text-[#373737] font-bold w-24">Producto:</p>
                    <p>{{ product.name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#373737] font-bold w-24">N. Parte:</p>
                    <p>{{ product.part_number }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#373737] font-bold w-24">Cantidad:</p>
                    <p>{{ quantity }} {{ quantity == 1 ? 'unidad' : 'unidades' }}</p>
                </div>
            </article>
            <article class="flex flex-col items-end justify-end space-y-3">
                <figure class="rounded-xl flex items-center justify-center bg-gray-200 w-3/4 h-28">
                    <img v-if="product.media?.length" class="object-contain h-full"
                        :src="product.media[0]?.original_url">
                    <i v-else class="fa-regular fa-image text-gray-300 text-5xl"></i>
                </figure>

                <p v-if="shippingInfo?.is_fragile"
                    class="inline-flex rounded-md justify-center items-center px-3 bg-[#FDB9C9] text-primary">
                    <svg class="mr-2" width="8" height="14" viewBox="0 0 8 14" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.63137 0H0.0145374C0.12422 5.10188 -0.263825 5.06154 0.404279 6.24615C0.961078 5.11539 1.42056 4.46654 2.35299 3.82308C2.1303 3.33846 1.68488 2.90769 1.35079 2.47692C1.87381 1.53081 2.15193 0.986847 2.63137 0Z"
                            fill="#D90537" />
                        <path
                            d="M2.18598 2.42308C2.13029 2.36923 3.52224 0 3.52224 0H7.92076C8.0261 1.75289 8.00576 2.78859 7.97644 4.63077C8.03212 6.78462 6.41747 7.91539 4.58012 7.96923V12.7615H7.25263V14H0.571346V12.7615H3.24386V7.96923C2.01354 7.86996 1.50341 7.61407 0.849733 6.89231C1.51786 5.33077 2.18948 4.63672 3.46657 3.98462C2.96487 3.35599 2.24167 2.47692 2.18598 2.42308Z"
                            fill="#D90537" />
                    </svg>
                    Frágil
                </p>
            </article>
        </section>

        <h2 class="font-semibold text-[#373737] mt-5 px-4 flex items-center justify-between">
            <span>Especificaciones de la(s) caja(s)</span>
            <div v-if="shippingInfo" class="flex space-x-2 items-center">
                <button
                    @click="$inertia.get(route('shipping-rates.edit', shippingInfo.id), { route: routePage, idRoute: idRoute })"
                    type="button"
                    class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                </button>
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Remover?"
                    @confirm="deleteItem()">
                    <template #reference>
                        <button type="button"
                            class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </template>
                </el-popconfirm>
            </div>
            <PrimaryButton @click="openNewWindow()" type="button" v-else>
                Agregar cajas
            </PrimaryButton>
        </h2>

        <section v-if="shippingInfo">
            <p class="mt-2 px-4">Cajas necesarias: <span class="ml-4">{{ shippingInfo?.boxes?.length }}</span></p>
            <div class="overflow-x-auto mt-4">
                <table class="min-w-full">
                    <thead class="bg-[#373737] text-white">
                        <tr class="*:px-4 *:py-2 *:text-left">
                            <th>#Caja</th>
                            <th>Dimensiones</th>
                            <th>Peso</th>
                            <th>Piezas</th>
                            <th>Costo por caja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(box, index) in shippingInfo?.boxes" :key="box" class="*:px-4 *:py-2">
                            <td>{{ (index + 1) }}</td>
                            <td>{{ box.length + 'x' + box.width + 'x' + box.height }} cm</td>
                            <td>{{ box.weight }} kg</td>
                            <td>{{ box.quantity }} piezas</td>
                            <td>${{ box.cost }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <div v-else class="text-center text-[#373737] px-6 mt-5 text-sm">
            Faltan datos sobre las dimensiones de las cajas para este producto. Si tienes la información da clic
            en el botón “Agregar cajas”. <br>
            <p class="bg-yellow-200">
                Si acabas de registrar una tarifa nueva para este producto y no te aparece, da
                <button @click="fetchCatalogProuctShippingRates()" class="underline text-secondary">clic aqui</button>
                para
                actualizar la información
            </p>
        </div>
    </main>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
    data() {
        return {
            shippingInfo: null,
            loading: false,
        }
    },
    components: {
        PrimaryButton,
    },
    props: {
        product: Object,
        quantity: Number,
        routePage: {
            type: String,
            default: null
        },
        idRoute: {
            type: Number,
            default: null
        }
    },
    emits: ['total-boxes', 'total-cost'],
    watch: {
        quantity() {
            this.calculateShippingData();
        }
    },
    methods: {
        openNewWindow() {
            const url = route('shipping-rates.create', { catalog_product_id: this.product.id, route: this.routePage, idRoute: this.idRoute, dismiss: true, quantity: this.quantity });
            window.open(url, '_blank');
        },
        deleteItem() {
            this.$inertia.delete(route('shipping-rates.destroy', this.shippingInfo.id));
        },
        calculateShippingData() {
            this.shippingInfo = this.product.shipping_rates.find(item => item.quantity === this.quantity);
            const totalBoxes = this.shippingInfo?.boxes?.length;
            this.$emit('total-boxes', totalBoxes);
            const totalCost = this.shippingInfo?.boxes?.reduce((acc, box) => {
                return acc + parseFloat(box.cost); // Asegúrate de convertir el 'cost' a número
            }, 0);
            this.$emit('total-cost', totalCost);
        },
        async fetchCatalogProuctShippingRates() {
            this.loading = true;
            try {
                const response = await axios.get(route('productions.fetch-catalog-product-shipping-rates', this.product?.id));

                if (response.status === 200) {
                    // Actualiza las propiedades de this.product en lugar de asignarlo directamente
                    Object.assign(this.product, response.data.item);

                    // seleccionar la tarifa coincidente y emitir cantidad de cajas y costo
                    this.calculateShippingData();
                    // this.shippingInfo = this.product.shipping_rates.find(item => item.quantity === this.quantity);
                    // const totalBoxes = this.shippingInfo?.boxes?.length;
                    // this.$emit('total-boxes', totalBoxes);
                    // const totalCost = this.shippingInfo?.boxes?.reduce((acc, box) => {
                    //     return acc + parseFloat(box.cost); // Asegúrate de convertir el 'cost' a número
                    // }, 0);
                    // this.$emit('total-cost', totalCost);
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        if (this.product.shipping_rates?.length) {
            this.calculateShippingData();
            // this.shippingInfo = this.product.shipping_rates.find(item => item.quantity === this.quantity);
            // const totalBoxes = this.shippingInfo?.boxes?.length;
            // this.$emit('total-boxes', totalBoxes);
            // const totalCost = this.shippingInfo?.boxes?.reduce((acc, box) => {
            //     return acc + parseFloat(box.cost); // Asegúrate de convertir el 'cost' a número
            // }, 0);
            // this.$emit('total-cost', totalCost);
        } else {
            this.fetchCatalogProuctShippingRates();
        }
    }
}
</script>
