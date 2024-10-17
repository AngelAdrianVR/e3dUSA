<template>
    <div class="px-7 py-7 text-sm">
        <div v-if="sample.catalog_product?.id">
            <div class="flex mb-2 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
                <p>{{ sample.catalog_product?.id }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Características</p>
                <p>
                    {{
                        sample.catalog_product?.features?.raw ??
                        "--"
                    }}
                </p>
            </div>
            <div class="flex mb-2 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
                <p>{{ sample.catalog_product?.part_number }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
                <p>{{ sample.catalog_product?.measure_unit }}</p>
            </div>
            <div class="flex mb-6 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">Costo de producción</p>
                <p class="text-[#4FC03D]">
                    {{ sample.catalog_product?.cost.number_format }}
                </p>
            </div>
            <div class="flex mb-2 space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">
                    Cantidad miníma permitida en almacén
                </p>
                <p>
                    {{ sample.catalog_product?.min_quantity }}
                    {{ sample.catalog_product?.measure_unit }}
                </p>
            </div>
            <div class="flex space-x-2">
                <p class="w-1/3 text-[#9A9A9A]">
                    Cantidad máxima permitida en almacén
                </p>
                <p>
                    {{ sample.catalog_product?.max_quantity }}
                    {{ sample.catalog_product?.measure_unit }}
                </p>
            </div>
        </div>
        <p v-else class="text-center text-sm text-gray-500">
            El producto de muestra no está registrado en catálogo de productos
        </p>
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