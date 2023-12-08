<template>
    <div class="lg:h-64 h-52 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 relative text-xs lg:text-sm">
        <img class="lg:h-16 h-6 absolute top-4 left-14 lg:top-2 lg:left-32" src="@/../../public/images/star.png">
        <h3 class="text-center text-gray-700 my-3">
            Desempeño de Ventas<i class="fa-solid fa-shop ml-2"></i>
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
                        <th class="text-left bg-[#9a9a9a] border">Ventas</th>
                        <th class="text-left bg-[#9a9a9a] border">Nuevos clientes</th>
                        <th class="text-left bg-[#9a9a9a] border">Nuevos productos (ventas verticales)</th>
                        <th class="text-left bg-[#9a9a9a] border"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(dayPoints, day) in selectedUser.weekly_points" :key="day">
                        <td class="bg-white py-1 text-xs px-6">{{ day }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.sales <= 0 }">
                            ${{ dayPoints.sales.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}
                        </td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.new_companies <= 0 }">{{
                            dayPoints.new_companies }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.new_products <= 0 }">{{
                            dayPoints.new_products }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">Total</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">${{ calculateTotal('sales').toLocaleString('en-US', {
                            minimumFractionDigits: 2
                        }) }} ({{ calculateTotal('sales') / 100 }})</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('new_companies') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('new_products') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateGrandTotal() }}</td>
                    </tr>
                </tfoot>
            </table>
            <div class="text-xs text-secondary px-10 mt-5">
                <li><strong>Ventas: </strong>Este concepto se trata de calcular las ventas totales que realizaste durante la
                    semana. Luego, tomamos ese total y lo dividimos entre 100 para obtener tus puntos. Así que, cuanto más
                    vendas, más puntos ganarás. </li>
                <li><strong>Nuevos clientes: </strong>Recibe 20 puntos por cada cliente nuevo registrado en la semana. </li>
                <li><strong>Nuevos productos: </strong>Recibe 20 puntos por cada producto nuevo registrado en la semana. </li>
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

export default {
    data() {
        return {
            showModal: false,
            selectedUser: null,
        }
    },
    components: {
        DialogModal,
        CancelButton
    },
    props: {
        users: Array,
    },
    methods: {
        calculatePercentage() {
            const maxPoints = Math.max(...this.users.map(user => user.total_points));

            this.users.forEach(user => {
                const percentage = (user.total_points / maxPoints) * 100;
                user.percentage = percentage.toFixed(2);
            });
        },
        calculateTotalPoints(dayPoints) {
            return (dayPoints.sales / 100) + dayPoints.new_companies;
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