<template>
    <Head :title="cpcs.id" />
    <Loading v-if="loading" class="my-36" />
    <div v-else class="text-[11px]">
        <header class="bg-[#373737] h-24 flex justify-between items-center pl-5 pr-20 text-white">
            <ApplicationLogo class="h-auto w-1/6 inline-block" />
            <h1 class="flex flex-col items-center text-sm">
                <span>Hoja viajera</span>
                <b>llaveros</b>
            </h1>
            <div class="flex flex-col w-1/3">
                <div class="flex justify-between border-b border-white">
                    <span>Código:</span>
                    <span class="text-center">Por asignar</span>
                </div>
                <div class="flex justify-between">
                    <span>No. de revisión:</span>
                    <span class="text-center">01</span>
                </div>
                <div class="flex justify-between">
                    <span>Fecha de elaboración:</span>
                    <span class="text-center">{{ dateFormat(today) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Vigencia:</span>
                    <span class="text-center">{{ dateFormat(new Date(today.getFullYear() + 2, today.getMonth(),
                        today.getDate())) }}</span>
                </div>
            </div>
        </header>
        <main class="m-5">
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3 mx-5 relative">
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 1</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de almacén de llaveros</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha de entrega</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de llaveros</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Entregó</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Recibió</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de almacén de emblemas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha de entrega</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Logo / Diseño de emblema:</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Entregó</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Recibió</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Criterio de aceptación</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[0]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(0, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[0][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[0][index].userId)?.name }}<br />
                                            {{ travelerData[0][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[0][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(0, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[0][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[0][index].userId)?.name }}<br />
                                            {{ travelerData[0][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[0][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3 mx-5 relative">
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 2</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de grabado láser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de llaveros grabados</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones grabados</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Motivo de merma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Criterio de aceptación</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[1]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(1, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[1][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[1][index].userId)?.name }}<br />
                                            {{ travelerData[1][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[1][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(1, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[1][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[1][index].userId)?.name }}<br />
                                            {{ travelerData[1][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[1][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3 mx-5 relative">
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 3</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de aplicación de emblmeas y medallones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de emblemas</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones aplicados</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Motivo de merma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Criterio de aceptación</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[2]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(2, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[2][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[2][index].userId)?.name }}<br />
                                            {{ travelerData[2][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[2][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(2, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[2][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[2][index].userId)?.name }}<br />
                                            {{ travelerData[2][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[2][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-2 gap-x-6 gap-y-3 my-3 mx-5 relative">
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 4</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de empaque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>No. de paquete / Pzs. Por paquete</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de Cajas:</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Control de logística</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[3]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(3, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[3][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[3][index].userId)?.name }}<br />
                                            {{ travelerData[3][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[3][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(3, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[3][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[3][index].userId)?.name }}<br />
                                            {{ travelerData[3][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[3][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            {{ productions.filter(item =>
                item.tasks.toLowerCase().includes('grabado láser llavero'.toLowerCase())) }}
        </main>
    </div>
</template>
<script>
import { Head } from "@inertiajs/vue3";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            loading: true,
            today: new Date(),
            users: [],
            productions: [],
            travelerData: [
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
            ],
            criterias: [
                [
                    'Llaveros',
                    'Mini medallones',
                    'Emblemas',
                ],
                [
                    'Diseño de grabado correcto',
                    'Definición y calidad de grabado',
                    'Cantidad correcta',
                ],
                [
                    'Posición de emblema correcto',
                    'Logo/Diseño de emblema correcto',
                    'Juego de llavero y mini medallón correcto',
                ],
                [
                    'Producto coincide con orden de pedido',
                    'Se tiene guía de paquetería',
                    'Se tienen documentos para entrega',
                ],
            ],
        }
    },
    components: {
        Head,
        ApplicationLogo,
        Loading,
    },
    props: {
        cpcs: Object,
    },
    methods: {
        dateFormat(date) {
            const formattedDate = format(date, 'dd-MMMM-yyyy', { locale: es });

            return formattedDate;
        },
        async storeCriteriaValue(stage, CriteriaIndex, value) {
            this.travelerData[stage][CriteriaIndex].userId = 2; //this.$page.props.auth.user.id;
            this.travelerData[stage][CriteriaIndex].value = value;
            this.travelerData[stage][CriteriaIndex].timestamp = format(new Date(), 'dd-MMM-yyyy h:mm a', { locale: es });
            try {
                const response = await axios.post(route('catalog-product-company-sale.store-traveler-data', this.cpcs.id), { traveler_data: this.travelerData });

            } catch (error) {
                console.log(error);
            }
        },
        async fetchUsers() {
            this.loading = true;
            try {
                const response = await axios.get(route('users.get-all'));

                if (response.status === 200) {
                    this.users = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchTravelerData() {
            this.loading = true;
            try {
                const response = await axios.get(route('catalog-product-company-sale.get-traveler-data', this.cpcs.id));

                if (response.status === 200 && response.data.item != null) {
                    this.travelerData = response.data.item;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchCpcsProductions() {
            this.loading = true;
            try {
                const response = await axios.get(route('catalog-product-company-sale.get-productions', this.cpcs.id));

                if (response.status === 200) {
                    this.productions = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },

    },
    async mounted() {
        await this.fetchUsers();
        await this.fetchTravelerData();
        await this.fetchCpcsProductions();
    }
}
</script>