<template>
    <div>
        <AppLayout title="Editar órden de venta">
            <template #header>
                <div class="flex justify-between">
                    <Back />
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Editar órden de venta</h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="update" class="relative overflow-x-hidden">
                <!-- company branch important notes -->
                <div class="absolute top-5 -right-1">
                    <div v-if="importantNotes" class="text-xs border border-[#9A9A9A] rounded-[5px] py-2 px-3 w-72">
                        <div class="absolute bg-primary top-1 -left-3 h-2 w-10 transform -rotate-45"></div>
                        <div class="absolute bg-primary top-1 -right-3 h-2 w-10 transform rotate-45"></div>
                        <h3 class="flex items-center justify-center mb-2">
                            Este cliente tiene nota
                            <i class="fa-regular fa-note-sticky ml-3"></i>
                        </h3>
                        <p style="white-space: pre-line;" class="px-1">{{ importantNotes }}</p>
                        <div class="mt-3">
                            <button @click="editImportantNotes()" type="button" class="text-[#9A9A9A] pr-2">Editar</button>
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                title="¿Borrar notas?" @confirm="clearImportantNotes()">
                                <template #reference>
                                    <button type="button"
                                        class="text-[#9A9A9A] border-l border-[#9A9A9A] px-2">Eliminar</button>
                                </template>
                            </el-popconfirm>
                        </div>
                    </div>
                    <div v-else-if="form.company_branch_id">
                        <button @click="showImportantNotesModal = true" type="button"
                            class="text-primary text-xs border border-[#9A9A9A] rounded-[5px] py-2 px-3">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Agregar nota para este cliente
                        </button>
                    </div>
                </div>
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
                    <el-radio-group v-model="form.is_sale_production" @change="handleChangeProductionType" size="small">
                        <el-radio :label="1">Orden de venta</el-radio>
                        <el-radio :label="0">Orden de stock</el-radio>
                    </el-radio-group>
                    <div class="flex items-center">
                        <el-tooltip content="Cliente: Seleccione para poder habilitar sus productos" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </el-tooltip>
                        <el-select @change="getImportantNotes()" v-model="form.company_branch_id" class="mt-2" clearable
                            filterable placeholder="Selecciona un cliente">
                            <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                :value="item.id" />
                        </el-select>
                    </div>
                    <div v-if="form.company_branch_id" class="flex items-center mt-3">
                        <el-tooltip content="Contacto" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9">
                                <i class="fa-solid fa-id-badge"></i>
                            </span>
                        </el-tooltip>
                        <el-radio-group v-model="form.contact_id" size="small">
                            <el-radio v-for="contact in company_branches.find(cb => cb.id ==
                                form.company_branch_id)?.contacts" :key="contact" :label="contact.id">
                                {{ contact.charge }}: {{ contact.name }} ({{ contact.email }}, {{
                                    contact.additional_emails?.join(', ') }})
                            </el-radio>
                        </el-radio-group>
                        <p v-if="!form.contact_id" class="text-xs text-primary ml-2">No olvides seleccionar el contacto.</p>
                        <InputError :message="form.errors.contact_id" />
                    </div>
                    <section v-if="form.is_sale_production">
                        <el-divider content-position="left">Logistica</el-divider>
                        <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-2">
                            <div class="ml-7 col-span-full">
                                <label class="text-sm ml-2 mb-px flex items-center">Fecha de embarque esperado
                                    <el-tooltip
                                        content="Esta aparecerá en producción para dar prioridad a ventas cercanas a su fecha de entrega"
                                        placement="right">
                                        <div
                                            class="rounded-full border border-primary w-3 h-3 flex items-center justify-center ml-2">
                                            <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                        </div>
                                    </el-tooltip>
                                </label>
                                <el-date-picker v-model="form.promise_date" type="date" placeholder="Fecha de entrega esperada"
                                    format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
                                <InputError :message="form.errors.promise_date" />
                            </div>
                            <div class="flex items-center">
                                <el-tooltip content="Paquetería" placement="top">
                                    <i
                                        class="fa-solid fa-truck-fast font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"></i>
                                </el-tooltip>
                                <el-select v-model="form.shipping_company" placeholder="Paquetería">
                                    <el-option v-for="(item, index) in shippingCompanies" :key="item" :label="item"
                                        :value="item" />
                                </el-select>
                                <InputError :message="form.errors.shipping_company" />
                            </div>
                            <div>
                                <IconInput v-model="form.freight_cost" inputPlaceholder="Costo logística" inputType="text">
                                    <el-tooltip content="Costo logística" placement="top">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="form.errors.freight_cost" />
                            </div>
                            <div class="col-span-full">
                                <IconInput v-model="form.tracking_guide" inputPlaceholder="Guía" inputType="text">
                                    <el-tooltip content="Guía" placement="top">
                                        <i class="fa-solid fa-magnifying-glass-location"></i>
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="form.errors.tracking_guide" />
                            </div>
                        </div>
                        <!-- Agregar parcialidades -->
                        <div v-for="(item, index) in form.partialities" :key="index" class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-2">
                            <h2 class="col-span-full font-bold flex items-center space-x-3">
                                <span>Parcialidad {{ (index + 2) }}</span>
                                <button @click="removePartial(index)" type="button" class="text-xs size-6 text-primary rounded-full hover:bg-gray-200">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </h2>
                            <div class="ml-7 col-span-full">
                                <label class="text-sm ml-2 mb-px flex items-center">Fecha de entrega esperada
                                    <el-tooltip
                                        content="Esta aparecerá en producción para dar prioridad a ventas cercanas a su fecha de entrega"
                                        placement="right">
                                        <div
                                            class="rounded-full border border-primary w-3 h-3 flex items-center justify-center ml-2">
                                            <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                        </div>
                                    </el-tooltip>
                                </label>
                                <el-date-picker v-model="form.partialities[index].promise_date" type="date" placeholder="Fecha de entrega esperada"
                                    format="YYYY/MM/DD" value-format="YYYY-MM-DD" :disabled-date="disabledDate" />
                            </div>
                            <div class="flex items-center">
                                <el-tooltip content="Paquetería" placement="top">
                                    <i
                                        class="fa-solid fa-truck-fast font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"></i>
                                </el-tooltip>
                                <el-select v-model="form.partialities[index].shipping_company" placeholder="Paquetería">
                                    <el-option v-for="(item, index) in shippingCompanies" :key="item" :label="item"
                                        :value="item" />
                                </el-select>
                            </div>
                            <div>
                                <IconInput v-model="form.partialities[index].freight_cost" inputPlaceholder="Costo logística" inputType="text">
                                    <el-tooltip content="Costo logística" placement="top">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </el-tooltip>
                                </IconInput>
                            </div>
                            <div class="col-span-full">
                                <IconInput v-model="form.partialities[index].tracking_guide" inputPlaceholder="Guía" inputType="text">
                                    <el-tooltip content="Guía" placement="top">
                                        <i class="fa-solid fa-magnifying-glass-location"></i>
                                    </el-tooltip>
                                </IconInput>
                            </div>
                        </div>
                        <!-- btn agregar parcialidad -->
                        <button @click="addPartial" type="button" class="col-span-full w-full text-primary text-xs text-right underline">+ Agregar parcialidad</button>
                    </section>
                    <section v-if="form.is_sale_production">
                        <el-divider content-position="left">Datos de la órden</el-divider>
                        <div class="grid gap-x-6 gap-y-2 mb-6 md:grid-cols-2">
                            <div class="flex items-center">
                                <el-tooltip content="Medio de petición *" placement="top">
                                    <i
                                        class="fa-solid fa-arrow-right-to-bracket font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"></i>
                                </el-tooltip>
                                <el-select v-model="form.order_via" placeholder="Medio de petición *">
                                    <el-option v-for="(item, index) in orderVias" :key="item" :label="item" :value="item" />
                                </el-select>
                                <InputError :message="form.errors.order_via" />
                            </div>
                            <div>
                                <IconInput v-model="form.invoice" inputPlaceholder="Factura">
                                    <el-tooltip content="Factura" placement="top">
                                        <i class="fa-solid fa-money-check-dollar"></i>
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="form.errors.invoice" />
                            </div>
                            <div class="md:col-span-3">
                                <IconInput v-model="form.oce_name" inputPlaceholder="Nombre / folio OCE">
                                    <el-tooltip content="Nombre / folio OCE" placement="top">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </el-tooltip>
                                </IconInput>
                                <InputError :message="form.errors.oce_name" />
                            </div>
                            <div class="col-span-full">
                                <div class="flex items-center">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                        <el-tooltip content="OCE" placement="top">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </el-tooltip>
                                    </span>
                                    <input @input="form.media = $event.target.files[0]" class="input h-12 rounded-lg
                                file:mr-4 file:py-1 file:px-2
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary file:text-white
                                file:cursor-pointer
                                hover:file:bg-red-600" aria-describedby="file_input_help" id="file_input" type="file">
                                </div>
                                <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">SVG, PNG, JPG o
                                    GIF (MAX. 4 MB).</p>
                            </div>
                            <div class="flex items-center space-x-2 col-span-full">
                                <label class="flex items-center">
                                    <Checkbox v-model:checked="form.is_high_priority" class="bg-transparent" />
                                    <span class="ml-2 text-xs">Prioridad alta</span>
                                </label>
                                <el-tooltip
                                    content="Al seleccionar esta opción, se recordará diariamente por notificación si no se ha creado una orden de producción"
                                    placement="top">
                                    <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                    </div>
                                </el-tooltip>
                            </div>
                        </div>
                    </section>
                    <div class="flex">
                        <el-tooltip content="Notas de la órden" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                        </el-tooltip>
                        <textarea v-model="form.notes" class="textarea" autocomplete="off"
                            placeholder="Notas de la órden"></textarea>
                        <InputError :message="form.errors.notes" />
                    </div>
                    <!-- products -->
                    <el-divider v-if="form.company_branch_id" content-position="left">Agregar productos</el-divider>

                    <InputError :message="form.errors.products" class="col-span-full" />
                    <ol v-if="form.products.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
                        <template v-for="(item, index) in form.products" :key="index">
                            <li class="flex justify-between items-center">
                                <p class="text-sm">
                                    <span class="text-primary">{{ index + 1 }}.</span>
                                    {{ company_branches.find(cb => cb.id ==
                                        form.company_branch_id)?.catalog_products?.find(prd => prd.pivot.id ===
                                            item.catalog_product_company_id)?.name
                                    }}
                                    (x{{ item.quantity }} unidades)
                                </p>
                                <div class="flex space-x-2 items-center">
                                    <el-tag v-if="editIndex == index">En edición</el-tag>
                                    <el-button @click="editProduct(index)" type="primary" circle>
                                        <i class="fa-sharp fa-solid fa-pen-to-square"></i>
                                    </el-button>
                                    <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                                        title="¿Continuar?" @confirm="deleteProduct(index)">
                                        <template #reference>
                                            <el-button type="danger" circle><i
                                                    class="fa-sharp fa-solid fa-trash"></i></el-button>
                                        </template>
                                    </el-popconfirm>
                                </div>
                            </li>
                        </template>
                    </ol>
                    <div v-if="form.company_branch_id"
                        class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-3 rounded-lg border-2 border-[#b8b7b7] px-5 py-3 col-span-full space-y-1 my-7">
                        <div class="col-span-2">
                            <div class="flex items-center">
                                <el-tooltip content="Producto: Seleccione entre los productos registrados para este cliente"
                                    placement="top">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                </el-tooltip>
                                <el-select @change="fetchCatalogProductData" v-model="product.catalog_product_company_id"
                                    class="w-full" no-data-text="No hay productos registrados a este cliente"
                                    placeholder="Selecciona un producto *">
                                    <el-option
                                        v-for="item in company_branches.find(cb => cb.id == form.company_branch_id)?.catalog_products"
                                        :key="item.pivot.id" :label="item.name" :value="item.pivot.id" />
                                </el-select>
                            </div>
                            <label v-if="product.part_number?.split('-')[1] == 'LL'" class="mt-1 inline">
                                <Checkbox v-model:checked="product.requires_medallion" class="bg-transparent" />
                                <span class="ml-2 text-xs">Requiere medallón</span>
                            </label>
                        </div>
                        <div v-if="loading" class="rounded-md bg-[#CCCCCC] text-xs text-gray-500 text-center p-4">
                            cargando imagen...
                        </div>
                        <figure v-else-if="selectedCatalogProduct" class="rounded-md h-24 border">
                            <img :src="selectedCatalogProduct.media[0]?.original_url"
                                class="rounded-md h-24 object-contain">
                            <div
                                class="w-full text-[#656262] border border-[#9A9A9A] px-1 py-px rounded-[5px] text-[10px] mt-1">
                                Stock mínimo: <span class="text-black">{{
                                    selectedCatalogProduct.min_quantity.toLocaleString('en-US', {
                                        minimumFractionDigits: 2
                                    }) }} unidades</span>
                            </div>
                        </figure>
                        <p v-if="selectedCatalogProduct" class="col-span-full text-xs flex items-center space-x-2 pt-5">
                            Stock disponible en almacén de producto terminado (no materia prima):
                            <b class="ml-1">{{ availableStock ? availableStock.quantity.toLocaleString('en-US', {
                                minimumFractionDigits: 2
                            }) : 0 }} unidades.</b>
                            <el-tooltip placement="top">
                                <template #content>
                                    Se descontarán de estas existencias para despachar la orden. <br>
                                    Se refiere a las piezas ya procesadas para tener el producto final, <br>
                                    no se refiere a la materia prima.
                                </template>
                                <div class="rounded-full border border-primary size-3 flex items-center justify-center">
                                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                </div>
                            </el-tooltip>
                        </p>
                        <div class="col-span-full">
                            <!-- <IconInput @change="validateQuantity()" v-model="product.quantity" inputPlaceholder="Cantidad *"
                                inputType="number" inputStep="0.01">
                                <el-tooltip content="Cantidad" placement="top">
                                    #
                                </el-tooltip>
                            </IconInput> -->
                            <div class="flex items-center space-x-6 ml-5">
                                <div>
                                    <label class="block text-xs">Cantidad *</label>
                                    <el-input-number @change="validateQuantity()" v-model="product.quantity" :min="0.01" />
                                </div>
                                <div class="flex items-center space-x-2 mt-3">
                                    <label class="flex items-center">
                                        <Checkbox v-model:checked="product.is_new_design" class="bg-transparent" />
                                        <span class="ml-2 text-xs">Diseño nuevo</span>
                                    </label>
                                    <el-tooltip placement="top">
                                        <template #content>
                                            <p>
                                                Selecciona esta opción cuando el diseño <br>
                                                del producto sea nuevo o distinto de <br>
                                                los existentes.
                                            </p>
                                        </template>
                                        <div
                                            class="rounded-full border border-primary w-3 h-3 flex items-center justify-center">
                                            <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                        </div>
                                    </el-tooltip>
                                </div>
                            </div>
                            <p v-if="alertMaxQuantity" class="text-red-600 text-xs"> Sólo hay material para producir {{
                                alertMaxQuantity }} unidades. No olvides reportar la adquisición de más mercancía </p>
                        </div>
                        <div class="flex col-span-full">
                            <el-tooltip content="Notas de producto" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                    ...
                                </span>
                            </el-tooltip>
                            <textarea v-model="product.notes" class="textarea" autocomplete="off"
                                placeholder="Notas de producto"></textarea>
                        </div>
                        <div class="col-span-full">
                            <SecondaryButton @click="addProduct"
                                :disabled="form.processing || !product.catalog_product_company_id || !product.quantity">
                                {{ editIndex !== null ? 'Actualizar producto' : 'Agregar producto a lista' }}
                            </SecondaryButton>
                        </div>
                    </div>
                    <div class="mt-7 mx-3 md:text-right">
                        <PrimaryButton :disabled="form.processing"> Actualizar órden de venta </PrimaryButton>
                    </div>
                </div>
            </form>
            <DialogModal :show="showImportantNotesModal" @close="showImportantNotesModal = false">
                <template #title>
                    {{ editIMportantNotes ? 'Editar' : 'Agregar' }} notas importantes para {{ company_branches.find(item =>
                        item.id == form.company_branch_id).name
                    }}
                </template>
                <template #content>
                    <div class="flex mt-6">
                        <el-tooltip
                            content="Estas notas se mostraran cuando se seleccione esta sucursal para crear cotizacion, orden de venta u otro movimiento"
                            placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                <i class="fa-solid fa-grip-lines"></i>
                            </span>
                        </el-tooltip>
                        <textarea v-model="importantNotesToStore" rows="4" class="textarea mb-1" autocomplete="off"
                            placeholder="Notas. Ejemplo: Precio acordado de 'x' producto en siguiente cotizacion $45.30"></textarea>
                    </div>
                </template>
                <template #footer>
                    <CancelButton @click="showImportantNotesModal = false">Cancelar</CancelButton>
                    <PrimaryButton @click="storeImportantNotes()" :disabled="!importantNotesToStore">Guardar notas
                    </PrimaryButton>
                </template>
            </DialogModal>
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            company_branch_id: this.sale.company_branch_id,
            contact_id: this.sale.contact_id,
            shipping_company: this.sale.shipping_company,
            freight_cost: this.sale.freight_cost,
            invoice: this.sale.invoice,
            oce_name: this.sale.oce_name,
            order_via: this.sale.order_via,
            tracking_guide: this.sale.tracking_guide,
            notes: this.sale.notes,
            promise_date: this.sale.promise_date,
            is_high_priority: Boolean(this.sale.is_high_priority),
            products: [],
            media: null,
            partialities: this.sale.partialities,
            is_sale_production: this.sale.is_sale_production,
        });

        return {
            form,
            loading: false,
            importantNotes: null,
            showImportantNotesModal: false,
            importantNotesToStore: null,
            isEditImportantNotes: false,
            showCreateProjectModal: false,
            availableStock: null,
            product: {
                catalog_product_company_id: null,
                quantity: null,
                notes: null,
                part_number: null,
                is_new_design: false,
                requires_medallion: false,
            },
            editIndex: null,
            alertMaxQuantity: 0,
            selectedCatalogProduct: null,
            commitedUnits: null,
            shippingCompanies: [
                'PAQUETEXPRESS',
                'LOCAL',
                'DHL',
                'FEDEX',
                'TRES GUERRAS',
            ],
            orderVias: [
                'Correo electrónico',
                'WhatsApp',
                'Llamada telefónica',
                'Resurtido programado',
                'Otro',
            ],
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        InputError,
        IconInput,
        DialogModal,
        CancelButton,
        Checkbox,
        Back,
        Link
    },
    props: {
        company_branches: Array,
        catalog_products_company_sale: Array,
        sale: Array,
        media: Array,
    },
    methods: {
        handleChangeProductionType() {
            
        },
        addPartial() {
            const partiality = {
                shipping_company: null,
                freight_cost: null,
                tracking_guide: null,
                promise_date: null,
            };

            this.form.partialities.push({... partiality});
        },
        removePartial(index) {
            this.form.partialities.splice(index, 1);
        },
        async fetchCatalogProductData() {
            try {
                this.loading = true;
                const catalogProductId =
                    this.company_branches.find(cb => cb.id == this.form.company_branch_id)?.catalog_products?.find(cp => cp.pivot.id == this.product.catalog_product_company_id)?.id;
                const response = await axios.get(route('catalog-products.get-data', catalogProductId));

                if (response.status === 200) {
                    this.commitedUnits = response.data.commited_units;
                    this.selectedCatalogProduct = response.data.item;
                    this.product.part_number = this.selectedCatalogProduct.part_number;
                    this.availableStock = response.data.stock;
                    this.loading = false;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'Problemas con el servidor',
                    message: 'No se pudo obtener la imagen del producto seleccionado debido a inconvenientes con el servidor. Inteéntalo más tarde',
                    type: 'error'
                });
            }
        },
        validateQuantity() {
            const catalogProducts = this.company_branches.find(cb => cb.id == this.form.company_branch_id)?.catalog_products;
            const components = catalogProducts.find(item => this.product.catalog_product_company_id == item.pivot.id)?.raw_materials;

            let maxQuantity = null;
            components.forEach((element, index) => {
                const currentMax = (element.storages[0].quantity - this.commitedUnits[index]) / element.pivot.quantity;
                if (maxQuantity === null || maxQuantity > currentMax) {
                    maxQuantity = currentMax;
                }

            });

            if (maxQuantity !== null && this.product.quantity > maxQuantity) {
                this.alertMaxQuantity = maxQuantity;
            } else {
                this.alertMaxQuantity = null;
            }
        },
        update() {
            if (this.form.media !== null) {
                this.form.post(route("sales.update-with-media", this.sale), {
                    method: '_put',
                    onSuccess: () => {
                        this.$notify({
                            title: "Éxito",
                            message: "Se actualizó correctamente",
                            type: "success",
                        });
                    },
                });
            } else {
                this.form.put(route("sales.update", this.sale), {
                    onSuccess: () => {
                        this.$notify({
                            title: "Éxito",
                            message: "Se actualizó correctamente",
                            type: "success",
                        });
                    },
                });
            }
        },
        getImportantNotes() {
            this.importantNotes = this.company_branches.find(item => item.id == this.form.company_branch_id)?.important_notes;
        },
        async clearImportantNotes() {
            try {
                const response = await axios.put(route('company-branches.clear-important-notes', this.form.company_branch_id));

                if (response.status === 200) {
                    this.importantNotes = null;
                    this.company_branches.find(item => item.id == this.form.company_branch_id).important_notes = null;
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                }
            } catch (error) {
                console.log(error);
            }
        },
        async storeImportantNotes() {
            try {
                const response = await axios.put(route('company-branches.store-important-notes', this.form.company_branch_id), { notes: this.importantNotesToStore });

                if (response.status === 200) {
                    this.importantNotes = this.importantNotesToStore;
                    this.company_branches.find(item => item.id == this.form.company_branch_id).important_notes = this.importantNotesToStore;
                    this.importantNotesToStore = null;
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.showImportantNotesModal = false;
            }
        },
        editImportantNotes() {
            this.isEditImportantNotes = true;
            this.showImportantNotesModal = true;
            this.importantNotesToStore = this.importantNotes;
        },
        addProduct() {
            const product = { ...this.product };
            if (this.editIndex !== null) {
                this.form.products[this.editIndex] = product;
                this.editIndex = null;
            } else {
                this.form.products.push(product);
            }

            this.resetProductForm();
        },
        deleteProduct(index) {
            this.form.products.splice(index, 1);
        },
        editProduct(index) {
            const product = { ...this.form.products[index] };
            this.product = product;
            this.editIndex = index;
        },
        resetProductForm() {
            this.product.id = null;
            this.product.catalog_product_company_id = null;
            this.product.quantity = null;
            this.product.notes = null;
            this.product.part_number = null;
            this.product.is_new_design = false;
            this.product.requires_medallion = false;
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
            return time < today;
        },
    },
    mounted() {
        this.catalog_products_company_sale.forEach(element => {
            const product = {
                id: element.id,
                catalog_product_company_id: element.catalog_product_company_id,
                quantity: element.quantity,
                notes: element.notes,
                part_number: element.catalog_product_company.catalog_product.part_number,
                is_new_design: Boolean(element.is_new_design),
                requires_medallion: Boolean(element.requires_medallion),
            }

            this.form.products.push(product);
        });
        this.getImportantNotes();
    }
};
</script>
