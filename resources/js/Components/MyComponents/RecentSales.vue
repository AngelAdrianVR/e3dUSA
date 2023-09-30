<template>
    <div class="lg:min-h-52 min-h-44 bg-[#D9D9D9] rounded-[30px] lg:rounded-xl lg:py-2 py-2 px-8 text-xs lg:text-sm">
        <div class="my-3 col-start-2 col-span-2">
            <h1 class="font-bold text-center">Ventas recientes <i class="fa-solid fa-star ml-2"></i></h1>
        </div>
        <div v-if="sales.length" class="h-full">
            <div class="h-2/3 overflow-y-scroll">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="text-xs">
                            <th class="text-center">Fecha de cierre</th>
                            <th class="text-center">Cliente</th>
                            <th class="text-center">Valor de venta</th>
                            <th class="text-center">Vendedor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in sales" :key="sale.id" class="text-xs w-full">
                            <td class="w-1/4 text-center">{{ sale.close_date }}</td>
                            <td class="w-1/4 text-center">{{ sale.customer_name }}</td>
                            <td class="w-1/4 text-center">{{ sale.total_sold }}</td>
                            <td class="w-1/4 text-center">{{ sale.seller }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mx-6">
                <button class="text-primary text-xs">Ver detalles</button>
            </div>
        </div>
        <p v-else class="text-gray-400 text-center mt-8">No hay ventas recientes</p>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    components: {
    },
    props: {
        sales: Array,
    }
}
</script>