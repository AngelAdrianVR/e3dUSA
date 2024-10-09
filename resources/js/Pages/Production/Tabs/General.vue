<template>
    <main class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
            <p class="text-secondary col-span-2 mb-2">Logística</p>
            <div v-if="sale.partialities" class="col-span-full">
                <article v-for="(item, index) in sale.partialities" :key="index" class="grid grid-cols-2 gap-1">
                    <span class="col-span-full font-bold mt-2">Envio {{ (index + 1) }}</span>
                    <span class="text-gray-500">Paquetería</span>
                    <span>{{ item.shipping_company }}</span>
                    <span class="text-gray-500">Guía</span>
                    <span>{{ item.tracking_guide }}</span>
                    <span class="text-gray-500">Costo de envío</span>
                    <span>{{ item.shipping_cost ? '$' + item.shipping_cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                        ",") : 'No especificado' }}</span>
                    <span v-if="item.promise_date" class="text-gray-500">Fecha promesa de envio</span>
                    <span v-if="item.promise_date" class="text-red-600 bg-red-200 px-2 py-1">
                        {{ dateFormat(item.promise_date) }}</span>
                    <span class="text-gray-500">Enviada el</span>
                    <span>
                        {{ item.sent_at ? dateTimeFormat(item.sent_at) : 'Envio no realizado aún' }}</span>
                    <span class="text-gray-500">Enviada por</span>
                    <span>
                        {{ item.sent_by ?? 'Envio no realizado aún' }}</span>
                </article>
            </div>

            <p class="text-secondary col-span-2 mb-2 mt-8">Datos de la órden</p>

            <span class="text-gray-500">ID</span>
            <span>{{ sale.id }}</span>
            <span class="text-gray-500 my-1">Solicitada por</span>
            <span>{{ sale.user.name }}</span>
            <span class="text-gray-500 my-1">Solicitada el</span>
            <span>{{ sale.created_at }}</span>
            <span class="text-gray-500 my-1">Medio de petición</span>
            <span>{{ sale.order_via }}</span>
            <span class="text-gray-500 my-1">Es prioridad alta</span>
            <span>
                <i v-if="sale.is_high_priority" class="fa-solid fa-check text-red-500"></i>
                <i v-else class="fa-solid fa-minus"></i>
            </span>
            <span class="text-gray-500 my-1">OCE</span>
            <span>{{ sale.oce_name ?? '-' }}</span>
            <span class="text-gray-500 my-1">Factura</span>
            <span>{{ sale.invoice ?? '-' }}</span>
            <span class="text-gray-500 my-1">Estatus</span>
            <span :class="sale.status['text-color'] +
                ' ' +
                sale.status['border-color']
                " class="rounded-full border text-center">{{ sale.status["label"] }}</span>
            <span class="text-gray-500 my-1">Notas</span>
            <span>{{ sale.notes ?? '-' }}</span>

            <p class="text-secondary col-span-2 mb-2 mt-5">Archivos adjuntos (OCE)</p>

            <div v-if="sale?.media?.length">
                <li v-for="file in sale?.media" :key="file" class="flex items-center justify-between col-span-full">
                    <a :href="file.original_url" target="_blank" class="flex items-center">
                        <i :class="getFileTypeIcon(file.file_name)"></i>
                        <span class="ml-2">{{ file.file_name }}</span>
                    </a>
                </li>
            </div>
            <p class="text-sm text-gray-400" v-else><i class="fa-regular fa-file-excel mr-3"></i>No hay archivos
                adjuntos</p>

        </div>
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center self-start">
            <p class="text-secondary col-span-2 mb-2">Datos del cliente</p>

            <span class="text-gray-500 mb-6">Razón solcial</span>
            <span class="mb-6">{{ sale.company_branch?.company?.business_name }}</span>
            <span class="text-gray-500">ID</span>
            <span>{{ sale.company_branch?.id }}</span>
            <span class="text-gray-500 my-1">Sucursal</span>
            <span>{{ sale.company_branch?.name }}</span>
            <span class="text-gray-500 my-1">Dirección</span>
            <span>{{ sale.company_branch?.address }}</span>
            <span class="text-gray-500 my-1">Código postal</span>
            <span>{{ sale.company_branch?.post_code }}</span>
            <span class="text-gray-500 my-1">Teléfono</span>
            <span>{{ sale.company_branch?.phone ?? '-' }}</span>

            <p class="text-secondary col-span-2 mt-7">Contacto</p>

            <span class="text-gray-500 my-1">Nombre</span>
            <span>{{ sale.contact?.name ?? '-' }}</span>
            <span class="text-gray-500 my-1">Correo(s) electrónico(s)</span>
            <span>{{ sale.contact?.email ?? '-' }}, {{ sale.contact?.additional_emails?.join(', ') }}</span>
            <span class="text-gray-500 my-1">telefono(s)</span>
            <span>{{ sale.contact?.phone ?? '-' }}, {{ sale.contact?.additional_phones?.join(', ') }}</span>
            <span class="text-gray-500 my-1">Cargo</span>
            <span>{{ sale.contact?.charge ?? '-' }}</span>
        </div>
    </main>
</template>

<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {

    props: {
        sale: Object
    },
    methods: {
        dateFormat(date) {
            const formattedDate = format(new Date(date), 'dd MMMM yyyy', { locale: es });
            return formattedDate;
        },
        dateTimeFormat(dateTime) {
            const formattedDate = format(new Date(dateTime), 'dd MMMM yyyy • h:mm a', { locale: es });
            return formattedDate;
        },
        getFileTypeIcon(fileName) {
            // Asocia extensiones de archivo a iconos
            const fileExtension = fileName.split('.').pop().toLowerCase();
            switch (fileExtension) {
                case 'pdf':
                    return 'fa-regular fa-file-pdf text-red-700';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'fa-regular fa-image text-blue-300';
                default:
                    return 'fa-regular fa-file-lines';
            }
        },
    }
}
</script>
