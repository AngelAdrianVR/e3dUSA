<template>
    <div v-for="(company_branch, index) in company_branches" :key="index" class="bg-transparent rounded-lg border border-[#cccccc] shadow-md shadow-[#9b9999] p-4 w-full">
        <p class="text-secondary ">{{ company_branch.name }}</p>
        
        <p class="my-3">Datos</p>
        <div class="md:w-1/2 grid grid-cols-2 text-left">
            <span class="text-gray-500">Método de pago</span>
            <span>{{company_branch.sat_method}}</span>
            <span class="text-gray-500 my-2">Medio de pago</span>
            <span>{{company_branch.sat_way}}</span>
            <span class="text-gray-500 my-2">Uso de factura</span>
            <span>{{company_branch.sat_type}}</span>
            <span class="text-gray-500 my-2">Dirección</span>
            <span>{{company_branch.address}}</span>
        </div>

        <div class="border border-[#0355B5] rounded-lg p-5 my-2" v-for="(contact, index) in company_branch.contacts" :key="contact.id">
            <p class="mt-9 mb-3">Contácto {{index + 1}}</p>
            <div class="md:w-1/2 grid grid-cols-2 text-left">
                <span class="text-gray-500">Nombre</span>
                <span>{{contact.name}}</span>
                <span class="text-gray-500 my-2">Correo electrónico</span>
                <span>{{contact.email}}</span>
                <span class="text-gray-500 my-2">Teléfono</span>
                <span>{{contact.phone}}</span>
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
        company_branches: Array
    },
    components: {

    }
}
</script>