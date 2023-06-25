<template>
    <div>
        <AppLayout title="Crear órden de venta">
            <template #header>
                <div class="flex justify-between">
                    <Link :href="route('sales.index')"
                        class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center">
                    <i class="fa-solid fa-chevron-left"></i>
                    </Link>
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Crear órden de venta</h2>
                    </div>
                </div>
            </template>

            <!-- Form -->
            <form @submit.prevent="store">
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] rounded-lg p-9 shadow-md">
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
                            <IconInput v-model="form.currency" inputPlaceholder="Moneda" inputType="text">
                                <i class="fa-solid fa-coins"></i>
                            </IconInput>
                            <InputError :message="form.errors.currency" />
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
                        <textarea v-model="form.notes" class="textarea" autocomplete="off" placeholder="Notas"
                            required></textarea>
                        <InputError :message="form.errors.notes" />
                    </div>
                    <!-- products -->
                    <el-divider v-if="form.company_branch_id" content-position="left">Agregar productos</el-divider>
                    <div v-if="form.company_branch_id"
                        class="md:grid gap-6 mb-6 grid-cols-4 rounded-lg border-2 border-[#b8b7b7] px-5 py-3 col-span-full space-y-1 my-7">
                        <div class="col-span-3 flex items-center">
                            <el-tooltip content="Producto: Seleccione entre los productos registrados para este cliente" placement="top">
                                <span
                                    class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC] border border-r-8 border-transparent rounded-l-md h-9 w-12">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </el-tooltip>
                            <el-select v-model="product.catalog_product_company_id"
                                placeholder="Selecciona un producto *">
                                <el-option
                                    v-for="item in company_branches.find(cb => cb.id == form.company_branch_id)?.company.catalog_products"
                                    :key="item.id" :label="item.name" :value="item.id" />
                            </el-select>
                        </div>
                        <div class="col-span-1">
                            <IconInput v-model="product.quantity" inputPlaceholder="Cantidad *" inputType="number">
                            </IconInput>
                            <!-- <InputError :message="form.errors.fiscal_address" /> -->
                        </div>
                        <div class="flex col-span-4">
                            <span
                                class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC]border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                                ...
                            </span>
                            <textarea v-model="product.notes" class="textarea" autocomplete="off"
                                placeholder="Notas"></textarea>
                            <!-- <InputError :message="form.errors.notes" /> -->
                        </div>
                        <div class="col-span-full">
                            <SecondaryButton class=""
                                :disabled="form.processing || !product.catalog_product_company_id || !product.quantity">
                                Agregar
                                Producto </SecondaryButton>
                        </div>
                    </div>
                    <div class="mt-7 mx-3 md:text-right">
                        <PrimaryButton :disabled="form.processing"> Crear órden de venta </PrimaryButton>
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
            company_branch_id: null,
            contact_id: null,
            shipping_company: null,
            freight_cost: null,
            invoice: null,
            status: null,
            oce_name: null,
            order_via: null,
            tracking_guide: null,
            authorized_user_name: null,
            notes: null,
            products: [],

        });

        return {
            form,
            product: {
                catalog_product_company_id: null,
                quantity: null,
                notes: null,
            }
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        PrimaryButton,
        Link,
        InputError,
        IconInput,
    },
    props: {
        company_branches: Array
    },
    methods: {
        store() {
            this.form.post(route('sales.store'));
        }
    },
};
</script>
