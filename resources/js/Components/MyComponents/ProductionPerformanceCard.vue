<template>
    <div class="lg:h-64 h-52 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 relative text-xs lg:text-sm">
        <img class="lg:h-16 h-6 absolute top-4 left-14 lg:top-2 lg:left-32" src="@/../../public/images/star.png">
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
            <h1>Detalles de puntuacion</h1>
        </template>
        <template #content>
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="bg-gray-300">Días</th>
                        <th class="bg-gray-300" colspan="3">Criterios</th>
                        <th class="bg-gray-300">Puntos Totales</th>
                    </tr>
                    <tr>
                        <th class="text-left"></th>
                        <th class="text-left">Puntualidad</th>
                        <th class="text-left">Tiempo</th>
                        <th class="text-left">Merma</th>
                        <th class="text-left"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(dayPoints, day) in selectedUser.weekly_points" :key="day">
                        <td class="border">{{ day }}</td>
                        <td class="border" :class="{ 'text-red-500': dayPoints.punctuality < 0 }">{{ dayPoints.punctuality
                        }}</td>
                        <td class="border" :class="{ 'text-red-500': dayPoints.time < 0 }">{{ dayPoints.time }}</td>
                        <td class="border" :class="{ 'text-red-500': dayPoints.scrap < 0 }">{{ dayPoints.scrap }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="border">Total</td>
                        <td class="border">{{ calculateTotal('punctuality') }}</td>
                        <td class="border">{{ calculateTotal('time') }}</td>
                        <td class="border">{{ calculateTotal('scrap') }}</td>
                        <td class="border">{{ calculateGrandTotal() }}</td>
                    </tr>
                </tfoot>
            </table>
        </template>
        <template #footer>
            <CancelButton @click="showModal = false">Cerrar</CancelButton>
        </template>
    </DialogModal>
</template>

<script>
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import moment from 'moment';

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
        formattedDate(date) {
            return moment(date).format('ddd DD MMM');
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