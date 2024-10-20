<template>
    <div class="mx-auto my-8">
        <div v-if="opportunity.clientMonitors?.length" class="overflow-x-auto">
            <table class="w-full mx-auto text-sm">
                <thead>
                    <tr class="text-center">
                        <th class="font-bold pb-5">
                            Folio <i class="fa-solid fa-arrow-down-long ml-3"></i>
                        </th>
                        <th class="font-bold pb-5">
                            Tipo que interacciones <i class="fa-solid fa-arrow-down-long ml-3"></i>
                        </th>
                        <th class="font-bold pb-5">
                            Fecha <i class="fa-solid fa-arrow-down-long ml-3"></i>
                        </th>
                        <th class="font-bold pb-5">
                            Concepto <i class="fa-solid fa-arrow-down-long ml-3"></i>
                        </th>
                        <th class="font-bold pb-5">
                            Vededor <i class="fa-solid fa-arrow-down-long ml-3"></i>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr @click="showMonitorType(monitor)" v-for="monitor in opportunity.clientMonitors" :key="monitor"
                        class="mb-4 hover:bg-[#dfdbdba8] cursor-pointer">
                        <td class="text-center py-2 px-2 rounded-l-full text-secondary">
                            {{ monitor.folio }}
                        </td>
                        <td class="text-center py-2 px-2">
                            <span class="py-1 px-4 rounded-full">{{ monitor.type }}</span>
                        </td>
                        <td class="text-center py-2 px-2">
                            <span class="py-1 px-2 rounded-full">{{ monitor.date }}</span>
                        </td>
                        <td class="text-center py-2 px-2">
                            {{ monitor.concept }}
                        </td>
                        <td class="text-center py-2 px-2 text-secondary">
                            {{ monitor.seller.name }}
                        </td>
                        <td v-if="$page.props.auth.user.permissions.includes('Eliminar tareas de oportunidades')"
                            class="text-center py-2 px-2 rounded-r-full">
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537"
                                title="¿Eliminar?" @confirm="deleteClientMonitor(monitor)">
                                <template #reference>
                                    <i @click.stop=""
                                        class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
                                </template>
                            </el-popconfirm>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p class="text-sm text-center text-gray-400">No hay seguimiento en esta oportunidad</p>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {

        }
    },
    props: {
        opportunity: Object,
    },
    components: {
    },
    methods: {
        showMonitorType(monitor) {
            if (monitor.type == 'Correo') {
                this.$inertia.get(route('email-monitors.show', monitor.emailMonitor?.id));
            } else if (monitor.type == 'Pago') {
                this.$inertia.get(route('payment-monitors.show', monitor.paymentMonitor?.id));
            } else if (monitor.type == 'Reunión') {
                this.$inertia.get(route('meeting-monitors.show', monitor.mettingMonitor?.id));
            } else if (monitor.type == 'WhatsApp') {
                this.$inertia.get(route('whatsapp-monitors.show', monitor.whatsappMonitor?.id));
            }
        },
        async deleteClientMonitor(monitor) {
            try {
                const response = await axios.delete(route('client-monitors.destroy', monitor.id));

                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha eliminado correctamente",
                        type: "success",
                    });
                    const index = this.opportunity.clientMonitors.findIndex(item => item.id === monitor.id);

                    if (index !== -1) {
                        this.opportunity.clientMonitors.splice(index, 1);
                    }
                }
            } catch (error) {
                console.log(error);
            }
        },
    },
}
</script>