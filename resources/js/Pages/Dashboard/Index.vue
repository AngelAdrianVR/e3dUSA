<template>
    <AppLayoutNoHeader title="Gestión de dias festivos">
        <div class="px-9 pt-3 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- attendance -->
            <div class="lg:hidden mx-auto w-4/5 rounded-[20px] bg-[#d9d9d9] py-3 px-5 flex flex-col space-y-2 mt-4">
                <figure class="flex justify-center">
                    <img class="w-[40%]" src="@/../../public/images/rainbow.png">
                </figure>
                <el-popconfirm v-if="nextAttendance && $page.props.auth.user.permissions.includes('Registrar asistencia')"
                    confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
                    @confirm="setAttendance">
                    <template #reference>
                        <SecondaryButton v-if="nextAttendance != 'Dia terminado'" class="self-center">
                            {{ nextAttendance }}
                        </SecondaryButton>
                        <span v-else class="bg-[#75b3f9] text-[#0355B5] self-center rounded-md px-3 py-1">
                            {{ nextAttendance }}
                        </span>
                    </template>
                </el-popconfirm>
                <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Crear kiosco')" confirm-button-text="Si"
                    cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?" @confirm="createKiosk">
                    <template #reference>
                        <el-tooltip v-if="$page.props.isKiosk || temporalFlag"
                            content="Se puede registrar asistencias desde este dispositivo">
                            <span class="bg-[#75b3f9] text-[#0355B5] self-center rounded-md px-3 py-1  text-xs">
                                Kiosco
                            </span>
                        </el-tooltip>
                        <SecondaryButton v-else class="self-center">
                            Hacer kiosco
                        </SecondaryButton>
                    </template>
                </el-popconfirm>
                <div v-if="$page.props.auth.user?.employee_properties"
                    class="text-xs text-[#0355B5] flex justify-around px-6">
                    <span>Horas / semana</span>
                    <span>{{ $page.props.week_time.formatted }} / {{
                        $page.props.auth.user?.employee_properties?.hours_per_week ?? 0 }}h</span>
                </div>
                <!-- <button class="text-xs text-primary hover:underline self-end">Ver detalles</button> -->
            </div>

            <hr class="lg:hidden block border-[#cccccc] mt-8 mb-6">

            <div class="flex justify-between items-center lg:mt-14 mt-0">
                <h2 class="text-primary lg:text-xl text-lg">Reuniones</h2>
                <thirthButton>Registrar reunion</thirthButton>
            </div>
            <MeetingCard :meetings="meetings" class="mt-4" />

            <!-- operative -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-16 mt-6">Operativo</h2>
            <div class="grid lg:grid-cols-4 grid-cols-2 gap-3 mt-4">
                <DashboardCard v-for="(quickCard, index) in quickCards" :key="index" :title="quickCard.title"
                    :counter="quickCard.counter" :icon="quickCard.icon" :href="quickCard.url" />
            </div>

            <!-- Collaborators -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-16 mt-6">Colaboradores</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 space-y-5 lg:space-y-0 mt-4">
                <ProductionPerformanceCard
                    :users="[{ id: 1, name: 'Miguel Osvaldo vr' }, { id: 2, name: 'Angel Adrian vr' }, { id: 2, name: 'Angel Adrian vr' }]" />
                <BirthdateCard
                    :users="[{ id: 1, name: 'Miguel Osvaldo vr', employee_properties: { job_position: 'Auxiliar de prodccion', birthdate: { formatted: '13 Marzo' } } }, { id: 1, name: 'Angel Adrian vr', employee_properties: { job_position: 'Jefe de prodccion', birthdate: { formatted: '13 Marzo' } } }]" />
                <RecentlyAddedCard
                    :users="[{ id: 1, name: 'Miguel Osvaldo vr', email: 'miguel@e3dusa.com', employee_properties: { job_position: 'Auxiliar de prodccion', department: 'Ventas' } }, { id: 1, name: 'Angel Adrian vr', email: 'angel@e3dusa.com', employee_properties: { job_position: 'Jefe de prodccion', department: 'Ventas' } }]" />
                <InformationCard :years="3"
                    :users="[{ id: 1, name: 'Miguel Osvaldo vr', employee_properties: { job_position: 'Auxiliar de prodccion', birthdate: { formatted: '13 Marzo' } } }, { id: 1, name: 'Angel Adrian vr', employee_properties: { job_position: 'Jefe de prodccion', birthdate: { formatted: '13 Marzo' } } }]" />
            </div>

            <!-- customers -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-16 mt-6">Clientes</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <BirthdateCardCustomer
                    :contacts="[{ id: 1, name: 'Miguel Osvaldo vr', email: 'miguel@e3dusa.com', contactable: { name: 'FORD QRO-CELAYA' } }, { id: 1, name: 'Angel Adrian vr', email: 'angel@e3dusa.com', contactable: { name: 'FORD QRO-CELAYA' } }]" />
            </div>
        </div>
    </AppLayoutNoHeader>
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import MeetingCard from '@/Components/MyComponents/MeetingCard.vue';
import DashboardCard from '@/Components/MyComponents/DashboardCard.vue';
import ProductionPerformanceCard from '@/Components/MyComponents/ProductionPerformanceCard.vue';
import BirthdateCard from '@/Components/MyComponents/BirthdateCard.vue';
import BirthdateCardCustomer from '@/Components/MyComponents/BirthdateCardCustomer.vue';
import RecentlyAddedCard from '@/Components/MyComponents/RecentlyAddedCard.vue';
import InformationCard from '@/Components/MyComponents/InformationCard.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayoutNoHeader from '@/Layouts/AppLayoutNoHeader.vue';
import axios from 'axios';

export default {
    data() {
        return {
            quickCards: [
                {
                    title: 'Cotizaciones por autorizar',
                    counter: 0,
                    icon: 'fa-solid fa-file-invoice',
                    url: route('quotes.index')
                },
                {
                    title: 'Órdenes de venta por autorizar',
                    counter: 1,
                    icon: 'fa-solid fa-clipboard',
                    url: route('sales.index')
                },
                {
                    title: 'Órdenes de compra por autorizar',
                    counter: 0,
                    icon: 'fa-solid fa-cart-shopping',
                    url: route('purchases.index')
                },
                {
                    title: 'Órden de diseño por autorizar',
                    counter: 0,
                    icon: 'fa-solid fa-pen-ruler',
                    url: route('designs.index')
                },
                {
                    title: 'Productos con existencia baja',
                    counter: 4,
                    icon: 'fa-solid fa-arrows-down-to-line',
                    url: route('raw-materials.index')
                },
                {
                    title: 'Órdenes de producción sin iniciar',
                    counter: 0,
                    icon: 'fa-solid fa-spinner',
                    url: route('dashboard')
                },
                {
                    title: 'Órdenes de diseño por iniciar',
                    counter: 10,
                    icon: 'fa-solid fa-compass-drafting',
                    url: route('dashboard')
                },
                {
                    title: 'Venta sin órden de producción',
                    counter: 0,
                    icon: 'fa-solid fa-list-check',
                    url: route('sales.index')
                },
                {
                    title: 'Horas adicionales por autorizar',
                    counter: 3,
                    icon: 'fa-solid fa-users',
                    url: route('admin-additional-times.index')
                },
            ],
            nextAttendance: null,
            temporalFlag: false
        }
    },
    props: {
        meetings: Object,
    },
    components: {
        ThirthButton,
        ApplicationLogo,
        MeetingCard,
        DashboardCard,
        PrimaryButton,
        SecondaryButton,
        ProductionPerformanceCard,
        InformationCard,
        RecentlyAddedCard,
        BirthdateCard,
        AppLayoutNoHeader,
        BirthdateCardCustomer
    },
    methods: {
        async getAttendanceTextButton() {
            try {
                const response = await axios.get(route('users.get-next-attendance'));
                this.nextAttendance = response.data.next;
            } catch (error) {
                console.error(error);
            }
        },
        async setAttendance() {
            try {
                const response = await axios.get(route('users.set-attendance'));
                if (response.status === 200) {
                    this.nextAttendance = response.data.next;
                    this.$notify({
                        title: 'Éxito',
                        message: 'Registro correcto',
                        type: 'success'
                    });
                }
            } catch (error) {
                console.error(error);
                this.$notify({
                    title: 'Error',
                    message: 'error:' + error.message,
                    type: 'error'
                });
            }
        },
        async createKiosk() {
            try {
                const response = await axios.post(route('kiosk.store'));

                if (response.status === 200) {

                    // Set cookie in browser
                    document.cookie = 'kioskToken=' + response.data.token + '; path=/; expires=Fri, 31 Dec 9999 23:59:59 GMT';
                    this.$notify({
                        title: 'Éxito',
                        message: 'Dispositivo agregado como kiosco',
                        type: 'success'
                    });

                    this.temporalFlag = true;
                }
            } catch (error) {
                console.error(error);
            }
        }
    }
}
</script>
