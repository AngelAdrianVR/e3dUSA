<template>
    <div class="rounded-lg border border-gray1 py-4 px-4 my-2 lg:my-0">
        <figure class="rounded-md p-4 bg-gray-200 w-4/5 mx-auto">
            <img class="rounded-md object-contain w-full h-28" :src="product.media[0]?.original_url">
        </figure>
        <div class="grid grid-cols-2 gap-2 mt-5 text-xs">
            <p class="text-gray1">Nombre del producto:</p>
            <p>{{ product.name }}</p>
            <p class="text-gray1">Descripción:</p>
            <p>{{ product.description }}</p>
            <p class="text-gray1">Cantidad mínima de pedido:</p>
            <p>{{ product.min_quantity_purchase }}</p>
            <p class="text-gray1">Precio unitario:</p>
            <p>${{ product.cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            <p class="text-gray1" v-if="product.quantity">Cantidad comprada:</p>
            <p v-if="product.quantity">{{ product.quantity ?? '--' }}</p>
            <p class="text-gray1" v-if="product.quantity">Costo total de compra</p>
            <p v-if="product.quantity">${{ (product.quantity * product.cost).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
            <!-- <p class="text-gray1">Notas del producto:</p>
            <p>{{ product.notes ?? '--' }}</p> -->
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
        product: Object
    }
}
</script>
