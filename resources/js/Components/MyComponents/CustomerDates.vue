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
            <form @submit.prevent="update()" class="lg:grid grid-cols-2 gap-3">
                <h2 class="font-normal col-span-full text-xs text-secondary text-start mb-4">Detalles del cliente</h2>
                <div>
                    <label for="customer" class="font-bold block text-start text-sm">Cliente</label>
                    <el-select @change="handleCompanySelected()" v-model="form.company_id" :disabled="!editForm"
                        no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias" clearable
                        filterable placeholder="Seleccionar *" class="w-full">
                        <el-option v-for="item in companies" :key="item.id" :label="item.business_name" :value="item.id" />
                    </el-select>
                    <!-- <InputError :message="branch.errors.sat_method" /> -->
                </div>
                <div>
                    <label for="company-branch" class="font-bold block text-start text-sm">Sucursal</label>
                    <el-select @change="handleBranchSelected()" v-model="form.company_branch_id" :disabled="!editForm"
                        no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias" clearable
                        filterable placeholder="Seleccionar *" class="w-full">
                        <el-option v-for="item in companyBranches" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                    <!-- <InputError :message="branch.errors.sat_method" /> -->
                </div>
                <div>
                    <label for="contact" class="font-bold block text-start text-sm">Contacto</label>
                    <el-select v-model="form.contact_id" :disabled="!editForm" no-data-text="No hay opciones por mostrar"
                        no-match-text="No se encontraron coincidencias" clearable filterable placeholder="Seleccionar *" class="w-full">
                        <el-option v-for="item in contacts" :key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                    <!-- <InputError :message="branch.errors.sat_method" /> -->
                </div>
                <div>
                    <label for="phone" class="font-bold block text-start text-sm">Teléfono</label>
                    <p class="block text-start font-normal text-sm mt-2">{{ contacts?.find(item => item.id ==
                        form.contact_id)?.phone }}</p>
                </div>
                <h2 class="font-normal col-span-full text-xs text-secondary text-start mt-4 mb-1">Detalles de la cita</h2>
                <div>
                    <label for="date" class="font-bold block text-start text-sm">Fecha</label>
                    <el-date-picker v-model="form.date" :disabled="!editForm" type="date" placeholder="Fecha" format="YYYY/MM/DD"
                        value-format="YYYY-MM-DD" />
                    <!-- <InputError :message="form.errors.branches.old_date" /> -->
                </div>
                <div>
                    <label for="time" class="font-bold block text-start text-sm">Hora</label>
                    <input v-model="form.time" :disabled="!editForm" type="time" class="bg-[#cccccc] text-sm rounded-md border-0 w-full">
                </div>
                <div>
                    <label for="date-type" class="font-bold block text-start text-sm">Cita</label>
                    <el-select v-model="form.date_type" :disabled="!editForm" no-data-text="No hay opciones por mostrar"
                        no-match-text="No se encontraron coincidencias" clearable filterable placeholder="Seleccionar *" class="w-full">
                        <el-option label="Presencial" value="Presencial" />
                        <el-option label="Llamada" value="Llamada" />
                        <el-option label="Virtual" value="Virtual" />
                    </el-select>
                    <!-- <InputError :message="branch.errors.sat_method" /> -->
                </div>
                <div>
                    <label for="location" class="font-bold block text-start text-sm">Ubicación</label>
                    <input v-model="form.location" :disabled="!editForm" type="text" class="bg-[#cccccc] text-sm rounded-md border-0 w-full">
                    <button v-if="editForm" class="text-xs text-primary underline block mr-auto">Agregar ubicación de la sucursal</button>
                </div>
                <div class="col-span-full">
                    <label for="reason" class="font-bold block text-start text-sm">Motivo</label>
                    <textarea v-model="form.reason" :disabled="!editForm" rows="3" class="bg-[#cccccc] text-sm rounded-md border-0 w-full"></textarea>
                </div>
            </form>
        </template>
        <template #content>
        </template>
        <template #footer>
            <div v-if="editForm" class="flex space-x-2">
                <CancelButton @click="editForm = false" class="bg-[#9A9A9A] text-white">
                    Cancelar
                </CancelButton>
                <PrimaryButton @click="">
                    Actualizar
                </PrimaryButton>
            </div>
            <div v-else class="flex space-x-4">
                <button class="text-sm text-primary block ml-auto">Ir a calendario</button>
                <PrimaryButton @click="editForm = true">
                    Editar
                </PrimaryButton>
            </div>
        </template>
    </DialogModal>
</template>
<script>
import DialogModal from '@/Components/DialogModal.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            company_id: 2,
            company_branch_id: 2,
            contact_id: 1,
            date: '23-09-2023',
            time: '17:15',
            date_type: 'Presencial',
            location: 'Av. Juan Gil preciado #336',
            reason: 'Porta Placas tipo Dalton, ver detalles y especificaciones de la solicitud, además de precios acordados',
        });

        return {
            form,
            editForm: false,
            showDetailsModal: false,
            selectedDate: null,
            companies: [],
            companyBranches: [],
            contacts: [],
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
        handleCompanySelected() {
            this.companyBranches = this.companies?.find(item => item.id == this.form.company_id)?.company_branches;
        },
        handleBranchSelected() {
            this.contacts = this.companyBranches?.find(item => item.id == this.form.company_branch_id)?.contacts;
        },
        update() {

        },
        async fetchCompanies() {
            try {
                const response = await axios.post(route('companies.get-all-companies'));

                if (response.status === 200) {
                    this.companies = response.data.items;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this.fetchCompanies();
        this.handleCompanySelected();
        this.handleBranchSelected();
    }
}
</script>