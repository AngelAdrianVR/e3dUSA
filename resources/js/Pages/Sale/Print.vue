<template>

    <Head title="Orden de venta" />
    <div>
        <div v-for="(product, index) in sale.data.catalogProductCompanySales" :key="index"
            class="mx-10 grid grid-cols-4 gap-3 my-5 border-b-2 text-lg">
            <figure class="rounded-[10px]">
                <!-- <el-image style="height: 100%; border-radius: 10px;"
                :src="product.catalog_product_company.catalog_product.media[0]?.original_url" fit="contain">
                <template #error>
                    <div class="flex justify-center items-center text-[#ababab]">
                        <i class="fa-solid fa-image text-3xl"></i>
                    </div>
                </template>
</el-image> -->
                <img class="object-contain rounded-md"
                    :src="product.catalog_product_company.catalog_product.media[0]?.original_url" alt="">
            </figure>
            <div class="col-span-3">
                <div class="flex justify-between font-bold uppercase text-lg">
                    <div class="flex items-center space-x-2">
                        <h2>{{ product.catalog_product_company.catalog_product.name }}</h2>
                        <svg v-if="product.confusion_alert" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <p>{{ product.catalog_product_company.catalog_product.part_number }}</p>
                        <p>
                            {{
                                product.catalog_product_company.catalog_product.part_number.startsWith('C-PP')
                                    ? product.catalog_product_company.new_price.toFixed(2)
                                    : parseInt(product.catalog_product_company.new_price)
                            }}
                        </p>
                    </div>
                </div>
                <!-- <div class="mt-2 text-base flex justify-between">
                <p class="text-primary">OCE: <span class="text-black ml-3">{{ product.sale.oce ?? 'No especificado' }}</span></p>
                <p class="text-primary">Cliente: <span class="text-black ml-3">{{ product.sale.company_branch.name }}</span></p>
            </div> -->
                <div class="mt-2 text-base">
                    <p v-if="product.confusion_alert" class="text-primary font-bold">¡Riesgo de confusión! Revisar con
                        vendedor
                        antes de producir o empacar</p>
                </div>
                <div class="mt-2 text-base flex justify-between">
                    <p class="text-primary">Solicitado por: <span class="text-black ml-3">{{ product.sale.user?.name
                            }}</span></p>
                    <p class="text-primary">
                        Solicitado el: 
                        <span class="text-black ml-3">
                            {{ formatDate(product.created_at) }}
                        </span>
                    </p>
                </div>
                <div class="mt-2 text-base flex justify-between">
                    <p class="text-primary">Folio de orden: <span class="text-black ml-3">{{ sale.data.folio }}</span>
                    </p>
                    <p class="text-primary">Unidades ordenadas: <span class="text-black ml-3">{{
                        product.quantity?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
                </div>
                <div class="mt-2 text-base flex justify-between">
                    <span></span>
                    <p class="text-primary">Autorizado el: <span class="text-green-600 ml-3">{{ product.sale.authorized_at?.split('T')[0] ?? '-' }}</span></p>
                </div>
                <!-- <div class="mt-2 text-base">
                <p class="text-primary">Operadores asignados:
                    <p v-for="task in product.productions" :key="task.id" class="text-black ml-3 text-base flex items-center">
                        -{{ task.operator.name }} <i class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Estimado de {{ task.estimated_time_hours }}h {{ task.estimated_time_minutes }}m <i class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Tareas {{ task.tasks }}
                    </p>
                </p>
            </div> -->
                <div class="mt-2 text-base">
                    <p class="text-secondary">Última actualización de precio:
                        <span class="text-black ml-3">
                            {{ formattedLastUpdate(product.catalog_product_company) }}
                        </span>
                    </p>
                </div>
                <div class="mt-2 text-base">
                    <p class="text-primary">Paquetería:
                        <span class="text-black ml-3">
                            {{ product.sale?.shipping_company }}
                        </span>
                    </p>
                </div>
                <div class="mt-2 text-base mb-2">
                    <p class="text-primary">Notas:
                        <span class="text-black ml-3">
                            {{ product.notes }}
                        </span>
                    </p>
                </div>
                <!-- <div class="mt-2 text-base flex">
                <p class="text-primary w-1/2">Fecha y hora de inicio:</p>
                <p class="text-primary w-1/2">Fecha y hora de término:</p>
            </div>
            <div class="mt-2 text-base flex">
                <p class="text-primary w-1/2">Piezas reales hechas:</p>
                <p class="text-primary w-1/2">Cantidad de mermas:</p>
            </div>
            <div class="mt-2 text-base flex">
                <p class="text-primary w-1/2">Firma de supervisor:</p>
                <p class="text-primary w-1/2">Calificación otorgada:</p>
            </div> -->
                <!-- <p class="text-secondary font-bold text-base mt-2"><i class="fa-solid fa-circle-exclamation"></i> Si alguien más continuará con el folio, debe tener nombre</p> -->
            </div>
        </div>
    </div>
</template>

<script>
import { formatDistanceToNow } from 'date-fns'
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { Head } from '@inertiajs/vue3';

export default {
    data() {
        return {

        }
    },
    components: {
        Head
    },
    props: {
        sale: Object
    },
    methods: {
        formattedLastUpdate(productData) {
            const { new_date, old_date, new_updated_by } = productData;
            const lastDate = new_date || old_date
            return lastDate
                ? `hace ${formatDistanceToNow(new Date(lastDate), { locale: es })}${new_updated_by ? ` por ${new_updated_by}` : ''}`
                : 'No disponible';
        },
        formatDate(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            return new Intl.DateTimeFormat('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }).format(date).replace('.', '');
        }
    },
}
</script>