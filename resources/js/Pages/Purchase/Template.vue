<template>
    <div class="text-[11px]">

        <Head :title="'Orden de compra ' + String(purchase.id).padStart(4, '0')" />
        <header class="mt-10">
            <div class="flex items-center justify-between ml-8">
                <ApplicationLogo class="w-[30%]" />
                <p class="bg-gray2 py-px font-bold text-center px-20">Orden de compra</p>
            </div>
            <div class="flex flex-col items-end mr-8">
                <p class="w-48">
                    <span class="mr-6 w-1/2">Folio:</span>
                    <span class="w-1/2">OC-{{ String(purchase.id).padStart(4, "0") }}</span>
                </p>
                <p class="w-48">
                    <span class="mr-6 w-1/2">Fecha:</span>
                    <span class="w-1/2">{{ formatDate((purchase.created_at)) }}</span>
                </p>
            </div>
            <section class="mx-8 px-4 py-1 bg-gray2 grid grid-cols-2 gap-1 mt-4">
                <span>EMBLEMS 3D USA</span>
                <span>Av. Aurelio Ortega 518, Seattle, 45150 Zapopan, Jal.</span>
                <span>EDU211206DC9</span>
                <span>33 38338209</span>
            </section>
            <section class="mx-8 px-4 py-1 grid grid-cols-7 gap-1 mt-4">
                <span>Proveedor:</span>
                <span class="col-span-6">{{ purchase.supplier.name }}</span>
                <span>Domicilio:</span>
                <span class="col-span-6">{{ purchase.supplier.address }}</span>
                <span>Telefono:</span>
                <span class="col-span-6">{{ purchase.supplier.phone }}</span>
                <span>Observaciones: </span>
                <span class="col-span-6">{{ purchase.notes ?? '-' }}</span>
            </section>
        </header>
        <main class="mx-8 mt-8">
            <table class="w-full">
                <thead>
                    <tr class="*:bg-gray2 *:px-4 *:py-3 *:border *:border-gray1 text-left">
                        <th>Número de parte</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Valor unit.</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in raw_materials" :key="item.id"
                        class="*:px-4 *:py-3 *:border *:border-gray1 text-left">
                        <td>{{ item.part_number }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.description }}</td>
                        <td v-if="editQuantity === item.id" class="cursor-pointer" title="Editar cantidad">
                            <div class="flex space-x-2 items-center">
                                <input type="number" v-model="quantity"
                                    class="border border-gray1 rounded-[3px] text-xs w-24" />
                                <button @click="updateQuantity(item.id)"
                                    class="size-5 text-xs rounded-full bg-primary text-white"><i
                                        class="fa-solid fa-check"></i></button>
                                <button @click="editQuantity = null"
                                    class="size-5 text-xs rounded-full bg-gray1 text-white"><i
                                        class="fa-solid fa-xmark"></i></button>
                            </div>
                        </td>
                        <td v-else class="group" title="Editar cantidad">
                            <span>{{ purchase.products.find(prd => prd.id === item.id)?.quantity }}</span>
                            <button
                                @click="editQuantity = item.id; quantity = purchase.products.find(prd => prd.id === item.id)?.quantity"
                                class="size-5 text-xs ml-4 rounded-full bg-gray1 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-100 ease-in-out"><i
                                    class="fa-solid fa-pen"></i></button>

                        </td>
                        <td>{{ item.measure_unit }}</td>
                        <td>${{ item.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                        <td>${{ (item.cost * purchase.products.find(prd => prd.id ===
                            item.id)?.quantity).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</td>
                    </tr>
                </tbody>
            </table>
            <!-- imagenes -->
            <section>
                <div class="w-11/12 mx-auto my-3 grid grid-cols-3 gap-4 ">
                    <template v-for="item in raw_materials" :key="item.id">
                        <div class="bg-gray-200 rounded-t-xl rounded-b-md border" style="font-size: 8px;">
                            <img class="rounded-t-xl max-h-52 mx-auto" :src="item.media[0]?.original_url">
                            <p class="py-px px-1 uppercase text-gray-600">{{ item.name }}</p>
                        </div>
                    </template>
                </div>
            </section>
        </main>
        <footer class="mx-8 mt-8 grid grid-cols-4 gap-x-3 gap-y-1s">
            <section class="flex flex-col col-span-3">
                <header class="bg-gray2 text-center py-1">
                    Importe con letra
                </header>
                <p class="text-center mt-3">{{ turnNumberIntoText(getSubtotal * 1.16) }}</p>
            </section>
            <section class="grid grid-cols-2 gap-x-4 gap-y-2">
                <span>Subtotal</span>
                <span>{{ getSubtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                <!-- <span>Descuento</span>
                <span>0.00</span> -->
                <span>IVA</span>
                <span>{{ (getSubtotal * 0.16).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
                <span>Total</span>
                <span class="font-bold border-y-2 border-gray1">
                    {{ (getSubtotal * 1.16).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            </section>
        </footer>
    </div>
</template>
  
<script>
import { Head } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { NumerosALetras } from 'numero-a-letras';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
    data() {
        return {
            quantity: null,
            editQuantity: false,
        }
    },
    components: {
        Head,
        ApplicationLogo,
    },
    props: {
        purchase: Object,
        raw_materials: Array,
    },
    computed: {
        getSubtotal() {
            const subtotal = this.raw_materials.reduce((accumulator, item) => {
                const quantity = this.purchase.products.find(prd => prd.id === item.id)?.quantity || 0;
                return accumulator + item.cost * quantity;
            }, 0);

            return subtotal;
        },
    },
    methods: {
        async updateQuantity(id) {
            try {
                const response = await axios.put(route('purchases.update-quantity', this.purchase.id), { quantity: this.quantity, id: id });

                if (response.status === 200) {
                    this.editQuantity = null;
                    this.purchase.products.find(prd => prd.id === id).quantity = this.quantity;

                    this.$notify({
                        title: "Éxito",
                        message: "Cantidad actualizada",
                        type: "success",
                    });
                }
            } catch (error) {
                console.log(error);
            }
        },
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMMM Y', { locale: es }); // Formato personalizado
        },
        turnNumberIntoText(amount) {
            // Utiliza la función para convertir el número a letras
            const QuantityInText = NumerosALetras(amount, {
                plural: true,
                centavos: true
            });

            // Formatea el resultado según tus necesidades específicas
            const result = QuantityInText.replace(/^./, (str) => str.toUpperCase());

            return result;
        }
    },
}
</script>
  
<style></style>