<template>
    <div v-for="(company_branch, index) in company_branches" :key="index"
        class="bg-transparent rounded-lg border border-[#cccccc] text-sm shadow-md shadow-[#9b9999] w-full mb-6">
        <p class="text-secondary px-4 pt-4">{{ company_branch.name }}</p>

        <p class="my-3 px-4">Datos</p>
        <div class="grid grid-cols-3 text-left items-center px-4">
            <span class="text-gray-500">ID</span>
            <span class="col-span-2 bg-green-200 text-black">{{ company_branch.id }}</span>
            <span class="text-gray-500">Método de pago</span>
            <span class="col-span-2">{{ company_branch.sat_method }}</span>
            <span class="text-gray-500 my-2">Medio de pago</span>
            <span class="col-span-2">{{ company_branch.sat_way }}</span>
            <span class="text-gray-500 my-2">Uso de factura</span>
            <span class="col-span-2">{{ company_branch.sat_type }}</span>
            <span class="text-gray-500 my-2">Dirección</span>
            <span class="col-span-2">{{ company_branch.address }}</span>
            <span class="text-gray-500 my-2">Estado</span>
            <span class="col-span-2">{{ company_branch.state ?? 'No especificado' }}</span>
            <span class="text-gray-500 my-2">Cómo nos conocieron</span>
            <span class="col-span-2">{{ company_branch.meet_way ?? '* No especificado' }}</span>
        </div>
        <div v-if="company_branch.important_notes" class="bg-[#FED1DC] dark:bg-red-400 py-1 border-l-2 border-[#D90537] grid grid-cols-3 text-left items-center px-4">
            <span class="text-black my-2 font-bold ">Notas importantes</span>
            <span style="white-space: pre-line;">{{ company_branch.important_notes }}</span>
        </div>

        <div class="rounded-lg p-5 my-2" v-for="(contact, index) in company_branch.contacts"
            :key="contact.id">
            <p class="mb-3">Contácto {{ index + 1 }}</p>
            <div class="grid grid-cols-3 text-left items-center">
                <span class="text-gray-500">Nombre</span>
                <span class="col-span-2">{{ contact.name }}</span>
                <span class="text-gray-500">Cargo o puesto</span>
                <span class="col-span-2">{{ contact.charge }}</span>
                <span class="text-gray-500 my-1">Correo electrónico</span>
                <span class="col-span-2">{{ contact.email }}</span>
                <span class="text-gray-500 my-1">Correos electrónicos adicionales</span>
                <span class="col-span-2">{{ contact.additional_emails?.join(', ') }}</span>
                <span class="text-gray-500 my-1">Teléfono</span>
                <span class="col-span-2">{{ contact.phone }}</span>
                <span class="text-gray-500 my-1">Teléfonos adicionales</span>
                <span class="col-span-2">{{ contact.additional_phones?.join(', ') }}</span>
                <span class="text-gray-500 my-1">Cumpleaños</span>
                <span class="col-span-2">{{ contact.birthdate_day ?? '-' }} {{ contact.birthdate_month !== null ? months[contact.birthdate_month] : '-' }}</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            months: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
            ],
        }
    },
    props: {
        company_branches: Array
    },
    components: {

    }
}
</script>