<template>
    <div v-for="catalogProductCompany in catalogProductsCompany" :key="catalogProductCompany" class="w-[700px] mx-auto relative mb-16">
        <!-- Encabezado de la tabla -->
        <div class="grid grid-cols-3 table-header text-left rounded-t-md bg-[#D9D9D9] border border-[#9A9A9A]">
            <div class="table-cell font-bold py-3 px-4">
            Fecha de cambio
            </div>
            <div class="table-cell font-bold py-3 px-4">
            Precio
            </div>
            <div class="table-cell font-bold py-3 px-4">
            Modificado por
            </div>
        </div>

        <!-- Cuerpo de la tabla -->
        <div class="">
            <div class="mb-4 grid grid-cols-3">
                <p class="absolute -top-7 left-2 font-bold text-sm">Producto: <span @click="$inertia.get(route('catalog-products.show', catalogProductCompany.id ))" class="underline text-secondary cursor-pointer">{{ catalogProductCompany.name }}</span></p>
                <PrimaryButton @click="showUpdatePriceModal = true; form.product_company_id = catalogProductCompany.pivot.id" class="absolute -top-7 right-2 font-bold text-sm !py-1">Actualizar precio</PrimaryButton>
                <div class="table-cell text-left py-2 px-4 border-l border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.oldest_date ?? '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.oldest_price ?? '--' + ' ' + catalogProductCompany.pivot.oldest_currency ?? '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-r border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.user?.name ?? '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-l border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.old_date }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.old_price + ' ' + catalogProductCompany.pivot.old_currency }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-r border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.user?.name ?? '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-l border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.new_date }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.new_price + ' ' + catalogProductCompany.pivot.new_currency }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-r border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.user?.name ?? '--' }}
                </div>
            </div>
        </div>
    </div>

    <!-- ----------------- modal ----------- -->
    <Modal :show="showUpdatePriceModal"
      @close="showUpdatePriceModal = false">
      <section class="mx-7 my-4 space-y-4 relative">
        <div>
          <label>Actualizar precio
          </label>
          <div class="mt-4 mb-2">
            <IconInput v-model="form.new_price" inputPlaceholder="Precio nuevo *" inputType="number"
                inputStep="0.01">
                <el-tooltip content="Precio nuevo" placement="top">
                <i class="fa-solid fa-money-bill"></i>
                </el-tooltip>
            </IconInput>
            </div>
            <div class="flex items-center">
                <el-select v-model="form.new_currency" placeholder="Moneda *" :fit-input-width="true">
                    <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                    <span style="float: left">{{ item.label }}</span>
                    <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                    </el-option>
                </el-select>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-5 pb-1">
          <CancelButton @click="showUpdatePriceModal = false">Cancelar</CancelButton>
          <PrimaryButton @click="updatePrice">Actualizar precio</PrimaryButton>
        </div>
      </section>
    </Modal>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";

export default {
data() {
    const form = useForm({
        new_price: null,
        new_currency: null,
        product_company_id: null,
    });
    return {
        form,
        showUpdatePriceModal: false,
        currencies: [
        { value: "$MXN", label: "MXN" },
        { value: "$USD", label: "USD" },
      ],
    }
},
components:{
PrimaryButton,
CancelButton,
IconInput,
Modal
},
props:{
  catalogProductsCompany: Object  
},
methods:{
    updatePrice() {
        this.form.put(route('company-branches.update-product-price', this.form.product_company_id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Precio actualizado",
            type: "success",
          });
          this.showUpdatePriceModal = false;
        },
      });
    }
}
}
</script>
