<template>
    <p>Folio de la orden: <strong class="ml-7">OP-{{ shipping.id.toString().padStart(4, '0') }}</strong></p>

    <!-- informacion de la orden -->
    <div class="border border-[#999999] rounded-2xl p-4 mt-5">
        <h2 class="font-bold">Información de la orden</h2>

        <section class="grid grid-cols-2">
            <!-- lado izquierdo del grid -->
            <article class="mt-4 border-r border-[#999999] space-y-1">
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Solicitada por:</p>
                    <p>{{ shipping.user.name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Solicitada el:</p>
                    <p>{{ formatDate(shipping.created_at) }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Prioridad:</p>
                    <p :class="shipping.is_high_priority ? 'text-red-600 font-bold' : 'text-black'">{{ shipping.is_high_priority ? 'Urgente' : 'Normal' }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Fecha prevista entrega:</p>
                    <p>{{ formatDate(shipping.promise_date) ?? 'Sin fecha promesa' }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Notas:</p>
                    <p>{{ shipping.notes ?? '-' }}</p>
                </div>
            </article>

            <!-- lado derecho del grid -->
            <article class="mt-4 space-y-1 ml-5">
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Cliente:</p>
                    <p>{{ shipping.company_branch?.company?.business_name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Sucursal:</p>
                    <p>{{ shipping.company_branch?.name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Contacto:</p>
                    <p>{{ shipping.contact.name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Email:</p>
                    <p>{{ shipping.contact.email }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Teléfono:</p>
                    <p>{{ shipping.contact.phone }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Dirección:</p>
                    <p class="w-full">{{ shipping.company_branch?.address }}</p>
                </div>
            </article>
        </section>
    </div>

    <section class="my-7 space-y-1 ml-3">
        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Opciones de envío:</p>
            <p>{{ shipping.type }}</p>
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Proveedor de paquetería:</p>
            <p>{{ shipping.shipping_company ?? '-' }}</p>
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Guía:</p>
            <p class="font-bold">{{ shipping.tracking_guide ?? '-' }}</p>
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Cantidad de cajas:</p>
            <!-- <p>{{ shipping.tracking_guide ?? '-' }}</p> -->
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Costo total de envío:</p>
            <!-- <p>${{ shipping.tracking_guide ?? '-' }}</p> -->
        </div>
    </section>
</template>

<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data() {
    return {

    }
},
props:{
    shipping: Object
},
methods:{
    formatDate(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
        }
    },
}
}
</script>
