<template>
    <div class="rounded-[30px] lg:rounded-[20px] bg-[#D9D9D9] py-4 px-6 text-sm">
        <h2 class="text-black font-bold flex justify-between items-center">
            <p>Oportunidades de tiempo extra</p>
            <button @click="showCreateModal = true"
                class="w-6 h-6 border border-transparent hover:border-primary rounded-full text-primary">
                <i class="fa-solid fa-plus"></i>
            </button>
        </h2>
        <div v-if="loading" class="animate-pulse flex space-x-4">
            <div class="flex-1 space-y-3 py-1 h-28 mt-5">
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
                <div class="h-4 bg-[#a9a9a9] rounded"></div>
            </div>
        </div>
        <div v-else class="mt-5 h-28">
            <table v-if="extraTimeRequests.length" class="w-full">
                <thead>
                    <tr>
                        <th class="text-start px-1">Operador</th>
                        <th class="text-start px-1">Fecha</th>
                        <th class="text-start px-1">Hrs solicitadas</th>
                        <th class="text-start px-1">Estatus</th>
                        <th class="text-start px-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in extraTimeRequests" :key="item.id" class="text-xs">
                        <td class="px-1 truncate">{{ item.operator.name }}</td>
                        <td class="px-1">{{ formatDate(item.date) }}</td>
                        <td class="px-1">{{ item.hours }}</td>
                        <td class="px-1" :class="{'text-[#31a126]': item.is_accepted == true, 'text-primary': item.is_accepted == false }">{{ getStatus(item.is_accepted) }}</td>
                        <td class="px-1">
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                title="¿Continuar?" @confirm="deleteExtraTimeRequest(item.id)">
                                <template #reference>
                                    <button
                                        class="text-primary text-xs">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </template>
                            </el-popconfirm>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="text-xs text-gray-400 text-center py-3">No hay solicitudes próximas</p>
        </div>

        <DialogModal :show="showCreateModal" @close="showCreateModal = false">
            <template #title>
                Crear solicitud de tiempo extra
            </template>
            <template #content>
                <form @submit.prevent="store" ref="createForm" class="lg:grid grid-cols-2 gap-2">
                    <div class="col-span-full">
                        <label class="text-sm ml-2">Operador *</label>
                        <el-select v-model="form.operator_id" placeholder="Selecciona el operador" filterable>
                            <el-option v-for="item in operators" :key="item.id" :label="item.name" :value="item.id">
                                <div v-if="$page.props.jetstream.managesProfilePhotos"
                                    class="flex text-sm rounded-full items-center mt-[3px]">
                                    <img class="h-7 w-7 rounded-full object-cover mr-4" :src="item.profile_photo_url"
                                        :alt="item.name" />
                                    <p>{{ item.name }}</p>
                                </div>
                            </el-option>
                        </el-select>
                        <InputError :message="form.errors.operator_id" />
                    </div>
                    <div>
                        <label class="text-sm ml-2 block">Fecha *</label>
                        <el-date-picker v-model="form.date" type="date" placeholder="Fecha" format="DD-MM-YYYY"
                            value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
                        <InputError :message="form.errors.date" />
                    </div>
                    <div>
                        <label class="text-sm ml-2">Horas solicitadas *</label>
                        <input v-model="form.hours" placeholder="Ingresa la cantidad del producto" class="input"
                            type="number" min="1" required>
                        <InputError :message="form.errors.hours" />
                    </div>
                </form>
            </template>
            <template #footer>
                <CancelButton @click="showCreateModal = false" :disabled="form.processing">Cancelar</CancelButton>
                <PrimaryButton @click="submitForm" :disabled="form.processing">Crear</PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
<script>
import axios from 'axios';
import DialogModal from '@/Components/DialogModal.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        const form = useForm({
            operator_id: null,
            date: null,
            hours: 1,
        });

        return {
            form,
            extraTimeRequests: [],
            operators: [],
            loading: true,
            showCreateModal: false,
        };
    },
    components: {
        DialogModal,
        CancelButton,
        PrimaryButton,
        InputError,
    },
    methods: {
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMMM', { locale: es }); // Formato personalizado
        },
        async deleteExtraTimeRequest(extraTimeRequestId) {
            try {
                const response = await axios.delete(route('extra-time-requests.destroy', extraTimeRequestId));

                if (response.status === 200) {
                    this.fetchRequests();
                    this.$notify({
                        title: 'Éxito',
                        message: 'Se eliminó correctamente.',
                        type: 'success',
                    });
                }
            } catch (error) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: 'no se pudo eliminar la oportunidad de tiempo extra por inconvenientes en el servidor. Inténtalo más tarde',
                    type: 'error',
                });
                console.log(error);
            }
        },
        getStatus(isAccepted) {
            if (isAccepted === null) return 'En espera de respuesta';
            else if (isAccepted) return 'Aceptada';
            else return 'Rechazada';
        },
        submitForm() {
            this.$refs.createForm.dispatchEvent(new Event('submit', { cancelable: true }));
        },
        store() {
            this.form.post(route('extra-time-requests.store'), {
                onSuccess: () => {
                    this.fetchRequests();
                    this.$notify({
                        title: 'Éxito',
                        message: 'Se envió la solicitud correctamente. Espera la respuesta',
                        type: 'success',
                    });
                    this.showCreateModal = false;
                    this.form.reset();
                },
            });
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return time.getTime() < today.getTime();
        },
        async fetchRequests() {
            try {
                const response = await axios.get(route('extra-time-requests.index'));

                if (response.status === 200) {
                    this.extraTimeRequests = response.data.items;
                }
            } catch (error) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: 'no se pudieron obtener las oportunidades de tiempo extra por inconvenientes en el servidor. Inténtalo más tarde',
                    type: 'error',
                });
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchOperators() {
            try {
                this.loading = true;
                const response = await axios.get(route('users.get-operators'));

                if (response.status === 200) {
                    this.operators = response.data.items;
                }
            } catch (error) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: 'No se pudo obtener la lista de operadores por inconvenientes en el servidor. Inténtalo más tarde',
                    type: 'error',
                });
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fetchRequests();
        this.fetchOperators();
    }
}
</script>