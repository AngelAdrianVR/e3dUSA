<template>

    <Head title="Orden de producción" />
    <div v-for="(product, index) in ordered_products" :key="index"
        class="mx-10 grid grid-cols-4 gap-3 mb-7 border-b-2 ">
        <!-- <div class="grid grid-cols-2 gap-4" v-if="product.catalog_product_company.catalog_product.media?.length > 1">
            <figure v-for="image in product.catalog_product_company.catalog_product.media" :key="image"
                class="rounded-[10px]">
                <img class="object-cover rounded-md size-48" :src="image.original_url" alt="">
            </figure>
        </div>
        <figure v-else class="rounded-[10px]">
            <img class="object-contain rounded-md"
                :src="product.catalog_product_company.catalog_product.media[0]?.original_url" alt="">
        </figure> -->
        <div
            class="grid grid-cols-2 gap-4"
            v-if="product.catalog_product_company.catalog_product.media?.length > 1"
            >
            <figure
                v-for="image in product.catalog_product_company.catalog_product.media"
                :key="image.original_url"
                class="rounded-[10px] flex justify-center items-center w-48 h-48 overflow-hidden"
            >
                <img
                class="rounded-md max-w-full max-h-full transition-transform duration-300"
                :class="{
                    'rotate-90': imageOrientations[image.original_url] === 'rotate',
                    'object-contain': imageOrientations[image.original_url] === 'normal',
                }"
                :src="image.original_url"
                @load="handleImageLoad(image.original_url, $event)"
                alt=""
                />
            </figure>
        </div>

        <figure
            v-else
            class="rounded-[10px] flex justify-center items-center w-full h-full overflow-hidden"
            >
            <img
                class="rounded-md max-w-full max-h-full transition-transform duration-300"
                :class="{
                    'rotate-90':
                        imageOrientations[
                        product.catalog_product_company.catalog_product.media[0]?.original_url
                        ] === 'rotate',
                    'object-contain':
                        imageOrientations[
                        product.catalog_product_company.catalog_product.media[0]?.original_url
                        ] === 'normal',
                }"
                :src="product.catalog_product_company.catalog_product.media[0]?.original_url"
                @load="
                handleImageLoad(
                    product.catalog_product_company.catalog_product.media[0]?.original_url,
                    $event
                )"
                alt=""
            />
        </figure>

        <div class="col-span-3">
            <div class="flex justify-between font-bold uppercase text-sm">
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
                    <!-- <p>DA{{ product.catalog_product_company.new_price.toString().replace('.', '-') }}GT</p> -->
                    <p>
                        {{
                            product.catalog_product_company.catalog_product.part_number.startsWith('C-PP')
                                ? product.catalog_product_company.new_price.toFixed(2) :
                                parseInt(product.catalog_product_company.new_price)
                        }}
                    </p>
                </div>
            </div>
            <div class="mt-2 text-base">
                <p v-if="product.confusion_alert" class="text-primary font-bold">¡Riesgo de confusión! Revisar con
                    vendedor antes de producir o empacar</p>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <p class="text-secondary">Última actualización de precio: <span class="text-black ml-3">{{
                    formattedLastUpdate(product.catalog_product_company) }}</span></p>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <p class="text-primary">OCE: <span class="text-black ml-3">{{ product.sale.oce ?? 'No especificado'
                        }}</span></p>
                <p class="text-primary">Cliente: <span class="text-black ml-3">{{ product.sale.company_branch.name
                        }}</span></p>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <p class="text-primary">Solicitado por: <span class="text-black ml-3">{{ product.sale.user.name
                        }}</span></p>
                <p class="text-primary">
                    Solicitado el: 
                    <span class="text-black ml-3">
                        {{ formatDate(product.sale.created_at) }}
                    </span>
                </p>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <span></span>
                <p class="text-primary">Autorizado el: <span class="text-green-600 ml-3">{{ product.sale.authorized_at?.split('T')[0] ?? '-' }}</span></p>
            </div>
            <div class="mt-2 text-xs flex justify-between">
                <p class="text-primary">Folio de orden: <span class="text-black ml-3">{{ folio }}</span></p>
                <p class="text-primary">Unidades ordenadas: <span class="text-black ml-3">{{ product.quantity }}</span>
                </p>
            </div>
            <div class="mt-2 text-xs">
                <p class="text-primary">Operadores asignados:
                    <span v-for="task in product.productions" :key="task.id"
                        class="text-black ml-3 text-xs flex items-center">
                        -{{ task.operator.name }} <i class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Estimado de {{ task.estimated_time_hours }}h {{ task.estimated_time_minutes }}m <i
                            class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Tareas {{ task.tasks }}
                    </span>
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

            <!-- Area operativa -->
            <el-divider content-position="center">Área operativa</el-divider>
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
            <div class="mt-2 text-xs flex">
                <p class="text-primary w-1/2">Fecha promesa de embarque: <span class="text-black">{{
                    product.sale?.promise_date?.split('T')[0] }}</span></p>
                <p class="text-primary w-1/2">Embarcado el:</p>
            </div>
            <p class="text-secondary font-bold text-xs mt-2"><i class="fa-solid fa-circle-exclamation"></i> Si alguien
                más continuará con el folio, debe tener nombre</p>
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
            imageOrientations: {}, // { [image.original_url]: 'rotate' | 'normal' }
        }
    },
    components: {
        Head,
    },
    props: {
        ordered_products: Array,
        folio: Array,
    },
    methods: {
        procesarUrlImagenLocal(originalUrl) {
            // Reemplaza la parte inicial de la URL
            // const nuevaUrl = originalUrl.replace('https://https://intranetemblems3d.dtw.com.mx/', 'http://www.intranetemblems3d.dtw.com.mx');
            const nuevaUrl = originalUrl?.replace('http://localhost:8000', 'https://intranetemblems3d.dtw.com.mx/');  // para hacer pruebas en local
            return nuevaUrl;
        },
        handleImageLoad(imageUrl, event) {
            const img = event.target;
            const isLandscape = img.naturalWidth > img.naturalHeight;
            this.imageOrientations[imageUrl] = isLandscape ? 'rotate' : 'normal';
        },
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
    }
}
</script>
