<template>
    <div>
        <table class="w-full mx-auto text-sm">
            <thead>
                <tr class="text-left">
                    <th class="font-bold pb-2 pl-2">Producto</th>
                    <th class="font-bold pb-2">Etapa 1</th>
                    <th class="font-bold pb-2">Etapa 2</th>
                    <th class="font-bold pb-2">Etapa 3</th>
                    <th class="font-bold pb-2">Etapa 4</th>
                </tr>
            </thead>
            <tbody>
                <tr @click="openTemplate(item.id)" v-for="(item, index) in sale.catalogProductCompanySales" :key="index"
                    class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]">
                    <template v-if="item.catalog_product_company.catalog_product.part_number.split('-')[1] == 'LL'">
                        <td class="text-left py-2 rounded-l-full px-2 max-w-[140px] truncate"
                            :title="item.catalog_product_company.catalog_product.name">
                            {{ item.catalog_product_company.catalog_product.name }}
                        </td>
                        <td class="text-left py-2 pr-2 w-[18%]" :class="isStageCompleted(index, 1) ? 'text-green-600' : null">
                            {{ isStageCompleted(index, 1) ? 'Terminado' : '-' }}
                        </td>
                        <td class="text-left py-2 pr-2 w-[18%]" :class="isStageCompleted(index, 2) ? 'text-green-600' : null">
                            {{ isStageCompleted(index, 2) ? 'Terminado' : '-' }}
                        </td>
                        <td class="text-left py-2 pr-2 w-[18%]" :class="isStageCompleted(index, 3) ? 'text-green-600' : null">
                            {{ isStageCompleted(index, 3) ? 'Terminado' : '-' }}
                        </td>
                        <td class="text-left py-2 w-[18%] rounded-r-full" :class="isStageCompleted(index, 4) ? 'text-green-600' : null">
                            {{ isStageCompleted(index, 4) ? 'Terminado' : '-' }}
                        </td>
                    </template>
                </tr>
            </tbody>
        </table>
        <!-- <p v-else class="text-xs text-center text-gray-600">No hay para mostrar</p> -->
        <!-- <ul>
            <li @click="openTemplate(item.id)" v-for="(item, index) in sale.catalogProductCompanySales" :key="index"
                class="hover:underline cursor-pointer">
                <template v-if="item.catalog_product_company.catalog_product.part_number.split('-')[1] == 'LL'">
                    {{ item.catalog_product_company.catalog_product.part_number }}
                </template>
            </li>
        </ul> -->
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    props: {
        sale: Object,
    },
    methods: {
        isStageCompleted(cpcsIndex, stage) {
            const index = stage - 1;
            const item = this.sale.catalogProductCompanySales[cpcsIndex];
            return item.traveler_data && !item.traveler_data[index].some(item => item.value === null);
        },
        openTemplate(cpcs) {
            const url = route('productions.show-traveler-template', cpcs);

            window.open(url, '_blank');
        },
    },
    mounted() {

    }
}
</script>