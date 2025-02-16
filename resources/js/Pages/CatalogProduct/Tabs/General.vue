<template>
    <div class="py-3 text-sm">
        <div class="flex space-x-2 mb-6">
            <p class="w-1/3 text-[#9A9A9A]">Fecha de Alta</p>
            <p>{{ product.created_at }}</p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">ID del producto</p>
            <p>{{ product.id }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Características</p>
            <p>{{ Array.isArray(product.features?.raw) ?
                product.features.raw.join(', ') :
                '- SIN CARACTERISTICAS -' }}</p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Número parte</p>
            <p>{{ product.part_number }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Unidad de medida</p>
            <p>{{ product.measure_unit }}</p>
        </div>
        <div class="flex mb-4 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Materia(s) prima(s)</p>
            <div class="flex flex-col">
                <div class="text-secondary underline cursor-pointer uppercase"
                    v-for="raw_material in product.rawMaterials" :key="raw_material">
                    <p @click="$inertia.get(route('storages.show', comp_storage.id))"
                        v-for="comp_storage in raw_material.storages" :key="comp_storage">{{
                            comp_storage.storageable.name }}</p>
                </div>
            </div>
        </div>
        <div class="flex mb-4 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Dimensiones</p>
            <div
                v-if="product.large || product.height || product.width || product.diameter">
                <p v-if="product.large">largo: {{ product.large }}mm</p>
                <p v-if="product.height">Alto: {{ product.height }}mm</p>
                <p v-if="product.width">Ancho: {{ product.width }}mm</p>
                <p v-if="product.diameter">Diámetro: {{ product.diameter }}mm
                </p>
            </div>
            <p v-else>- SIN INFORMACIÓN -</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Descripción</p>
            <p>{{ product.description }}</p>
        </div>
        <div class="flex mb-6 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Costo de producción</p>
            <p class="text-[#4FC03D]">{{ product.cost.number_format }}</p>
        </div>
        <div class="flex mb-2 space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Cantidad miníma permitida en almacén</p>
            <p>{{ product.min_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                {{
                    product.measure_unit }}</p>
        </div>
        <div class="flex space-x-2">
            <p class="w-1/3 text-[#9A9A9A]">Cantidad máxima permitida en almacén</p>
            <p>{{ product.max_quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                {{
                    product.measure_unit }}</p>
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
        product: Object,
    }
}
</script>