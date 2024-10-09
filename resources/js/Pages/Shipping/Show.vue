<template>
    <AppLayoutNoHeader title="Envíos">
        <main class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1 text-sm">
            <div class="flex justify-between mb-5">
                <label class="text-lg">Envíos</label>

                <!-- <ShippingTimeLine /> -->

                <Link :href="route('shippings.index')"
                    class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </Link>
            </div>

            <div class="flex justify-between">
                <div class="w-1/2 md:w-1/3 mr-2">
                    <el-select @change="$inertia.get(route('shippings.show', shippingSelected))" v-model="shippingSelected"
                    filterable placeholder="Buscar registro de logística" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in shippings" :key="item.id" :label="'EV-' + item.id.toString().padStart(4, '0') + '/ ' + 'OV-' + item.id" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <PrimaryButton @click="openPrintPage">Confirmar envío completo</PrimaryButton>
                </div>
            </div>

            <section class="pt-10 md:w-[80%] md:mx-auto">
                <OVInfo :shipping="shipping" />

                <div class="flex space-x-2 mt-4 ml-5 pb-3 border-b border-[#999999]">
                    <p class="text-[#999999] w-40">Opciones de envío:</p>
                    <p>
                        {{ shipping.partialities?.length > 1 
                            ? shipping.partialities?.length + ' Parcialidades' 
                            : 'Entrega única' 
                        }}
                    </p>
                </div>

                <section class="border-b border-[#999999] py-4" v-for="partiality in shipping.partialities" :key="partiality">
                    <article class="ml-5">
                        <div class="flex space-x-2">
                            <p class="text-[#999999] w-48">Proveedor de paquetería:</p>
                            <p>{{ partiality.shipping_company ?? '-' }}</p>
                        </div>

                        <div class="flex space-x-2">
                            <p class="text-[#999999] w-48">Guía:</p>
                            <p class="font-bold">{{ partiality.tracking_guide ?? '-' }}</p>
                        </div>

                        <div class="flex space-x-2">
                            <p class="text-[#999999] w-48">Cantidad de cajas:</p>
                            <!-- <p>{{ partiality.tracking_guide ?? '-' }}</p> -->
                        </div>

                        <div class="flex space-x-2">
                            <p class="text-[#999999] w-48">Costo total de envío:</p>
                            <!-- <p>${{ partiality.tracking_guide ?? '-' }}</p> -->
                        </div>
                    </article>

                    <h2 class="font-bold mt-10 ml-5">Desgloce</h2>
                    <ShippingCard class="my-3" v-for="item in shipping.catalog_product_company_sales" :key="item" :product="item" />
                </section>
            </section>

        </main>
    </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ShippingTimeLine from "@/Components/MyComponents/ShippingTimeLine.vue";
import OVInfo from "@/Components/MyComponents/Shipping/OVInfo.vue";
import ShippingCard from "@/Components/MyComponents/Shipping/ShippingCard.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data(){
    return {
        shippingSelected: this.shipping.id
    }
},
components:{
    AppLayoutNoHeader,
    ShippingTimeLine,
    PrimaryButton,
    ShippingCard,
    OVInfo
},
props:{
    shipping: Object,
    shippings: Array
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
