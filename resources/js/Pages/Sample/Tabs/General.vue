<template>
    <div class="px-7 py-7 text-sm">
        <div class="flex space-x-2 mb-6">
            <p class="w-1/3 text-[#9A9A9A]">Muestra enviada el</p>
            <p>{{ sample.sent_at }}</p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Folio de muestra</p>
            <p>{{ sample.folio }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Nombre de la muestra</p>
            <p>{{ sample.name }}</p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Cantidad de muestras enviada</p>
            <p>
                {{ sample.quantity }}
                {{ sample.catalog_product?.measure_unit ?? 'Unidades' }}
            </p>
        </div>
        <div v-if="sample.devolution_date" class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Fecha esperada de devolución</p>
            <p class="bg-red-200 dark:text-black px-3">{{ sample.devolution_date }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Devuelta el</p>
            <p :class="sample.returned_at ? 'bg-green-200 px-3 dark:text-black' : ''">{{ sample.returned_at ?? "--" }}
            </p>
        </div>
        <div v-if="sample.sale_order_at" class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Orden generada el</p>
            <p class="text-[#4FC03D]">
                {{ sample.sale_order_at ?? "--" }}
            </p>
        </div>
        <div v-if="sample.denied_at" class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Venta rechazada el</p>
            <p class="text-primary">
                {{ sample.denied_at ?? "--" }}
            </p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Producto(s)</p>
            <p>
                <span v-for="product in sample.products" :key="product">{{ product ?? "--" }},
                </span>
            </p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Comentarios/notas de seguimiento</p>
            <p class="bg-yellow-200 px-2 dark:text-black">
                {{ sample.comments ?? "--" }}
            </p>
        </div>
        <div class="flex space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Estatus</p>
            <p :class="sample.status['text-color'] +
                ' ' +
                sample.status['border-color']
                " class="border rounded-full px-2">
                {{ sample.status.label }}
            </p>
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