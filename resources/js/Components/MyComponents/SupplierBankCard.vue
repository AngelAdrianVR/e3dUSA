<template>
    <div v-for="(bank, index) in banks" :key="index" class="text-sm bg-transparent rounded-lg border border-[#cccccc] shadow-md shadow-[#9b9999] p-4 w-full">
        <p class="text-secondary ">{{ bank.name }}</p>
        
        <p class="my-3">Datos</p>
        <div class="grid grid-cols-3 text-left items-center">
            <span class="text-gray-500">Nombre del beneficiario</span>
            <span class="col-span-2">{{bank.beneficiary_name}}</span>
            <span class="text-gray-500 my-2">Número de cuenta</span>
            <span class="col-span-2">{{bank.accountNumber}}</span>
            <span class="text-gray-500 my-2">Clabe</span>
            <span class="col-span-2">{{bank.clabe}}</span>
            <span class="text-gray-500 my-2">Nombre de banco</span>
            <span class="col-span-2">{{bank.bank_name}}</span>
        </div>

        <div class="border border-[#0355B5] rounded-lg p-5 my-2" v-for="(contact, index) in contacts" :key="contact.id">
            <p class="mt-9 mb-3">Contácto {{index + 1}}</p>
            <div class="grid grid-cols-3 text-left items-center">
                <span class="text-gray-500">Nombre</span>
                <span class="col-span-2">{{contact.name}}</span>
                <span class="text-gray-500 my-2">Correo electrónico</span>
                <span class="col-span-2">{{contact.email}}</span>
                <span class="text-gray-500 my-2">Teléfono</span>
                <span class="col-span-2">{{contact.phone}}</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {

        }
    },
    props: {
        banks: Array,
        contacts: Array
    },
    components: {

    }
}
</script>