<template>
    <div v-for="(company_branch, index) in company_branches" :key="index" class="bg-transparent rounded-lg border border-[#cccccc] text-sm shadow-md shadow-[#9b9999] p-4 w-full">
        <p class="text-secondary ">{{ company_branch.name }}</p>
        
        <p class="my-3">Datos</p>
        <div class="grid grid-cols-3 text-left items-center">
            <span class="text-gray-500">Método de pago</span>
            <span class="col-span-2">{{company_branch.sat_method}}</span>
            <span class="text-gray-500 my-2">Medio de pago</span>
            <span class="col-span-2">{{company_branch.sat_way}}</span>
            <span class="text-gray-500 my-2">Uso de factura</span>
            <span class="col-span-2">{{company_branch.sat_type}}</span>
            <span class="text-gray-500 my-2">Dirección</span>
            <span class="col-span-2">{{company_branch.address}}</span>
        </div>

        <div class="border border-[#0355B5] rounded-lg p-5 my-2" v-for="(contact, index) in company_branch.contacts" :key="contact.id">
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
        company_branches: Array
    },
    components: {

    }
}
</script>