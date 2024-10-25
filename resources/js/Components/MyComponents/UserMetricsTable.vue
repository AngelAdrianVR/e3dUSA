<template>
    <div>
        <p>
            La siguiente tabla presenta métricas clave relacionadas con la productividad y desempeño, considerando un
            período de 1 hora de trabajo para cada nivel de experiencia.
        </p>
        <div class="w-full overflow-auto mt-6">
            <table class="w-full">
                <thead>
                    <tr>
                        <th rowspan="2">Proceso</th>
                        <th rowspan="1" colspan="3">Niveles de experiencia</th>
                    </tr>
                    <tr>
                        <th :class="experience == 'Novato' ? 'text-secondary bg-secondarylight' : null">Novato</th>
                        <th :class="experience == 'Intermedio' ? 'text-[#FD8827] bg-[#FEDBBD]' : null">Intermedio</th>
                        <th :class="experience == 'Experto' ? 'text-[#9E0FA9] bg-[#F7B7FC]' : null">Experto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in processes" :key="index">
                        <td>{{ item.name }}</td>
                        <td>{{ item.n }}</td>
                        <td>{{ item.i }}</td>
                        <td>{{ item.e }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            processes: [
                { name: 'Inyección de porta placas maquina 1', n: 70, i: 80, e: 120 },
                { name: 'Armar tapetes de uso rudo', n: 70, i: 80, e: 120 },
                { name: 'Serigrafía tapete foamy ', n: 70, i: 80, e: 120 },
                { name: 'Serigrafía porta placa en mesa ', n: 70, i: 80, e: 120 },
                { name: 'Serigrafía porta placa en máquina semi automática', n: 70, i: 80, e: 120 },
                { name: 'Serigrafía porta placa en pulpo ', n: 70, i: 80, e: 120 },
                { name: 'Serigrafía estirno en mesa', n: 70, i: 80, e: 120 },
                { name: 'Grabado medallón ambos lados', n: 70, i: 80, e: 120 },
                { name: 'Pegar emblema a llaveros', n: 70, i: 80, e: 120 },
                { name: 'Colocar medallón a llaveros', n: 70, i: 80, e: 120 },
            ],
        };
    },
    props: {
        experience: String,
    },
}
</script>
<style scoped>
table {
    border-collapse: collapse;
}

th,
td {
    border: 1px solid #000;
    text-align: left;
    padding: 8px;
}
.dark th,
.dark td {
    border: 1px solid #fff;
}

</style>