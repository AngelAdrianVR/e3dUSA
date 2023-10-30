<template>
    <div>
        <AppLayout title="Crear órden de venta">
            <template #header>
                <div class="flex justify-between">
                    <Link :href="route('sales.index')"
                        class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
                    <i class="fa-solid fa-chevron-left"></i>
                    </Link>
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Crear órden de venta</h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="store" class="relative overflow-x-hidden">
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
                    <div class="flex items-center">
                        <el-tooltip content="Cliente: Seleccione para poder habilitar sus productos" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </el-tooltip>
                        <el-select @change="getImportantNotes()" v-model="form.company_branch_id" class="mt-2" clearable filterable
                            placeholder="Selecciona un cliente">
                            <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                :value="item.id" />
                        </el-select>
                    </div>
                    <div v-if="form.company_branch_id" class="flex items-center mt-3">
                        <el-tooltip content="Contacto" placement="top">
                            <span
                                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                <i class="fa-solid fa-id-badge"></i>
                            </span>
                        </el-tooltip>
                        <el-radio-group v-model="form.contact_id" size="small">
                            <el-radio-button v-for="contact in company_branches.find(cb => cb.id ==
                                form.company_branch_id)?.contacts" :key="index" :label="contact.id">
                                {{ contact.name }} ({{ contact.email }})
                            </el-radio-button>
                        </el-radio-group>
                        <InputError :message="form.errors.contact_id" />
                    </div>
                    <el-divider content-position="left">Logistica</el-divider>


                    <div class="md:grid gap-6 mb-6 grid-cols-2">
                        <div>
                            <IconInput v-model="form.shipping_company" inputPlaceholder="Paquetería" inputType="text">
                                <el-tooltip content="Paquetería" placement="top">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </el-tooltip>
                            </IconInput>
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
                        <div>
                            <IconInput v-model="form.tracking_guide" inputPlaceholder="Guía" inputType="text">
                                <el-tooltip content="Guía" placement="top">
                                    <i class="fa-solid fa-magnifying-glass-location"></i>
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.tracking_guide" />
                        </div>
                    </div>

                    <el-divider content-position="left">Datos de la órden</el-divider>
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <IconInput v-model="form.order_via" inputPlaceholder="Medio de petición">
                                <el-tooltip content="Medio de petición" placement="top">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                </el-tooltip>
                            </IconInput>
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
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9">
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
                    </div>
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
                                        form.company_branch_id)?.company.catalog_products.find(prd => prd.pivot.id ===
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
                        class="md:grid gap-6 mb-6 grid-cols-3 rounded-lg border-2 border-[#b8b7b7] px-5 py-3 col-span-full space-y-1 my-7">
                        <div class="flex items-center col-span-2">
                            <el-tooltip content="Producto: Seleccione entre los productos registrados para este cliente"
                                placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </el-tooltip>
                            <el-select v-model="product.catalog_product_company_id"
                                no-data-text="No hay productos registrados a este cliente" clearable
                                placeholder="Selecciona un producto *">
                                <el-option
                                    v-for="item in company_branches.find(cb => cb.id == form.company_branch_id)?.company.catalog_products"
                                    :key="item.pivot.id" :label="item.name" :value="item.pivot.id" />
                            </el-select>
                        </div>
                        <div>
                            <IconInput @change="validateQuantity()" v-model="product.quantity" inputPlaceholder="Cantidad *"
                                inputType="number" inputStep="0.01">
                                <el-tooltip content="Cantidad" placement="top">
                                    #
                                </el-tooltip>
                            </IconInput>
                            <p v-if="alertMaxQuantity" class="text-red-600 text-xs"> Sólo hay material para producir {{
                                alertMaxQuantity }} unidades. No olvides reportar la adquisición de más mercancía </p>
                            <!-- <InputError :message="form.errors.fiscal_address" /> -->
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
                            <!-- <InputError :message="form.errors.notes" /> -->
                        </div>
                        <div class="col-span-full">
                            <SecondaryButton @click="addProduct"
                                :disabled="form.processing || !product.catalog_product_company_id || !product.quantity">
                                {{ editIndex !== null ? 'Actualizar producto' : 'Agregar producto a lista' }}
                            </SecondaryButton>
                        </div>
                    </div>
                    <div class="mt-7 mx-3 md:text-right">
                        <PrimaryButton :disabled="form.processing"> Crear órden de venta </PrimaryButton>
                    </div>
                </div>
            </form>
            <DialogModal :show="showImportantNotesModal" @close="showImportantNotesModal = false">
                <template #title>
                    {{ editIMportantNotes ? 'Editar' : 'Agregar' }} notas importantes para {{ company_branches.find(item => item.id == form.company_branch_id).name
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
                    <PrimaryButton @click="storeImportantNotes()" :disabled="!importantNotesToStore">Guardar notas
                    </PrimaryButton>
                    <CancelButton @click="showImportantNotesModal = false">Cancelar</CancelButton>
                </template>
            </DialogModal>

            <Modal :show="showCreateProjectModal" @close="showCreateProjectModal = false">
                <section class="mx-7 my-4 space-y-4">
                    <div>
                    <p class="text-secondary text-center mt-10 font-bold">
                        ¿Quieres crear un proyecto de esta venta para llevar un mejor flujo de trabajo?
                    </p>
                    </div>
                    <div class="flex justify-end space-x-3 pt-5 pb-1">
                    <a :href="route('sales.index')">
                        <CancelButton>No crear proyecto</CancelButton>
                    </a>
                    <PrimaryButton @click="$inertia.get(route('projects.create'))">Crear proyecto</PrimaryButton>
                    </div>
                </section>
            </Modal>
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Modal from "@/Components/Modal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";

export default {
    data() {
        const form = useForm({
            company_branch_id: null,
            oportunity_id: null,
            contact_id: null,
            shipping_company: null,
            freight_cost: null,
            invoice: null,
            oce_name: null,
            order_via: null,
            tracking_guide: null,
            notes: null,
            products: [],
            media: null,
        });

        return {
            form,
            importantNotes: null,
            showImportantNotesModal: false,
            importantNotesToStore: null,
            isEditImportantNotes: false,
            showCreateProjectModal: false,
            product: {
                catalog_product_company_id: null,
                quantity: null,
                notes: null,
            },
            editIndex: null,
            alertMaxQuantity: 0,
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        PrimaryButton,
        Link,
        InputError,
        IconInput,
        CancelButton,
        DialogModal,
        Modal,
    },
    props: {
        company_branches: Array,
        data: Array,
    },
    methods: {
        store() {
            this.form.post(route('sales.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Orden de venta creada. Se han descontado las cantidades del stock automáticamente',
                        type: 'success'
                    });
                    this.showCreateProjectModal = true;
                }
            });
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
                const response = await axios.put(route('company-branches.store-important-notes', this.form.company_branch_id), {notes: this.importantNotesToStore});

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
        validateQuantity() {
            const catalogProducts = this.company_branches.find(cb => cb.id == this.form.company_branch_id)?.company.catalog_products;
            const components = catalogProducts.find(item => this.product.catalog_product_company_id == item.pivot.id)?.raw_materials;

            let maxQuantity = null;
            components.forEach(element => {
                const currentMax = element.storages[0].quantity / element.pivot.quantity;
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
            this.product.catalog_product_company_id = null;
            this.product.quantity = null;
            this.product.notes = null;
        }
    },
    mounted(){
        if(this.data) {
            this.form.company_branch_id = this.data.company_branch_id;
            this.form.oportunity_id = this.data.oportunity_id;
        }
    }
};
</script>
