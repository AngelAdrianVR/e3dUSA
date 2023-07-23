<template>
    <div>
        <AppLayoutNoHeader title="Órdenes de producción">
            <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
                <div class="flex justify-between">
                    <label class="text-lg">Órdenes de producción</label>
                    <Link :href="route('productions.index')"
                        class="cursor-pointer w-7 h-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                    </Link>
                </div>
                <div class="flex justify-between">
                    <div class="w-1/3">
                        <el-select v-model="selectedSale" clearable filterable placeholder="Buscar orden de producción"
                            no-data-text="No hay órdenes registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in sales.data" :key="item.id"
                                :label="item.folio.replace('OV', 'OP') + ' | ' + item.company_branch.name"
                                :value="item.id" />
                        </el-select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <el-tooltip v-if="$page.props.auth.user.permissions.includes('Ordenes de produccion todas')"
                            content="Editar" placement="top">
                            <Link :href="route('productions.edit', selectedSale)">
                            <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]"><i class="fa-solid fa-pen text-sm"></i></button>
                            </Link>
                        </el-tooltip>
                    </div>
                </div>
            </div>
            <p class="text-center font-bold text-lg mb-4">
                {{ currentSale?.folio.replace('OV', 'OP') + ' | ' + currentSale?.company_branch.name }}
            </p>
            <!-- ------------- tabs section starts ------------- -->
            <div class="border-y-2 border-[#cccccc] flex justify-between items-center py-2">
                <div class="flex">
                    <p @click="tabs = 1" :class="tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
                        "
                        class="h-10 p-2 cursor-pointer md:ml-5 transition duration-300 ease-in-out text-sm md:text-base">
                        Datos de la órden
                    </p>
                    <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
                    <p @click="tabs = 2" :class="tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
                        "
                        class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
                        Productos
                    </p>
                </div>
            </div>
            <!-- ------------- tabs section ends ------------- -->

            <!-- ------------- Informacion general Starts 1 ------------- -->
            <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
                <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
                    <h2 class="text-secondary col-span-full">Logística</h2>
                    <span class="text-gray-500">ID</span>
                    <span>{{ currentSale?.folio.replace('OV', 'OP') }}</span>
                    <span class="text-gray-500">Paquetería</span>
                    <span>{{ currentSale?.shipping_company }}</span>
                    <span class="text-gray-500">Guía</span>
                    <span>{{ currentSale?.tracking_guide ?? '--' }}</span>
                    <span class="text-gray-500">Costo logística</span>
                    <span>${{ currentSale?.freight_cost }}</span>

                    <h2 class="text-secondary col-span-full mt-6">Datos de la orden</h2>
                    <span class="text-gray-500 my-2">Solicitada por</span>
                    <span>{{ currentSale?.productions[0].user.name }}</span>
                    <span class="text-gray-500 my-2">Solicitada el</span>
                    <span>{{ currentSale?.productions[0].created_at }}</span>
                    <span class="text-gray-500 my-2">Medio de petición</span>
                    <span>{{ currentSale?.order_via }}</span>
                    <span class="text-gray-500 my-2">Factura</span>
                    <span>{{ currentSale?.invoice ?? '--' }}</span>
                    <span class="text-gray-500 my-2">OCE</span>
                    <a :href="currentSale?.media[0].original_url" target="_blank"
                        class="text-secondary cursor-pointer hover:underline">
                        {{ currentSale?.oce_name ?? currentSale?.media[0].file_name }}
                    </a>
                    <span class="text-gray-500 my-2">Notas</span>
                    <span>{{ currentSale?.notes ?? '--' }}</span>
                    <span class="text-gray-500 my-2">Estatus</span>
                    <span class="rounded-full border text-center" :class="currentSale?.status['text-color'] +
                        ' ' +
                        currentSale?.status['border-color']
                        ">{{ currentSale?.status["label"] }}</span>
                </div>

                <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
                    <h2 class="text-secondary col-span-full">Datos del cliente</h2>
                    <span class="text-gray-500">Razon social</span>
                    <span>{{ currentSale?.company_branch.company.business_name }}</span>
                    <span class="text-gray-500 my-2">RFC</span>
                    <span>{{ currentSale?.company_branch.company.rfc }}</span>
                    <span class="text-gray-500 my-2">Método de pago</span>
                    <span>{{ currentSale?.company_branch.sat_method }}</span>
                    <span class="text-gray-500 my-2">Medio de pago</span>
                    <span>{{ currentSale?.company_branch.sat_way }}</span>
                    <span class="text-gray-500 my-2">Uso de factura</span>
                    <span>{{ currentSale?.company_branch.sat_type }}</span>
                    <span class="text-gray-500 my-2">Cliente / sucursal</span>
                    <span>{{ currentSale?.company_branch.name }}</span>
                    <span class="text-gray-500 my-2">Dirección de entrega</span>
                    <span>{{ currentSale?.company_branch.address }}. C.P. {{ currentSale?.company_branch.post_code }}</span>

                    <h2 class="text-secondary mt-6 col-span-full">Contacto</h2>
                    <span class="text-gray-500 my-2">Nombre</span>
                    <span>{{ currentSale?.contact.name }}</span>
                    <span class="text-gray-500 my-2">Correo electronico</span>
                    <span>{{ currentSale?.contact.email }}</span>
                    <span class="text-gray-500 my-2">Teléfono</span>
                    <span>{{ currentSale?.contact.phone }}</span>

                </div>
            </div>
            <!-- ------------- Informacion general ends 1 ------------- -->

            <!-- -------------tab 2 productos starts ------------- -->

            <div v-if="tabs == 2" class="p-7">
                <div class="mb-5">
                    <PrimaryButton
                        @click="$inertia.get(route('productions.print', JSON.stringify(orderedProductsSelected)))"
                        class="rounded-[10px]" :disabled="!orderedProductsSelected.length">
                        Imprimir
                    </PrimaryButton>
                </div>

                <div class="grid grid-cols-4 gap-7">
                    <ProductSaleCard @selected="handleSelections(index, $event)"
                        v-for="(productSale, index) in currentSale?.catalogProductCompanySales" :key="productSale.id"
                        :catalog_product_company_sale="productSale" />
                </div>
            </div>

            <!-- ------------- tab 2 productos ends ------------ -->

            <!-- -------------- Modal starts----------------------- -->

            <!-- --------------------------- Modal ends ------------------------------------ -->
        </AppLayoutNoHeader>
    </div>
</template>
  
<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductSaleCard from "@/Components/MyComponents/ProductSaleCard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
// import Modal from "@/Components/Modal.vue";
import { Link, useForm } from "@inertiajs/vue3";

export default {
    data() {
        const form = useForm({
            expected_end_at: null,
        });

        return {
            form,
            selectedSale: "",
            currentSale: null,
            // startOrderModal: false,
            // helpDialog: false,
            tabs: 1,

            // selections
            orderedProductsSelected: [],
        };
    },
    props: {
        sale: Object,
        sales: Object,
    },
    components: {
        AppLayoutNoHeader,
        Dropdown,
        DropdownLink,
        Link,
        CancelButton,
        PrimaryButton,
        // Modal,
        CancelButton,
        InputError,
        ProductSaleCard
    },
    methods: {
        handleSelections(index, isSelected) {
            if (isSelected) {
                this.orderedProductsSelected.push(this.currentSale.catalogProductCompanySales[index].id);
            }
            else {
                const opsIndex = this.orderedProductsSelected.findIndex(item => item == this.currentSale.catalogProductCompanySales[index].id)
                this.orderedProductsSelected.splice(opsIndex, 1);
            }
        },
    },

    watch: {
        selectedSale(newVal) {
            this.currentSale = this.sales.data.find((item) => item.id == newVal);
        },
    },

    mounted() {
        this.selectedSale = this.sale.data.id;
    },
};
</script>