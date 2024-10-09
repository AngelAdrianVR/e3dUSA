<template>
    <div class="p-7">
        <div class="mb-5">
            <el-dropdown @click="openPrintPage" split-button type="primary" :disabled="!orderedProductsSelected.length">
                Imprimir
                <template #dropdown>
                    <el-dropdown-menu>
                        <el-dropdown-item @click="showPackageLabelForm = true">
                            Generador de etiqueta para envío
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </template>
            </el-dropdown>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-7">
            <ProductSaleCard @selected="handleSelections(index, $event)"
                v-for="(productSale, index) in sale.catalogProductCompanySales" :key="productSale.id"
                :catalog_product_company_sale="productSale" :isHighPriority="Boolean(parseInt(sale.is_high_priority))"
                :qualities="qualities" />
        </div>

        <!-- ----------------- etiqueta de envío modal ----------- -->
        <Modal :show="showPackageLabelForm" @close="showPackageLabelForm = false">
            <form @submit.stop="createBoxLabel" class="p-5 grid grid-cols-2 gap-x-3">
                <h1 class="col-span-full font-bold mb-1">Generar etiqueta</h1>
                <p class="text-xs col-span-full mb-3">Este sólo es un generador de etiquetas, por lo tanto no se
                    guardan en el
                    sistema.</p>

                <div class="mt-2">
                    <InputLabel value="Guía*" class="ml-2" />
                    <input v-model="labelForm.guide" type="text" class="input" placeholder="Agrega la guía" />
                    <InputError :message="labelForm.errors.guide" />
                </div>

                <div class="mt-2">
                    <InputLabel value="Orden de compra" class="ml-2" />
                    <input v-model="labelForm.ov" type="text" class="input" placeholder="Escriba la orden de compra" />
                    <InputError :message="labelForm.errors.ov" />
                </div>

                <div class="mt-2">
                    <InputLabel value="Folio" class="ml-2" />
                    <input v-model="labelForm.folio" type="text" class="input" placeholder="Escriba el folio" />
                    <InputError :message="labelForm.errors.folio" />
                </div>

                <div class="mt-2">
                    <InputLabel value="Factura" class="ml-2" />
                    <input v-model="labelForm.invoice" type="text" class="input" placeholder="Escriba la factura" />
                    <InputError :message="labelForm.errors.invoice" />
                </div>

                <div class="my-2">
                    <InputLabel value="No. de parte" class="ml-2" />
                    <input v-model="labelForm.part_number" type="text" class="input"
                        placeholder="Agregue el número de parte" />
                </div>

                <!-- ----- Número de cajas ----- -->
                <section v-for="(box, index) in labelForm.boxes" :key="index"
                    class="col-span-full flex items-center space-x-3 mt-2">
                    <div class="w-1/4">
                        <InputLabel value="Caja" class="ml-2" />
                        <input v-model="labelForm.boxes[index].name" disabled type="text" class="input"
                            placeholder="Nombre de caja" />
                    </div>
                    <div class="w-1/4">
                        <InputLabel value="Producto" class="ml-2" />
                        <input v-model="labelForm.boxes[index].product_name" type="text" class="input"
                            placeholder="Escribe el nombre del producto" />
                    </div>
                    <div class="w-1/4">
                        <InputLabel value="Piezas" class="ml-2" />
                        <input v-model="labelForm.boxes[index].quantity" type="text" class="input"
                            placeholder="Escribe la cantidad de piezas"
                            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                            :parser="(value) => value.replace(/\D/g, '')" />
                    </div>
                    <i @click="deleteBox(index)" v-if="index === boxIndex - 1 && labelForm.boxes.length > 1"
                        class="fa-regular fa-trash-can text-primary cursor-pointer p-1 mt-5"></i>
                </section>

                <p @click="addBox" class="text-primary mt-2 cursor-pointer ml-4">+ Agregar caja</p>

                <div class="block ml-4 mt-4 col-span-full">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="labelForm.is_fragil" class="bg-transparent" />
                        <span class="ml-2 text-sm">El producto es frágil</span>
                    </label>
                </div>

                <div class="col-span-full flex justify-end space-x-3 items-start mt-4">
                    <CancelButton @click="showPackageLabelForm = false">Cancelar</CancelButton>
                    <PrimaryButton :disabled="!labelForm.boxes[0].product_name || !labelForm.boxes[0].quantity">
                        Crear etiqueta
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import ProductSaleCard from '@/Components/MyComponents/ProductSaleCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    data() {
        const labelForm = useForm({
            client: this.sale.company_branch.company.business_name,
            company_branch: this.sale.company_branch.name,
            contact: this.sale.contact?.name,
            company_branch_address: this.sale.company_branch.address,
            post_code: this.sale.company_branch.post_code,
            contact_phone: this.sale.contact?.phone,
            ov: this.sale.folio,
            folio: null,
            guide: null,
            invoice: null,
            part_number: null,
            is_fragil: false,
            boxes: [
                {
                    name: 'Caja 1',
                    product_name: null,
                    quantity: null,
                }
            ],
        });

        return {
            labelForm,
            boxIndex: 1, //cuenta el index de cada caja del arreglo en labelForm
            orderedProductsSelected: [],
            showPackageLabelForm: false, //muestra formulario para imprir etiqueta de envío
        }
    },
    props: {
        sale: Object,
        qualities: Object,
    },
    components: {
        ProductSaleCard,
        Modal,
        InputLabel,
        InputError,
        Checkbox,
        CancelButton,
        PrimaryButton,
    },
    methods: {
        openPrintPage() {
            const url = route('productions.print', JSON.stringify(this.orderedProductsSelected));
            window.open(url, '_blank');
        },
        handleSelections(index, isSelected) {
            if (isSelected) {
                this.orderedProductsSelected.push(this.sale.catalogProductCompanySales[index].id);
            }
            else {
                const opsIndex = this.orderedProductsSelected.findIndex(item => item == this.sale.catalogProductCompanySales[index].id)
                this.orderedProductsSelected.splice(opsIndex, 1);
            }
        },
        createBoxLabel() {
            this.$inertia.post(route('productions.generate-box-label'), { data: this.labelForm });
        },
        addBox() {
            this.labelForm.boxes.push({ name: 'Caja ' + (this.boxIndex + 1), quantity: null });
            this.boxIndex++;
        },
        deleteBox(index) {
            this.labelForm.boxes.splice(index, 1);
            this.boxIndex--;
        },
    }
}
</script>
