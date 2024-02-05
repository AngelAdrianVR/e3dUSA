<template>
    <div class="bg-transparent rounded-lg border border-[#cccccc] shadow-md shadow-[#9b9999] p-4 w-full text-xs">
        <div class="grid grid-cols-3 text-left items-center">
            <span class="text-gray-500">Nombre</span>
            <span class="col-span-2">{{ contact.name }}</span>
            <span class="text-gray-500 my-2">Correo electrónico</span>
            <span class="col-span-2">{{ contact.email }}</span>
            <span class="text-gray-500 my-2">Teléfono</span>
            <span class="col-span-2">{{ contact.phone }}</span>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    props: {
        contact: Object
    },
    components: {

    }
}
</script>