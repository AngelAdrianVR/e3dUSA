<template>
    <div>
        <div v-if="showUsersList" @click="showUsersList = false" class="inset-0 absolute top-0 left-0 z-10"></div>
        <header
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
        </header>
        <div contenteditable="true" @input="onInput" ref="editor" id="editor"
            class="bg-transparent border border-[#9A9A9A] placeholder:text-gray-400 text-gray-700 text-sm rounded-[5px] focus:border-primary block w-full p-2.5 rounded-tr-none rounded-tl-none min-h-[85px] focus:outline-none"
            :class="{ 'rounded-none': withFooter }">
        </div>
        {{ value }}
        <footer v-if="withFooter"
            class="border border-t-0 border-[#9A9A9A] rounded-br-[5px] rounded-bl-[5px] p-2 flex justify-between relative">
            <button @click="showUsersList = !showUsersList" type="button" class="text-primary text-sm">@Mención</button>
            <PrimaryButton @click="deleteProjectTask">Agregar comentarios</PrimaryButton>
            <ul v-if="showUsersList"
                class="z-20 border border-[#a9a9a9] absolute -top-28 left-20 shadow-md rounded-[3px] bg-[#CCCCCC] w-56 h-32 overflow-y-auto">
                <template v-for="item in userList" :key="item.id">
                    <li @click="mentionUser(item)"
                        class="flex items-center px-2 py-1 space-x-2 text-xs mb-1 hover:bg-primarylight cursor-pointer">
                        <img class="h-7 w-7 rounded-full object-cover" :src="item.profile_photo_url" :alt="item.name" />
                        <p>{{ item.name }}</p>
                    </li>
                </template>
            </ul>
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
        };
    },
    components: {
        PrimaryButton,
        CancelButton,
    },
    props: {
        // Propiedad para recibir y enviar el valor del componente padre
        value: String,
        withFooter: {
            type: Boolean,
            default: false
        },
        userList: {
            type: Array,
            default: []
        },
    },
    emits: ['content'], // Emite un evento personalizado para actualizar "value",
    methods: {
        toggleStyle(style) {
            const editor = this.$refs.editor;
            // Cambia el estado del estilo
            this.styles[style] = !this.styles[style];

            // Aplica el estilo seleccionado al texto seleccionado o al texto que se escribirá en el futuro
            document.execCommand(style, false, null);

            editor.focus();
            // Enfoca nuevamente el editor de texto después de aplicar el estilo
            setCaretPositionToEnd(editor);
        },
        updateContent() {
            // Actualiza el contenido del editor y emite el evento personalizado "content"
            this.$emit('content', this.$refs.editor.innerHTML);
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
                this.mentions.push({id:user.id, tag:`@${user.name}`});

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
        },

        onInput() {
            const editor = this.$refs.editor;
            const text = editor.innerHTML;
            const mentionElements = editor.querySelectorAll('span[id^="m-"]');

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

        // onInput() {
        //     const editor = this.$refs.editor;
        //     const text = editor.innerHTML;

        //     // Iterar sobre las menciones en orden inverso para evitar problemas con índices al borrar
        //     for (let i = this.mentions.length - 1; i >= 0; i--) {
        //         const mention = this.mentions[i];

        //         // Comprobar si la mención está presente en el contenido
        //         if (!text.includes(mention.mention)) {
        //             this.mentions.splice(i, 1);
        //         }
        //     }

        //     // Actualiza el contenido del editor y emite el evento personalizado "content"
        //     this.$emit('content', this.$refs.editor.innerHTML);
        // },

        setCaretPositionToEnd(elem) {
            const range = document.createRange();
            const sel = window.getSelection();
            range.selectNodeContents(elem);
            range.collapse(false);
            sel.removeAllRanges();
            sel.addRange(range);
        },
    }
}
</script>