<template>
    <Head :title="cpcs.id" />
    <div class="text-[11px]">
        <header class="bg-[#373737] h-24 flex justify-between items-center pl-5 pr-20 text-white">
            <ApplicationLogo class="h-auto w-1/6 inline-block" />
            <h1 class="flex flex-col items-center text-sm">
                <span>Hoja viajera</span>
                <b>llaveros</b>
            </h1>
            <div class="flex flex-col w-1/3">
                <div class="flex justify-between border-b border-white">
                    <span>Código:</span>
                    <span class="text-center">Por asignar</span>
                </div>
                <div class="flex justify-between">
                    <span>No. de revisión:</span>
                    <span class="text-center">01</span>
                </div>
                <div class="flex justify-between">
                    <span>Fecha de elaboración:</span>
                    <span class="text-center">20-Feb-2024</span>
                </div>
                <div class="flex justify-between">
                    <span>Vigencia:</span>
                    <span class="text-center">20-Feb-2026</span>
                </div>
            </div>
        </header>
        <main class="m-5">
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3">
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de almacén de llaveros</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha de entrega</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de llaveros</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Entregó</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Recibió</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de almacén de emblemas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha de entrega</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Logo / Diseño de emblema:</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Entregó</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Recibió</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th></th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>Llaveros</td>
                            <td class="w-[10%]"></td>
                            <td class="w-[10%]"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Mini medallones</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Emblemas</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Fecha</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Nombre</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3">
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de grabado láser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de llaveros grabados</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones grabados</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Motivo de merma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th></th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>Diseño de grabado correcto</td>
                            <td class="w-[10%]"></td>
                            <td class="w-[10%]"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Definición y calidad de grabado</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Cantidad correcta</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Fecha</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Nombre</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3">
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de aplicación de emblmeas y medallones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de emblemas</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones aplicados</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Motivo de merma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th></th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>Posición de emblema correcto</td>
                            <td class="w-[10%]"></td>
                            <td class="w-[10%]"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Logo/Diseño de emblema correcto</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Juego de llavero y mini medallón correcto</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Fecha</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Nombre</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-2 gap-x-6 gap-y-3 my-3">
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de aplicación de emblmeas y medallones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>No. de paquete / Pzs. Por paquete</td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de Cajas:</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th></th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>Posición de emblema correcto</td>
                            <td class="w-[10%]"></td>
                            <td class="w-[10%]"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Logo/Diseño de emblema correcto</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Juego de llavero y mini medallón correcto</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Fecha</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Nombre</td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
        </main>
    </div>
</template>
<script>
import { Head } from "@inertiajs/vue3";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

export default {
    components: {
        Head,
        ApplicationLogo,
    },
    props: {
        cpcs: Object,
    }
}
</script>