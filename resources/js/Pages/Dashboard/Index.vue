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
                <thirthButton @click="showMeetingModal = true">Registrar reunion</thirthButton>
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

        <DialogModal :show="showMeetingModal" @close="showMeetingModal = false">
            <template #title>

            </template>
            <template #content>
                <!-- Form -->
                <form @submit.prevent="store">
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
            </template>
            <template #footer>
                <CancelButton @click="showMeetingModal = false" :disabled="form.processing">
                    Cancelar
                </CancelButton>
                <PrimaryButton :disabled="form.processing">
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
import Checkbox from "@/Components/Checkbox.vue";
import axios from 'axios';
import { useForm } from '@inertiajs/vue3';

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
                    url: route('quotes.index')
                },
                {
                    title: 'Órdenes de venta por autorizar',
                    counter: this.counts[1],
                    icon: 'fa-solid fa-clipboard',
                    url: route('sales.index')
                },
                {
                    title: 'Órdenes de compra por autorizar',
                    counter: this.counts[2],
                    icon: 'fa-solid fa-cart-shopping',
                    url: route('purchases.index')
                },
                {
                    title: 'Órden de diseño por autorizar',
                    counter: this.counts[3],
                    icon: 'fa-solid fa-pen-ruler',
                    url: route('designs.index')
                },
                {
                    title: 'Productos con existencia baja',
                    counter: this.counts[4],
                    icon: 'fa-solid fa-arrows-down-to-line',
                    url: route('raw-materials.index')
                },
                {
                    title: 'Órdenes de producción sin iniciar',
                    counter: this.counts[5],
                    icon: 'fa-solid fa-spinner',
                    url: route('dashboard')
                },
                {
                    title: 'Órdenes de diseño por iniciar',
                    counter: this.counts[6],
                    icon: 'fa-solid fa-compass-drafting',
                    url: route('dashboard')
                },
                {
                    title: 'Venta sin órden de producción',
                    counter: this.counts[7],
                    icon: 'fa-solid fa-list-check',
                    url: route('sales.index')
                },
                {
                    title: 'Horas adicionales por autorizar',
                    counter: this.counts[8],
                    icon: 'fa-solid fa-users',
                    url: route('admin-additional-times.index')
                },
            ],
            nextAttendance: null,
            temporalFlag: false,
            showMeetingModal: false,
            form,
        }
    },
    props: {
        meetings: Object,
        counts: Array,
    },
    components: {
        ThirthButton,
        MeetingCard,
        DashboardCard,
        PrimaryButton,
        SecondaryButton,
        ProductionPerformanceCard,
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
