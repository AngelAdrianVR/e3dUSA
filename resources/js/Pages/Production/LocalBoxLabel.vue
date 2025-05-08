<template>
    <Head :title="'Etiqueta ' + data.ov" />

    <div v-for="(box, index) in data.boxes" :key="box" class="p-4 mx-auto bg-white" :class="printing ? 'w-full' : 'w-1/2'">

        <section class="flex justify-between">
            <div  v-if="index == 0" class="flex justify-end w-72">
                <figure>
                    <img src="@/../../public/images/E3DUSACOMLOGO.jpg" alt="Logo">
                </figure>
            </div>
            <!-- Solo se muestra en la primera etiqueta -->
            <PrimaryButton v-if="index == 0 && !printing" @click="print" class="!py-1">Imprimir</PrimaryButton>
        </section>

        <p v-if="index == 0"  class="mt-2 font-bold">{{ formattedDate }}</p>
        
        <main class="mt-3">
            <section class="border border-dashed border-[#CCCCCC] px-4 py-2 space-y-1">
                <div class="flex">
                    <p class="font-bold w-40">Empresa:</p>
                    <p>{{ data.company_branch ?? '-' }}</p>
                </div>
                <div class="flex">
                    <p class="font-bold w-40">Orden de compra:</p>
                    <p>{{ data.ov ?? '-' }}</p>
                </div>
                <div class="flex">
                    <p class="font-bold w-40">Producto:</p>
                    <p>{{ box.product_name ?? '-' }}</p>
                </div>
                <div class="flex">
                    <p class="font-bold w-40">Piezas:</p>
                    <p>{{ box.quantity?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") ?? '-' }}</p>
                </div>
                <div class="flex bg-yellow-200">
                    <p class="font-bold w-40">Caja:</p>
                    <p>{{ (index + 1) + ' de ' + data.boxes.length }}</p>
                </div>
            </section>
        </main>
    </div>
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head } from "@inertiajs/vue3";

export default {
data() {
    return {
        formattedDate: new Date().toLocaleDateString('es-ES', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }),
        printing: false,
    }
},
components:{
ApplicationLogo,
PrimaryButton,
Head
},
props:{
data: Object
},
methods:{
    print() {
      this.printing = true;
      setTimeout(() => {
        window.print();
      }, 200);
    },
    handleAfterPrint() {
      this.printing = false;
    },
},
mounted() {
    window.addEventListener('afterprint', this.handleAfterPrint);
},
beforeDestroy() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
}
}
</script>
