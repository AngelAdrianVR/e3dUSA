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
                Guadalajara, Jalisco {{ quote.data.created_at }}
                <i
                  @click="authorize"
                  :title="quote.data.authorized_at ? 'Cotización autorizada' : 'Autorizar cotización'" 
                  v-if="$page.props.auth.user.permissions.includes('Autorizar cotizaciones')" 
                  class="fa-solid fa-check ml-3" 
                  :class="quote.data.authorized_at ? 'text-green-500' : 'hover:text-green-500 cursor-pointer'">
                </i>
            </p>
            <p class="w-11/12 text-lg mx-auto font-bold text-gray-700">
                {{ quote.data.companyBranch?.name ?? quote.data.prospect.name }}
            </p>
            <p class="w-11/12 mx-auto font-bold mt-2 text-gray-700">
                Estimado(a) {{ quote.data.receiver }} - {{ quote.data.department }}
            </p>
            <p class="w-11/12 mx-auto my-2 pb-2 text-gray-700">
                Por medio de la presente reciba un cordial saludo y a su vez le proporciono la cotización que nos
                solicitó,
                con base en la plática sostenida con ustedes y sabiendo de sus condiciones del producto a aplicar:
            </p>
        </div>

        <!-- table -->
        <table class="rounded-t-lg m-2 w-11/12 mx-auto bg-gray-300 text-gray-800" style="font-size: 10.2px;">
            <thead>
                <tr class="text-left border-b-2 border-gray-400">
                    <th class="px-2 py-1">Concepto</th>
                    <th class="px-2 py-1">Notas</th>
                    <th class="px-2 py-1">$ unitario</th>
                    <th class="px-2 py-1">Unidades</th>
                    <th class="px-2 py-1 text-right">Total sin IVA</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in quote.data.products" :key="index" class="bg-gray-200 text-gray-700 uppercase">
                    <td class="px-2 py-px">{{ item.name + ' (N. de parte: ' + item.part_number + ')' }}</td>
                    <td class="px-2 py-px">{{ item.pivot.notes ?? '--' }}</td>
                    <td class="px-2 py-px">{{ item.pivot.price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ quote.data.currency }}</td>
                    <td class="px-2 py-px">{{ item.pivot.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ item.measure_unit }}</td>
                    <td class="px-2 py-px text-right">{{ (item.pivot.quantity * item.pivot.price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }} {{ quote.data.currency }}
                    </td>
                </tr>
            </tbody>
            <!-- <tfoot>
                <tr>
                    <td class="text-center border-t-2 border-gray-400 py-1 font-bold" colspan="6">
                        TOTAL SIN IVA: {{ quote.data.total.number_format }} {{ quote.data.currency }}
                    </td>
                </tr>
            </tfoot> -->
        </table>

        <!-- Images -->
        <div class="w-11/12 mx-auto my-3 grid grid-cols-3 gap-4 ">
            <template v-for="item in quote.data.products" :key="item.id">
                <div v-if="item.pivot.show_image" class="bg-gray-200 rounded-t-xl rounded-b-md border" style="font-size: 8px;">
                    <img class="rounded-t-xl max-h-52 mx-auto" :src="item.media[0]?.original_url">
                    <p class="py-px px-1 uppercase text-gray-600">{{ item.name }}</p>
                </div>
            </template>
        </div>

        <div class="md:flex justify-between items-center mx-10 mt-9">
            <!-- goodbyes -->
            <p class="w-11/12 mx-auto my-2 pb-2 text-gray-700">
                Sin más por el momento y en espera de su preferencia,
                quedo a sus órdenes para cualquier duda o comentario.
                Folio de cotización: <span class="font-bold bg-yellow-100">{{ quote.data.folio }}</span>
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
            <h2 class="text-center font-extrabold">IMPORTANTE <i class="fas fa-exclamation-circle text-amber-500"></i>
            </h2>
            <ol class="list-decimal mx-2 mb-2">
                <li v-if="quote.data.notes !== '--'" class="font-bold text-blue-500">{{ quote.data.notes }}</li>
                <li>PRECIOS ANTES DE IVA</li>
                <li>COSTO DE HERRAMENTAL: <span class="font-bold text-blue-500" :class="quote.data.tooling_cost_stroked ? 'line-through' : ''">{{ quote.data.tooling_cost }} {{
                    quote.data.tooling_currency }}</span></li>
                <li>TIEMPO DE ENTREGA PARA LA PRIMER PRODUCCIÓN <span class="font-bold text-blue-500">{{
                    quote.data.first_production_days }}</span>.
                    EL TIEMPO CORRE UNA VEZ PAGANDO EL 100% DEL HERRAMENTAL Y EL 50% DE LOS PRODUCTOS.</li>
                <li>FLETES Y ACARREOS CORREN POR CUENTA DEL CLIENTE: <span class="font-bold text-blue-500">{{
                    quote.data.freight_cost }} {{ !isNaN(quote.data.freight_cost) ? quote.data.currency : '' }}</span></li>
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
        <div class="mt-1 text-gray-700 flex justify-around" style="font-size: 11px;">
            <div>
                Creado por: {{ quote.data.user.name }} &nbsp;&nbsp;
                Tel: {{ quote.data.user.employee_properties?.phone }} &nbsp;&nbsp;
                correo: {{ quote.data.user.email }} &nbsp;&nbsp;
            </div>
            <div>
                Autorizado por:
                <span v-if="quote.data.authorized_user_name != 'No autorizado'" class="text-green-600">{{ quote.data.authorized_user_name }}</span>
                <!-- No authorized Banner -->
                <span v-else class="text-amber-500">Sin autorización</span>

                <div v-if="quote.data.authorized_user_name == 'No autorizado'" class="absolute left-28 top-1/3 text-red-700 text-5xl border-4 border-red-700 p-6">
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
                    <i class="fas fa-globe"></i> www.emblemas<b class="text-sky-600">3</b><b class="text-red-600">d</b>.com
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

        };
    },
    components: {
        ApplicationLogo,
        Head,
    },
    props: {
        quote: Object
    },
    methods:{
        async authorize() {
            if (!this.quote.data.authorized_at)  {
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
    }
}
</script>