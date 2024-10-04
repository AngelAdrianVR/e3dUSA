<template>
    <section class="md:w-1/2 grid grid-cols-2 text-left p-4 md:ml-20 text-sm">
        <span class="text-gray-500">ID</span>
        <span>{{ supplier.id }}</span>
        <span class="col-span-2 mt-8 mb-2">Datos</span>
        <span class="text-gray-500 my-2">Nombre</span>
        <span>{{ supplier.name }}</span>
        <span class="text-gray-500 my-2">Nick name</span>
        <span>{{ supplier.nickname }}</span>
        <span class="text-gray-500 my-2">Dirección</span>
        <span>{{ supplier.address }}</span>
        <span class="text-gray-500 my-2">Código postal</span>
        <span>{{ supplier.post_code }}</span>
        <span class="text-gray-500 my-2">Teléfono</span>
        <span>{{ supplier.phone }}</span>
        <span class="text-gray-500 my-2">Agregado el</span>
        <span>{{ supplier.created_at }}</span>
    </section>
</template>
<script>

export default {
    data() {
        return {

        }
    },
    props: {
        supplier: Object,
    },
    components: {

    },
    methods: {
    },
}
</script>