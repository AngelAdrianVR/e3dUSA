<template>
    <div class="h-auto py-3 px-2 border bg-[#d9d9d9] rounded-[20px] lg:rounded-lg">
        <template v-if="meetings.data.length">
            <div v-for="(meeting, index) in meetings.data" :key="meeting.id" class="lg:grid grid-cols-3 px-3 py-2 border-b border-[#a9a9a9]">
                <div>
                <Link v-if="meeting.user.id == $page.props.auth.user.id" class="hover:underline" :href="route('meetings.edit', meeting.id)">
                    <h2 class="font-bold ml-2 mb-3">{{ meeting.subject }}</h2>
                </Link>
                    <h2 v-else class="font-bold ml-2 mb-3">{{ meeting.subject }}</h2>
                    <p class="text-sm"><i class="fa-regular fa-calendar mr-1"></i> {{ meeting.date }} </p>
                    <p class="text-sm"><i class="fa-regular fa-clock mr-1"></i> {{ meeting.start.format }} - {{ meeting.end.format }} </p>
                    <p class="text-sm"><i class="fa-solid fa-location-dot mr-1"></i> {{ meeting.location }} </p>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-5 border-t-2 border-[#cccccc] pt-2 mt-2 lg:border-none col-span-2 text-sm">
                    <p>Anfitrión:</p> <span class="lg:col-span-4 truncate">{{ meeting.user.name }}</span>
                    <p>Participantes:</p> <span class="lg:col-span-4 truncate">{{ meeting.participants?.map(objeto => objeto.name).join(', ') }}</span>
                    <p>Descripción:</p> <span class="lg:col-span-4 truncate">{{ meeting.description }}</span>
                    <p v-if="meeting.user.id !== $page.props.auth.user.id">Asistenca:</p> <span v-if="meeting.user.id !== $page.props.auth.user.id" class="lg:col-span-4 flex space-x-2">
                        <div v-if="meeting.participants.find(item => item.id == $page.props.auth.user.id)?.pivot?.attendance_confirmation == 'Revisando'">
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                            title="¿Continuar?" @confirm="setAttendanceConfirmation('Confirmado', index)">
                            <template #reference>
                                <button class="text-sm text-white bg-green-500 rounded-[10px] py-px px-2 mr-3 disabled:opacity-25 disabled:cursor-not-allowed" :disabled="loading">Aceptar</button>
                            </template>
                        </el-popconfirm>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar ordenes de venta')" confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                            title="¿Continuar?" @confirm="setAttendanceConfirmation('Rechazado', index)">
                            <template #reference>
                                <button class="text-sm text-white bg-red-500 rounded-[10px] py-px px-2 mr-3 disabled:opacity-25 disabled:cursor-not-allowed" :disabled="loading">Rechazar</button>
                            </template>
                        </el-popconfirm>
                        <i v-if="loading" class="fa-solid fa-spinner fa-spin text-sm text-primary"></i>
                        </div>
                        <span class="text-sm text-green-500 flex items-center" v-else-if="meeting.participants.find(item => item.id == $page.props.auth.user.id)?.pivot?.attendance_confirmation == 'Confirmado'">
                            Solicitud aceptada <i class="fa-solid fa-check ml-3"></i>
                        </span>
                        <span class="text-sm text-red-500 flex items-center" v-else-if="meeting.participants.find(item => item.id == $page.props.auth.user.id)?.pivot?.attendance_confirmation == 'Rechazado'">
                            Solicitud rechazada <i class="fa-solid fa-xmark ml-3"></i>
                        </span>
                    </span>
                </div>
            </div>
        </template>

        <div v-else>
            <p class="text-center text-sm">No hay reuniones próximas</p>
        </div>
    </div>
</template>

<script>
import {Link} from "@inertiajs/vue3";

export default {
    data() {
        return {
            loading: false,
        }
    },
    props: {
        meetings: {
            type: Object,
            default: { data: [] }
        },
    },
    components: {
        Link,
    },
    methods: {
        async setAttendanceConfirmation(status, meetingIndex) {
            this.loading = true;
            try {
                const response = await axios.put(route('meetings.set-attendance-confirmation', this.meetings.data[meetingIndex].id), {
                    status: status
                });

                if (response.status === 200) {
                    let meetingUser = this.meetings.data[meetingIndex].participants.find(item => item.id == this.$page.props.auth.user.id);
                    meetingUser.pivot.attendance_confirmation = status;

                    this.$notify({
                        title: 'Éxito',
                        message: 'Se ha cambiado el status de la invitacion',
                        type: 'success'
                    });
                }
            } catch (error) {
                this.$notify({
                        title: 'Error',
                        message: error.message,
                        type: 'error'
                    });
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>