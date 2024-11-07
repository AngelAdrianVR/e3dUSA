<template>
    <div v-for="catalogProductCompany in catalogProductsCompany" :key="catalogProductCompany"
        class="w-[700px] mx-auto mb-16">
        <div class="flex justify-between">
            <p class="font-bold text-sm">
                Producto:
                <span @click="$inertia.get(route('catalog-products.show', catalogProductCompany.id))"
                    class="underline text-secondary cursor-pointer">{{ catalogProductCompany.name }}
                </span>
            </p>
            <PrimaryButton
                @click="handleUpdatePrice(catalogProductCompany)"
                class="font-bold text-sm !py-1">Actualizar precio</PrimaryButton>
        </div>
        <!-- Encabezado de la tabla -->
        <div class="grid grid-cols-3 table-header text-left rounded-t-md bg-[#D9D9D9] dark:bg-[#333333] border border-[#9A9A9A] mt-2">
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
                <div class="table-cell text-left py-2 px-4 border-l border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.oldest_date ? formatDate(catalogProductCompany.pivot.oldest_date) :
                        '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.oldest_price ?? '--' }}
                    {{ catalogProductCompany.pivot.oldest_currency }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-r border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.oldest_updated_by ?? '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-l border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.old_date ? formatDate(catalogProductCompany.pivot.old_date) : '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.old_price ?? '--' }}
                    {{ catalogProductCompany.pivot.old_currency }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-r border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.old_updated_by ?? '--' }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-l border-b border-[#9A9A9A]">
                    {{ formatDate(catalogProductCompany.pivot.new_date) }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.new_price + ' ' + catalogProductCompany.pivot.new_currency }}
                </div>
                <div class="table-cell text-left py-2 px-4 border-b border-r border-[#9A9A9A]">
                    {{ catalogProductCompany.pivot.new_updated_by ?? '--' }}
                </div>
            </div>
        </div>
    </div>

    <DialogModal :show="showUpdatePriceModal" @close="showUpdatePriceModal = false" maxWidth="lg">
        <template #title>
            <h1>Actualizar precio</h1>
        </template>
        <template #content>
            <section class="grid grid-cols-2 gap-3">
                <div>
                    <InputLabel value="Precio nuevo*" />
                    <el-input v-model="form.new_price" type="text"
                    :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                    :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 30.90" />
                    <InputError :message="form.errors.new_price" />
                </div>
                <div>
                    <InputLabel value="Moneda*" />
                    <el-select v-model="form.new_currency" placeholder="Seleccionar" :fit-input-width="true">
                        <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                            <span style="float: left">{{ item.label }}</span>
                            <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                        </el-option>
                    </el-select>
                    <InputError :message="form.errors.new_currency" />
                </div>
                <p v-if="form.new_price && (form.new_price - catalogProductsCompanySelected.pivot.new_price) < (catalogProductsCompanySelected.pivot.new_price * 0.04)"
                class="text-xs text-red-600 col-span-full">El incremento de precio no debe ser menor al 4% del precio actual</p>
            </section>
        </template>
        <template #footer>
            <div class="flex justify-end space-x-1">
                <CancelButton @click="showUpdatePriceModal = false" :disabled="form.processing">Cancelar</CancelButton>
                <PrimaryButton @click="updatePrice" :disabled="form.processing
                || !form.new_price 
                || (form.new_price - catalogProductsCompanySelected.pivot?.new_price) < (catalogProductsCompanySelected.pivot?.new_price * 0.04)">Actualizar precio</PrimaryButton>
            </div>
        </template>
    </DialogModal>
</template>

<script>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { useForm } from "@inertiajs/vue3";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

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
            catalogProductsCompanySelected: null,
            currencies: [
                { value: "$MXN", label: "MXN" },
                { value: "$USD", label: "USD" },
            ],
        }
    },
    components: {
        PrimaryButton,
        CancelButton,
        DialogModal,
        InputLabel,
        InputError,
    },
    props: {
        catalogProductsCompany: Object
    },
    methods: {
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMMM, Y', { locale: es }); // Formato personalizado
        },
        updatePrice() {
            this.form.put(route('company-branches.update-product-price', this.form.product_company_id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Precio actualizado",
                        type: "success",
                    });
                    this.showUpdatePriceModal = false;
                    this.form.reset();
                },
            });
        },
        handleUpdatePrice(catalogProductCompany) {
            this.catalogProductsCompanySelected = catalogProductCompany;
            this.showUpdatePriceModal = true;
            this.form.product_company_id = catalogProductCompany.pivot.id;
        }
    }
}
</script>
