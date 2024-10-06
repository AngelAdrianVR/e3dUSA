<template>
    <p>Folio de la orden: <strong class="ml-7">OP-{{ logistic.sale.id.toString().padStart(4, '0') }}</strong></p>

    <!-- informacion de la orden -->
    <div class="border border-[#999999] rounded-2xl p-4 mt-5">
        <h2 class="font-bold">Información de la orden</h2>

        <section class="grid grid-cols-2">
            <!-- lado izquierdo del grid -->
            <article class="mt-4 border-r border-[#999999] space-y-1">
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Solicitada por:</p>
                    <p>{{ logistic.sale.user.name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Solicitada el:</p>
                    <p>{{ formatDate(logistic.sale.created_at) }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Prioridad:</p>
                    <p :class="logistic.sale.is_high_priority ? 'text-red-600 font-bold' : 'text-black'">{{ logistic.sale.is_high_priority ? 'Urgente' : 'Normal' }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Fecha prevista entrega:</p>
                    <p>{{ formatDate(logistic.sale.promise_date) ?? 'Sin fecha promesa' }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Notas:</p>
                    <p>{{ logistic.sale.notes ?? '-' }}</p>
                </div>
            </article>

            <!-- lado derecho del grid -->
            <article class="mt-4 space-y-1 ml-5">
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Cliente:</p>
                    <p>{{ logistic.sale.company_branch?.company?.business_name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Sucursal:</p>
                    <p>{{ logistic.sale.company_branch?.name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Contacto:</p>
                    <p>{{ logistic.sale.contact.name }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Email:</p>
                    <p>{{ logistic.sale.contact.email }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Teléfono:</p>
                    <p>{{ logistic.sale.contact.phone }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#999999] w-48">Dirección:</p>
                    <p class="w-full">{{ logistic.sale.company_branch?.address }}</p>
                </div>
            </article>
        </section>
    </div>

    <section class="my-7 space-y-1 ml-3">
        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Opciones de envío:</p>
            <p>{{ logistic.type }}</p>
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Proveedor de paquetería:</p>
            <p>{{ logistic.sale.shipping_company ?? '-' }}</p>
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Guía:</p>
            <p class="font-bold">{{ logistic.sale.tracking_guide ?? '-' }}</p>
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Cantidad de cajas:</p>
            <!-- <p>{{ logistic.sale.tracking_guide ?? '-' }}</p> -->
        </div>

        <div class="flex space-x-2">
            <p class="text-[#999999] w-48">Costo total de envío:</p>
            <!-- <p>${{ logistic.sale.tracking_guide ?? '-' }}</p> -->
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
    logistic: Object
},
methods:{
    formatDate(date) {
      const parsedDate = new Date(date);
      return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
    },
}
}
</script>
