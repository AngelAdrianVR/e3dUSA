<template>
    <div class="lg:h-64 h-52 bg-[#D9D9D9] dark:bg-[#202020] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 relative text-xs lg:text-sm transition-all ease-linear duration-500">
        <img class="lg:h-16 h-6 absolute top-4 left-14 lg:top-2 lg:left-32" src="@/../../public/images/star.png">
        <h3 class="text-center dark:text-white text-gray-700 my-3">
            Desempeño de Diseño <i class="fa-solid fa-pen-ruler ml-2"></i>
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
                            backgroundColor: index === 0 ? '#4ade80' : index === users.length - 1 ? '#D90537' : '#fb923c'
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
                        <th class="text-left bg-[#9a9a9a] border">Entregas a tiempo</th>
                        <th class="text-left bg-[#9a9a9a] border">Promedio</th>
                        <th class="text-left bg-[#9a9a9a] border"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(dayPoints, day) in selectedUser.weekly_points" :key="day">
                        <td class="bg-white py-1 px-2 text-xs">{{ day }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.punctuality < 0 }">{{
                            dayPoints.punctuality
                        }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.on_time < 0 }">{{
                            dayPoints.on_time }}</td>
                        <td class="bg-white py-1 px-2 text-xs" :class="{ 'text-red-500': dayPoints.average < 0 }">{{
                            dayPoints.average }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">Total</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('punctuality') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('on_time') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateTotal('average') }}</td>
                        <td class="bg-[#9a9a9a] py-1 px-2 text-xs">{{ calculateGrandTotal() }}</td>
                    </tr>
                </tfoot>
            </table>
            <div class="text-xs text-secondary px-10 mt-5">
                <li><strong>Puntualidad: </strong>Este se refiere a llegar a tiempo para trabajar. Si llegas a tiempo, ganas
                    10 puntos. Si llegas tarde, perderás puntos equivalentes a los minutos de retraso. Así que, ¡llegar a
                    tiempo es clave para obtener una buena calificación! </li>
                <li><strong>Entregas a tiempo: </strong>Obtendrás 10 puntos por cada trabajo que completes antes o en la
                    fecha y
                    hora programada. Si te pasas de esa fecha y hora, no recibirás puntos. En resumen, premiamos la
                    puntualidad
                    en la entrega de tus proyectos.
                </li>
                <li><strong>Promedio: </strong>Este concepto se refiere al tiempo promedio que asignas para finalizar cada
                    trabajo de diseño. Calculamos el promedio utilizando todas las órdenes que trabajaste durante la semana.
                    Luego, para calcular tus puntos, dividimos 10,000 puntos entre ese promedio. Esto significa que si
                    asignas
                    menos tiempo en promedio para cada trabajo, obtendrás más puntos. En otras palabras, se recompensa la
                    eficiencia en la gestión del tiempo para completar proyectos de diseño.</li>
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
            return dayPoints.punctuality + dayPoints.on_time + dayPoints.average;
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