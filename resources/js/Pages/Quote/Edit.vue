<template>
    <div>
        <AppLayout title="Editar cotización">
            <template #header>
                <div class="flex justify-between">
                    <Link :href="route('quotes.index')"
                        class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
                    <i class="fa-solid fa-chevron-left"></i>
                    </Link>
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Editar cotización</h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="edit">
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg px-9 py-5 shadow-md">
                    <div class="md:grid gap-6 mb-6 grid-cols-2">
                        <div class="col-span-2">
                            <el-radio-group v-model="form.is_spanish_template" size="small">
                                <el-radio-button :label="1">Plantilla en español</el-radio-button>
                                <el-radio-button :label="0">Plantilla en inglés</el-radio-button>
                            </el-radio-group>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <el-tooltip
                                    content="La moneda que se elija se usará para productos y costos de flete y herramental"
                                    placement="top">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
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
                            <div class="flex items-center">
                                <el-tooltip
                                    content="Para poder cotizar, los clientes (sucursales) deben de estar registrados"
                                    placement="top">
                                    <span
                                        class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                </el-tooltip>
                                <el-select v-model="form.company_branch_id" clearable filterable
                                    placeholder="Busca el cliente" no-data-text="No hay clientes registrados"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                        :value="item.id" />
                                </el-select>
                            </div>
                            <InputError :message="form.errors.company_branch_id" />
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
                                inputType="number">
                                <el-tooltip content="Costo de herramental" placement="top">
                                    <i class="fa-solid fa-hammer"></i>
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.tooling_cost" />
                        </div>
                        <div>
                            <IconInput v-model="form.freight_cost" inputPlaceholder="Costo de flete *" inputType="number">
                                <el-tooltip content="Costo de flete" placement="top">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </el-tooltip>
                            </IconInput>
                            <InputError :message="form.errors.freight_cost" />
                        </div>
                        <div>
                            <IconInput v-model="form.first_production_days"
                                inputPlaceholder="Dias para primera producción *" inputType="text">
                                <el-tooltip content="Dias para primera producción" placement="top">
                                    <i class="fa-solid fa-info"></i>
                                </el-tooltip>
                            </IconInput>
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
                                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
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

                        <div class="flex items-center">
                            <el-tooltip content="Producto de catálogo" placement="top">
                                <span
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
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
                                    class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </el-tooltip>
                            <el-switch v-model="product.show_image" inline-prompt size="large"
                                style="--el-switch-on-color: #0355B5; --el-switch-off-color: #CCCCCC"
                                active-text="Mostrar imagen en cotización"
                                inactive-text="No mostrar imagen en cotización" />
                        </div>
                        <div>
                            <IconInput v-model="product.quantity" inputPlaceholder="Cantidad a cotizar *"
                                inputType="number">
                                <el-tooltip content="Cantidad a cotizar" placement="top">
                                    #
                                </el-tooltip>
                            </IconInput>
                        </div>

                        <div>
                            <IconInput v-model="product.price" inputPlaceholder="Precio unitario *" inputType="number"
                                inputStep="0.1">
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
                        <PrimaryButton :disabled="form.processing"> Actualizar </PrimaryButton>
                    </div>
                </div>
            </form>
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

export default {
    data() {
        const form = useForm({
            receiver: this.quote.receiver,
            department: this.quote.department,
            tooling_cost: this.quote.tooling_cost,
            freight_cost: this.quote.freight_cost,
            first_production_days: this.quote.first_production_days,
            notes: this.quote.notes,
            currency: this.quote.currency,
            is_spanish_template: this.quote.is_spanish_template,
            company_branch_id: this.quote.company_branch_id,
            products: [],
        });

        return {
            form,
            editIndex: null,
            product: {
                catalog_product_id: null,
                quantity: null,
                price: null,
                show_image: true,
                notes: null,
            },
            currencies: [
                {
                    label: 'Peso mexicano',
                    value: '$MXN'
                },
                {
                    label: 'Dólar estadounidense',
                    value: '$USD'
                }
            ]
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        Link,
        InputError,
        IconInput,
    },
    props: {
        catalog_products: Array,
        company_branches: Array,
        quote: Object,
    },
    methods: {
        edit() {
            this.form.put(route('quotes.update', this.quote), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Cotización actualizada',
                        type: 'success'
                    });
                }
            });
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
            this.product.catalog_product_id = null;
            this.product.quantity = null;
            this.product.notes = null;
            this.product.price = null;
            this.product.show_image = true;
        }
    },
    mounted() {
        this.quote.catalog_products.forEach(element => {
            const product = {
                catalog_product_id: element.pivot.catalog_product_id,
                quantity: element.pivot.quantity,
                notes: element.pivot.notes,
                price: element.pivot.price,
                show_image: Boolean(element.pivot.show_image),
            }

            this.form.products.push(product);
        });
    }
};
</script>
