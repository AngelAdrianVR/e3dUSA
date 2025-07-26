<template>
    <Head :title="tabTitle" />
    <!-- logo -->
    <div class="flex items-center justify-center space-x-6 dark:bg-slate-800">
        <ApplicationLogo class="h-auto w-3/12 inline-block" />
    </div>

    <!-- content -->
    <div class="text-xs dark:bg-slate-800">
        <section v-if="$page.props.auth.user.permissions.includes('Descuentos cotizaciones')">
            <!-- boton de descuento de pago anticipado -->
            <figure v-if="quote.data.early_payment_discount && !quote.data.early_paid_at" class="ml-3 md:ml-16 my-2 w-[370px] relative">
                <svg class="absolute top-3 left-3" width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.15527 0C9.57271 0.953908 11.3524 2.23003 12.2705 7.08301C12.6323 7.25786 13.0912 6.56184 13.9756 5.11523C18.2995 13.5451 15.0251 18.2319 10.4346 18.3633H5.97461C2.30572 18.3633 -0.842483 14.5589 0.203125 8.52539C1.11282 9.8512 1.48847 9.99154 2.04004 9.70605C0.85957 5.11534 8.598 4.06604 7.15527 0ZM10.5732 6.05664C10.3949 5.99143 10.1997 6.06337 10.1035 6.21875L10.0684 6.29102C9.30024 8.39053 8.70097 10.0296 8.10156 11.668L6.13379 17.0459C6.05912 17.25 6.16407 17.4761 6.36816 17.5508C6.54675 17.616 6.74192 17.5436 6.83789 17.3877L6.87305 17.3164C7.64129 15.2165 8.24131 13.5771 8.84082 11.9385L10.8076 6.56055C10.8817 6.35675 10.7769 6.13129 10.5732 6.05664ZM10.7002 12.0654C9.68635 12.0656 8.86447 12.8885 8.86426 13.9023C8.86438 14.9163 9.6863 15.7381 10.7002 15.7383C11.7141 15.7381 12.537 14.9163 12.5371 13.9023C12.5369 12.8885 11.714 12.0656 10.7002 12.0654ZM10.7002 12.8525C11.2794 12.8528 11.7498 13.3231 11.75 13.9023C11.7499 14.4816 11.2795 14.951 10.7002 14.9512C10.1209 14.951 9.65149 14.4816 9.65137 13.9023C9.65158 13.3231 10.121 12.8528 10.7002 12.8525ZM6.50293 7.60645C5.48915 7.60667 4.6673 8.42863 4.66699 9.44238C4.66699 10.4564 5.48897 11.2791 6.50293 11.2793C7.5169 11.2791 8.33984 10.4564 8.33984 9.44238C8.33954 8.42862 7.51671 7.60666 6.50293 7.60645ZM6.50293 8.39355C7.08207 8.39377 7.55243 8.86326 7.55273 9.44238C7.55273 10.0218 7.08226 10.492 6.50293 10.4922C5.9236 10.492 5.4541 10.0218 5.4541 9.44238C5.45441 8.86326 5.92379 8.39378 6.50293 8.39355Z" fill="#BC0B0B"/>
                </svg>
                <button class="flex items-center justify-center absolute top-[8px] right-3 bg-[#F2F2F2] rounded-full size-7 cursor-default">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3 text-black">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                    </svg>
                </button>
                <p class="text-[#005660] absolute left-12 top-2 text-xs text-center">PAGA ESTA COTIZACIÓN POR ADELANTADO <br> Y RECIBE UN {{ quote.data.discount }}% DE DESCUENTO EXCLUSIVO</p>
                <img draggable="false" class="w-[370px] h-12" src="@/../../public/images/earlyPaymentButton.webp" alt="">
            </figure>
            
            <!-- descuento por pago anticipado aplicado -->
            <div v-else-if="quote.data.early_payment_discount && showAdditionalElements" class="my-2 flex items-center gap-3 p-4 rounded-xl bg-green-100 border border-green-300 text-green-800 shadow-md max-w-lg mx-auto mt-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm font-semibold">
                    ¡Descuento por pago anticipado del {{ quote.data.discount }}% aplicado correctamente!
                </p>
            </div>
        </section>
        <!-- header -->
        <div>
            <p class="flex items-center justify-end ml-auto font-bold mr-6 text-xs text-gray-700 dark:text-white">
                Guadalajara, Jalisco {{ quote.data.created_at }}
                <i v-show="showAdditionalElements" @click="authorize"
                    :title="quote.data.authorized_at ? 'Cotización autorizada' : 'Autorizar cotización'"
                    v-if="$page.props.auth.user?.permissions?.includes('Autorizar cotizaciones')"
                    class="fa-solid fa-check ml-3"
                    :class="quote.data.authorized_at ? 'text-green-500' : 'hover:text-green-500 cursor-pointer'">
                </i>
            </p>
            <!-- botones para cambiar de cot -->
            <div v-show="showAdditionalElements" class="flex items-center justify-center space-x-8 mt-5 dark:text-white">
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
            <p class="w-11/12 text-lg mx-auto font-bold text-gray-700 dark:text-white">
                {{ quote.data.companyBranch?.name ?? quote.data.prospect?.name }}
            </p>
            <p class="w-11/12 mx-auto font-bold mt-2 text-gray-700 dark:text-white">
                Estimado(a) {{ quote.data.receiver }} - {{ quote.data.department }}
            </p>
            <p class="w-11/12 mx-auto my-2 pb-2 text-gray-700 dark:text-white">
                Por medio de la presente reciba un cordial saludo y a su vez le proporciono la cotización que nos
                solicitó,
                con base en la plática sostenida con ustedes y sabiendo de sus condiciones del producto a aplicar:
            </p>
        </div>

        <!-- table -->
        <table class="rounded-t-lg m-2 w-11/12 mx-auto bg-gray-300 text-gray-800 dark:bg-gray-600 dark:text-white" style="font-size: 10.2px;">
            <thead>
                <tr class="text-left border-b-2 border-gray-400">
                    <th class="px-2 py-1">Concepto</th>
                    <th class="px-2 py-1">Notas</th>
                    <th class="px-2 py-1">$ unitario</th>
                    <th class="px-2 py-1">
                        {{ !labelChanged ? 'Unidades' : 'MOQ' }}
                        <button v-show="showAdditionalElements" @click="labelChanged = !labelChanged"
                            class="rounded-full size-4 bg-black text-white">
                            <i class="fa-solid fa-shuffle"></i>
                        </button>
                    </th>
                    <th class="px-2 py-1 text-right">Total sin IVA</th>
                </tr>
            </thead>
            <!-- Información de los productos de catalogo cotizados -->
            <tbody>
                <tr v-for="(item, index) in quote.data.catalog_products" :key="index" class="text-gray-700 uppercase dark:bg-gray-300"
                    :class="quote.data.approved_products?.includes(item.id) ? 'bg-green-200 dark:bg-green-400' : 'bg-gray-200'">
                    <td class="px-2 py-px">
                        <b>{{ quote.data.approved_products?.includes(item.id) ? '(ACEPTADO)' : '' }}</b>
                        {{ item.name }}
                    </td>
                    <!-- <td class="px-2 py-px">{{ item.name + ' (N. de parte: ' + item.part_number + ')' }}</td> se quitó el numero de parte. descomentar si se quiere revertir cambios-->
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
                    class="bg-gray-200 text-gray-700 dark:bg-gray-500 dark:text-white uppercase">
                    <td class="px-2 py-px">{{ item.name }}</td>
                    <!-- <td class="px-2 py-px">{{ item.name + ' (N. de parte: ' + item.part_number + ')' }}</td> -->
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
            <tfoot v-if="quote.data.show_breakdown">
                <tr>
                    <td class="text-end pr-2 py-px" colspan="6">
                        TOTAL PRODUCTOS SIN IVA: {{ quote.data.total.number_format }} {{ quote.data.currency }}
                    </td>
                </tr>
                <tr v-if="!isNaN(quote.data.freight_cost)">
                    <td class="text-end pr-2 py-px" colspan="6">
                        COSTO DE FLETE: {{
                            parseFloat(quote.data.freight_cost).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                            quote.data.currency }}
                    </td>
                </tr>
                <tr v-if="!isNaN(quote.data.tooling_cost)">
                    <td class="text-end pr-2 py-px" colspan="6">
                        COSTO DE HERRAMENTAL: {{
                            parseFloat(quote.data.tooling_cost).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{
                            quote.data.tooling_currency }}
                    </td>
                </tr>
                <tr>
                    <td class="text-end pr-2 py-px font-bold" colspan="6">
                        TOTAL SIN IVA: {{
                            (
                                parseFloat(quote.data.total.number_format.replace(/,/g, '')) +
                                (!isNaN(quote.data.freight_cost) ? parseFloat(quote.data.freight_cost) : 0) +
                                (!isNaN(quote.data.tooling_cost) ? parseFloat(quote.data.tooling_cost) : 0)
                            ).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        }} {{ quote.data.currency }}
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="w-11/12 mx-auto my-3 grid grid-cols-3 gap-4 ">
            <!-- Images de los productos de catalogo -->
            <template v-for="(item, productIndex) in quote.data.catalog_products" :key="item.id">
                <div v-if="item.pivot.show_image" class="bg-gray-200 dark:bg-gray-500 dark:text-gray-300 rounded-t-xl rounded-b-md border"
                    style="font-size: 9px;">
                    <img class="rounded-t-xl max-h-52 mx-auto"
                        :src="item.media[currentCatalogProductImages[productIndex]]?.original_url">
                    <!-- selector de imagen cuando son varias -->
                    <div v-if="item.media?.length > 1" class="my-3 flex items-center justify-center space-x-3">
                        <i @click="currentCatalogProductImages[productIndex] = index"
                            v-for="(image, index) in item.media?.length" :key="index"
                            :class="index == currentCatalogProductImages[productIndex] ? 'text-black' : 'text-white'"
                            class="fa-solid fa-circle text-[7px] cursor-pointer"></i>
                    </div>
                    <p class="py-px px-1 uppercase text-gray-600 dark:text-gray-300">{{ item.name }}</p>
                    <p v-if="item.large || item.height || item.width || item.diameter"
                        class="py-px px-1 uppercase text-gray-600 dark:text-gray-300 font-bold">Dimensiones:</p>
                    <div class="flex items-center space-x-3 *:dark:text-gray-300">
                        <p v-if="item.large" class="py-px px-1 text-gray-600">Largo: {{ item.large }}mm</p>
                        <p v-if="item.height" class="py-px px-1 text-gray-600">Alto: {{ item.height }}mm</p>
                        <p v-if="item.width" class="py-px px-1 text-gray-600">Ancho: {{ item.width }}mm</p>
                        <p v-if="item.diameter" class="py-px px-1 text-gray-600">Diámetro: {{ item.diameter }}mm</p>
                    </div>
                </div>
            </template>
            <!-- Images de las materias primas -->
            <template v-for="(item, productIndex) in quote.data.raw_materials" :key="item.id">
                <div v-if="item.pivot.show_image" class="bg-gray-200 rounded-t-xl dark:text-gray-300 rounded-b-md border"
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
                    <p class="py-px px-1 uppercase text-gray-600 dark:text-gray-300">{{ item.name }}</p>
                </div>
            </template>
        </div>
        <div class="md:flex justify-between items-center mx-10 mt-9">
            <!-- goodbyes -->
            <p class="w-11/12 mx-auto my-2 pb-2 text-gray-700 dark:text-white">
                Sin más por el momento y en espera de su preferencia,
                quedo a sus órdenes para cualquier duda o comentario.
                Folio de cotización: <span class="font-bold bg-yellow-100 dark:bg-yellow-800">{{ quote.data.folio }}</span>
            </p>
            <!-- signature -->
            <div class="mr-7 flex space-x-4 w-96 relative mt-20 md:mt-0">
                <p class="text-gray-500">Firma de autorización: </p>
                <figure class="w-32 absolute md:right-24 lg:right-16 -top-[55px] bg-gray-100 rounded-lg">
                    <img :src="procesarUrlImagen(quote.data.signature_media[0]?.original_url)" alt="">
                </figure>
            </div>
        </div>
        <!-- Notes -->
        <div class="w-11/12 mx-auto border border-gray-500 px-3 pb-1 mt-1 rounded-xl text-gray-500 leading-normal uppercase"
            style="font-size: 10.5px;">
            <h2 class="text-center font-extrabold">IMPORTANTE <i class="fas fa-exclamation-circle text-amber-500"></i>
            </h2>
            <ol class="list-decimal mx-2 mb-2">
                <li v-if="quote.data.notes !== '--'" class="font-bold text-blue-500 whitespace-pre-line">{{ quote.data.notes }}</li>
                <li>PRECIOS ANTES DE IVA</li>
                <li>COSTO DE HERRAMENTAL: <span class="font-bold text-blue-500"
                        :class="quote.data.tooling_cost_stroked ? 'line-through' : ''">{{ quote.data.tooling_cost }} {{
                            quote.data.tooling_currency }}</span></li>
                <li>TIEMPO DE ENTREGA PARA LA PRIMER PRODUCCIÓN <span class="font-bold text-blue-500">{{
                    quote.data.first_production_days }}</span>.
                    EL TIEMPO CORRE UNA VEZ PAGANDO EL 100% DEL HERRAMENTAL Y EL 50% DE LOS PRODUCTOS.</li>
                <li>FLETES Y ACARREOS: 
                    <span v-if="quote.data.freight_option !== 'Cargo del flete prorrateado en producto'" class="font-bold text-blue-500">
                        {{ quote.data.freight_option }} &nbsp;
                    </span>
                    <span
                        v-if="quote.data.freight_option !== 'Cargo del flete prorrateado en producto'" class="font-bold text-blue-500"
                        :class="quote.data.freight_cost_stroked ? 'line-through' : ''">
                        ({{
                            quote.data.freight_cost }} {{ !isNaN(quote.data.freight_cost) ? quote.data.currency : ''
                        }})
                        </span>
                    <span v-else class="font-bold text-blue-500">0 {{ quote.data.currency }}</span>
                </li>
                <li>PRECIOS EN <span class="font-bold text-blue-500">{{ quote.data.currency }}</span></li>
                <li>COTIZACIÓN VÁLIDA POR 21 DÍAS. EL PRODUCTO ESTÁ SUJETO A LA REVISIÓN DEL DISEÑO FINAL, PRUEBAS Y
                    SUBSECUENTE APROBACIÓN</li>
            </ol>
            PAGOS.- POR TRANSFERENCIA BANCARIA O DEPÓSITO ENVIARSE A MARIBEL@EMBLEMAS3D.COM O
            ASISTENTE.DIRECTOR@EMBLEMAS3D.COM <br>
            NO SE ACEPTAN PAGOS EN EFECTIVO, TODOS LOS CHEQUES DEBEN USAR NOMBRE DE: EMBLEMS 3D USA
            SA DE CV. Y SELLO PARA ABONO EN CUENTA DEL BENEFICIARIO
        </div>
        <!-- banks -->
        <div class="grid grid-cols-2 gap-0 text-xs mt-1 font-semibold" style="font-size: 10px;">
            <div class="bg-sky-600 text-white p-1 flex justify-between rounded-l-xl">
                <span>BANORTE M.N.</span>
                <span>CUENTA: 1180403344</span>
                <span>CLABE: 072 320 011804033446</span>
            </div>
            <div class="bg-red-600 text-white p-1 flex justify-between rounded-r-xl">
                <span>BANORTE USD</span>
                <span>CUENTA: 1181103856</span>
                <span>CLABE: 072 320 011811038560</span>
            </div>
        </div>
        <!-- Author -->
        <div class="mt-1 text-gray-700 dark:text-white flex justify-around" style="font-size: 11px;">
            <div>
                Creado por: {{ quote.data.user.name }} &nbsp;&nbsp;
                Tel: {{ quote.data.user.employee_properties?.phone }} &nbsp;&nbsp;
                correo: {{ quote.data.user.email }} &nbsp;&nbsp;
            </div>
            <div>
                Autorizado por:
                <span v-if="quote.data.authorized_user_name != 'No autorizado'" class="text-green-600">{{
                    quote.data.authorized_user_name }}</span>
                <!-- No authorized Banner -->
                <span v-else class="text-amber-500">Sin autorización</span>

                <div v-if="quote.data.authorized_user_name == 'No autorizado'"
                    class="absolute left-28 top-1/3 text-red-700 text-5xl border-4 border-red-700 p-6">
                    <i class="fas fa-exclamation"></i>
                    <span class="ml-2">SIN AUTORIZACIÓN</span>
                </div>

            </div>
        </div>
        <!-- footer -->
        <footer class="text-gray-400 w-11/12 mx-auto mt-3" style="font-size: 11px;">
            <div class="grid grid-cols-3 gap-x-4">
                <div class="text-center text-sm font-bold">
                    Especialistas en
                    Emblemas de alta calidad
                </div>
                <div>
                    <i class="fas fa-mobile-alt"></i> 333 46 46 485 <br>
                    <i class="fas fa-phone-alt"></i> (33) 38 33 82 09
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
                    Emblemas de alta calidad, somos los mejores fabricantes. Ramo automotríz,
                    electrodomésticos, electrónica, textíl, calzado, muebles y juguetes.
                    En división electrónica, somos desarrolladores de tecnología. Conoce
                    nuestras nuevas memorias USB personalizadas desde el molde, son exclusivos.
                    En división automotríz somos fabricantes especialistas en emblemas cromados,
                    porta placas, llaveros, porta documentos, placas de estireno. Lo nuevo,
                    LLAVERO USB, diseño original y personalizado, todo con molde único para tu
                    empresa (personalización total y exclusiva).
                </p>
                <!-- <x-company-shield width="50" /> -->
            </div>
        </footer>
    </div>
</template>
<script>

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';

export default {
    data() {
        return {
            showAdditionalElements: true,
            currentCatalogProductImages: [], // Array to store current image index for each product
            currentRawMaterialImages: [], // Array to store current image index for each product
            labelChanged: false,
            tabTitle: null,
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
                    // this.$inertia.get(route('quotes.index'));
                }
            }
        },
        // Método para procesar la URL de la imagen
        procesarUrlImagen(originalUrl) {
            // Reemplaza la parte inicial de la URL 
            const nuevaUrl = originalUrl?.replace('https://intranetemblems3d.dtw.com.mx', 'https://clientes-emblems3d.dtw.com.mx');
            return nuevaUrl;
        },
    },
    created() {
        const clientName = this.quote.data.companyBranch?.name ?? this.quote.data.prospect?.name;
        this.tabTitle = this.quote.data.folio + ' - ' + clientName;
        // Initialize currentImages array with default values for each product (productos de catalogo)
        this.currentCatalogProductImages = this.quote.data.catalog_products.map(() => 0);

        // Initialize currentImages array with default values for each product (materias primas)
        this.currentRawMaterialImages = this.quote.data.catalog_products.map(() => 0);
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