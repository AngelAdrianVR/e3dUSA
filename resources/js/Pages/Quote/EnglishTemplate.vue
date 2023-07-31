<template>
    <!-- logo -->
    <div class="text-center">
        <ApplicationLogo class="h-auto w-3/12 inline-block" />
    </div>

    <!-- content -->
    <div class="text-xs">

        <!-- header -->
        <div>
            <p class="flex justify-end ml-auto font-bold mr-6 text-xs text-gray-700">
                San Antonio TX
            </p>
            <p class="w-11/12 text-lg mx-auto font-bold text-gray-700">
                {{ quote.data.companyBranch.name }}
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
                    <th class="px-2 py-1">Units</th>
                    <th class="px-2 py-1">Total without taxes</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in quote.data.products" :key="index" class="bg-gray-200 text-gray-700">
                    <td class="px-2 py-px">{{ item.name }}</td>
                    <td class="px-2 py-px">{{ item.pivot.notes ?? '--' }}</td>
                    <td class="px-2 py-px">{{ item.pivot.price }} {{ quote.data.currency }}</td>
                    <td class="px-2 py-px">{{ item.pivot.quantity }} {{ item.measure_unit }}</td>
                    <td class="px-2 py-px text-right">{{ item.pivot.quantity * item.pivot.price }} {{ quote.data.currency }}</td>
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
            <template v-for="item in quote.data.products" ::key="item.id">
                <div class="bg-gray-200 rounded-t-xl rounded-b-md border" style="font-size: 8px;">
                    <img class="rounded-t-xl max-h-52 mx-auto" :src="item.media[0]?.original_url">
                    <p class="py-px px-1 uppercase text-gray-600">{{ item.name }}</p>
                </div>
            </template>
        </div>

        <!-- goodbyes -->
        <p class="w-11/12 mx-auto my-2 pb-2 text-gray-700">
            Nothing more for the moment and awaiting your preference, I remain at your service for any questions or
            comments. Quotation sheet: <span class="font-bold bg-yellow-100">{{ quote.data.folio }}</span>
        </p>

        <!-- Notes -->
        <div class="w-11/12 mx-auto border border-gray-500 px-3 pb-1 mt-1 rounded-xl text-gray-500 leading-normal uppercase"
            style="font-size: 10.5px;">
            <h2 class="text-center font-extrabold">IMPORTANT <i class="fas fa-exclamation-circle text-amber-500"></i>
            </h2>
            <ol class="list-decimal mx-2 mb-2">
                <li v-if="quote.data.notes !== '--'" class="font-bold text-blue-500">{{ quote.data.notes }}</li>
                <li>PRICES WITHOUT TAXES</li>
                <li>TOOLING COSTS: <span class="font-bold text-blue-500">{{ quote.data.tooling_cost }} {{ quote.data.currency }}</span></li>
                <li>DELIVERY TIME FOR THE FIRST PRODUCTION <span class="font-bold text-blue-500">{{ quote.data.first_production_days }}</span>.
                    TIME RUNS ONCE PAYING 100% OF THE TOOLING AND THE 50% OF THE
                    PRODUCTS.</li>
                <li>FREIGHTS AND CARRIAGES ARE PAID BY CUSTOMER: <span class="font-bold text-blue-500">{{
                    quote.data.freight_cost }} {{ quote.data.currency }}</span></li>
                <li>PRICES IN <span class="font-bold text-blue-500">{{ quote.data.currency }}</span></li>
                <li>QUOTE VALID FOR 21 DAYS. PRODUCT IS SUBJECT TO FINAL DESIGN REVIEW, TESTING AND SUBSEQUENT APPROVAL</li>
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
                <span v-if="quote.data.authorized_user_name" class="text-green-600">{{ quote.data.authorized_user_name }}</span>
                <!-- No authorized Banner -->
                <span v-else class="text-amber-500">No authorized</span>
                <div v-if="!quote.data.authorized_user_name" class="absolute left-28 top-1/3 text-red-700 text-5xl border-4 border-red-700 p-6">
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
                    <i class="fas fa-globe"></i> www.emblemas<b class="text-sky-600">3</b><b class="text-red-600">d</b>.com
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
export default {
    data() {
        return {

        };
    },
    components: {
        ApplicationLogo,
    },
    props: {
        quote: Object
    }
}
</script>