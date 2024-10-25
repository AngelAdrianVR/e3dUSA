<template>
    <section class="lg:grid grid-cols-3 gap-x-5 md:mt-12 md:px-14 mb-6 dark:text-white">
        <h2 class="font-bold col-span-full mb-2">Cuentas bancarias</h2>
        <SupplierBankCard v-for="(bank, index) in supplier.banks" :key="index" :bank="bank" />

        <h2 class="font-bold col-span-full mb-2 mt-6">Contactos</h2>
        <SupplierContactCard v-for="(contact, index) in supplier.contacts" :key="index" :contact="contact" />
    </section>
</template>
<script>
import SupplierBankCard from '@/Components/MyComponents/SupplierBankCard.vue';
import SupplierContactCard from '@/Components/MyComponents/SupplierContactCard.vue';

export default {
    data() {
        return {

        }
    },
    props: {
        supplier: Object,
    },
    components: {
        SupplierBankCard,
        SupplierContactCard,
    },
    methods: {
    },
}
</script>