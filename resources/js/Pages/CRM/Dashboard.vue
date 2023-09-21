<template>
    <AppLayoutNoHeader title="Inicio">
        <div class="px-9 pt-3 lg:px-14 lg:pt-8">
            <h1>Inicio</h1>

            <!-- customers -->
            <h2 class="text-primary lg:text-xl text-lg lg:mt-6 mt-6">Clientes</h2>

            <div class="lg:grid grid-cols-2 gap-x-16 gap-y-14 mt-4">
                <CustomerDates :dates="[{contact: 'Elena Jimenez', date: '22 agosto', time:'14:30 hrs', type: 'Presencial', reason: 'Revision de temas varios para implementacion de proyecto'},]" />
                <BirthdateCardCustomer :contacts="customers_birthdays" />
            </div>
        </div>
        <DialogModal :show="false"  @close="">
            <template #title>
                
            </template>
            <template #content>
               
            </template>
            <template #footer>
                <CancelButton @click="">
                    Cerrar
                </CancelButton>
            </template>
        </DialogModal>
    </AppLayoutNoHeader>
</template>

<script>
import DialogModal from '@/Components/DialogModal.vue';
import BirthdateCardCustomer from '@/Components/MyComponents/BirthdateCardCustomer.vue';
import CustomerDates from '@/Components/MyComponents/CustomerDates.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import AppLayoutNoHeader from '@/Layouts/AppLayoutNoHeader.vue';
import axios from 'axios';
import { useForm, Link } from '@inertiajs/vue3';

export default {
    data() {
        return {
        }
    },
    props: {
        customers_birthdays: Array,
    },
    components: {
        AppLayoutNoHeader,
        BirthdateCardCustomer,
        DialogModal,
        CancelButton,
        CustomerDates,
    },
    methods: {
       
    },
    mounted() {
       
    }
}
</script>
