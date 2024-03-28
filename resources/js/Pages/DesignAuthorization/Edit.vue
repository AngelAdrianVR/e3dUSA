<template>
    <AppLayout title="Editar formato de autorización">
            <template #header>
                <div class="flex justify-between">
                    <Back />
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">
                            Editar formato de autorización
                        </h2>
                    </div>
                </div>
            </template>

        <div class="lg:mx-20 mx-2 mt-5">

            <!-- formario -->
            <div class="border border-[#9A9A9A] rounded-md my-4">
                <!-- header -->
                <div class="w-full flex justify-between items-center p-5 border-b-2">
                    <div class="w-52">
                        <ApplicationLogo />
                    </div>
                    <h1 class="lg:text-xl font-bold">FORMATO DE AUTORIZACIÓN</h1>
                    <div class="flex items-center">
                        <p class="font-bold mr-3">Versión</p>
                        <div>
                            <input v-model="form.version" type="text" class="input border border-[#9A9A9A] bg-transparent">
                            <InputError :message="form.errors.version" />
                        </div>
                    </div>
                </div>

                <!-- content -->
                <div class="text-xs lg:grid grid-cols-2 lg:gap-x-7 lg:mt-10 lg:px-7 p-3">   
                    <!-- imagen -->
                    <InputFilePreview
                     :imageUrl="design_authorization.media[0]?.original_url"
                     @imagen="saveImage($event); form.imageCleared = false" 
                     @cleared="form.image = null; form.imageCleared = true"
                     class=""
                      />
                
                    <!-- información del diseño -->
                    <div class="grid grid-cols-2 gap-x-2 text-sm self-start mt-4 md:mt-0">
                        <p class="text-[#9A9A9A] my-2">Nombre del producto:</p>
                        <div>
                            <input v-model="form.name" type="text" class="input border border-[#9A9A9A] bg-transparent">
                            <InputError :message="form.errors.name" />
                        </div>
                        <p class="text-[#9A9A9A] my-2">Color:</p>
                        <div>
                            <input v-model="form.color" type="text" class="input border border-[#9A9A9A] bg-transparent">
                            <InputError :message="form.errors.color" />
                        </div>
                        <p class="text-[#9A9A9A] my-2">Material:</p>
                        <div>
                            <input v-model="form.material" type="text" class="input border border-[#9A9A9A] bg-transparent">
                            <InputError :message="form.errors.material" />
                        </div>
                        <p class="text-[#9A9A9A] my-2">Técnica de impresión:</p>
                        <div>
                            <input v-model="form.engrave_method" type="text" class="input border border-[#9A9A9A] bg-transparent">
                            <InputError :message="form.errors.engrave_method" />
                        </div>
                        <p class="text-[#9A9A9A] my-2">Vendedor:</p>
                        <input :value="this.$page.props.auth.user.name" disabled type="text" class="input border border-[#9A9A9A] bg-transparent">
                        <p class="text-[#9A9A9A] my-2">Logística:</p>
                        <div>
                            <input v-model="form.logistic" type="text" class="input border border-[#9A9A9A] bg-transparent">
                            <InputError :message="form.errors.logistic" />
                        </div>

                        <p class="text-[#9A9A9A] my-2 mt-7 col-span-full">Datos del cliente:</p>
                        <p class="text-[#9A9A9A] my-2">Sucursal:</p>
                        <div>
                            <el-select @change="form.contact_id = null; form.contact_charge = null" v-model="form.company_branch_id" class="" clearable
                                filterable placeholder="Selecciona un cliente">
                                <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                            <InputError :message="form.errors.company_branch_id" />
                        </div>
                        <p class="text-[#9A9A9A] my-2">Contacto:</p>
                        <div>
                            <el-select @change="getContactCharge()" v-model="form.contact_id" class="" clearable
                                filterable placeholder="Selecciona un contacto">
                                <el-option v-for="contact in company_branches.find(cb => cb.id ==
                                    form.company_branch_id)?.contacts" :key="contact" :label="contact.name"
                                    :value="contact.id" />
                            </el-select>
                            <InputError :message="form.errors.contact_id" />
                        </div>
                        <p class="text-[#9A9A9A] my-2">Puesto:</p>
                        <input v-model="form.contact_charge" disabled type="text" class="input border border-[#9A9A9A] bg-transparent">
                        <!-- <p class="text-[#9A9A9A] my-2">Fecha de autorización:</p>
                        <input v-model="form.responded_at" type="text" class="input border border-[#9A9A9A] bg-transparent"> -->

                        <div class="w-96">
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

                <div class="text-right my-5 lg:px-8">
                    <PrimaryButton @click="update">Guardar cambios</PrimaryButton>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import InputFilePreview from "@/Components/MyComponents/InputFilePreview.vue";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
      version: this.design_authorization.version,
      name: this.design_authorization.name,
      color: this.design_authorization.color,
      material: this.design_authorization.material,
      engrave_method: this.design_authorization.engrave_method,
      logistic: this.design_authorization.logistic,
      quantity: this.design_authorization.quantity,
      seller_id: this.$page.props.auth.user.id,
      company_branch_id: this.design_authorization.company_branch_id,
      contact_id: this.design_authorization.contact_id,
      contact_charge: null,
      imageCleared: false,
      image: null,
    });

    return {
        form,
    }
},
components:{
AppLayout,
InputFilePreview,
ApplicationLogo,
PrimaryButton,
InputError,
Back
},
props:{
company_branches: Array,
design_authorization: Object
},
methods:{
    update() {
        if (this.form.image != null ) {
            this.form.post(route("design-authorizations.update-with-media", this.design_authorization.id), {
                method: '_put',
                onSuccess: () => {
                  this.$notify({
                  title: "Correcto",
                  message: "Se ha editado el formato de autorización de diseño",
                  type: "success",
                      });
                  },
              });
        } else {
        this.form.put(route("design-authorizations.update", this.design_authorization.id), {
            onSuccess: () => {
                this.$notify({
                    title: "Correcto",
                    message: "Se ha editado el formato de autorización de diseño",
                    type: "success",
                });
                },
            });
        }
    },
    getContactCharge() {
        const companyBranch = this.company_branches.find(cb => cb.id === this.form.company_branch_id);
        this.form.contact_charge = companyBranch.contacts.find(contact => contact.id === this.form.contact_id).charge;
    },
    saveImage(image) {
        this.form.image = image;
    },
},
mounted() {
    this.getContactCharge();
}
}
</script>
