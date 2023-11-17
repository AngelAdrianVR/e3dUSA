<template>
    <div class="lg:h-64 h-52 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 relative text-xs lg:text-sm">
        <img class="lg:h-16 h-6 absolute top-4 left-14 lg:top-2 lg:left-24" src="@/../../public/images/star.png">
        <h3 class="text-center text-gray-700 my-3">
            Desempeño de Producción <i class="fa-solid fa-helmet-safety ml-2"></i>
        </h3>
        <div v-if="users?.length" class="mb-28 px-2 w-full h-full">
            <p class="text-end w-1/2 text-primary text-xs mb-3">Puntos</p>
            <ol class="text-xs h-[65%] overflow-y-auto">
                <li @click="showModal = true; selectedUser = user" v-for="(user, index) in users" :key="index"
                    class="flex items-center mb-2 cursor-pointer">
                    <div class="w-1/2 flex justify-between items-center">
                        <p><strong class="text-primary mr-1">{{ index + 1 }}</strong> {{ user.name }}</p>
                        <span class="mr-2 justify-self-end">{{ user.total_points }}</span>
                    </div>
                    <div class="w-1/2">
                        <div v-if="user.percentage > 0" :style="{
                            width: user.percentage + '%',
                            backgroundColor: index === 0 ? '#44E536' : index === users.length - 1 ? '#D90537' : '#EC8B1F'
                        }" class="h-5 bg-[#44E536] rounded-tr-full rounded-br-full"></div>
                        <div v-else class="h-5 w-[1%] bg-[#D90537] rounded-tr-full rounded-br-full"></div>
                    </div>
                </li>
            </ol>
        </div>
        <p v-if="!users?.length" class="h-full w-full text-center text-primary">
            No hay usuarios para mostrar
        </p>
    </div>

    <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
            <h1>Detalles de puntuacion: <strong>{{ selectedUser.name }}</strong></h1>
        </template>
        <template #content>
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="bg-[#9a9a9a] rounded-tl-lg">Días</th>
                        <th class="bg-[#9a9a9a] border" colspan="3">Criterios</th>
                        <th class="bg-[#9a9a9a] rounded-tr-lg">Puntos Totales</th>
                    </tr>
                    <tr>
                        <th class="text-left bg-[#9a9a9a] border"></th>
                        <th class="text-left bg-[#9a9a9a] border">Puntualidad</th>
                        <th class="text-left bg-[#9a9a9a] border">Tiempo</th>
                        <th class="text-left bg-[#9a9a9a] border">Merma</th>
                        <th class="text-left bg-[#9a9a9a] border"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(dayPoints, day) in selectedUser.weekly_points" :key="day">
                        <td class="bg-white py-1 text-xs px-6">{{ day }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.punctuality < 0 }">{{
                            dayPoints.punctuality
                        }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.time < 0 }">{{
                            dayPoints.time }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.scrap < 0 }">{{
                            dayPoints.scrap }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">Total</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('punctuality') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('time') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('scrap') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateGrandTotal() }}</td>
                    </tr>
                </tfoot>
            </table>
            <button @click="showDetails = !showDetails" class="text-primary font-semibold mt-5 flex items-center">
                Ver detalle de productos realizados
                <i class="fa-solid fa-angle-down ml-3 text-[9px] transform origin-center transition duration-200 ease-out"
                    :class="{ '!rotate-180': showDetails }"></i>
            </button>
            <ul v-if="showDetails" class="my-2 text-xs overflow-x-auto">
                <table>
                    <thead>
                        <tr class="font-bold text-left">
                            <th class="px-2">Día</th>
                            <th class="px-2">Orden producción</th>
                            <th class="px-2">Descripción</th>
                            <th class="px-2">Tiempo estimado</th>
                            <th class="px-2">Inicio y término de tareas </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in selectedUser.productions" :key="item.id" style="white-space: pre-line;">
                            <td class="px-2 min-w-[140px]">{{ formatDate(item.finished_at) }}</td>
                            <td class="px-2 min-w-[140px]">
                                <Link :href="route('productions.show', item.catalog_product_company_sale.sale_id)"
                                    class="text-secondary hover:underline">
                                OP-{{ item.catalog_product_company_sale.sale_id }}
                                </Link>
                            </td>
                            <td :title="item.tasks" class="px-2 min-w-[140px] max-w-[200px] truncate">{{ item.tasks }}</td>
                            <td class="px-2 min-w-[120px]">{{ item.estimated_time_hours }}h {{ item.estimated_time_minutes
                            }}m</td>
                            <td class="px-2 min-w-[300px]">{{ formatDateTime(item.started_at) }} - {{ formatDateTime(item.finished_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </ul>
            <div class="text-xs text-secondary px-10 mt-5">
                <li><strong>Puntualidad: </strong>Este se refiere a llegar a tiempo para trabajar. Si llegas a tiempo, ganas
                    10 puntos. Si llegas tarde, perderás puntos equivalentes a los minutos de retraso. Así que, ¡llegar a
                    tiempo es clave para obtener una buena calificación! </li>
                <li><strong>Tiempo: </strong>El tiempo se trata de cuánto trabajaste de manera efectiva en la producción.
                    Cada minuto que trabajas se convierte en puntos, y estos se suman a tu puntaje total. Así que, cuantos
                    más minutos de trabajo productivo tengas, mejor será tu calificación.</li>
                <li><strong>Merma: </strong>La merma se refiere a productos defectuosos o piezas que salieron mal durante la
                    producción. Si tienes merma, perderás puntos equivalentes a la cantidad de merma. Pero si logras evitar
                    cualquier problema y no hay
                    merma, ganarás 1 punto extra. Así que, tratar de reducir los errores en el trabajo es importante para
                    tu calificación.</li>
            </div>
        </template>
        <template #footer>
            <CancelButton @click="showModal = false">Cerrar</CancelButton>
        </template>
    </DialogModal>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
            showModal: false,
            selectedUser: null,
            showDetails: false,
        }
    },
    components: {
        DialogModal,
        CancelButton,
        Link,
    },
    props: {
        users: Array,
    },
    methods: {
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'EEE dd MMM', { locale: es });
        },
        formatDateTime(dateTime) {
            const parsedDate = new Date(dateTime);
            return format(parsedDate, 'EEE dd MMM, HH:mm a', { locale: es });
        },
        calculatePercentage() {
            const maxPoints = Math.max(...this.users.map(user => user.total_points));

            this.users.forEach(user => {
                const percentage = (user.total_points / maxPoints) * 100;
                user.percentage = percentage.toFixed(2);
            });
        },
        calculateTotalPoints(dayPoints) {
            return dayPoints.punctuality + dayPoints.time + dayPoints.scrap;
        },
        calculateTotal(criterion) {
            return Object.values(this.selectedUser.weekly_points).reduce((total, dayPoints) => total + dayPoints[criterion], 0);
        },
        calculateGrandTotal() {
            return Object.values(this.selectedUser.weekly_points).reduce((total, dayPoints) => total + this.calculateTotalPoints(dayPoints), 0);
        },
    },
    mounted() {
        this.calculatePercentage();
    }
}
</script>