<template>
    <div>
        <AppLayout title="Crear cotización">
            <template #header>
                <div class="flex justify-between">
                    <Back />
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Crear cotización</h2>
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

                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg px-9 py-5 shadow-md">
                    <div class="md:grid gap-x-6 gap-y-2 mb-6 grid-cols-2">
                        <div class="col-span-2 flex justify-between">
                            <el-radio-group v-model="form.is_spanish_template" size="small">
                                <el-radio :label="1">Plantilla en español</el-radio>
                                <el-radio :label="0">Plantilla en inglés</el-radio>
                            </el-radio-group>
                            <el-radio-group v-model="form.is_customer" @change="handleChangeModelId()" size="small">
                                <el-radio :label="1">Para cliente</el-radio>
                                <el-radio :label="0">Para prospecto</el-radio>
                            </el-radio-group>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <el-tooltip
                                    content="La moneda que se elija se usará para productos y costos de flete y herramental"
                                    placement="top">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                        <i class="fa-solid fa-dollar-sign"></i>
                                    </span>
                                </el-tooltip>
                                <el-select v-model="form.currency" placeholder="Moneda *" :fit-input-width="true">
                                    <el-option v-for="item in currencies" :key="item.value" :label="item.label"
                                        :value="item.value">
                                        <span style="float: left">{{ item.label }}</span>
                                        <span style="
                                            float: right;
                                            color: #cccccc;
                                            font-size: 13px;
                                            ">{{ item.value }}</span>
                                    </el-option>
                                </el-select>
                            </div>
                            <InputError :message="form.errors.currency" />
                        </div>
                        <div>
                            <div class="flex items-center mb-2">
                                <el-tooltip
                                    content="Para poder cotizar, los clientes (sucursales) deben de estar registrados"
                                    placement="top">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                </el-tooltip>
                                <el-select v-if="form.is_customer" @change="getImportantNotes()" v-model="form.company_branch_id" clearable
                                    filterable placeholder="Busca el cliente" no-data-text="No hay clientes registrados"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                        :value="item.id" />
                                </el-select>
                                <el-select v-else v-model="form.prospect_id" @change="handleSelectProspect()" clearable
                                    filterable placeholder="Busca el prospecto" no-data-text="No hay prospectos registrados"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="item in prospects" :key="item.id" :label="item.name"
                                        :value="item.id" />
                                </el-select>
                            </div>
                            <InputError :message="form.errors.company_branch_id" />
                            <InputError :message="form.errors.prospect_id" />
                        </div>
                        <div>
                            <IconInput v-model="form.receiver" inputPlaceholder="Nombre de quien recibe *" inputType="text">
                                <el-tooltip content="Nombre de quien recibe" placement="top">
                                    A
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.receiver" />
                        </div>
                        <div>
                            <IconInput v-model="form.department" inputPlaceholder="Departamento o puesto *"
                                inputType="text">
                                <el-tooltip content="Departamento o puesto" placement="top">
                                    A
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.department" />
                        </div>
                        <div>
                            <IconInput v-model="form.tooling_cost" inputPlaceholder="Costo de herramental *"
                                inputType="text">
                                <el-tooltip content="Costo de herramental" placement="top">
                                    <i class="fa-solid fa-hammer"></i>
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.tooling_cost" />
                        </div>
                        <div class="flex space-x-3">
                            <el-select v-model="form.tooling_currency" placeholder="Moneda *" :fit-input-width="true">
                                <el-option v-for="item in toolingCurrencies" :key="item.value" :label="item.label"
                                    :value="item.value">
                                    <span style="float: left">{{ item.label }}</span>
                                    <span style="
                                            float: right;
                                            color: #cccccc;
                                            font-size: 13px;
                                            ">{{ item.value }}</span>
                                </el-option>
                            </el-select>
                            <InputError :message="form.errors.tooling_currency" />
                            <label class="flex items-center text-gray-600">
                                <input type="checkbox" v-model="form.tooling_cost_stroked"
                                    class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                                <span class="ml-2 text-sm">Tachar:</span>
                                <span class="text-gray-600 ml-3" :class="form.tooling_cost_stroked ? 'line-through' : ''">{{
                                    form.tooling_cost }} {{ form.tooling_currency }}</span>
                            </label>
                        </div>
                        <div>
                            <IconInput v-model="form.freight_cost" inputPlaceholder="Costo de flete *" inputType="text"
                                inputStep="0.01"> <el-tooltip content="Costo de flete" placement="top">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.freight_cost" />
                        </div>
                        <div class="flex items-center">
                            <el-tooltip content="Dias para primera producción *" placement="top">
                                <i
                                    class="fa-solid fa-info font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md"></i>
                            </el-tooltip>
                            <el-select v-model="form.first_production_days" placeholder="Dias para primera producción *">
                                <el-option v-for="(item, index) in firstProductionDaysList" :key="item" :label="item"
                                    :value="item" />
                            </el-select>
                            <InputError :message="form.errors.first_production_days" />
                        </div>
                        <div class="flex col-span-full">
                            <el-tooltip content="Notas" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                    ...
                                </span>
                            </el-tooltip>
                            <textarea v-model="form.notes" class="textarea" autocomplete="off"
                                placeholder="Notas"></textarea>
                            <InputError :message="form.errors.notes" />
                        </div>

                        <el-divider content-position="left" class="col-span-full">Productos</el-divider>

                        <!-- products -->
                        <InputError :message="form.errors.products" class="col-span-full" />
                        <ol v-if="form.products.length" class="rounded-lg bg-[#CCCCCC] px-5 py-3 col-span-full space-y-1">
                            <template v-for="(item, index) in form.products" :key="index">
                                <li class="flex justify-between items-center">
                                    <p class="text-sm">
                                        <span class="text-primary">{{ index + 1 }}.</span>
                                        {{ catalog_products.find(prd => prd.id === item.id)?.name }}
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

                        <div class="flex items-center mt-2">
                            <el-tooltip content="Producto de catálogo" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </el-tooltip>
                            <el-select v-model="product.id" clearable filterable placeholder="Busca el producto"
                                no-data-text="No hay productos registrados" no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in catalog_products" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                        </div>
                        <div class="flex items-center">
                            <el-tooltip content="¿Mostrar imagen en cotización?" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </el-tooltip>
                            <el-switch v-model="product.show_image" inline-prompt size="large"
                                style="--el-switch-on-color: #0355B5; --el-switch-off-color: #CCCCCC"
                                active-text="Mostrar imagen en cotización"
                                inactive-text="No mostrar imagen en cotización" />
                        </div>
                        <div>
                            <IconInput v-model="product.quantity" inputPlaceholder="Cantidad a cotizar *" inputType="number"
                                inputStep="0.01">
                                <el-tooltip content="Cantidad a cotizar" placement="top">
                                    #
                                </el-tooltip>
                            </IconInput>
                        </div>

                        <div>
                            <IconInput v-model="product.price" inputPlaceholder="Precio unitario *" inputType="number"
                                inputStep="0.01">
                                <el-tooltip content="Precio unitario" placement="top">
                                    <i class="fa-solid fa-dollar-sign"></i>
                                </el-tooltip>
                            </IconInput>
                        </div>

                        <div class="flex col-span-full">
                            <el-tooltip content="Notas del producto a cotizar" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                    ...
                                </span>
                            </el-tooltip>
                            <textarea v-model="product.notes" class="textarea" autocomplete="off"
                                placeholder="Notas"></textarea>
                            <InputError :message="form.errors.notes" />
                        </div>
                        <div>
                            <div>
                                <SecondaryButton @click="addProduct" type="button"
                                    :disabled="!product.id || !product.quantity || !product.price || form.processing">
                                    {{ editIndex !== null ? 'Actualizar producto' : 'Agregar producto a lista' }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                    <el-divider />
                    <!-- buttons -->
                    <div class="md:text-right">
                        <PrimaryButton :disabled="form.processing"> Crear </PrimaryButton>
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
                    <PrimaryButton @click="storeImportantNotes()" :disabled="!importantNotesToStore">Guardar notas
                    </PrimaryButton>
                    <CancelButton @click="showImportantNotesModal = false">Cancelar</CancelButton>
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
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import axios from "axios";

export default {
    data() {
        const form = useForm({
            receiver: null,
            department: null,
            tooling_cost: null,
            tooling_currency: null,
            tooling_cost_stroked: false,
            freight_cost: null,
            first_production_days: null,
            notes: null,
            currency: null,
            is_spanish_template: 1,
            is_customer: 1,
            company_branch_id: null,
            prospect_id: null,
            products: [],
        });
        return {
            form,
            importantNotes: null,
            showImportantNotesModal: false,
            importantNotesToStore: null,
            isEditImportantNotes: false,
            editIndex: null,
            product: {
                id: null,
                quantity: null,
                price: null,
                show_image: true,
                notes: null,
            },
            firstProductionDaysList: [
                'Inmediata',
                '1 a 2 semanas',
                '2 a 3 semanas',
                '3 a 4 semanas',
                '4 a 5 semanas',
                '5 a 6 semanas',
            ],
            currencies: [
                {
                    label: 'Peso mexicano',
                    value: '$MXN'
                },
                {
                    label: 'Dólar estadounidense',
                    value: '$USD'
                }
            ],
            toolingCurrencies: [
                {
                    label: 'No colocar moneda',
                    value: ''
                },
                {
                    label: 'Peso mexicano',
                    value: '$MXN'
                },
                {
                    label: 'Dólar EUA',
                    value: '$USD'
                }
            ],
            toolingCostStroked: false,
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        InputError,
        IconInput,
        CancelButton,
        DialogModal,
        Back,
        Link
    },
    props: {
        catalog_products: Array,
        company_branches: Array,
        opportunity: Object,
        prospects: Array,
    },
    methods: {
        handleSelectProspect() {
            const prospect = this.prospects.find(item => item.id === this.form.prospect_id);
            this.form.receiver = prospect.contact_name;
            this.form.department = prospect.contact_charge;
        },
        handleChangeModelId() {
            this.form.company_branch_id = null;
            this.form.prospect_id = null;
            this.form.receiver = null;
            this.form.department = null;
        },
        store() {
            this.form.post(route('quotes.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Cotización creada',
                        type: 'success'
                    });

                    this.form.reset();
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
            this.product.quantity = null;
            this.product.notes = null;
            this.product.price = null;
            this.product.show_image = true;
        },
    },
    mounted() {
        if (this.opportunity) {
            this.form.company_branch_id = parseInt(this.opportunity.company_branch_id);
            this.form.receiver = this.opportunity.contact;
            this.form.currency = '$MXN';
            this.getImportantNotes();
        }
    }
};
</script>
