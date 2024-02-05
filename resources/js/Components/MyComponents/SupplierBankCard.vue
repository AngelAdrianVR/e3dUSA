<template>
    <div class="bg-transparent rounded-lg border border-[#cccccc] shadow-md shadow-[#9b9999] p-4 w-full text-xs">
        <p class="text-secondary ">{{ bank.name }}</p>
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
    </div>
</template>

<script>
export default {
    data(){
        return {

        }
    },
    props: {
        bank: Object,
    },
    components: {

    }
}
</script>