<template>
  <div class="overflow-x-auto">
      <table class="w-full mx-auto text-sm">
          <thead>
            <tr class="text-center">
              <th class="font-bold pb-5">
                Folio
              </th>
              <th class="font-bold pb-5">
                Creado por
              </th>
              <th class="font-bold pb-5">
                Creado el
              </th>
              <th class="font-bold pb-5">
                Receptor
              </th>
              <th class="font-bold pb-5">
                Autorizado por
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
            <tr @click="openQuoteInNewTab(quote.id)" v-for="quote in quotes" :key="quote" class="border-b border-[#9A9A9A] cursor-pointer hover:border-primary">
              <td class="text-center py-2 px-2">
                  <div class="flex items-center">
                      <img class="w-14 object-contain" src="@/../../public/images/pdf.png" alt="">
                      <p class="font-bold">{{ quote.folio }}</p>
                  </div>
              </td>
              <td class="text-center py-2 px-2">
                  {{ quote.user.name }}
              </td>
              <td class="text-center py-2 px-2">
                  {{ quote.created_at }}
              </td>
              <td class="text-center py-2 px-2">
                  {{ quote.receiver }}
              </td>
              <td class="text-center py-2 px-2">
                  {{ quote.authorized_user_name }}
              </td>
              <td class="text-center py-2 px-2">
                  {{ quote.responded_at ?? '--' }}
              </td>
              <td class="text-center py-2 px-2">
                <div :class="quote.status.color" class="flex items-center justify-center space-x-2"><p>{{ quote.status.label }}</p><span v-html="quote.status.icon"></span></div>
              </td>
              <td class="text-center py-2 px-2 rounded-r-full">
                <div class="flex items-center space-x-2">
                  <i v-if="!quote.responded_at && !quote.authorized_at" @click.stop="$inertia.get(route('quote.edit', quote.id))" class="fa-solid fa-pencil text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
                  <!-- <el-popconfirm v-if="!quote.responded_at" confirm-button-text="Si" cancel-button-text="No" icon-color="#C30303" title="¿Continuar?" @confirm="deleteItem(quote.id)">
                          <template #reference>
                            <i @click.stop="" class="fa-regular fa-trash-can text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
                          </template>
                      </el-popconfirm> -->
                  <i @click.stop="authorizeQuote(quote.id)" title="Autorizar cotización" v-if="!quote.authorized_at && $page.props.auth.user.permissions.includes('Autorizar cotizaciones')" class="fa-solid fa-check text-xs py-2 px-[10px] rounded-full hover:bg-gray-200"></i>
                  <i @click.stop="rejected_razon = quote.rejected_razon; rejectedRazonModal = true" title="Motivo de rechazo" v-if="quote.quote_acepted === 0" class="fa-solid fa-info py-2 px-[13px] rounded-full hover:bg-gray-200"></i>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
  </div>

  <!-- -------------- Modal starts----------------------- -->
  <Modal :show="rejectedRazonModal" @close="rejectedRazonModal = false">
    <div class="p-5 relative">
        <h2 class="font-bold">Información de rechazo de cotización</h2>
        <i @click="rejectedRazonModal = false"
        class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3 top-3"></i>
        <p class="text-sm text-gray-600">Por favor, revisa los detalles las razones del rechazo para tomar las medidas necesarias para abordar las preocupaciones del cliente. </p>

        <div class="mt-5">
          <InputLabel value="Motivo de rechazo" class="ml-3 mb-1" />
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
quotes: Array,
},
emits:['design-deleted'],
methods:{ 
  async authorizeQuote(quoteId) {
        try {
            const response = await axios.put(route('quotes.authorize', quoteId));

        if (response.status === 200) {
            this.quotes.find(quote => quote.id === quoteId).authorized_at = response.data.item.authorized_at;
            this.quotes.find(quote => quote.id === quoteId).status.label = 'Esperando respuesta del cliente';
            this.$notify({
                title: 'Éxito',
                message: response.data.message,
                type: 'success'
            });
        }
        } catch (err) {
            this.$notify({
                title: 'Algo salió mal',
                message: err.message,
                type: 'error'
            });
            console.log(err);
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
    },
    openQuoteInNewTab(quoteId) {
        window.open(route('quotes.show', { id: quoteId }), '_blank');
    }
}
}
</script>
