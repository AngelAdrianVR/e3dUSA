<template>
    <div class="lg:h-56 h-52 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:p-5 py-2 px-4 relative text-xs lg:text-sm">
        <img class="lg:h-16 h-6 absolute top-4 left-14 lg:top-2 lg:left-32" src="@/../../public/images/star.png">
        <h3 class="text-center text-gray-700 my-3">
            Desempeño de Ventas<i class="fa-solid fa-shop ml-2"></i>
        </h3>
        <div v-if="users?.length" class="mb-28 px-2 w-full h-full">
            <p class="text-end w-1/2 text-primary text-xs mb-3">Puntos</p>
            <ol class="text-xs h-[65%] overflow-y-auto">
                <li v-for="(user, index) in users" :key="index" class="flex items-center mb-2">
                    <div class="w-1/2 flex justify-between items-center">
                        <p><strong class="text-primary mr-1">{{ index + 1 }}</strong> {{ user.name }}</p>
                        <span class="mr-2 justify-self-end">{{ user.points }}</span>
                    </div>
                    <div class="w-1/2">
                        <div :style="{
                            width: user.percentage + '%',
                            backgroundColor: index === 0 ? '#44E536' : index === users.length - 1 ? '#D90537' : '#EC8B1F'
                        }" class="h-5 bg-[#44E536] rounded-tr-full rounded-br-full"></div>
                    </div>
                </li>
            </ol>
        </div>
        <p v-if="!users?.length" class="h-full w-full text-center text-primary">
            No hay usuarios para mostrar
        </p>
    </div>
</template>

<script>
export default {
    components: {
    },
    props: {
        users: Array,
    },
    methods: {
        calculatePercentage() {
            const maxPoints = Math.max(...this.users.map(user => user.points));

            this.users.forEach(user => {
                const percentage = (user.points / maxPoints) * 100;
                user.percentage = percentage.toFixed(2);
            });
        }
    },
    mounted() {
        this.calculatePercentage();
    }
}
</script>