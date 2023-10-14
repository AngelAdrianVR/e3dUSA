<template>
    <div>
        <div class="inline-block relative py-1 text-xs">
            <div class="absolute inset-0 flex" :style="{ color: bgColor }">
                <svg height="100%" viewBox="0 0 50 100">
                    <path
                        d="M49.9,0a17.1,17.1,0,0,0-12,5L5,37.9A17,17,0,0,0,5,62L37.9,94.9a17.1,17.1,0,0,0,12,5ZM25.4,59.4a9.5,9.5,0,1,1,9.5-9.5A9.5,9.5,0,0,1,25.4,59.4Z"
                        fill="currentColor" />
                </svg>
                <div class="flex-grow h-full -ml-px rounded-md rounded-l-none" :style="{ backgroundColor: bgColor }"></div>
            </div>
            <span class="relative font-semibold pr-px" :style="{ color: textColor }">
                <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>{{ name }}<span>&nbsp;</span>
            </span>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        name: {
            type: String,
            default: 'Nombre'
        },
        color: {
            type: String,
            default: '#FF0000'
        },
    },
    computed: {
        bgColor() {
            return this.color;
        },
        brighterColor() {
            return this.adjustColorBrightness(this.color, 20); // Ajusta el brillo en +20
        },
        textColor() {
            // Determina el color del texto en función del brillo del color de fondo
            return this.isColorDark(this.color) ? 'white' : 'black';
        }
    },
    methods: {
        adjustColorBrightness(hex, percent) {
            // Función para ajustar el brillo del color
            const color = hex.replace(/^#/, '');
            const num = parseInt(color, 16);
            let r = (num >> 16) + percent;
            r = Math.min(255, Math.max(0, r));
            let b = ((num >> 8) & 0x00ff) + percent;
            b = Math.min(255, Math.max(0, b));
            let g = (num & 0x0000ff) + percent;
            g = Math.min(255, Math.max(0, g));
            return `#${(g | (b << 8) | (r << 16)).toString(16)}`;
        },
        isColorDark(hex) {
            // Función para determinar si el color es oscuro o claro
            const color = hex.replace(/^#/, '');
            const num = parseInt(color, 16);
            const r = (num >> 16) & 0x0000ff;
            const g = (num >> 8) & 0x0000ff;
            const b = num & 0x0000ff;
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            return brightness < 128;
        }
    }
}
</script>