<template>
    <div v-if="showUsersList" @click="showUsersList = false" class="inset-0 absolute top-0 left-0 z-10"></div>
    <div class="relative w-full">
        <!-- <header
            class="border border-b-0 border-[#9A9A9A] bg-[#CCCCCC] rounded-tl-[3px] rounded-tr-[3px] h-7 flex items-center">
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
        </header> -->
        <div contenteditable="true" @input="onInput" ref="editor" id="editor" @keypress="checkForAtSign"
            class="bg-transparent border border-[#9A9A9A] placeholder:text-gray-400 text-gray-700 text-sm rounded-[5px] focus:border-primary block w-full p-2.5 min-h-[85px] focus:outline-none"
            :class="{ 'rounded-none': withFooter }">
        </div>
        <footer v-if="withFooter"
            class="border border-t-0 border-[#9A9A9A] bg-transparent rounded-br-[5px] rounded-bl-[5px] p-2 flex justify-between">
            <button @click="showUsersList = !showUsersList" type="button"
                class="text-primary text-sm cursor-pointer">@Mención</button>
            <PrimaryButton type="button" @click="$emit('submitComment')" :disabled="disabled">Agregar comentarios
            </PrimaryButton>
            <transition name="fade">
                <ul v-if="showUsersList"
                    class="z-20 border border-[#a9a9a9] absolute -top-40 left-0 rounded-[3px] bg-[#CCCCCC] w-60 h-40 overflow-y-auto">
                    <template v-for="item in userList" :key="item.id">
                        <li type="button" @click="mentionUser(item)"
                            class="flex items-center px-2 py-1 space-x-2 text-xs mb-1 hover:bg-primarylight cursor-pointer">
                            <img class="h-7 w-7 rounded-full object-cover" :src="item.profile_photo_url" :alt="item.name" />
                            <p>{{ item.name }}</p>
                        </li>
                    </template>
                </ul>
            </transition>
        </footer>
    </div>
</template>
<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
    data() {
        return {
            styles: {
                bold: false,
                italic: false,
                underline: false
            },
            showUsersList: false,
            mentions: [],
            content: null,
        };
    },
    components: {
        PrimaryButton,
        CancelButton,
    },
    props: {
        // Propiedad para recibir y enviar el valor del componente padre
        withFooter: {
            type: Boolean,
            default: false
        },
        userList: {
            type: Array,
            default: []
        },
        disabled: {
            type: Boolean,
            default: false
        },
        defaultValue: {
            type: String,
            default: ''
        },
    },
    emits: ['content', 'submitComment'], // Emite un evento personalizado para actualizar "value",
    methods: {
        clearContent() {
            const editor = this.$refs.editor;
            editor.innerHTML = null;
            this.mentions = [];
        },
        toggleStyle(style) {
            const editor = this.$refs.editor;
            editor.focus();
            this.styles[style] = !this.styles[style];
            document.execCommand(style, false, null);
            editor.focus();
        },
        addUserToMentions(user) {
            const userWithSomeProperties = { id: user.id, name: user.name };
            this.mentions.push(userWithSomeProperties);
        },
        mentionUser(user) {
            // Comprobar si el usuario ya ha sido mencionado
            const editor = this.$refs.editor;
            if (!this.mentions.some(mention => mention.id === user.id)) {
                const cursorPosition = editor.selectionEnd;
                const text = editor.innerHTML;
                const mention = `<span id="m-${user.id}" class="text-primary">@${user.name}</span> &nbsp;`;

                // Insertar la mención en el contenido existente
                const newText = text.slice(0, cursorPosition) + mention;

                // Actualizar el contenido del editor usando innerHTML
                editor.innerHTML = newText;

                // Registrar el usuario mencionado en el arreglo
                this.mentions.push({ id: user.id, tag: `@${user.name}` });

                // Enfocar el editor
                editor.focus();
                // Establecer el cursor al final del contenido
                this.setCaretPositionToEnd(editor);

            }
            // Enfocar el editor
            editor.focus();
            // Establecer el cursor al final del contenido
            this.setCaretPositionToEnd(editor);
            // Cerrar la lista de usuarios
            this.showUsersList = false;

            this.$emit('content', this.$refs.editor.innerHTML);
        },
        onInput() {
            const editor = this.$refs.editor;
            const text = editor.innerHTML;
            const mentionElements = editor.querySelectorAll('span[id^="m-"]');

            if (!mentionElements.length && this.mentions.length) this.mentions = [];

            // Iterar sobre las menciones en orden inverso para evitar problemas con los índices al borrar
            for (let i = this.mentions.length - 1; i >= 0; i--) {
                const mention = this.mentions[i];
                const mentionId = `m-${mention.id}`;
                const mentionElement = Array.from(mentionElements).find((element) => element.id === mentionId);

                if (mentionElement && mentionElement.innerText !== mention.tag) {
                    // Eliminar la mención tanto del editor como del arreglo mentions
                    this.mentions.splice(i, 1);
                    editor.removeChild(mentionElement);
                }
            }

            // Actualiza el contenido del editor y emite el evento personalizado "content"
            this.$emit('content', this.$refs.editor.innerHTML);
        },
        checkForAtSign(event) {
            const editor = this.$refs.editor;
            if (event.key === '@') {
                // Mostrar la lista de usuarios cuando se escribe "@".
                this.showUsersList = true;
                // Enfocar el editor nuevamente para continuar escribiendo.
                editor.focus();
                // Evitar que el carácter "@" aparezca en el editor.
                event.preventDefault();
            }
        },
        setCaretPositionToEnd(elem) {
            const range = document.createRange();
            const sel = window.getSelection();
            range.selectNodeContents(elem);
            range.collapse(false);
            sel.removeAllRanges();
            sel.addRange(range);
        },
    },
    mounted() {
        // Establecer el contenido inicial del editor con el valor por defecto y aplicar estilos si es necesario
        if (this.defaultValue) {
            this.$refs.editor.innerHTML = this.defaultValue;
            // this.updateContent();
        }
    },
}
</script>
<style scoped>
.slide-fade-enter-active,
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>