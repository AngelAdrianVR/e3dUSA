<template>
    <div>
        <header class="border border-b-0 border-[#9A9A9A] bg-[#CCCCCC] rounded-tl-[3px] rounded-tr-[3px] h-7 flex items-center">
            <button type="button" @click="toggleStyle('bold')" :class="{ 'text-primary': styles.bold }"
                class="border-r border-[#9A9A9A] px-3 text-sm">
                <i class="fa-solid fa-bold"></i>
            </button>
            <button type="button" @click="toggleStyle('italic')" :class="{ 'text-primary': styles.italic }"
                class="border-r border-[#9A9A9A] px-3 text-sm">
                <i class="fa-solid fa-italic"></i>
            </button>
            <button type="button" @click="toggleStyle('underline')" :class="{ 'text-primary': styles.underline }"
                class="border-r border-[#9A9A9A] px-3 text-sm">
                <i class="fa-solid fa-underline"></i>
            </button>
        </header>
        <div contenteditable="true" @input="updateContent" ref="editor" id="editor"
            class="bg-transparent border border-[#9A9A9A] placeholder:text-gray-400 text-gray-700 text-sm rounded-lg focus:ring-[#0355B5] focus:border-[#0355B5] block w-full p-2.5 rounded-tr-none rounded-tl-none min-h-[160px] focus:outline-none"></div>
            {{ value }}
    </div>
</template>
<script>

export default {
    data() {
        return {
            styles: {
                bold: false,
                italic: false,
                underline: false
            }
        };
    },
    props:{
         // Propiedad para recibir y enviar el valor del componente padre
    value: String,
    },
    emits: ['update:value'], // Emite un evento personalizado para actualizar "value",
    methods: {
        toggleStyle(style) {
            // Cambia el estado del estilo
            this.styles[style] = !this.styles[style];

            // Aplica el estilo seleccionado al texto seleccionado o al texto que se escribirá en el futuro
            document.execCommand(style, false, null);
            // Enfoca nuevamente el editor de texto después de aplicar el estilo
            this.$refs.editor.focus();
        },
        updateContent() {
      // Actualiza el contenido del editor y emite el evento personalizado "update:value"
      this.$emit('update:value', this.$refs.editor.innerHTML);
    },
    }
}
</script>
<!-- <style scoped>
/* Establece el color de fondo y el color de texto para el texto seleccionado */
#editor::selection {
    background-color: #FD8827;
    /* Cambia el color de fondo de selección a tu preferencia */
    color: #000000;
    /* Cambia el color del texto seleccionado a tu preferencia */
}
</style> -->