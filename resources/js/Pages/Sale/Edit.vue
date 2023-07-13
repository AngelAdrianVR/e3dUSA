<template>
    <div>
        <AppLayout title="Editar órden de venta">
            <template #header>
                <div class="flex justify-between">
                    <Link :href="route('sales.index')"
                        class="hover:bg-gray-200/50 rounded-full w-10 h-10 flex justify-center items-center">
                    <i class="fa-solid fa-chevron-left"></i>
                    </Link>
                    <div class="flex items-center space-x-2 text-white">
                        <h2 class="font-semibold text-xl leading-tight">Editar órden de venta {{ sale.id }}</h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="edit">
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg px-9 py-5 shadow-md">
                    <div class="flex items-center">
                        <el-tooltip content="Cliente: Seleccione para poder habilitar sus productos" placement="top">
                            <span
                                class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC] border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </el-tooltip>
                        <el-select v-model="form.company_branch_id" class="mt-2" placeholder="Selecciona un cliente">
                            <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                :value="item.id" />
                        </el-select>
                    </div>
                    <div v-if="form.company_branch_id" class="flex items-center mt-3">
                        <el-tooltip content="Contacto" placement="top">
                            <span
                                class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC] border border-r-8 border-transparent rounded-l-md h-9 w-12">
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
                                <i class="fa-solid fa-truck-fast"></i>
                            </IconInput>
                            <InputError :message="form.errors.shipping_company" />
                        </div>
                        <div>
                            <IconInput v-model="form.freight_cost" inputPlaceholder="Costo logística" inputType="text">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </IconInput>
                            <InputError :message="form.errors.freight_cost" />
                        </div>
                        <div>
                            <IconInput v-model="form.tracking_guide" inputPlaceholder="Guía" inputType="text">
                                <i class="fa-solid fa-magnifying-glass-location"></i>
                            </IconInput>
                            <InputError :message="form.errors.tracking_guide" />
                        </div>
                    </div>

                    <el-divider content-position="left">Datos de la órden</el-divider>
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <IconInput v-model="form.order_via" inputPlaceholder="Medio de petición">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </IconInput>
                            <InputError :message="form.errors.order_via" />
                        </div>
                        <div>
                            <IconInput v-model="form.invoice" inputPlaceholder="Factura">
                                <i class="fa-solid fa-money-check-dollar"></i>
                            </IconInput>
                            <InputError :message="form.errors.invoice" />
                        </div>
                        <div class="md:col-span-3">
                            <IconInput v-model="form.oce_name" inputPlaceholder="Nombre/folio OCE">
                                <i class="fa-solid fa-file-invoice"></i>
                            </IconInput>
                            <InputError :message="form.errors.oce_name" />
                        </div>
                        <div>
                            <label for="">Archivo OCE</label>
                            <input type="file" name="" id="">
                        </div>
                    </div>
                    <div class="flex">
                        <span
                            class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                            ...
                        </span>
                        <textarea v-model="form.notes" class="textarea" autocomplete="off" placeholder="Notas"></textarea>
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
                                        form.company_branch_id)?.company.catalog_products.find(prd => prd.id ===
                                            item.catalog_product_company_id)?.name
                                    }}
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

                    <div v-if="form.company_branch_id"
                        class="md:grid gap-6 mb-6 grid-cols-3 rounded-lg border-2 border-[#b8b7b7] px-5 py-3 col-span-full space-y-1 my-7">
                        <div class="flex items-center col-span-2">
                            <el-tooltip content="Producto: Seleccione entre los productos registrados para este cliente"
                                placement="top">
                                <span
                                    class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC] border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </el-tooltip>
                            <el-select v-model="product.catalog_product_company_id"
                                no-data-text="No hay productos registrados a este cliente" clearable
                                placeholder="Selecciona un producto *">
                                <el-option
                                    v-for="item in company_branches.find(cb => cb.id == form.company_branch_id)?.company.catalog_products"
                                    :key="item.id" :label="item.name" :value="item.id" />
                            </el-select>
                        </div>
                        <div>
                            <IconInput v-model="product.quantity" inputPlaceholder="Cantidad *" inputType="number">
                                #
                            </IconInput>
                            <!-- <InputError :message="form.errors.fiscal_address" /> -->
                        </div>
                        <div class="flex col-span-full">
                            <span
                                class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="product.notes" class="textarea" autocomplete="off"
                                placeholder="Notas"></textarea>
                            <!-- <InputError :message="form.errors.notes" /> -->
                        </div>
                        <div class="col-span-full" @click="addProduct">
                            <SecondaryButton
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
            company_branch_id: this.sale.company_branch_id,
            contact_id: this.sale.contact_id,
            shipping_company: this.sale.shipping_company,
            freight_cost: this.sale.freight_cost,
            invoice: this.sale.invoice,
            oce_name: this.sale.oce_name,
            order_via: this.sale.order_via,
            tracking_guide: this.sale.tracking_guide,
            notes: this.sale.notes,
            products: [],
        });

        return {
            form,
            product: {
                catalog_product_company_id: null,
                quantity: null,
                notes: null,
            },
            editIndex: null,
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
        company_branches: Array,
        catalog_products_company_sale: Array,
        sale: Array,
    },
    methods: {
        edit() {
            this.form.put(route('sales.update', this.sale), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'órden de venta actualizada',
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
            this.product.catalog_product_company_id = null;
            this.product.quantity = null;
            this.product.notes = null;
        }
    },
    mounted() {
        this.catalog_products_company_sale.forEach(element => {
            const product = {
                catalog_product_company_id: element.catalog_product_company_id,
                quantity: element.quantity,
                notes: element.notes,
            }

            this.form.products.push(product);
        });
    }
};
</script>