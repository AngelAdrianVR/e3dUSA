<template>
    <div class="rounded-lg border border-gray1 py-5 px-16 my-2 lg:my-0">
        <figure class="rounded-md p-4 bg-gray-200 w-2/3 lg:w-1/2 mx-auto">
            <img class="rounded-md object-contain w-full h-36" :src="product.media[0]?.original_url" alt="">
        </figure>
        <div class="grid grid-cols-2 gap-2 mt-5">
            <div class="text-gray1 text-sm space-y-2">
                <p>Nombre del producto:</p>
                <p>Descripción:</p>
                <p>Cantidad mínima de pedido:</p>
                <p>Precio unitario:</p>
                <p v-if="product.quantity">Cantidad comprada:</p>
                <p v-if="product.quantity">Costo total de compra</p>
                <p>Notas del producto:</p>
            </div>
            <div class="text-sm space-y-2">
                <p>{{ product.name }}</p>
                <p>{{ product.description }}</p>
                <p>{{ product.min_quantity_purchase ?? '--' }}</p>
                <p>${{ product.cost.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                <p v-if="product.quantity">{{ product.quantity }}</p>
                <p v-if="product.quantity">${{ (product.quantity * product.cost).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</p>
                <p>{{ product.notes ?? '--' }}</p>
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
props:{
product: Object
}
}
</script>
