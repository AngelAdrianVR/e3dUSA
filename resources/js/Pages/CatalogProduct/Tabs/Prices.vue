<template>
    <section class="py-3 text-sm">
        <div v-for="company in product.companies" :key="company.id"
            class="grid grid-cols-4 gap-3 uppercase odd:bg-gray-200 dark:odd:bg-gray-600 p-1 rounded-md">
            <p class="col-span-3">{{ company.business_name }}</p>
            <div class="flex items-center space-x-2">
                <button @click="handleUpdateProductPrice(company)"
                    class="rounded-full size-5 dark:hover:bg-gray-200 text-black flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                </button>
                <p>
                    {{ company.pivot.new_price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' ' +
                        company.pivot.new_currency }}
                    ({{ formatDate(company.pivot.new_date) }})
                </p>
            </div>
        </div>
    </section>

    <DialogModal :show="showUpdatePriceModal" @close="showUpdatePriceModal = false" maxWidth="2xl">
        <template #title>
            <h1>Actualizar precio</h1>
        </template>
        <template #content>
            <section class="grid grid-cols-3 gap-3 mt-3">
                <div>
                    <InputLabel value="Precio nuevo en porcentaje*" />
                    <el-input @change="calculateNewPrice()" v-model="new_price_percentage" type="number" :max="100"
                        :min="5" step="0.1" placeholder="Ej. 5.8%"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')">
                        <template #prepend>
                            %
                        </template>
                    </el-input>
                    <InputError :message="priceForm.errors.new_price" />
                </div>
                <div>
                    <InputLabel value="Precio nuevo en moneda*" />
                    <el-input @input="calculateNewPercentage()" v-model="priceForm.new_price" type="text"
                        :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                        :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 30.90">
                        <template #prepend>
                            $
                        </template>
                    </el-input>
                    <InputError :message="priceForm.errors.new_price" />
                </div>
                <div>
                    <InputLabel value="Moneda*" />
                    <el-select v-model="priceForm.new_currency" placeholder="Seleccionar" :fit-input-width="true">
                        <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
                            <span style="float: left">{{ item.label }}</span>
                            <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
                        </el-option>
                    </el-select>
                    <InputError :message="priceForm.errors.new_currency" />
                </div>
                <div>
                    <InputLabel value="Fecha de cambio*" />
                    <el-date-picker
                        v-model="priceForm.new_date"
                        type="date"
                        placeholder="Selecciona una fecha"
                    />
                    <InputError :message="priceForm.errors.new_date" />
                </div>
                <p v-if="priceForm.new_price && (priceForm.new_price - itemToUpdatePrice.new_price) < (itemToUpdatePrice.new_price * 0.04)"
                    class="text-xs text-red-600 col-span-full">El incremento de precio no debe ser menor al 4%
                    del precio actual</p>
            </section>
        </template>
        <template #footer>
            <div class="flex justify-end space-x-1">
                <CancelButton @click="showUpdatePriceModal = false" :disabled="priceForm.processing">Cancelar
                </CancelButton>
                <PrimaryButton type="button" @click="updatePrice" :disabled="priceForm.processing
                    || !priceForm.new_price
                    || (priceForm.new_price - itemToUpdatePrice.new_price) < (itemToUpdatePrice.new_price * 0.04)">
                    Actualizar precio
                </PrimaryButton>
            </div>
        </template>
    </DialogModal>
</template>
<script>
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        const priceForm = useForm({
            new_price: null,
            new_currency: null,
            new_date: new Date().toISOString().split('T')[0], // Fecha actual en formato YYYY-MM-DD,
            product_company_id: null,
        });

        return {
            priceForm,
            showUpdatePriceModal: false,
            new_price_percentage: 5, //porcentaje de aumento de precio
            itemToUpdatePrice: null, //producto de compañia seleccionado para cambiar precio
            currencies: [
                { value: "$MXN", label: "MXN" },
                { value: "$USD", label: "USD" },
            ],
        }
    },
    props: {
        product: Object,
    },
    components: {
        DialogModal,
        PrimaryButton,
        CancelButton,
        InputLabel,
        InputError,
    },
    methods: {
        handleUpdateProductPrice(company) {
            this.itemToUpdatePrice = company.pivot;
            // asigna el id del producto al formulario de cambio de precio
            this.priceForm.product_company_id = company.pivot.id;
            // calcular el 5% del precio actual y agregarlo a nuevo precio como cantidad por defecto
            this.priceForm.new_price = (company.pivot.new_price * 1.05).toFixed(2);
            // Agregar moneda del producto seleccionado
            this.priceForm.new_currency = company.pivot.new_currency;
            // Abrir modal
            this.showUpdatePriceModal = true;
        },
        calculateNewPrice() {
            // factor para calcular porcentaje del precio
            const factor = 1 + this.new_price_percentage * .01;
            // guarda el precio calculado con el porcentaje seleccionado
            this.priceForm.new_price = (factor * this.itemToUpdatePrice.new_price).toFixed(2);
        },
        calculateNewPercentage() {
            if (!this.priceForm.new_price || !this.itemToUpdatePrice.new_price) {
                this.new_price_percentage = 0;
                return;
            }

            // Convierte los valores a número
            const oldPrice = parseFloat(this.itemToUpdatePrice.new_price);
            const newPrice = parseFloat(this.priceForm.new_price);

            // Calcula el porcentaje de aumento
            this.new_price_percentage = (((newPrice / oldPrice) - 1) * 100).toFixed(2);
        },
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMM, Y', { locale: es }); // Formato personalizado
        },
        updatePrice() {
            this.priceForm.put(route('company-branches.update-product-price', this.priceForm.product_company_id), {
                onSuccess: () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Precio actualizado",
                        type: "success",
                    });
                    this.showUpdatePriceModal = false;
                    this.priceForm.reset();
                    this.new_price_percentage = 5;
                },
            });
        },
    },
}
</script>