<template>
    <div class="px-7 py-7 text-sm">
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">ID del cliente</p>
            <p>{{ sample.company_branch?.id }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Nombre</p>
            <p>
                {{ sample.company_branch?.name }}
            </p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Dirección</p>
            <p>{{ sample.company_branch?.address }}</p>
        </div>
        <div class="flex mb-8 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">C.P</p>
            <p>{{ sample.company_branch?.post_code }}</p>
        </div>

        <p class="text-secondary mb-2">Contacto</p>

        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Nombre</p>
            <p>{{ sample.contact?.name ?? "--" }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Correo</p>
            <p>{{ sample.contact?.email ?? "--" }}</p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Teléfono</p>
            <p>{{ sample.contact?.phone ?? "--" }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Cargo</p>
            <p>{{ sample.contact?.charge ?? "--" }}</p>
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
        sample: Object,
    },
    components: {
    },
    methods: {
    },
}
</script>