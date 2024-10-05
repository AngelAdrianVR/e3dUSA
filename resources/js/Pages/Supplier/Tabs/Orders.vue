<template>
    <Loading v-if="loading" class="mt-10" />
    <section v-else class="mt-6">
        <table v-if="purchases.length" class="w-full mx-auto text-sm">
            <thead>
                <tr class="text-left *:font-bold *:pb-2 *:px-4">
                    <th>Folio</th>
                    <th>Creado por</th>
                    <th>Creado el</th>
                    <th>Estatus</th>
                    <th>Emitido a proveedor</th>
                    <th>Recibido el</th>
                    <th>Evaluación</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in purchases" :key="index" class="mb-4 *:text-left *:py-2 *:pr-2 *:px-4">
                    <td>
                        <Link :href="route('purchases.show', item.id)" class="text-secondary underline">
                        {{ 'OC-' + item.id.toString().padStart(4, '0') }}
                        </Link>
                    </td>
                    <td>
                        {{ item.user.name }}
                    </td>
                    <td>
                        {{ formatDateTime(item.created_at) }}
                    </td>
                    <td>
                        <p class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle text-[9px]" :class="statuses[parseInt(item.status)].color"></i>
                            <span>{{ statuses[parseInt(item.status)].label }}</span>
                        </p>
                    </td>
                    <td>
                        {{ item.emited_at ? formatDateTime(item.emited_at) : '-' }}
                    </td>
                    <td>
                        {{ item.recieved_at ? formatDateTime(item.recieved_at) : '-' }}
                    </td>
                    <td>
                        <button type="button" @click="openRatingModal(index)" v-if="item.rating"
                            class="flex items-center space-x-2 text-[#109816]">
                            <i class="fa-solid fa-check"></i>
                            <span class="border-b border-[#109816] border-dashed">Realizada</span>
                        </button>
                        <p v-else>-</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <el-empty v-else description="No hay ordenes de compra registrados a este proveedor" />
    </section>

    <DialogModal :show="showRatingModal" @close="showRatingModal = false" maxWidth="4xl">
        <template #title>
            <h1 class="flex items-center justify-between font-bold mt-3">
                <p>Evaluación de proveedor</p>
                <p>REG-CO-07</p>
            </h1>                
        </template>
        <template #content>
            <section class="px-5">
                <div v-for="(question, index) in questions" :key="index" class="py-3 border-[#CCCCCC] border-b-2 text-[#373737]">
                    <h2 class="font-bold">{{ index+1 }}. {{ question }}</h2>
                    <p class="flex justify-between">
                        <span>{{ purchases[selectedPurchaseIndex].rating.questions[index].answer }}</span>
                        <span class="text-black">{{ purchases[selectedPurchaseIndex].rating.questions[index].points }} pts</span>
                    </p>
                </div>
                <p class="text-end text-black mt-3">
                    {{ purchases[selectedPurchaseIndex].rating.questions.reduce((accum, elem) => accum += elem.points, 0) }} pts
                </p>
            </section>
        </template>
    </DialogModal>
</template>
<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import Loading from '@/Components/MyComponents/Loading.vue';
import { Link } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';

export default {
    data() {
        return {
            showRatingModal: false,
            loading: true,
            selectedPurchaseIndex: null,
            purchases: [],
            statuses: [
                {
                    label: 'Autorización pendiente',
                    color: 'text-red-500',
                },
                {
                    label: 'Autorizado. Compra no realizada',
                    color: 'text-yellow-500',
                },
                {
                    label: 'Compra realizada',
                    color: 'text-blue-500',
                },
                {
                    label: 'Product / servicio recibido',
                    color: 'text-green-500',
                },
            ],
            questions: [
                '¿Cumplió con el tiempo de entrega?',
                '¿Las características solicitadas de los productos o servicio fueron cubiertos?',
                '¿Cumplió con el apoyo técnico ofrecido?',
                'Ante alguna urgencia, ¿se ofreció apoyo en la entrega?',
                '¿Hubo alguna incidencia?',
            ],
        }
    },
    props: {
        supplier: Object
    },
    components: {
        Loading,
        Link,
        DialogModal,
    },
    methods: {
        openRatingModal(index) {
            this.selectedPurchaseIndex = index;
            this.showRatingModal = true;
        },
        formatDateTime(dateTime) {
            const parsedDate = new Date(dateTime);
            return format(parsedDate, 'dd \'de\' MMMM, Y • h:mm a', { locale: es });
        },
        async fetchSupplierOrders() {
            this.loading = true;
            try {
                const response = await axios.get(route("suppliers.get-orders", this.supplier.id));

                if (response.status == 200) {
                    this.purchases = response.data.items;
                }
            } catch (err) {
                this.$notify({
                    title: "Algo salió mal",
                    message: err.message,
                    type: "error",
                });
                console.log(err);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchSupplierOrders();
    }
}
</script>