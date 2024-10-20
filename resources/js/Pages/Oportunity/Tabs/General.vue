<template>
    <div class="md:grid grid-cols-2 text-sm">
        <div class="grid grid-cols-2 gap-1 text-left p-4 border-r-2 border-gray-[#cccccc] items-center">
            <p class="text-secondary col-span-2 mb-2">Información de la oportunidad</p>
            <span class="text-gray-500">Folio</span>
            <span>{{ opportunity.folio }}</span>
            <span class="text-gray-500">Nombre de la oportunidad</span>
            <span>{{ opportunity.name }}</span>
            <span class="text-gray-500">Descripción</span>
            <span v-html="opportunity.description"></span>
            <span class="text-gray-500">Creado por</span>
            <span>{{ opportunity.user?.name }}</span>
            <span class="text-gray-500">Responsable</span>
            <span>{{ opportunity.seller?.name }}</span>
            <span class="text-gray-500">Estatus</span>
            <div class="flex items-center relative">
                <div :class="getColorStatus()" class="absolute -left-10 top-5 rounded-full w-3 h-3"></div>
                <el-select @change="status == 'Perdida' ? showLostOportunityModal = true
                    : status == 'Cerrada' || status == 'Pagado' ? showCreateSaleModal = true
                        : updateStatus()" class="lg:w-1/2 mt-2" v-model="status" filterable
                    placeholder="Seleccionar estatus" no-data-text="No hay estatus registrados"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in statuses" :key="item" :label="item.label" :value="item.label">
                        <span style="float: left"><i :class="item.color" class="fa-solid fa-circle"></i></span>
                        <span style="float: center; margin-left: 5px; font-size: 13px">
                            {{ item.label }}
                        </span>
                    </el-option>
                </el-select>
            </div>
            <span class="text-gray-500">Prioridad</span>
            <span class="relative">{{ opportunity.priority.label }} <div :class="getColorPriority()"
                    class="absolute -left-10 top-1 rounded-full w-3 h-3"></div></span>
            <span class="text-gray-500">Probabilidad</span>
            <span>{{ opportunity.probability }}%</span>
            <span class="text-gray-500">Valor de oportunidad</span>
            <span>${{ opportunity.amount?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>
            <span class="text-gray-500">Fecha de inicio</span>
            <span>{{ opportunity.start_date }}</span>
            <span class="text-gray-500">Fecha estimada de cierre</span>
            <span>{{ opportunity.estimated_finish_date }}</span>

            <p class="text-secondary col-span-2 mt-4 mb-2">Información del cliente</p>
            <span class="text-gray-500">Cliente</span>
            <span>{{ opportunity.company_name ? opportunity.company_name :
                opportunity.company.business_name }}</span>
            <span class="text-gray-500">Sucursal</span>
            <span>{{ opportunity.companyBranch?.name ?? '--' }}</span>
            <span class="text-gray-500">Contacto</span>
            <span>{{ opportunity.contact }}</span>
            <span class="text-gray-500">Teléfono</span>
            <span>{{ opportunity.contact_phone }}</span>
            <span v-if="opportunity.lost_oportunity_razon" class="text-gray-500">Causa de pérdida</span>
            <span class="bg-red-300 py-1 px-2 rounded-full" v-if="opportunity.lost_oportunity_razon">{{
                opportunity.lost_oportunity_razon }}</span>
        </div>
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center self-start">
            <p class="text-secondary col-span-2 mb-2">Usuarios</p>
            <ul v-if="opportunity.users.length">
                <template v-for="item in opportunity.users" :key="item.id">
                    <li v-if="item.id != 1" class="text-gray-500">
                        {{ item.name }}
                    </li>
                </template>
            </ul>
            <p class="text-sm text-gray-400" v-else><i class="fa-solid fa-user-slash mr-3"></i>No hay tareas asignadas a
                usuarios</p>
            <p class="text-secondary col-span-full mb-2 mt-5">Archivos adjuntos</p>
            <div v-if="opportunity.media?.length" class="col-span-full">
                <li v-for="file in opportunity.media" :key="file" class="flex justify-between">
                    <a :href="file.original_url" target="_blank" class="flex space-x-2">
                        <i :class="getFileTypeIcon(file.file_name)" class="mt-1"></i>
                        <span>{{ file.file_name }}</span>
                    </a>
                </li>
            </div>
            <p class="text-sm text-gray-400" v-else>
                <i class="fa-regular fa-file-excel mr-3"></i>
                No hay archivos adjuntos en esta oportunidad
            </p>
            <p class="text-secondary col-span-full mt-7 mb-2">Etiquetas</p>
            <div class="col-span-full flex space-x-3">
                <Tag v-for="(item, index) in opportunity.tags" :key="index" :name="item.name" :color="item.color" />
                <p v-if="!opportunity.tags.length" class="text-gray-400 text-xs mt-1">No hay etiquetas en la oportunidad
                </p>
            </div>
            <div class="flex items-center justify-end space-x-2 col-span-2 mr-4">
                <el-tooltip content="Agendar reunión" placement="top">
                    <i @click="$inertia.get(route('meeting-monitors.create'), { opportunityId: opportunity.id })"
                        class="fa-regular fa-calendar text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                </el-tooltip>
                <el-tooltip content="Registrar pago" placement="top">
                    <i @click="$inertia.get(route('payment-monitors.create'), { opportunityId: opportunity.id })"
                        class="fa-solid fa-money-bill text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                </el-tooltip>
                <el-tooltip content="Interacción por correo" placement="top">
                    <i @click="$inertia.get(route('email-monitors.create'), { opportunityId: opportunity.id })"
                        class="fa-regular fa-envelope text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                </el-tooltip>
                <el-tooltip content="Interacción WhatsApp" placement="top">
                    <i @click="$inertia.get(route('whatsapp-monitors.create'), { opportunityId: opportunity.id })"
                        class="fa-brands fa-whatsapp text-primary cursor-pointer text-lg px-3 border-r border-[#9a9a9a]"></i>
                </el-tooltip>
                <el-tooltip content="Registrar llamada" placement="top">
                    <i @click="$inertia.get(route('call-monitors.create'), { opportunityId: opportunity.id })"
                        class="fa-solid fa-phone text-primary cursor-pointer text-lg px-3"></i>
                </el-tooltip>
            </div>
        </div>
    </div>

    <Modal :show="showLostOportunityModal || showCreateSaleModal"
        @close="showLostOportunityModal = false; showCreateSaleModal = false">
        <section v-if="showLostOportunityModal" class="mx-7 my-4 space-y-4 relative">
            <div>
                <label>Causa oportunidad perdida
                    <el-tooltip content="Escribe la causa por la cual se PERDIÓ esta oportunidad" placement="top">
                        <i class="fa-regular fa-circle-question ml-2 text-primary text-xs"></i>
                    </el-tooltip>
                </label>
                <textarea v-model="lost_oportunity_razon" class="textarea mt-3"></textarea>
            </div>
            <div class="flex justify-end space-x-3 pt-5 pb-1">
                <CancelButton @click="showLostOportunityModal = false">Cancelar</CancelButton>
                <PrimaryButton :disabled="!lost_oportunity_razon" @click="updateStatus">Actualizar estatus
                </PrimaryButton>
            </div>
        </section>
        <section v-if="showCreateSaleModal" class="mx-7 my-4 space-y-4 relative">
            <div>
                <h2 class="font bold text-center font-bold mb-5">Paso clave - Crear Orden de Venta</h2>
                <p class="px-5">Es necesario crear una orden de venta al haber marcado como <span
                        class="text-[#FD8827]">”cerrada”</span>
                    o <span class="text-[#37951F]">”Pagada”</span> la oportunidad para llevar un correcto seguimiento y
                    flujo de
                    trabajo.
                </p>
            </div>
            <div class="flex justify-end space-x-3 pt-5 pb-1">
                <CancelButton @click="cancelUpdating">Cancelar</CancelButton>
                <PrimaryButton @click="CreateSale">Continuar</PrimaryButton>
            </div>
        </section>
    </Modal>
</template>
<script>
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import Tag from '@/Components/MyComponents/Tag.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
    data() {
        return {
            showLostOportunityModal: false,
            showCreateSaleModal: false,
            status: this.opportunity.status,
            statuses: [
                {
                    label: "Nueva",
                    color: "text-[#d9d9d9]",
                },
                {
                    label: "Pendiente",
                    color: "text-[#F3FD85]",
                },
                {
                    label: "Cerrada",
                    color: "text-[#FEDBBD]",
                },
                {
                    label: "Pagado",
                    color: "text-[#AFFDB2]",
                },
                {
                    label: "Perdida",
                    color: "text-[#F7B7FC]",
                },
            ],
        }
    },
    props: {
        opportunity: Object,
    },
    components: {
        Tag,
        CancelButton,
        PrimaryButton,
    },
    methods: {
        getColorStatus() {
            if (this.opportunity.status == "Nueva") {
                return "bg-gray-300 text-[#97989A]";
            } else if (this.opportunity.status == "Pendiente") {
                return "bg-[#F3FD85] text-[#C88C3C]";
            } else if (this.opportunity.status == "Cerrada") {
                return "bg-[#FEDBBD] text-[#FD8827]";
            } else if (this.opportunity.status == "Pagado") {
                return "bg-[#AFFDB2] text-[#37951F]";
            } else if (this.opportunity.status == "Perdida") {
                return "bg-[#F7B7FC] text-[#9E0FA9]";
            } else {
                return "bg-transparent";
            }
        },
        getColorPriority() {
            if (this.opportunity.priority?.label == "Baja") {
                return "bg-[#87CEEB]";
            } else if (this.opportunity.priority?.label == "Media") {
                return "bg-[#D97705]";
            } else if (this.opportunity.priority?.label == "Alta") {
                return "bg-[#D90537]";
            } else {
                return "bg-transparent";
            }
        },
        getFileTypeIcon(fileName) {
            // Asocia extensiones de archivo a iconos
            const fileExtension = fileName.split('.').pop().toLowerCase();
            switch (fileExtension) {
                case 'pdf':
                    return 'fa-regular fa-file-pdf text-red-700';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'fa-regular fa-image text-blue-300';
                default:
                    return 'fa-regular fa-file-lines';
            }
        },
        async updateStatus() {
            try {
                const response = await axios.put(route('oportunities.update-status', this.opportunity.id), {
                    status: this.status,
                    lost_oportunity_razon: this.lost_oportunity_razon
                });
                if (response.status === 200) {
                    this.$notify({
                        title: "Éxito",
                        message: "Se ha actulizado el estatus de la oportunidad",
                        type: "success",
                    });
                    this.showLostOportunityModal = false;
                    this.showCreateSaleModal = false;
                    if (this.lost_oportunity_razon) {
                        this.opportunity.lost_oportunity_razon = this.lost_oportunity_razon;
                        this.lost_oportunity_razon = null;
                    } else {
                        this.opportunity.lost_oportunity_razon = null;
                    }
                    this.opportunity.finished_at = response.data.item.finished_at;
                    this.opportunity.status = this.status;
                }
            } catch (error) {
                console.log(error);
            }
        },
    },
}
</script>