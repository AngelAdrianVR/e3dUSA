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
                    <el-tag v-if="shipping.status === 'Enviado'" type="success" effect="light" size="large">
                        {{ 'Venta enviada' }}
                    </el-tag>
                    <el-popconfirm
                        v-else
                        confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                        title="¿Continuar?" @confirm="updateStatus()">
                        <template #reference>
                            <PrimaryButton>Confirmar envío completo</PrimaryButton>
                        </template>
                    </el-popconfirm>
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

                <section class="border-b border-[#999999] py-4" v-for="(partiality, index) in shipping.partialities" :key="partiality">

                    <article class="ml-5 space-y-1">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <h2 class="my-3 font-bold text-base">Parcialidad {{ index + 1 }}</h2>
                                <p class="px-4 py-1 flex items-center rounded-full" 
                                    :class="partiality.sent_at ? 'bg-[#C1F0B9] text-[#2F941E]' : 'bg-[#FDE0BD] text-[#F68C0F]'">
                                    <span v-html="getIcon(partiality.sent_at)" class="mr-2"></span>
                                    {{ partiality.sent_at ? 'Parcialidad enviada' : 'Pendiente de envío' }}
                                </p>
                            </div>
                            <el-popconfirm
                                v-if="!partiality.sent_at"
                                confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                title="¿Continuar?" @confirm="partialitySent(index)">
                                <template #reference>
                                    <PrimaryButton>Confirmar entrega parcial</PrimaryButton>
                                </template>
                            </el-popconfirm>
                        </div>

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

                    <h2 class="font-bold mt-3 ml-5">{{ shipping.partialities?.length > 1 ? 'Desglose de productos para esta parcialidad' : 'Desgloce'}}</h2>
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
import { Link } from "@inertiajs/vue3";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

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
    OVInfo,
    Link
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
    getIcon(partilitySent){
        if ( partilitySent ) {
            return '<svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.93141 12.096C5.93141 12.423 5.80152 12.7366 5.57031 12.9678C5.33911 13.199 5.02553 13.3289 4.69856 13.3289C4.37159 13.3289 4.05801 13.199 3.8268 12.9678C3.5956 12.7366 3.46571 12.423 3.46571 12.096M5.93141 12.096C5.93141 11.7691 5.80152 11.4555 5.57031 11.2243C5.33911 10.9931 5.02553 10.8632 4.69856 10.8632C4.37159 10.8632 4.05801 10.9931 3.8268 11.2243C3.5956 11.4555 3.46571 11.7691 3.46571 12.096M5.93141 12.096H10.8628M3.46571 12.096H1.92465C1.67942 12.096 1.44424 11.9986 1.27083 11.8252C1.09743 11.6518 1.00002 11.4166 1.00002 11.1714V8.39748M10.8628 12.096H12.7121M10.8628 12.096V8.39748M1.00002 8.39748V2.12229C0.998709 1.89722 1.08094 1.67968 1.2308 1.51176C1.38065 1.34384 1.58747 1.23748 1.81123 1.21327C4.55065 0.928911 7.31216 0.928911 10.0516 1.21327C10.516 1.26094 10.8628 1.65545 10.8628 2.12229V2.90966M1.00002 8.39748H10.8628M15.1778 12.096C15.1778 12.423 15.0479 12.7366 14.8167 12.9678C14.5855 13.199 14.2719 13.3289 13.9449 13.3289C13.6179 13.3289 13.3044 13.199 13.0732 12.9678C12.842 12.7366 12.7121 12.423 12.7121 12.096M15.1778 12.096C15.1778 11.7691 15.0479 11.4555 14.8167 11.2243C14.5855 10.9931 14.2719 10.8632 13.9449 10.8632C13.6179 10.8632 13.3044 10.9931 13.0732 11.2243C12.842 11.4555 12.7121 11.7691 12.7121 12.096M15.1778 12.096H16.1024C16.6128 12.096 17.0303 11.6818 16.9983 11.1722C16.8331 8.45801 15.919 5.84266 14.3575 3.6165C14.2088 3.40796 14.0146 3.23597 13.7896 3.11352C13.5646 2.99107 13.3148 2.92136 13.0589 2.90966H10.8628M10.8628 2.90966V8.39748" stroke="#2F941E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        } else {
            return '<svg width="16" height="16" class="size-5" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_13713_230" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#mask0_13713_230)"><path d="M12.433 6.26634L12.933 5.76634L11.6663 4.53301V2.66634H10.9997V4.79967L12.433 6.26634ZM2.66634 14.6663C2.47745 14.6663 2.31912 14.6025 2.19134 14.4747C2.06356 14.3469 1.99967 14.1886 1.99967 13.9997V12.633C1.77745 12.3997 1.61079 12.1413 1.49967 11.858C1.38856 11.5747 1.33301 11.2886 1.33301 10.9997V4.66634C1.33301 3.67745 1.79134 2.98579 2.70801 2.59134C3.62467 2.1969 5.22745 1.99967 7.51634 1.99967C7.3719 2.19967 7.2469 2.41079 7.14134 2.63301C7.03579 2.85523 6.94412 3.08301 6.86634 3.31634C5.73301 3.33856 4.85801 3.40523 4.24134 3.51634C3.62467 3.62745 3.19967 3.78856 2.96634 3.99967H6.71634C6.68301 4.2219 6.66634 4.44412 6.66634 4.66634C6.66634 4.88856 6.68301 5.11079 6.71634 5.33301H2.66634V7.33301H7.51634C7.70523 7.59967 7.91634 7.8469 8.14967 8.07467C8.38301 8.30245 8.64412 8.49967 8.93301 8.66634H2.66634V10.6663C2.66634 11.033 2.7969 11.3469 3.05801 11.608C3.31912 11.8691 3.63301 11.9997 3.99967 11.9997H9.33301C9.69967 11.9997 10.0136 11.8691 10.2747 11.608C10.5358 11.3469 10.6663 11.033 10.6663 10.6663V9.28301C10.8886 9.31634 11.1108 9.33301 11.333 9.33301C11.5552 9.33301 11.7775 9.31634 11.9997 9.28301V10.9997C11.9997 11.2886 11.9441 11.5747 11.833 11.858C11.7219 12.1413 11.5552 12.3997 11.333 12.633V13.9997C11.333 14.1886 11.2691 14.3469 11.1413 14.4747C11.0136 14.6025 10.8552 14.6663 10.6663 14.6663H9.99967C9.81079 14.6663 9.65245 14.6025 9.52467 14.4747C9.3969 14.3469 9.33301 14.1886 9.33301 13.9997V13.333H3.99967V13.9997C3.99967 14.1886 3.93579 14.3469 3.80801 14.4747C3.68023 14.6025 3.5219 14.6663 3.33301 14.6663H2.66634ZM11.333 7.99967C10.4108 7.99967 9.62467 7.67467 8.97467 7.02467C8.32467 6.37467 7.99967 5.58856 7.99967 4.66634C7.99967 3.75523 8.3219 2.9719 8.96634 2.31634C9.61078 1.66079 10.3997 1.33301 11.333 1.33301C12.2552 1.33301 13.0413 1.65801 13.6913 2.30801C14.3413 2.95801 14.6663 3.74412 14.6663 4.66634C14.6663 5.58856 14.3413 6.37467 13.6913 7.02467C13.0413 7.67467 12.2552 7.99967 11.333 7.99967ZM4.33301 11.333C4.61079 11.333 4.8469 11.2358 5.04134 11.0413C5.23579 10.8469 5.33301 10.6108 5.33301 10.333C5.33301 10.0552 5.23579 9.81912 5.04134 9.62467C4.8469 9.43023 4.61079 9.33301 4.33301 9.33301C4.05523 9.33301 3.81912 9.43023 3.62467 9.62467C3.43023 9.81912 3.33301 10.0552 3.33301 10.333C3.33301 10.6108 3.43023 10.8469 3.62467 11.0413C3.81912 11.2358 4.05523 11.333 4.33301 11.333ZM8.99967 11.333C9.27745 11.333 9.51356 11.2358 9.70801 11.0413C9.90245 10.8469 9.99967 10.6108 9.99967 10.333C9.99967 10.0552 9.90245 9.81912 9.70801 9.62467C9.51356 9.43023 9.27745 9.33301 8.99967 9.33301C8.7219 9.33301 8.48579 9.43023 8.29134 9.62467C8.0969 9.81912 7.99967 10.0552 7.99967 10.333C7.99967 10.6108 8.0969 10.8469 8.29134 11.0413C8.48579 11.2358 8.7219 11.333 8.99967 11.333Z" fill="#F68C0F"/></g></svg>';
        }
    },
    async updateStatus() {
        try {
            const response = await axios.post(route('shippings.update-status', this.shipping.id), {status: 'Enviado'});
            if ( response.status === 200 ) {
                this.$notify({
                    title: 'Éxito',
                    message: '',
                    type: 'success'
                });

                location.reload();
            }
        } catch (error) {
            console.log(error);
        }
    },
    async partialitySent(partialityIndex) {
        try {
            const response = await axios.post(route('shippings.partiality-sent', this.shipping.id), { partialityIndex: partialityIndex });
            if ( response.status === 200 ) {
                this.shipping.partialities = response.data.partialities;
                this.shipping.status = response.data.status;
                this.$notify({
                    title: 'Éxito',
                    message: '',
                    type: 'success'
                });
                // location.reload();
            }
        } catch (error) {
            console.log(error);
        }
    }
}
}
</script>
