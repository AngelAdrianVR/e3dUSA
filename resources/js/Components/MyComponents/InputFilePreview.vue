<template>
  <div class="inline">
    <figure @click="triggerImageInput"
      class="flex items-center justify-center rounded-md border border-dashed border-gray-400 w-full cursor-pointer relative" :class="height">
      <i v-if="image && canDelete" @click.stop="clearImage"
        class="fa-solid fa-xmark absolute p-1 top-1 right-1 z-10 text-sm"></i>
        <svg v-if="!image" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#a9a9a9" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
        </svg>
      <img v-if="image" :src="image" :alt="alt" class="w-full h-full object-contain bg-no-repeat rounded-md opacity-50" />
      <input ref="fileInput" type="file" @change="handleImageUpload" class="hidden" />
    </figure>
  </div>
</template>

<script>
export default {
  data() {
    return {
      image: null,
      formData: {
        file: null,
      },
    };
  },
  props: {
    alt: {
      type: String,
      default: "Vista previa no disponible",
    },
    canDelete: {
      type: Boolean,
      default: true,
    },
    imageUrl: {
      type: String,
      default: null,
    },
    height: {
      type: String,
      default: 'h-44',
    },
  },
  watch: {
    imageUrl: {
      immediate: true,
      handler(newImageUrl) {
        if (newImageUrl) {
          this.image = newImageUrl;
          this.convertUrlToFile(newImageUrl);
        }
      },
    },
  },
  emits: ['imagen', 'cleared'],
  methods: {
    triggerImageInput() {
      this.$refs.fileInput.click();
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      this.formData.file = file;

      if (file) {
        const imageURL = URL.createObjectURL(file);
        this.image = imageURL;
        // Emitir evento al componente padre con la imagen
        this.$emit("imagen", file);
      }
    },
    clearImage() {
      this.image = null;
      this.formData.file = null;
      this.$emit("cleared");
    },
    async convertUrlToFile(url) {
      try {
        const response = await fetch(url);
        const blob = await response.blob();
        const file = new File([blob], "defaultImage.jpg", { type: blob.type });
        
        // Guardar el archivo en formData y emitirlo
        this.formData.file = file;
        this.$emit("imagen", file);
      } catch (error) {
        console.error("Error al convertir URL a archivo:", error);
      }
    },
  },
};
</script>
