<template>
    <AppLayoutNoHeader :title="design_authorization?.data?.name">

        <Back class="mt-4 ml-4" />

        <div class="lg:mx-20 mx-2 mt-5">

            <!-- formario -->
            <div class="border border-[#9A9A9A] rounded-md my-4">
                <!-- header -->
                <div class="w-full flex justify-between items-center p-5 border-b-2">
                    <div class="w-52">
                        <ApplicationLogo />
                    </div>
                    <h1 class="lg:text-xl font-bold">FORMATO DE AUTORIZACIÓN</h1>
                    <div class="flex items-center space-x-3">
                        <p class="font-bold">Versión</p>
                        <p>{{ design_authorization.data.version }}</p>
                        <i @click="authorizeDesign" :title="design_authorization.data.authorized_at ? 'Diseño autorizado' : 'Autorizar diseño'" :class="design_authorization.data.authorized_at ? 'text-green-500' : 'hover:text-green-500 cursor-pointer'" class="fa-solid fa-check text-sm pl-4"></i>
                    </div>
                </div>

                <!-- content -->
                <div class="text-xs lg:grid grid-cols-2 lg:gap-x-7 lg:mt-10 lg:px-7 p-3">   
                    <!-- imagen -->
                    <figure v-if="design_authorization.data.media?.length > 0" class="w-full flex items-center justify-center h-[600px] border border-[#D9D9D9] rounded-lg">
                        <img class="object-cover" :src="design_authorization.data.media[0].original_url" alt="">
                    </figure>
                    <p class="text-center text-gray-400 text-lg" v-else>No hay imagen disponible</p>
                
                    <!-- información del diseño -->
                    <div class="grid grid-cols-2 gap-x-2 text-sm self-start mt-4 md:mt-0">
                        <p class="text-[#9A9A9A] my-2">Nombre del producto:</p>
                        <p class="my-2">{{ design_authorization.data.name }}</p>
                        <p class="text-[#9A9A9A] my-2">Color:</p>
                        <p class="my-2">{{ design_authorization.data.color }}</p>
                        <p class="text-[#9A9A9A] my-2">Material:</p>
                        <p class="my-2">{{ design_authorization.data.material ?? '--' }}</p>
                        <p class="text-[#9A9A9A] my-2">Técnica de impresión:</p>
                        <p class="my-2">{{ design_authorization.data.engrave_method }}</p>
                        <p class="text-[#9A9A9A] my-2">Vendedor:</p>
                        <p class="my-2">{{ design_authorization.data.seller?.name }}</p>
                        <p class="text-[#9A9A9A] my-2">Logística:</p>
                        <p class="my-2">{{ design_authorization.data.logistic ?? '--' }}</p>

                        <p class="text-[#9A9A9A] my-2 mt-7 col-span-full">Datos del cliente</p>
                        <p class="text-[#9A9A9A] my-2">Sucursal:</p>
                        <p class="my-2">{{ design_authorization.data.company_branch?.name }}</p>
                        <p class="text-[#9A9A9A] my-2">Contacto:</p>
                        <p class="my-2">{{ contact?.name }}</p>
                        <p class="text-[#9A9A9A] my-2">Puesto:</p>
                        <p class="my-2">{{ contact?.charge }}</p>
                        <p class="text-[#9A9A9A] my-2">correo:</p>
                        <p class="my-2">{{ contact?.email }}</p>
                        <p class="text-[#9A9A9A] my-2">Teléfono:</p>
                        <p class="my-2">{{ contact?.phone }}</p>
                        <p class="text-[#9A9A9A] my-2">Fecha de aceptación de diseño:</p>
                        <p class="my-2">{{ contact?.responded_at ?? '--' }}</p>

                        <div class="w-96">
                            <!-- <figure @click="showSideOptions = true" class="w-32 absolute right-4 -top-[70px] border border-dashed border-green-500" v-if="design.data.media?.find(file => file.collection_name === 'signarute')">
                                <img :src="quote.data.media[0].original_url" alt="">
                            </figure> -->
                            <p class="text-[#9A9A9A] mt-10">Firma de autorización: _________________________________</p>
                        </div>
                    </div>
                </div> 
            
                <footer class="lg:p-7 border-b border-[#9A9A9A]">
                    <h1 class="text-primary text-lg font-bold">Importante</h1>
                    <p class="font-bold">Se solicita una revisión cuidadosa del diseño, los colores y el texto. Una vez autorizado, cualquier omisión será responsabilidad de la persona que lo firme</p>
                    <p class="text-sm text-gray-500">*Los logotipos y marcas mostrados en este formato tienen un propósito exclusivamente ilustrativo, ya que los tonos de los grabados e impresiones pueden variar dependiendo del producto o lote.</p>
                    <p class="text-sm text-gray-500">*Los colores de la pantalla puede variar dependiendo del dispositivo en que se visualicen. </p>
                </footer>
            </div>
        </div>
    </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Back from "@/Components/MyComponents/Back.vue";
import axios from 'axios';

export default {
data() {
    return {
        contact: null
    }
},
components:{
AppLayoutNoHeader,
ApplicationLogo,
PrimaryButton,
Back
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
    }
},
mounted() {
    //Guardar la informacion del contacto
    this.contact = this.design_authorization.data.company_branch.contacts.find(contact => contact.id === this.design_authorization.data.contact_id);
}
}
</script>
