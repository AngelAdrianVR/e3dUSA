<template>
    <div>
        <el-tooltip placement="top">
            <template #content>
                <p>{{ getTooltipTitle() }}</p>
                <p class="text-[#D27927]">Venta: <span class="text-white">{{ profit.currency + ' ' + profit.total_sale.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
                <p class="text-[#D27927]">Costo de producción: <span class="text-white">{{ profit.currency + ' ' + profit.total_cost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
                <p class="text-[#2AD227]">Utilidad: <span class="text-white">{{ profit.currency + ' ' + profit.money.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span></p>
            </template>
            <div class="flex items-center space-x-2">
                <div class="flex flex-col space-y-1 items-center">
                    <div class="flex items-center space-x-1">
                        <i v-if="profit.percentage >= 25 && profit.percentage <= 34"
                            class="fa-solid fa-triangle-exclamation text-[9px] text-[#F09102]"></i>
                        <i v-else-if="profit.percentage < 24"
                            class="fa-solid fa-circle-minus text-[9px] text-[#D90505]"></i>
                        <i v-if="profit.percentage >= 200"
                            class="fa-solid fa-flag-checkered"></i>
                        <i v-else class="fa-solid fa-flag"  :style="{ color: getFlagColor() }"></i>
                    </div>
                    <StarRating :rating="profit.percentage / 100" />
                </div>
                <p class="flex-0 w-[80%]">{{ profit.percentage }} %</p>
            </div>
        </el-tooltip>
    </div>
</template>
<script>
import StarRating from "@/Components/MyComponents/StarRating.vue";

export default {
    data() {
        return {
            
        }
    },
    components: {
        StarRating,
    },
    props: {
        profit: Object,
    },
    methods: {
        getTooltipTitle() {
            if (this.profit.percentage <= 24) {
                return "¡Alto! Supervisar volumen y hacer contrato";
            } else if (this.profit.percentage <= 34) {
                return "¡Utilidad de alto riesgo! Supervisar";
            } else if (this.profit.percentage <= 49) {
                return "Potencial sin explorar";
            } else if (this.profit.percentage == 50) {
                return "Crecimiento positivo";
            } else if (this.profit.percentage <= 99) {
                return "Margen aceptable";
            } else if (this.profit.percentage == 100) {
                return "Ganancia sólida";
            } else if (this.profit.percentage <= 150) {
                return "Plusvalía destacada";
            } else if (this.profit.percentage <= 200) {
                return "Buen rendimiento!";
            } else if (this.profit.percentage < 300) {
                return "Excelencia en ganancia!";
            } else if (this.profit.percentage >= 300) {
                return "Objetivo ejemplar!";
            }
        },
        getFlagColor() {
            if (this.profit.percentage <= 24) {
                return "#D90505";
            } else if (this.profit.percentage <= 49) {
                return "#F09102";
            } else if (this.profit.percentage == 50) {
                return "#F0D802";
            } else if (this.profit.percentage <= 99) {
                return "#0355B5";
            } else if (this.profit.percentage <= 150) {
                return "#4DCC11";
            }
        },
    }
}
</script>