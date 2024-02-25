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
                <i v-if="!design.responded_at" @click.stop="" class="fa-solid fa-pencil text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
                <i v-if="!design.responded_at" @click.stop="" class="fa-regular fa-trash-can text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
                <i @click.stop="authorizeDesign(design.id)" title="Autorizar formato" v-if="!design.authorized_at" class="fa-solid fa-check text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
</div>
</template>

<script>
import axios from 'axios';
export default {
data(){
    return{

    }
},
components:{
},
props:{
designAuthorizations: Array,
},
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
    }
}
}
</script>
