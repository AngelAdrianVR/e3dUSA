<template>
    <main class="border border-[#999999] rounded-2xl py-4 mt-1">
        <h2 class="font-bold mb-3 px-4">Producto x</h2>

        <section class="flex justify-between px-4">
            <article class="space-y-1">
                <div class="flex space-x-2">
                    <p class="text-[#373737] font-bold w-24">Producto:</p>
                    <p>{{ 'xxxxxxxx' }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#373737] font-bold w-24">N. Parte:</p>
                    <p>{{ 'xxxx-xxxx' }}</p>
                </div>
                <div class="flex space-x-2">
                    <p class="text-[#373737] font-bold w-24">Cantidad:</p>
                    <p>{{ 'xx' }} unidades</p>
                </div>
            </article>

            <article class="space-y-3 text-right">
                <figure class="rounded-xl bg-gray-200 w-72 h-28">
                    <!-- <img src="" alt=""> -->
                </figure>

                <p class="inline-flex rounded-md justify-center items-center px-3 bg-[#FDB9C9] text-primary">
                    <svg class="mr-2" width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.63137 0H0.0145374C0.12422 5.10188 -0.263825 5.06154 0.404279 6.24615C0.961078 5.11539 1.42056 4.46654 2.35299 3.82308C2.1303 3.33846 1.68488 2.90769 1.35079 2.47692C1.87381 1.53081 2.15193 0.986847 2.63137 0Z" fill="#D90537"/>
                        <path d="M2.18598 2.42308C2.13029 2.36923 3.52224 0 3.52224 0H7.92076C8.0261 1.75289 8.00576 2.78859 7.97644 4.63077C8.03212 6.78462 6.41747 7.91539 4.58012 7.96923V12.7615H7.25263V14H0.571346V12.7615H3.24386V7.96923C2.01354 7.86996 1.50341 7.61407 0.849733 6.89231C1.51786 5.33077 2.18948 4.63672 3.46657 3.98462C2.96487 3.35599 2.24167 2.47692 2.18598 2.42308Z" fill="#D90537"/>
                    </svg>
                    Frágil
                </p>
            </article>
        </section>

        <p class="px-4">Especificaciones de la(s) caja(s)</p>
        <p class="mt-2 px-4">Cajas necesarias: <span class="ml-4">{{ '2' }}</span></p>

        <div class="overflow-x-auto mt-4">
            <table class="min-w-full">
                <thead class="bg-[#373737] text-white">
                    <tr class="*:px-4 *:py-2 *:text-left">
                        <th>#Caja</th>
                        <th>Dimensiones</th>
                        <th>Peso</th>
                        <th>Piezas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="*:px-4 *:py-2">
                        <td>1</td>
                        <td>50x40x30 cm</td>
                        <td>20 kg</td>
                        <td>100 piezas</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>
</template>

<script>
export default {

}
</script>
