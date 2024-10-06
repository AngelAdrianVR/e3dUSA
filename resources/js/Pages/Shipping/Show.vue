<template>
    <AppLayoutNoHeader title="Envíos">
        <main class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1 text-sm">
            <div class="flex justify-between mb-5">
                <label class="text-lg">Envíos</label>

                <ShippingTimeLine />

                <Link :href="route('shippings.index')"
                    class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </Link>
            </div>

            <div class="flex justify-between">
                <div class="w-1/2 md:w-1/3 mr-2">
                    <el-select @change="$inertia.get(route('shippings.show', shippingSelected))" v-model="shippingSelected" clearable
                    filterable placeholder="Buscar registro de logística" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in shippings" :key="item.id" :label="'EV-' + item.id.toString().padStart(4, '0') + '/ ' + 'OV-' + item.id" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <PrimaryButton @click="openPrintPage">Confirmar envío completo</PrimaryButton>
                    <!-- <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
                    sale.data.user_id == $page.props.auth.user.id" content="Editar" placement="top">
                    <Link :href="route('shippings.edit', sale.data.id)">
                    <button class="size-9 flex items-center justify-center rounded-lg bg-[#D9D9D9]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>
                    </Link>
                    </el-tooltip> -->
                </div>
            </div>

            <section class="pt-10 md:w-[80%] md:mx-auto">
                <OVInfo :shipping="shipping" />

                <h2 class="font-bold mt-10 ml-5">Desgloce</h2>
                <ShippingCard class="my-3" v-for="item in shipping.catalog_product_company_sales" :key="item" :product="item" />
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
