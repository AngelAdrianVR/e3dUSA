<template>
    <section class="md:w-1/2 grid grid-cols-2 text-left p-4 md:ml-20 text-sm items-center">
        <span class="text-gray-500">ID</span>
        <span>{{ company.id }}</span>
        <span class="text-gray-500">Sucursales totales con las que cuenta el cliente</span>
        <span>{{ company.branches_number ?? '* No especificado' }}</span>

        <span class="col-span-2 mt-8 mb-2">Datos fiscales</span>

        <span class="text-gray-500 my-2">Razón social</span>
        <span>{{ company.business_name }}</span>
        <span class="text-gray-500 my-2">RFC</span>
        <span>{{ company.rfc }}</span>
        <span class="text-gray-500 my-2">Código postal</span>
        <span>{{ company.post_code }}</span>
        <span class="text-gray-500 my-2">Dirección</span>
        <span>{{ company.fiscal_address }}</span>
        <span class="text-gray-500 my-2">Vendedor</span>
        <p class="mr-2" :style="{ color: getColorHex(company.seller?.id) }">
            <i class="fa-solid fa-star"></i>
            {{ company.seller?.name ?? '* Sin información' }}
        </p>
        <span class="text-gray-500 my-2">Registro creado por</span>
        <span>{{ company.user?.name ?? '* Sin información' }}</span>
    </section>
</template>
<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {

        }
    },
    props: {
        company: Object,
    },
    methods: {
        dateFormat(date) {
            const formattedDate = format(new Date(date), 'dd MMM yyyy', { locale: es });

            return formattedDate;
        },
        getColorHex(number) {
            if (number) {
                // Ajusta el tono (hue) en función del número proporcionado
                let tono = (number * 30) % 360;

                // Saturation y lightness se mantienen constantes para colores vibrantes
                let saturacion = 80;
                let luminosidad = 40;

                // Convierte de HSL a hexadecimal
                let colorHex = this.hslToHex(tono, saturacion, luminosidad);

                return colorHex;
            } else {

                return '#cccccc';
            }
        },
        // Función para convertir de HSL a hexadecimal
        hslToHex(h, s, l) {
            h /= 360;
            s /= 100;
            l = l > 40 ? 40 : l;
            l /= 100;

            let r, g, b;

            if (s === 0) {
                r = g = b = l;
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1 / 6) return p + (q - p) * 6 * t;
                    if (t < 1 / 2) return q;
                    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                    return p;
                };

                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;

                r = hue2rgb(p, q, h + 1 / 3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1 / 3);
            }

            const toHex = x => {
                const hex = Math.round(x * 255).toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            };

            return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
        },
    },
}
</script>