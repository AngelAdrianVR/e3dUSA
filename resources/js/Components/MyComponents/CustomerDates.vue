<template>
    <div class="lg:h-52 h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:py-2 py-2 px-8 text-xs lg:text-sm">
        <div class="my-3 col-start-2 col-span-2">
            <h3 class="text-center text-gray-700">Citas próximas <i class="fa-regular fa-calendar-check ml-2"></i></h3>
        </div>
        <div v-if="dates.length" class="h-full">
            <div class="h-2/3 overflow-y-scroll">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="text-xs">
                            <th>Contacto</th>
                            <th class="text-start">Fecha</th>
                            <th class="text-start">Hora</th>
                            <th class="text-start">Cita</th>
                            <th class="text-start">Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr @click="prepareModal()" v-for="date in dates" :key="date.id"
                            class="text-xs w-full cursor-pointer hover:bg-[#cccccc]">
                            <td class="w-1/5 py-1 rounded-tl-lg rounded-bl-lg"><i class="fa-regular fa-user mr-2"></i> {{
                                date.contact }}</td>
                            <td class="w-1/5">{{ date.date }}</td>
                            <td class="w-1/5">{{ date.time }}</td>
                            <td class="w-1/5">{{ date.type }}</td>
                            <td class="w-1/5 truncate rounded-tr-lg rounded-br-lg">{{ date.reason }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mx-6">
                <button class="text-primary text-xs">Ver todas</button>
            </div>
        </div>
        <p v-else class="text-gray-400 text-center mt-8">No hay citas proximas registradas</p>
    </div>
    <DialogModal :show="showDetailsModal" @close="showDetailsModal = false">
        <template #title>
            <h1 class="text-lg"><i class="fa-regular fa-calendar-check mr-2 mb-4"></i> Resumen de cita </h1>
            <h2 class="text-xs text-secondary text-start mb-4">Detalles del cliente</h2>
            <form @submit.prevent="update()" class="lg:grid grid-cols-2 gap-3">
                <div>
                    <label for="customer" class="font-bold">Cliente</label>
                    <el-select v-model="form.customer_id" placeholder="Seleccionar *" :fit-input-width="true">
                        <el-option v-for="item in customers" :key="item.id" :value="item.name" />
                    </el-select>
                    <!-- <InputError :message="branch.errors.sat_method" /> -->
                </div>
            </form>
        </template>
        <template #content>
        </template>
        <template #footer>
            <CancelButton @click="showDetailsModal = false">
                Cerrar
            </CancelButton>
            <PrimaryButton @click="">
                Guardar cambios
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
<script>
import DialogModal from '@/Components/DialogModal.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    data() {
        const form = useForm({
            customer_id: 1,
        });

        return {
            form,
            showDetailsModal: false,
            selectedDate: null,
            customers: [
                {
                    id: 1,
                    name: 'GoRinho'
                },
                {
                    id: 2,
                    name: 'Honda'
                },
                {
                    id: 3,
                    name: 'FIFA'
                },
            ]
        };
    },
    components: {
        DialogModal,
        CancelButton,
        PrimaryButton,
    },
    props: {
        dates: Array,
    },
    methods: {
        prepareModal(date) {
            this.selectedDate = date;
            this.showDetailsModal = true;
        },
        update() {

        },
    }
}
</script>