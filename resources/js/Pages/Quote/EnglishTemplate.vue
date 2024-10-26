<template>

    <Head :title="quote.data.folio" />
    <!-- logo -->
    <div class="text-center">
        <ApplicationLogo class="h-auto w-3/12 inline-block" />
    </div>

    <!-- content -->
    <div class="text-xs">

        <!-- header -->
        <div>
            <p class="flex items-center justify-end ml-auto font-bold mr-6 text-xs text-gray-700">
                San Antonio TX {{ quote.data.created_at }}
                <i v-show="showAdditionalElements" @click="authorize"
                    :title="quote.data.authorized_at ? 'Cotización autorizada' : 'Autorizar cotización'"
                    v-if="$page.props.auth.user.permissions.includes('Autorizar cotizaciones')"
                    class="fa-solid fa-check ml-3"
                    :class="quote.data.authorized_at ? 'text-green-500' : 'hover:text-green-500 cursor-pointer'">
                </i>
            </p>
            <!-- botones para cambiar de cot -->
            <div v-show="showAdditionalElements" class="flex items-center justify-center space-x-8 mt-5">
                <button @click="$inertia.visit(route('quotes.show', prev_quote))"
                    class="bg-gray-300 text-gray-800 size-5 text-[9px] rounded-full">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <span class="text-xs">{{ quote.data.folio }}</span>
                <button @click="$inertia.visit(route('quotes.show', next_quote))"
                    class="bg-gray-300 text-gray-800 size-5 text-[9px] rounded-full">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            <p class="w-11/12 text-lg mx-auto font-bold text-gray-700">
                {{ quote.data.companyBranch?.name ?? quote.data.prospect?.name }}
            </p>
            <p class="w-11/12 mx-auto font-bold mt-2 text-gray-700">
                Dear {{ quote.data.receiver }} - {{ quote.data.department }}
            </p>
            <p class="w-11/12 mx-auto my-2 pb-2 text-gray-700">
                By means of this letter receive a cordial greeting and also I provide you with the quote that you
                requested, based on the
                conversation held with you and knowing your conditions of the product to apply:
            </p>
        </div>

        <!-- table -->
        <table class="rounded-t-lg m-2 w-11/12 mx-auto bg-gray-300 text-gray-800" style="font-size: 10.2px;">
            <thead>
                <tr class="text-left border-b-2 border-gray-400">
                    <th class="px-2 py-1">Concept</th>
                    <th class="px-2 py-1">Notes</th>
                    <th class="px-2 py-1">$ per unit</th>
                    <th class="px-2 py-1">
                        {{ !labelChanged ? 'Units' : 'MOQ' }}
                        <button v-show="showAdditionalElements" @click="labelChanged = !labelChanged"
                            class="rounded-full size-4 bg-black text-white">
                            <i class="fa-solid fa-shuffle"></i>
                        </button>
                    </th>
                    <th class="px-2 py-1">Total without taxes</th>
                </tr>
            </thead>
            <!-- Información de los productos de catalogo cotizados -->
            <tbody>
                <tr v-for="(item, index) in quote.data.catalog_products" :key="index"
                    class="bg-gray-200 text-gray-700 uppercase">
                    <td class="px-2 py-px">{{ item.name + ' (N. de parte: ' + item.part_number + ')' }}</td>
                    <td class="px-2 py-px">{{ item.pivot.notes ?? '--' }}</td>
                    <td class="px-2 py-px">{{ item.pivot.price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                        quote.data.currency }}</td>
                    <td class="px-2 py-px">{{ item.pivot.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                        item.measure_unit }}</td>
                    <td class="px-2 py-px text-right">{{ (item.pivot.quantity *
                        item.pivot.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ quote.data.currency }}
                    </td>
                </tr>
            </tbody>

            <!-- Información de las materias primas cotizadas -->
            <tbody>
                <tr v-for="(item, index) in quote.data.raw_materials" :key="index"
                    class="bg-gray-200 text-gray-700 uppercase">
                    <td class="px-2 py-px">{{ item.name + ' (N. de parte: ' + item.part_number + ')' }}</td>
                    <td class="px-2 py-px">{{ item.pivot.notes ?? '--' }}</td>
                    <td class="px-2 py-px">{{ item.pivot.price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                        quote.data.currency }}</td>
                    <td class="px-2 py-px">{{ item.pivot.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                        item.measure_unit }}</td>
                    <td class="px-2 py-px text-right">{{ (item.pivot.quantity *
                        item.pivot.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ quote.data.currency }}
                    </td>
                </tr>
            </tbody>
            <!-- <tfoot>
                <tr>
                    <td class="text-center border-t-2 border-gray-400 py-1 font-bold" colspan="6">
                        TOTAL without taxes: {{ quote.data.total.number_format }} {{ quote.data.currency }}
                    </td>
                </tr>
            </tfoot> -->
        </table>

        <!-- Images -->
        <div class="w-11/12 mx-auto my-3 grid grid-cols-3 gap-4 ">
            <!-- Images de los productos de catalogo -->
            <template v-for="(item, productIndex) in quote.data.catalog_products" :key="item.id">
                <div v-if="item.pivot.show_image" class="bg-gray-200 rounded-t-xl rounded-b-md border"
                    style="font-size: 8px;">
                    <img class="rounded-t-xl max-h-52 mx-auto"
                        :src="item.media[currentCatalogProductImages[productIndex]]?.original_url">
                    <!-- selector de imagen cuando son varias -->
                    <div v-if="item.media?.length > 1" class="my-3 flex items-center justify-center space-x-3">
                        <i @click="currentCatalogProductImages[productIndex] = index"
                            v-for="(image, index) in item.media?.length" :key="index"
                            :class="index == currentCatalogProductImages[productIndex] ? 'text-black' : 'text-white'"
                            class="fa-solid fa-circle text-[7px] cursor-pointer"></i>
                    </div>
                    <p class="py-px px-1 uppercase text-gray-600">{{ item.name }}</p>
                </div>
            </template>

            <!-- Images de las materias primas -->
            <template v-for="(item, productIndex) in quote.data.raw_materials" :key="item.id">
                <div v-if="item.pivot.show_image" class="bg-gray-200 rounded-t-xl rounded-b-md border"
                    style="font-size: 8px;">
                    <img class="rounded-t-xl max-h-52 mx-auto"
                        :src="item.media[currentRawMaterialImages[productIndex]]?.original_url">
                    <!-- selector de imagen cuando son varias -->
                    <div v-if="item.media?.length > 1" class="my-3 flex items-center justify-center space-x-3">
                        <i @click="currentRawMaterialImages[productIndex] = index"
                            v-for="(image, index) in item.media?.length" :key="index"
                            :class="index == currentRawMaterialImages[productIndex] ? 'text-black' : 'text-white'"
                            class="fa-solid fa-circle text-[7px] cursor-pointer"></i>
                    </div>
                    <p class="py-px px-1 uppercase text-gray-600">{{ item.name }}</p>
                </div>
            </template>
        </div>

        <div class="flex justify-between items-center mx-10 mt-9">
            <!-- goodbyes -->
            <p class="w-11/12 mx-auto my-2 pb-2 text-gray-700">
                Nothing more for the moment and awaiting your preference, I remain at your service for any questions or
                comments. Quotation sheet: <span class="font-bold bg-yellow-100">{{ quote.data.folio }}</span>
            </p>

            <!-- signature -->
            <div class="mr-7 flex space-x-4 w-96 relative mt-20 md:mt-0">
                <p class="text-gray-500">Firma de autorización: </p>
                <figure class="w-32 absolute md:right-24 lg:right-16 -top-[55px]">
                    <img :src="procesarUrlImagen(quote.data.signature_media[0]?.original_url)" alt="">
                </figure>
            </div>
        </div>

        <!-- Notes -->
        <div class="w-11/12 mx-auto border border-gray-500 px-3 pb-1 mt-1 rounded-xl text-gray-500 leading-normal uppercase"
            style="font-size: 10.5px;">
            <h2 class="text-center font-extrabold">IMPORTANT <i class="fas fa-exclamation-circle text-amber-500"></i>
            </h2>
            <ol class="list-decimal mx-2 mb-2">
                <li v-if="quote.data.notes !== '--'" class="font-bold text-blue-500">{{ quote.data.notes }}</li>
                <li>PRICES WITHOUT TAXES</li>
                <li>TOOLING COSTS: <span class="font-bold text-blue-500"
                        :class="quote.data.tooling_cost_stroked ? 'line-through' : ''">{{ quote.data.tooling_cost }} {{
                            quote.data.tooling_currency }}</span></li>
                <li>DELIVERY TIME FOR THE FIRST PRODUCTION <span class="font-bold text-blue-500">{{
                    quote.data.first_production_days }}</span>.
                    TIME RUNS ONCE PAYING 100% OF THE TOOLING AND THE 50% OF THE
                    PRODUCTS.</li>
                <li>FREIGHTS AND CARRIAGES ARE PAID BY CUSTOMER: <span v-if="!quote.data.freight_cost_charged_in_product" class="font-bold text-blue-500">{{
                    quote.data.freight_cost }} {{ !isNaN(quote.data.freight_cost) ? quote.data.currency : ''
                        }}</span>
                    <span v-else class="font-bold text-blue-500">0 {{ quote.data.currency }}</span> 
                </li>
                <li>PRICES IN <span class="font-bold text-blue-500">{{ quote.data.currency }}</span></li>
                <li>QUOTE VALID FOR 21 DAYS. PRODUCT IS SUBJECT TO FINAL DESIGN REVIEW, TESTING AND SUBSEQUENT APPROVAL
                </li>
            </ol>
            PAYMENTS.- BY BANK TRANSFER OR DEPOSIT TO MARIBEL@EMBLEMAS3D.COM O ASISTENTE.DIRECTOR@EMBLEMAS3D.COM CASH
            PAYMENTS ARE NOT ACCEPTED, ALL CHECKS MUST USE THE NAME OF: EMBLEMS 3D USA SA DE CV. AND STAMP FOR
            PAYMENT IN ACCOUNT OF THE BENEFICIARY
        </div>

        <!-- banks -->
        <div class="grid grid-cols-2 gap-0 text-xs mt-1 font-semibold" style="font-size: 10px;">
            <div class="bg-sky-600 text-white p-1 flex justify-between rounded-l-xl">
                <span>Vantage Bank Texas</span>
                <span>Account number: 107862946</span>
                <span> Bank Phone: 956-664-84000</span>
            </div>
            <div class="bg-red-600 text-white p-1 flex justify-between rounded-r-xl">
                <span>Bank address: San Antonio Texas</span>
                <span>Swift/Aba: ITNBUS44XXX</span>
                <span></span>
            </div>
        </div>

        <!-- Author -->
        <div class="mt-1 text-gray-700 flex justify-around" style="font-size: 11px;">
            <div>
                Created by: {{ quote.data.user.name }} &nbsp;&nbsp;
                Tel: {{ quote.data.user.employee_properties?.phone }} &nbsp;&nbsp;
                email: {{ quote.data.user.email }} &nbsp;&nbsp;
            </div>
            <div>
                Authorized by:
                <span v-if="quote.data.authorized_user_name" class="text-green-600">{{ quote.data.authorized_user_name
                    }}</span>
                <!-- No authorized Banner -->
                <span v-else class="text-amber-500">No authorized</span>
                <div v-if="!quote.data.authorized_user_name"
                    class="absolute left-28 top-1/3 text-red-700 text-5xl border-4 border-red-700 p-6">
                    <i class="fas fa-exclamation"></i>
                    <span class="ml-2">NO AUTHORIZED</span>
                </div>

            </div>
        </div>

        <!-- footer -->
        <footer class="text-gray-400 w-11/12 mx-auto mt-3" style="font-size: 11px;">
            <div class="grid grid-cols-3 gap-x-4">
                <div class="text-center text-sm font-bold">
                    Specialists in high quality
                    emblems
                </div>
                <div>
                    <i class="fas fa-map-marker-alt"></i> 5460 Babcock Rd Suite 120-145, San
                    Antonio TX 78240 <br>
                    <i class="fas fa-phone-alt"></i> 210-858-9881
                </div>
                <div>
                    <i class="fas fa-globe"></i> www.emblemas<b class="text-sky-600">3</b><b
                        class="text-red-600">d</b>.com
                    <br>
                    <i class="fas fa-envelope"></i> j.sherman@emblemas<b class="text-sky-600">3</b><b
                        class="text-red-600">d</b>.com
                </div>
            </div>
            <div class="flex">
                <p class="mt-3 leading-tight mr-1" style="font-size: 10px;">
                    High quality emblems, we are the best manufacturers. Automotive industry, household appliances,
                    electronics, textiles, footwear, furniture and
                    toys. In the electronic division, we are technology developers. Get to know our new custom USB flash
                    drives from the mold, they are exclusive.
                    In the automotive division we are manufacturers specialized in chrome emblems, license plate
                    holders, key rings, document holders, styrene
                    plates. The new, USB KEYCHAIN, original and personalized design, all with a unique mold for your
                    company (total and exclusive
                    customization).
                </p>
                <!-- <x-company-shield width="50" /> -->
            </div>
        </footer>

    </div>
</template>
<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

export default {
    data() {
        return {
            showAdditionalElements: true,
            currentCatalogProductImages: [], // Array to store current image index for each product
            currentRawMaterialImages: [], // Array to store current image index for each product
            labelChanged: false,
        };
    },
    components: {
        ApplicationLogo,
        Head,
    },
    props: {
        quote: Object,
        next_quote: Number,
        prev_quote: Number,
    },
    methods: {
        handleBeforePrint() {
            this.showAdditionalElements = false;
        },
        handleAfterPrint() {
            this.showAdditionalElements = true;
        },
        print() {
            window.print();
        },
        async authorize() {
            if (!this.quote.data.authorized_at) {
                try {
                    const response = await axios.put(route('quotes.authorize', this.quote.data.id));

                    if (response.status == 200) {
                        this.$notify({
                            title: 'Éxito',
                            message: response.data.message,
                            type: 'success'
                        });
                    } else {
                        this.$notify({
                            title: 'Algo salió mal',
                            message: response.data.message,
                            type: 'error'
                        });
                    }
                } catch (err) {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: err.message,
                        type: 'error'
                    });
                    console.log(err);
                } finally {
                    this.$inertia.get(route('quotes.index'));
                }
            }
        },
        // Método para procesar la URL de la imagen
        procesarUrlImagen(originalUrl) {
            // Reemplaza la parte inicial de la URL 
            const nuevaUrl = originalUrl?.replace('https://intranetemblems3d.dtw.com.mx', 'https://clientes-emblems3d.dtw.com.mx');
            return nuevaUrl;
        },
        created() {
            // Initialize currentImages array with default values for each product (productos de catalogo)
            this.currentCatalogProductImages = this.quote.data.catalog_products.map(() => 0);

            // Initialize currentImages array with default values for each product (materias primas)
            this.currentRawMaterialImages = this.quote.data.catalog_products.map(() => 0);
        },
    },
    mounted() {
        window.addEventListener('beforeprint', this.handleBeforePrint);
        window.addEventListener('afterprint', this.handleAfterPrint);
    },
    beforeDestroy() {
        window.removeEventListener('beforeprint', this.handleBeforePrint);
        window.removeEventListener('afterprint', this.handleAfterPrint);
    }
}
</script>