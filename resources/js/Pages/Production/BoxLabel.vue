<template>
    <Head :title="'Etiqueta ' + data.ov" />

    <div v-for="(box, index) in data.boxes" :key="box" class="p-4 h-screen mx-auto bg-white" :class="printing ? 'w-full' : 'w-1/2'">

        <section class="flex justify-between">
            <div class="flex justify-end w-72">
                <figure>
                    <img src="@/../../public/images/E3DUSACOMLOGO.jpg" alt="Logo">
                </figure>
            </div>
            <!-- Solo se muestra en la primera etiqueta -->
            <PrimaryButton v-if="index == 0 && !printing" @click="print" class="!py-1">Imprimir</PrimaryButton>
        </section>

        <p class="mt-2 font-bold">{{ formattedDate }}</p>
        
        <main class="mt-5">
            <!-- <h1 class="font-bold ml-4">CLIENTE</h1>
            <p class="px-4 py-1 border border-[#CCCCCC]">{{ data.client }}</p> -->

            <h1 class="font-bold ml-4 mt-2">DESTINATARIO</h1>
            <section class="border border-[#CCCCCC] px-4 py-2 space-y-1">
                <div class="flex">
                    <p class="font-bold w-40">Contacto:</p>
                    <p>{{ data.contact ?? '-' }}</p>
                </div>
                <div class="flex">
                    <p class="font-bold w-40">Empresa:</p>
                    <p>{{ data.company_branch ?? '-' }}</p>
                </div>
                <div class="flex">
                    <p class="font-bold w-40">Dirección:</p>
                    <p>{{ data.company_branch_address ?? '-' }}</p>
                </div>
                <div class="flex">
                    <p class="font-bold w-40">Código postal:</p>
                    <p>{{ data.post_code ?? '-' }}</p>
                </div>
                <div class="flex">
                    <p class="font-bold w-40">Teléfono:</p>
                    <p>{{ data.contact_phone ?? '-' }}</p>
                </div>
            </section>

            <section class="grid grid-cols-3 border-[#CCCCCC] mt-3">
                <!-- Lado izquierdo -->
                <div class="px-4 py-2 border border-[#CCCCCC] space-y-1 col-span-full">
                    <!-- <div class="flex">
                        <p class="font-bold w-40">Guía:</p>
                        <p>{{ data.guide ?? '-' }}</p>
                    </div> -->
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
                    <!-- <div class="flex">
                        <p class="font-bold w-40">Folio:</p>
                        <p>{{ data.folio ?? '-' }}</p>
                    </div> -->
                    <!-- <div class="flex">
                        <p class="font-bold w-40">Factura:</p>
                        <p>{{ data.invoice ?? '-' }}</p>
                    </div> -->
                </div>

                <!-- Lado derecho -->
                <!-- <div class="px-4 py-2 border-r border-[#CCCCCC] space-y-1">
                    <div class="flex">
                        <p class="font-bold w-40">No. de parte:</p>
                        <p>{{ data.part_number ?? '-' }}</p>
                    </div>
                </div> -->
            </section>

            <h1 class="font-bold ml-4 mt-2">REMITENTE</h1>
            <section class="flex space-x-3">
                <article class="w-full border border-[#CCCCCC] px-4 py-2 *:space-y-1 flex">
                    <div class="w-56">
                        <p>Dirección del remitente:</p>
                        <p>Colonia:</p>
                        <p>Código postal:</p>
                        <p>Municipio o delegación:</p>
                        <p>Estado:</p>
                        <p>Teléfono:</p>
                        <p>Celular:</p>
                    </div>
                    <div>
                        <p>Calle 1 #19</p>
                        <p>Seattle</p>
                        <p>45150</p>
                        <p>Zapopan</p>
                        <p>Jalisco</p>
                        <p>33 36 24 00 54</p>
                        <p>33 21 83 56 78</p>
                    </div>
                </article>
                <figure v-if="data.is_fragil">
                    <img class="h-full w-60" src="@/../../public/images/fragil_image.png" alt="Logo">
                </figure>
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
