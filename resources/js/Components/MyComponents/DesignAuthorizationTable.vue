<template>
<div class="overflow-x-auto">
    <table class="w-3/4 mx-auto">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-5">
              Nombre del documento
            </th>
            <th class="font-bold pb-5">
              Fecha de creación
            </th>
            <th class="font-bold pb-5">
              Fecha de respuesta
            </th>
            <th class="font-bold pb-5">
              Estatus
            </th>
            <th class="font-bold pb-5">
            </th>
          </tr>
        </thead>
        <tbody>
          <tr @click="$inertia.get(route('design-authorizations.show', design.id))" v-for="design in designAuthorizations" :key="design" class="border-b border-[#9A9A9A] cursor-pointer hover:border-primary">
            <td class="text-center py-2 px-2">
                <div class="flex items-center">
                    <img class="w-14 object-contain" src="@/../../public/images/pdf.png" alt="">
                    <p class="font-bold">{{ design.name }}</p>
                </div>
            </td>
            <td class="text-center py-2 px-2">
                {{ design.created_at ?? '--' }}
            </td>
            <td class="text-center py-2 px-2">
                {{ design.responded_at ?? '--' }}
            </td>
            <td class="text-center py-2 px-2">
              <div :class="design.status.color" class="flex items-center justify-center space-x-2"><p>{{ design.status.label }}</p><span v-html="design.status.icon"></span></div>
            </td>
            <td class="text-center py-2 px-2 rounded-r-full">
              <div class="flex items-center space-x-2">
                <i v-if="!design.responded_at && !design.authorized_at" @click.stop="$inertia.get(route('design-authorizations.edit', design.id))" class="fa-solid fa-pencil text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
                <el-popconfirm v-if="!design.responded_at" confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(design.id)">
                    <template #reference>
                      <i @click.stop="" class="fa-regular fa-trash-can text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
                    </template>
                </el-popconfirm>
                <i @click.stop="rejected_razon = design.rejected_razon; rejectedRazonModal = true" title="Motivo de rechazo" v-if="design.design_accepted === 0" class="fa-solid fa-info py-2 px-[13px] rounded-full hover:bg-gray-200"></i>
                <i @click.stop="authorizeDesign(design.id)" title="Autorizar formato" v-if="!design.authorized_at && $page.props.auth.user.permissions.includes('Autorizar ordenes de diseño')" class="fa-solid fa-check text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
</div>

<!-- -------------- Modal starts----------------------- -->
  <Modal :show="rejectedRazonModal" @close="rejectedRazonModal = false">
    <div class="p-5 relative">
        <h2 class="font-bold">Información de rechazo de diseño</h2>
        <i @click="rejectedRazonModal = false"
        class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3 top-3"></i>
        <p class="text-sm text-gray-600">Por favor, revisa los detalles las razones del rechazo para tomar las medidas necesarias para abordar las preocupaciones del cliente. </p>

        <div class="mt-7">
          <InputLabel value="Motivo de rechazo" class="mb-1" />
          <p class="text-sm text-gray-500">{{ rejected_razon }}</p>
        </div>
    </div>
  </Modal>
  <!-- --------------------------- Modal ends ------------------------------------ -->
</template>

<script>
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import axios from 'axios';
export default {
data(){
    return{
      rejectedRazonModal: false,
      rejected_razon: null,
    }
},
components:{
  InputLabel,
  Modal
},
props:{
designAuthorizations: Array,
},
emits:['design-deleted'],
methods:{ 
  async authorizeDesign(designId) {
        try {
            const response = await axios.put(route('design-authorizations.authorize', designId));
            if ( response.status === 200 ) {
                this.designAuthorizations.find(design => design.id === designId).authorized_at = response.data.authorized_at;
                this.designAuthorizations.find(design => design.id === designId).status.label = 'Esperando respuesta del cliente';
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
    async deleteItem(designId) {
        try {
            const response = await axios.delete(route('design-authorizations.destroy', designId));
            if (response.status === 200) {
                // this.$emit('design-deleted', designId);              
                this.$notify({
                    title: "Éxito",
                    message: "Se ha eliminado el formato de autorización de diseño",
                    type: "success",
                });
                location.reload();
            }
        } catch (error) {
            console.log(error);
            this.$notify({
                title: "Algo salió mal",
                message: "No se pudo eliminar el formato de autorización de diseño. Inténta más tarde",
                type: "error",
            });
        }
    }
}
}
</script>
