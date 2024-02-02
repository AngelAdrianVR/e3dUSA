<template>
    <Head title="Orden de producción" />
    <div v-for="(product, index) in ordered_products" :key="index" class="mx-10 grid grid-cols-4 gap-3 mb-7 border-b-2 ">
        <figure class="rounded-[10px]">
            <!-- <el-image style="height: 100%; border-radius: 10px;"
                :src="product.catalog_product_company.catalog_product.media[0]?.original_url" fit="contain">
                <template #error>
                    <div class="flex justify-center items-center text-[#ababab]">
                        <i class="fa-solid fa-image text-3xl"></i>
                    </div>
                </template>
            </el-image> -->
            <img class="object-contain rounded-md" :src="product.catalog_product_company.catalog_product.media[0]?.original_url" alt="">
        </figure>
        <div class="col-span-3">
            <div class="flex justify-between font-bold uppercase text-sm">
                <h2>{{ product.catalog_product_company.catalog_product.name }}</h2>
                <span>{{ product.catalog_product_company.catalog_product.part_number }}</span>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <p class="text-primary">OCE: <span class="text-black ml-3">{{ product.sale.oce ?? 'No especificado' }}</span></p>
                <p class="text-primary">Cliente: <span class="text-black ml-3">{{ product.sale.company_branch.name }}</span></p>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <p class="text-primary">Solicitado por: <span class="text-black ml-3">{{ product.sale.user.name }}</span></p>
                <p class="text-primary">Solicitado el: <span class="text-black ml-3">{{ product.sale.created_at.split('T')[0] }}</span></p>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <p class="text-primary">Folio de orden: <span class="text-black ml-3">{{ folio }}</span></p>
                <p class="text-primary">Unidades ordenadas: <span class="text-black ml-3">{{ product.quantity }}</span></p>
            </div>
            <div class="mt-2 text-xs">
                <p class="text-primary">Operadores asignados:
                    <p v-for="task in product.productions" :key="task.id" class="text-black ml-3 text-xs flex items-center">
                        -{{ task.operator.name }} <i class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Estimado de {{ task.estimated_time_hours }}h {{ task.estimated_time_minutes }}m <i class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Tareas {{ task.tasks }}
                    </p>
                </p>
            </div>
            <div class="mt-2 text-xs">
                <p class="text-primary">Paquetería:
                    <span class="text-black ml-3">
                        {{ product.sale?.shipping_company }}
                    </span>
                </p>
            </div>
            <div class="mt-2 text-xs">
                <p class="text-primary">Notas:
                    <span class="text-black ml-3">
                        {{ product.notes }}
                    </span>
                </p>
            </div>
            <div class="mt-2 text-xs flex">
                <p class="text-primary w-1/2">Fecha y hora de inicio:</p>
                <p class="text-primary w-1/2">Fecha y hora de término:</p>
            </div>
            <div class="mt-2 text-xs flex">
                <p class="text-primary w-1/2">Piezas reales hechas:</p>
                <p class="text-primary w-1/2">Cantidad de mermas:</p>
            </div>
            <div class="mt-2 text-xs flex">
                <p class="text-primary w-1/2">Firma de supervisor:</p>
                <p class="text-primary w-1/2">Calificación otorgada:</p>
            </div>
            <p class="text-secondary font-bold text-xs mt-2"><i class="fa-solid fa-circle-exclamation"></i> Si alguien más continuará con el folio, debe tener nombre</p>
        </div>
    </div>
</template>
<script>
import { Head } from '@inertiajs/vue3';

export default {
    components: {
        Head,
    },
    props: {
        ordered_products: Array,
        folio: Array,
    }
}
</script>
