<template>
    <div class="rounded-[30px] lg:rounded-[20px] bg-[#D9D9D9] py-4 px-6 text-sm">
        <h2 class="text-black font-bold flex justify-center items-center">
            <p>¿Cero tareas pendientes?. ¡Pide tareas para avanzar!</p>
        </h2>
        <div v-if="loading" class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-3 py-1 h-28 mt-5">
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
            </div>
        </div>
        <div v-else class="mt-5 h-40">
            <p class="text-center">
                Si estás disponible, hay órdenes de venta por autorizar que puedes solicitar a la gerente o a tu supervisor
                para trabajar en ellas.
            </p>
            <div v-if="unauthorizedSales.length" class="h-[calc(10rem-2rem)] overflow-auto">
                <table class="table-fixed w-full">
                    <thead>
                        <tr>
                            <th class="text-start px-1">Folio</th>
                            <th class="text-start px-1">Productos ordenados</th>
                            <th class="text-start px-1">Piezas ordenadas</th>
                            <th class="text-start px-1">Disponibles</th>
                            <th class="text-start px-1 text-primary">A producir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in unauthorizedSales" :key="item.id" class="text-xs w-full">
                            <td class="px-1">{{ item.folio }}</td>
                            <td class="px-1">
                                <p class="truncate" v-for="op in item.catalogProductCompanySales" :key="op.id" :title="op.catalog_product_company?.catalog_product.name">
                                    • {{ op.catalog_product_company?.catalog_product.name }}
                                </p>
                            </td>
                            <td class="px-1">
                                <p class="truncate" v-for="op in item.catalogProductCompanySales" :key="op.id">
                                    {{ op.quantity.toFixed().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                </p>
                            </td>
                            <td class="px-1">
                                <p class="truncate" v-for="op in item.catalogProductCompanySales" :key="op.id">
                                    {{
                                        op.catalog_product_company?.catalog_product?.storages[0]?.quantity?.toFixed().replace(/\B(?=(\d{3})+(?!\d))/g,
                                            ",") ?? 0 }}
                                </p>
                            </td>
                            <td class="px-1">
                                <p class="truncate" v-for="op in item.catalogProductCompanySales" :key="op.id">
                                    {{ toProduce(op) }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p v-else class="text-xs text-gray-400 text-center py-3">Por el momento todas las ordenes de venta estan
                autorizadas</p>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import DialogModal from '@/Components/DialogModal.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        const form = useForm({
            operator_id: null,
            date: null,
            hours: 1,
            bonus: null,
            points: null,
        });

        return {
            form,
            unauthorizedSales: [],
            operators: [],
            loading: true,
            showCreateModal: false,
        };
    },
    components: {
        DialogModal,
        CancelButton,
        PrimaryButton,
        InputError,
    },
    methods: {
        toProduce(product) {
            const ordered = product.quantity;
            const available = product.catalog_product_company?.catalog_product?.storages[0]?.quantity ?? 0;
            const total = ordered - available;

            return total < 0 ? 0 : total.toFixed().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMMM', { locale: es }); // Formato personalizado
        },
        async fetchUnauthorizedSales() {
            try {
                const response = await axios.get(route('sales.get-unauthorized'));

                if (response.status === 200) {
                    this.unauthorizedSales = response.data.items;
                }
            } catch (error) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: 'No se pudieron obtener las OV sin autorizar por inconvenientes en el servidor. Inténtalo más tarde',
                    type: 'error',
                });
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchUnauthorizedSales();
    }
}
</script>