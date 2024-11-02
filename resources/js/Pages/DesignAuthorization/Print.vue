<template>
<Head :title="design_authorization.data.name + '-Imprimir'" />
        <div class="mx-2 mt-5 bg-white">
            <!-- formulario -->
            <div class="border border-[#9A9A9A] rounded-md my-4">
                <!-- header -->
                <div class="w-full flex justify-between items-center p-5 border-b-2">
                    <div class="w-52">
                        <ApplicationLogo />
                    </div>
                    <h1 class="text-xl font-bold">FORMATO DE AUTORIZACIÓN</h1>
                    <div class="flex items-center space-x-2">
                        <p>Versión {{ design_authorization.data.version }}</p>
                        <button v-if="!printing" @click="print()" class="size-8 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- content -->
                <div class="text-xs grid grid-cols-2 gap-x-5 mt-10 px-7">   
                    <!-- imagen -->
                    <figure v-if="design_authorization.data.media?.length > 0" class="w-full flex items-center justify-center h-[500px] border border-[#D9D9D9] rounded-lg">
                        <img class="object-cover" :src="design_authorization.data.media[0].original_url" alt="">
                    </figure>
                    <p class="text-center text-gray-400 text-lg" v-else>No hay imagen disponible</p>
                
                    <!-- información del diseño -->
                    <div class="grid grid-cols-2 gap-x-2 text-sm self-start mt-4 md:mt-0 mr-2">
                        <p class="text-[#9A9A9A] my-1">Nombre del producto:</p>
                        <p class="my-1">{{ design_authorization.data.name }}</p>
                        <p class="text-[#9A9A9A] my-1">Color:</p>
                        <p class="my-1">{{ design_authorization.data.color }}</p>
                        <p class="text-[#9A9A9A] my-1">Material:</p>
                        <p class="my-1">{{ design_authorization.data.material ?? '--' }}</p>
                        <p class="text-[#9A9A9A] my-1">Técnica de impresión:</p>
                        <p class="my-1">{{ design_authorization.data.engrave_method }}</p>
                        <p class="text-[#9A9A9A] my-1">Vendedor:</p>
                        <p class="my-1">{{ design_authorization.data.seller?.name }}</p>
                        <p class="text-[#9A9A9A] my-1">Logística:</p>
                        <p class="my-1">{{ design_authorization.data.logistic ?? '--' }}</p>

                        <p class="text-[#9A9A9A] my-1 mt-7 col-span-full">Datos del cliente</p>
                        <p class="text-[#9A9A9A] my-1">Sucursal:</p>
                        <p class="my-1">{{ design_authorization.data.company_branch?.name }}</p>
                        <p class="text-[#9A9A9A] my-1">Contacto:</p>
                        <p class="my-1">{{ contact?.name }}</p>
                        <p class="text-[#9A9A9A] my-1">Puesto:</p>
                        <p class="my-1">{{ contact?.charge }}</p>
                        <p class="text-[#9A9A9A] my-1">correo:</p>
                        <p class="my-1">{{ contact?.email }}</p>
                        <p class="text-[#9A9A9A] my-1">Teléfono:</p>
                        <p class="my-1">{{ contact?.phone }}</p>
                        <p class="text-[#9A9A9A] my-1">Fecha de aceptación de diseño:</p>
                        <p class="my-1">{{ contact?.responded_at ?? '--' }}</p>

                        <div class="w-96 relative">
                            <p class="text-[#9A9A9A] mt-16">Firma de autorización: __________________________</p>
                            <figure class="w-32 absolute right-20 top-4">
                                <img :src="procesarUrlImagen(design_authorization.data.signature_media[0]?.original_url)" alt="">
                            </figure>
                        </div>

                    </div>
                </div> 
            
                <footer class="p-7 border-b border-[#9A9A9A]">
                    <h1 class="text-primary text-lg font-bold">Importante</h1>
                    <p class="font-bold">Se solicita una revisión cuidadosa del diseño, los colores y el texto. Una vez autorizado, cualquier omisión será responsabilidad de la persona que lo firme</p>
                    <p class="text-sm text-gray-500">*Los logotipos y marcas mostrados en este formato tienen un propósito exclusivamente ilustrativo, ya que los tonos de los grabados e impresiones pueden variar dependiendo del producto o lote.</p>
                    <p class="text-sm text-gray-500">*Los colores de la pantalla puede variar dependiendo del dispositivo en que se visualicen. </p>
                </footer>
            </div>
        </div>
</template>

<script>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    return {
        contact: null,
        printing: false
    }
},
components:{
ApplicationLogo,
PrimaryButton,
Head 
},
props:{
design_authorization: Object
},
methods:{
    async authorizeDesign() {
        try {
            const response = await axios.put(route('design-authorizations.authorize', this.design_authorization.data.id));
            if ( response.status === 200 ) {
                this.design_authorization.data.authorized_at = response.data.authorized_at;
                this.$notify({
                    title: "Éxito",
                    message: "Se ha autorizado el formato de diseño",
                    type: "success",
                });
            }
        } catch (error) {
            console.log(error);
        }
    },
    // Método para procesar la URL de la imagen
    procesarUrlImagen(originalUrl) {
        // Reemplaza la parte inicial de la URL
        const nuevaUrl = originalUrl?.replace('https://intranetemblems3d.dtw.com.mx', 'https://clientes-emblems3d.dtw.com.mx');
        return nuevaUrl;
    },
    print() {
      this.printing = true;
      setTimeout(() => {
        window.print();
      }, 100);
    },
    handleAfterPrint() {
      this.printing = false;
    },
},
mounted() {
    //Guardar la informacion del contacto
    this.contact = this.design_authorization.data.company_branch.contacts.find(contact => contact.id === this.design_authorization.data.contact_id);
    window.addEventListener('afterprint', this.handleAfterPrint);
},
beforeDestroy() {
    window.removeEventListener('afterprint', this.handleAfterPrint);
}
}
</script>
