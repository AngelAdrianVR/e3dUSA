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
                        <el-select @change="$inertia.get(route('productions.show', selectedSale))" v-model="selectedSale"
                            clearable filterable placeholder="Buscar orden de producción"
                            no-data-text="No hay órdenes registradas" no-match-text="No se encontraron coincidencias">
                            <el-option v-for="item in sales" :key="item.id"
                                :label="item.folio?.replace('OV', 'OP') + ' | ' + item.company_name" :value="item.id" />
                        </el-select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <el-tooltip v-if="$page.props.auth.user?.permissions.includes('Ordenes de produccion todas')"
                            content="Editar" placement="top">
                            <Link :href="route('productions.edit', selectedSale)">
                            <button class="w-9 h-9 rounded-lg bg-[#D9D9D9]"><i class="fa-solid fa-pen text-sm"></i></button>
                            </Link>
                        </el-tooltip>
                    </div>
                </div>
            </div>
            <h1 class="font-bold text-lg mb-4 flex items-center justify-center space-x-3">
                <el-tooltip v-if="sale.data.is_sale_production" content="Orden de venta" placement="top">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </el-tooltip>
                <el-tooltip v-else content="Orden de stock" placement="top">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-rose-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                </el-tooltip>
                <span> {{ sale.data?.folio.replace('OV', 'OP') + ' | ' + sale.data?.company_branch?.name }}</span>
            </h1>
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
                    <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
                    <p @click="tabs = 3" :class="tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
                        "
                        class="md:ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out text-sm md:text-base">
                        Hojas viajeras
                    </p>
                </div>
            </div>

            <!-- ------------- Informacion general Starts 1 ------------- -->
            <div v-if="tabs == 1" class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
                <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
                    <h2 class="text-secondary col-span-full">Logística</h2>
                    <span class="text-gray-500 my-1">Paquetería</span>
                    <span>{{ sale.data?.shipping_company }}</span>
                    <span class="text-gray-500 my-1">Guía</span>
                    <span>{{ sale.data?.tracking_guide ?? '--' }}</span>
                    <span class="text-gray-500 my-1">Costo logística</span>
                    <span>$ {{ sale.data?.freight_cost }}</span>
                    <span v-if="sale.data?.promise_date" class="text-gray-500 my-1">Fecha de embarque esperada</span>
                    <span v-if="sale.data?.promise_date" class="text-red-600 bg-red-200 px-2 py-1">
                        {{ sale.data.promise_date }}
                    </span>
                    <div v-if="sale.data.partialities" class="col-span-full">
                        <article v-for="(item, index) in sale.data.partialities" :key="index" class="grid grid-cols-2">
                            <span class="col-span-full font-bold my-2">Parcialidad {{ (index + 2) }}</span>
                            <span class="text-gray-500">Paquetería</span>
                            <span>{{ item.shipping_company }}</span>
                            <span class="text-gray-500 my-1">Guía</span>
                            <span>{{ item.traking_guide }}</span>
                            <span class="text-gray-500 my-1">Costo de envío</span>
                            <span>$ {{ item.freight_cost }}</span>
                            <span v-if="item.promise_date" class="text-gray-500 my-1">Fecha de embarque esperada</span>
                            <span v-if="item.promise_date" class="text-red-600 bg-red-200 px-2 py-1">{{
                                dateFormat(item.promise_date) }}</span>
                        </article>
                    </div>

                    <h2 class="text-secondary col-span-full mt-6">Datos de la orden</h2>
                    <span class="text-gray-500">ID</span>
                    <span>{{ sale.data?.folio.replace('OV', 'OP') }}</span>
                    <span class="text-gray-500 my-2">Vendedor</span>
                    <span>{{ sale.data?.user?.name }}</span>
                    <span class="text-gray-500 my-2">Creador de orden de producción</span>
                    <span>{{ sale.data?.productions[0].user?.name }}</span>
                    <span class="text-gray-500 my-2">Solicitada el</span>
                    <span>{{ getDateFormtted(sale.data?.productions[0].created_at) }}</span>
                    <span class="text-gray-500 my-2">Medio de petición</span>
                    <span>{{ sale.data?.order_via }}</span>
                    <span class="text-gray-500 my-2">Factura</span>
                    <span>{{ sale.data?.invoice ?? '--' }}</span>
                    <span class="text-gray-500 my-2">OCE</span>
                    <a :href="sale.data?.media[0]?.original_url" target="_blank"
                        class="text-secondary cursor-pointer hover:underline">
                        {{ sale.data?.oce_name ?? sale.data?.media[0]?.file_name }}
                    </a>
                    <span class="text-gray-500 my-2">Notas</span>
                    <span>{{ sale.data?.notes ?? '--' }}</span>
                    <span class="text-gray-500 my-2">Estatus</span>
                    <span class="rounded-full border text-center" :class="sale.data?.status['text-color'] +
                        ' ' +
                        sale.data?.status['border-color']
                        ">{{ sale.data?.status["label"] }}</span>
                </div>

                <div class="grid grid-cols-2 text-left p-4 md:ml-10 items-center">
                    <h2 class="text-secondary col-span-full">Datos del cliente</h2>
                    <span class="text-gray-500">Razon social</span>
                    <span>{{ sale.data?.company_branch.company.business_name }}</span>
                    <span class="text-gray-500 my-2">RFC</span>
                    <span>{{ sale.data?.company_branch.company.rfc }}</span>
                    <span class="text-gray-500 my-2">Método de pago</span>
                    <span>{{ sale.data?.company_branch.sat_method }}</span>
                    <span class="text-gray-500 my-2">Medio de pago</span>
                    <span>{{ sale.data?.company_branch.sat_way }}</span>
                    <span class="text-gray-500 my-2">Uso de factura</span>
                    <span>{{ sale.data?.company_branch.sat_type }}</span>
                    <span class="text-gray-500 my-2">Cliente / sucursal</span>
                    <span>{{ sale.data?.company_branch.name }}</span>
                    <span class="text-gray-500 my-2">Dirección de entrega</span>
                    <span>{{ sale.data?.company_branch.address }}. C.P. {{ sale.data?.company_branch.post_code }}</span>

                    <h2 class="text-secondary mt-6 col-span-full">Contacto</h2>
                    <span class="text-gray-500 my-2">Nombre</span>
                    <span>{{ sale.data?.contact?.name }}</span>
                    <span class="text-gray-500 my-2">Correo electronico</span>
                    <span>{{ sale.data?.contact?.email }}</span>
                    <span class="text-gray-500 my-2">Teléfono</span>
                    <span>{{ sale.data?.contact?.phone }}</span>

                </div>
            </div>
            <!-- -------------tab 2 productos starts ------------- -->
            <div v-if="tabs == 2" class="p-7">
                <div class="mb-5">
                    <el-dropdown @click="$inertia.get(route('productions.print', JSON.stringify(orderedProductsSelected)))" split-button type="primary" 
                                :disabled="!orderedProductsSelected.length">
                    Imprimir
                    <template #dropdown>
                        <el-dropdown-menu>
                        <el-dropdown-item @click="showPackageLabelForm = true">Generador de etiqueta para envío</el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                    </el-dropdown>
                    <!-- <PrimaryButton
                        @click="$inertia.get(route('productions.print', JSON.stringify(orderedProductsSelected)))"
                        class="rounded-[10px]" :disabled="!orderedProductsSelected.length">
                        Imprimir
                    </PrimaryButton> -->
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-7">
                    <ProductSaleCard @selected="handleSelections(index, $event)"
                        v-for="(productSale, index) in sale.data.catalogProductCompanySales" :key="productSale.id"
                        :catalog_product_company_sale="productSale" :isHighPriority="sale.data.is_high_priority"
                        :qualities="qualities" />
                </div>
            </div>
            <!-- -------------tab 3 hojas viajeras starts ------------- -->
            <div v-if="tabs == 3" class="p-7">
                <Traveler :sale="sale.data" />
            </div>

            <!-- ----------------- etiqueta de envío modal ----------- -->
            <Modal :show="showPackageLabelForm"
                @close="showPackageLabelForm = false">
                <form @submit.stop="createBoxLabel" class="p-5 grid grid-cols-2 gap-x-3">
                    <h1 class="col-span-full font-bold mb-1">Generar etiqueta</h1>
                    <p class="text-xs col-span-full mb-3">Este sólo es un generador de etiquetas, por lo tanto no se guardan en el sistema.</p>
    
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
                        <input v-model="labelForm.part_number" type="text" class="input" placeholder="Agregue el número de parte" />
                    </div>
    
                    <!-- ----- Número de cajas ----- -->
                    <section v-for="(box, index) in labelForm.boxes" :key="index" class="col-span-full flex items-center space-x-3 mt-2">
                        <div class="w-1/4">
                            <InputLabel value="Caja" class="ml-2" />
                            <input v-model="labelForm.boxes[index].name" disabled type="text" class="input" placeholder="Nombre de caja" />
                        </div>
                        <div class="w-1/4">
                            <InputLabel value="Producto" class="ml-2" />
                            <input v-model="labelForm.boxes[index].product_name" type="text" class="input" placeholder="Escribe el nombre del producto" />
                        </div>
                        <div class="w-1/4">
                            <InputLabel value="Piezas" class="ml-2" />
                            <input v-model="labelForm.boxes[index].quantity" type="text" class="input" placeholder="Escribe la cantidad de piezas"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/\D/g, '')" />
                        </div>
                        <i @click="deleteBox(index)" v-if="index === boxIndex - 1 && labelForm.boxes.length >1" class="fa-regular fa-trash-can text-primary cursor-pointer p-1 mt-5"></i>
                    </section>
    
                    <p @click="addBox" class="text-primary mt-2 cursor-pointer ml-4">+ Agregar caja</p>
    
                    <div class="block ml-4 mt-4 col-span-full">
                        <label class="flex items-center">
                        <Checkbox v-model:checked="labelForm.is_fragil" class="bg-transparent"/>
                        <span class="ml-2 text-sm">El producto es frágil</span>
                        </label>
                    </div>
    
                    <div class="col-span-full flex justify-end space-x-3 items-start mt-4">
                        <CancelButton @click="showPackageLabelForm = false">Cancelar</CancelButton>
                        <PrimaryButton :disabled="!labelForm.boxes[0].product_name || !labelForm.boxes[0].quantity">Crear etiqueta</PrimaryButton>
                    </div>
                </form>
            </Modal>
        </AppLayoutNoHeader>
    </div>
</template>
  
<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ProductSaleCard from "@/Components/MyComponents/ProductSaleCard.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import Traveler from "./Tabs/Traveler.vue";
import { Link, useForm } from "@inertiajs/vue3";
import moment from "moment";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        const form = useForm({
            expected_end_at: null,
        });

        const labelForm = useForm({
            client: this.sale.data.company_branch.company.business_name,
            company_branch: this.sale.data.company_branch.name,
            contact: this.sale.data.contact?.name,
            company_branch_address: this.sale.data.company_branch.address,
            post_code: this.sale.data.company_branch.post_code,
            contact_phone: this.sale.data.contact?.phone,
            ov: this.sale.data.folio,
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
            form,
            labelForm,
            boxIndex: 1, //cuenta el index de cada caja del arreglo en labelForm
            orderedProductsSelected: [],
            showPackageLabelForm: false, //muestra formulario para imprir etiqueta de envío
            selectedSale: "",
            tabs: 1,
        };
    },
    props: {
        sale: Object,
        sales: Object,
        qualities: Object,
    },
    components: {
        AppLayoutNoHeader,
        ProductSaleCard,
        PrimaryButton,
        DropdownLink,
        InputLabel,
        CancelButton,
        CancelButton,
        InputError,
        Dropdown,
        Checkbox,
        Traveler,
        Modal,
        Link
    },
    methods: {
        createBoxLabel() {
            this.$inertia.post(route('productions.generate-box-label'), { data: this.labelForm });
        },
        addBox() {
            this.labelForm.boxes.push({ name: 'Caja ' + (this.boxIndex + 1), quantity: null });
            this.boxIndex ++;
        },
        deleteBox(index) {
            this.labelForm.boxes.splice(index, 1);
            this.boxIndex --;
        },
        dateFormat(date) {
            const formattedDate = format(new Date(date), 'dd MMMM yyyy', { locale: es });

            return formattedDate;
        },
        handleSelections(index, isSelected) {
            if (isSelected) {
                this.orderedProductsSelected.push(this.sale.data.catalogProductCompanySales[index].id);
            }
            else {
                const opsIndex = this.orderedProductsSelected.findIndex(item => item == this.sale.data.catalogProductCompanySales[index].id)
                this.orderedProductsSelected.splice(opsIndex, 1);
            }
        },
        getDateFormtted(dateTime) {
            if (!dateTime) return null;
            return moment(dateTime).format("DD MMM YYYY, hh:mmA");
        },
    },

    // watch: {
    //     selectedSale(newVal) {
    //         this.currentSale = this.sales.data.find((item) => item.id == newVal);
    //     },
    // },

    mounted() {
        this.selectedSale = this.sale.data.id;
    },
};
</script>