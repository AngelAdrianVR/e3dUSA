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
                    <label for="customer" class="font-bold block text-start text-sm">Cliente</label>
                    <el-select @change="handleCompanySelected()" v-model="form.company_id"
                        no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias"
                        clearable filterable placeholder="Seleccionar *">
                        <el-option v-for="item in companyBranches" ::key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                    <!-- <InputError :message="branch.errors.sat_method" /> -->
                </div>
                <div>
                    <label @change="handleBranchSelected()" for="company-branch" class="font-bold block text-start text-sm">Sucursal</label>
                    <el-select v-model="form.company_branch_id"
                        no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias"
                        clearable filterable placeholder="Seleccionar *">
                        <el-option v-for="item in contacts" ::key="item.id" :label="item.name" :value="item.id" />
                    </el-select>
                    <!-- <InputError :message="branch.errors.sat_method" /> -->
                </div>
                <div>
                    <label for="contact" class="font-bold block text-start text-sm">Contacto</label>
                    <el-select v-model="form.contact_id"
                        no-data-text="No hay opciones por mostrar" no-match-text="No se encontraron coincidencias"
                        clearable filterable placeholder="Seleccionar *">
                        <el-option v-for="item in contacts" ::key="item.id" :label="item.name" :value="item.id" />
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
import axios from 'axios';

export default {
    data() {
        const form = useForm({
            company_branch_id: 2,
            contact_id: 1,
        });

        return {
            form,
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
         handleCompnaySelected() {
            this.companyBranches = this.companies.find(item => item.id == this.form.company_id)?.company_branches;
        },
        handleBranchSelected() {
            this.contacts = this.companyBranches.find(item => item.id == this.form.company_branch_id)?.contacts;
        },
        update() {

        },
        async fetchCompanies() {
            try {
                const response = await axios.post(route('company-branches.get-all-branches'));

                if (response.status === 200) {
                    this.companyBranches = response.data.items;
                }
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this.fetchCompanies();
        this.handleBranchSelected();
        this.handleBranchSelected();
    }
}
</script>