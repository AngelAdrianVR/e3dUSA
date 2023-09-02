<template>
    <AppLayoutNoHeader title="Inicio">
        <div class="px-9 pt-3 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <div class="my-5">
                <h2 class="text-primary lg:text-xl text-lg mb-3">Avisos</h2>
                <div class="rounded-xl border border-[#D90537]">
                    <p
                        class="bg-primary text-white text-center px-5 py-3 rounded-tl-xl rounded-tr-xl text-[16px] lg:text-xl">
                        <i class="fa-solid fa-bullhorn mr-7"></i>
                        Ajustes en registros de salidas
                    </p>
                    <p class="text-sm text-justify px-12 py-4">
                        Se notifica a todos los colaboradores de emblems3d que a partir del 8 de Septiembre del 2023,
                        los registros de salida después de las horas de su jornada diaria no contarán como horas adicionales
                        a menos que se apruebe una solicitud de tiempo adicional.
                    </p>
                </div>
            </div>

            <!-- attendance -->
            <div class="lg:hidden mx-auto w-4/5 rounded-[20px] bg-[#d9d9d9] py-3 px-5 flex flex-col space-y-2 mt-4">
                <div class="flex flex-col items-center space-y-2">
                    <p class="text-center">{{ greeting?.text }} <strong>{{ $page.props.auth.user.name }}</strong></p>
                    <i :class="greeting?.class"></i>
                </div>
                <el-popconfirm v-if="nextAttendance && $page.props.auth.user.permissions.includes('Registrar asistencia')"
                    confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
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
                    cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?" @confirm="createKiosk">
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
                <!-- <button v-if="nextAttendance == 'Registrar salida'"
                    class="rounded-full border-2 border-[#0355B5] text-secondary px-3 py-px mt-2">
                    <p v-if="isPaused" class="flex items-center">
                        <i class="fa-solid fa-play w-5 h-5 text-xs rounded-full border-2 border-[#0355B5] mr-4"></i>
                        <span>Reanudar labores</span>
                    </p>
                    <p v-else class="flex items-center">
                        <i class="fa-solid fa-pause w-5 h-5 text-xs rounded-full border-2 border-[#0355B5] mr-4"></i>
                        <span>Pausar labores</span>
                    </p>
                </button> -->
                <div v-if="$page.props.auth.user?.employee_properties"
                    class="text-xs text-[#0355B5] flex justify-around px-6">
                    <span>Horas / semana</span>
                    <span>{{ $page.props.week_time.formatted }} / {{
                        $page.props.auth.user?.employee_properties?.hours_per_week ?? 0 }}h</span>
                </div>
                <button @click="showPayrollModal = true" class="text-xs text-primary hover:underline self-end">Ver
                    detalles</button>
            </div>

            <hr class="lg:hidden block border-[#cccccc] mt-8 mb-6">

            <!-- meetings -->
            <div
                v-if="$page.props.auth.user.permissions.some(item => ['Reuniones personal', 'Reuniones todas'].includes(item))">
                <div class="flex justify-between items-center lg:mt-14 mt-0">
                    <h2 class="text-primary lg:text-xl text-lg">Reuniones</h2>
                    <Link :href="route('meetings.create')">
                    <thirthButton>Registrar reunion</thirthButton>
                    </Link>
                    <!-- <thirthButton @click="showMeetingModal = true">Registrar reunion</thirthButton> -->
                </div>
                <MeetingCard :meetings="meetings" class="mt-4" />
            </div>

            <!-- operative -->
            <div v-if="this.$page.props.auth.user.permissions.includes('Autorizar cotizaciones') ||
                this.$page.props.auth.user.permissions.includes('Autorizar ordenes de venta') ||
                this.$page.props.auth.user.permissions.includes('Autorizar ordenes de compra') ||
                this.$page.props.auth.user.permissions.includes('Autorizar ordenes de diseño') ||
                this.$page.props.auth.user.permissions.includes('Autorizar Crear materia prima') ||
                this.$page.props.auth.user.permissions.includes('Autorizar Ordenes de produccion todas') ||
                this.$page.props.auth.user.permissions.includes('Autorizar Ordenes de diseño todas') ||
                this.$page.props.auth.user.permissions.includes('Autorizar solicitudes de tiempo adicional')
                ">
                <h2 class="text-primary lg:text-xl text-lg lg:mt-16 mt-6">Operativo</h2>
                <div class="grid lg:grid-cols-4 grid-cols-2 gap-3 mt-4">
                    <template v-for="(quickCard, index) in quickCards" :key="index">
                        <DashboardCard v-if="quickCard.show" :title="quickCard.title" :counter="quickCard.counter"
                            :icon="quickCard.icon" :href="quickCard.url" />
                    </template>
                </div>
            </div>

            <!-- Collaborators -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-16 mt-6">Colaboradores</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 space-y-5 lg:space-y-0 mt-4">
                <ProductionPerformanceCard :users="collaborators_production_performance" />
                <DesignPerformanceCard :users="collaborators_design_performance" />
                <SalesPerformanceCard :users="collaborators_sales_performance" />
                <BirthdateCard :users="collaborators_birthdays" />
                <RecentlyAddedCard :users="collaborators_added" />
                <InformationCard :users="collaborators_anniversaires" />
            </div>

            <!-- customers -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-16 mt-6">Clientes</h2>
            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <BirthdateCardCustomer :contacts="customers_birthdays" />
            </div>
        </div>

        <DialogModal :show="showMeetingModal || showPayrollModal"
            @close="showMeetingModal = false; showPayrollModal = false">
            <template #title>
                <p v-if="showMeetingModal">Crear reunion</p>
                <div v-else>
                    <p>Nómina semanal</p>
                    <div class="w-full mb-5">
                        <select class="input h-10" v-model="payrollId" filterable placeholder="Buscar nómina">
                            <option v-for="item in payrolls" :key="item.id" :label="'Nómina semana: ' + item.week"
                                :value="item.id" />
                        </select>
                    </div>
                </div>
            </template>
            <template #content>
                <!-- Form -->
                <form v-if="showMeetingModal" @submit.prevent="store">
                    <div class="space-y-4">
                        <div>
                            <IconInput v-model="form.subject" inputPlaceholder="Título de reunion *" inputType="text">
                                <el-tooltip content="Título de reunión" placement="top">
                                    A
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.subject" />
                        </div>

                        <div class="mb-3">
                            <el-date-picker v-model="form.date" type="date" placeholder="Selecciona una fecha" />
                            <InputError :message="form.errors.date" />
                        </div>
                        <div class="flex items-center">
                            <div>
                                <el-time-picker v-model="form.start" placeholder="Hora de comienzo" format="HH:mm" />
                                <InputError :message="form.errors.start" />
                            </div>
                            <span>a</span>
                            <div class="ml-7">
                                <el-time-picker v-model="form.end" placeholder="Hora de finalización" format="HH:mm" />
                                <InputError :message="form.errors.end" />
                            </div>

                            <label class="flex items-center">
                                <Checkbox v-model:checked="form.repit" name="repit" class="bg-transparent" />
                                <span class="ml-2 text-sm text-[#9A9A9A]">Repetir</span>
                            </label>
                        </div>

                        <div class="flex items-center">
                            <el-tooltip content="Participantes de la reunion" placement="top">
                                <i class="fa-solid fa-users text-gray-700 mr-3"></i>
                            </el-tooltip>
                            <el-select v-model="form.participants" multiple placeholder="Participantes"
                                style="width: 240px">
                                <el-option v-for="item in users" :key="item.id" :label="item.name" :value="item.id" />
                            </el-select>
                            <InputError :message="form.errors.participants" />
                            <p @click="availableModal = true" class="text-primary ml-4 text-sm cursor-pointer">
                                Ver disponibilidad
                            </p>
                        </div>

                        <div class="w-3/5">
                            <IconInput v-model="form.subject" inputPlaceholder="Ubicación" inputType="text">
                                <el-tooltip content="Lugar de la reunion" placement="top">
                                    <i class="fa-solid fa-location-dot text-gray-700"></i>
                                </el-tooltip>
                            </IconInput>

                            <InputError :message="form.errors.location" />
                        </div>

                        <div class="flex items-center">
                            <IconInput v-model="form.subject" inputPlaceholder="Recordario" inputType="text" class="w-1/5">
                                <el-tooltip content="Recordatorios" placement="top">
                                    <i class="fa-solid fa-stopwatch text-gray-700"></i>
                                </el-tooltip>
                            </IconInput>

                            <InputError :message="form.errors.location" />

                            <p class="text-primary ml-4 text-sm cursor-pointer">
                                Agregar recordatorio
                            </p>
                        </div>

                        <div class="flex col-span-full">
                            <el-tooltip content="Descripción" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                    ...
                                </span>
                            </el-tooltip>
                            <textarea v-model="form.description" class="textarea" autocomplete="off"
                                placeholder="Descripción"></textarea>
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>
                </form>

                <PayrollTemplate v-else :user="$page.props.auth.user" :payrollId="payrollId" dontShowDetails />
            </template>
            <template #footer>
                <CancelButton @click="showMeetingModal = false; showPayrollModal = false" :disabled="form.processing">
                    Cancelar
                </CancelButton>
                <PrimaryButton v-if="showMeetingModal" :disabled="form.processing">
                    Guardar
                </PrimaryButton>
            </template>
        </DialogModal>
    </AppLayoutNoHeader>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import MeetingCard from '@/Components/MyComponents/MeetingCard.vue';
import DashboardCard from '@/Components/MyComponents/DashboardCard.vue';
import ProductionPerformanceCard from '@/Components/MyComponents/ProductionPerformanceCard.vue';
import DesignPerformanceCard from '@/Components/MyComponents/DesignPerformanceCard.vue';
import SalesPerformanceCard from '@/Components/MyComponents/SalesPerformanceCard.vue';
import BirthdateCard from '@/Components/MyComponents/BirthdateCard.vue';
import BirthdateCardCustomer from '@/Components/MyComponents/BirthdateCardCustomer.vue';
import RecentlyAddedCard from '@/Components/MyComponents/RecentlyAddedCard.vue';
import InformationCard from '@/Components/MyComponents/InformationCard.vue';
import ThirthButton from '@/Components/MyComponents/ThirthButton.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayoutNoHeader from '@/Layouts/AppLayoutNoHeader.vue';
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import PayrollTemplate from "@/Components/MyComponents/payrollTemplate.vue";
import Checkbox from "@/Components/Checkbox.vue";
import axios from 'axios';
import { useForm, Link } from '@inertiajs/vue3';

export default {
    data() {
        const form = useForm({
            subject: null,
            location: null,
            url: null,
            description: null,
            date: null,
            start: null,
            end: null,
            repit: null,
            participants: null,
        });

        return {
            quickCards: [
                {
                    title: 'Cotizaciones por autorizar',
                    counter: this.counts[0],
                    icon: 'fa-solid fa-file-invoice',
                    url: route('quotes.index'),
                    show: this.$page.props.auth.user.permissions.includes('Autorizar cotizaciones')
                },
                {
                    title: 'Órdenes de venta por autorizar',
                    counter: this.counts[1],
                    icon: 'fa-solid fa-clipboard',
                    url: route('sales.index'),
                    show: this.$page.props.auth.user.permissions.includes('Autorizar ordenes de venta')
                },
                {
                    title: 'Órdenes de compra por autorizar',
                    counter: this.counts[2],
                    icon: 'fa-solid fa-cart-shopping',
                    url: route('purchases.index'),
                    show: this.$page.props.auth.user.permissions.includes('Autorizar ordenes de compra')
                },
                {
                    title: 'Órden de diseño por autorizar',
                    counter: this.counts[3],
                    icon: 'fa-solid fa-pen-ruler',
                    url: route('designs.index'),
                    show: this.$page.props.auth.user.permissions.includes('Autorizar ordenes de diseño')
                },
                {
                    title: 'Productos con existencia baja',
                    counter: this.counts[4],
                    icon: 'fa-solid fa-arrows-down-to-line',
                    url: route('storages.raw-materials.index'),
                    show: this.$page.props.auth.user.permissions.includes('Crear materia prima')
                },
                {
                    title: 'Tareas asignadas a operadores sin iniciar',
                    counter: this.counts[5],
                    icon: 'fa-solid fa-spinner',
                    url: route('productions.index'),
                    show: this.$page.props.auth.user.permissions.includes('Ordenes de produccion todas')
                },
                {
                    title: 'Órdenes de diseño sin iniciar',
                    counter: this.counts[6],
                    icon: 'fa-solid fa-compass-drafting',
                    url: route('designs.index'),
                    show: this.$page.props.auth.user.permissions.includes('Ordenes de diseño todas')
                },
                {
                    title: 'Venta sin órden de producción',
                    counter: this.counts[7],
                    icon: 'fa-solid fa-list-check',
                    url: route('sales.index'),
                    show: this.$page.props.auth.user.permissions.includes('Ordenes de produccion todas')
                },
                {
                    title: 'Horas adicionales por autorizar',
                    counter: this.counts[8],
                    icon: 'fa-solid fa-users',
                    url: route('admin-additional-times.index'),
                    show: this.$page.props.auth.user.permissions.includes('Autorizar solicitudes de tiempo adicional')
                },
            ],
            nextAttendance: null,
            temporalFlag: false,
            showMeetingModal: false,
            form,
            currentTime: new Date().getHours(),
            greeting: null,
            showPayrollModal: false,
            payrollId: null,
            payrolls: null,
        }
    },
    props: {
        meetings: Object,
        counts: Array,
        collaborators_production_performance: Array,
        collaborators_design_performance: Array,
        collaborators_sales_performance: Array,
        collaborators_birthdays: Array,
        collaborators_added: Array,
        collaborators_anniversaires: Array,
        customers_birthdays: Array,
    },
    components: {
        ThirthButton,
        MeetingCard,
        DashboardCard,
        PrimaryButton,
        SecondaryButton,
        ProductionPerformanceCard,
        DesignPerformanceCard,
        SalesPerformanceCard,
        InformationCard,
        RecentlyAddedCard,
        BirthdateCard,
        AppLayoutNoHeader,
        BirthdateCardCustomer,
        DialogModal,
        InputError,
        IconInput,
        CancelButton,
        Checkbox,
        Link,
        PayrollTemplate,
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
        },
        updateGreeting() {
            if (this.currentTime >= 0 && this.currentTime < 12) {
                this.greeting = { text: "Buenos días ", class: "fa-solid fa-sun text-yellow-500 text-6xl" };
            } else if (this.currentTime >= 12 && this.currentTime < 19) {
                this.greeting = { text: "Buenas tardes ", class: "fa-solid fa-cloud-sun text-orange-500 text-6xl" };
            } else {
                this.greeting = { text: "Buenas noches ", class: "fa-solid fa-moon text-purple-500 text-6xl" };
            }
        },
        async getAllPayrolls() {
            try {
                const response = await axios.post(route('payrolls.get-all-payrolls'));

                if (response.status === 200) {
                    this.payrolls = response.data.items;
                    this.payrollId = this.payrolls[0].id;
                }
            } catch (error) {
                console.log(error.message);
            }
        },
    },
    mounted() {
        // Actualizar el saludo inicial
        this.updateGreeting();

        // Actualizar el saludo cada minuto
        setInterval(() => {
            this.currentTime = new Date().getHours();
            this.updateGreeting();
        }, 60000); // 60000 ms = 1 minuto

        this.getAllPayrolls();
    }
}
</script>
