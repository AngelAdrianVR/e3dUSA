<template>
    <div>
        <AppLayout title="Crear órden de venta">
            <template #header>
                <div class="flex justify-between">
                    <Back />
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Crear órden de venta</h2>
                    </div>
                </div>
            </template>
            
            <form @submit.prevent="handleStoreSale" class="relative overflow-x-hidden dark:text-white h-screen">
                <!-- company branch important notes -->
                <div class="absolute top-5 -right-1">
                    <div v-if="importantNotes" class="text-sm border border-[#9A9A9A] rounded-[5px] py-2 px-3 w-[550px]">
                        <div class="absolute bg-primary top-1 -left-3 h-2 w-10 transform -rotate-45"></div>
                        <div class="absolute bg-primary top-1 -right-3 h-2 w-10 transform rotate-45"></div>
                        <h3 class="flex items-center justify-center mb-2">
                            Este cliente tiene nota
                            <i class="fa-regular fa-note-sticky ml-3"></i>
                        </h3>
                        <p style="white-space: pre-line;" class="px-1">{{ importantNotes }}</p>
                        <div class="mt-3">
                            <button @click="editImportantNotes()" type="button"
                                class="text-[#9A9A9A] pr-2">Editar</button>
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

                <!-- company branch productos -->
                <div class="absolute top-5 -right-1 mt-36">
                    <div v-if="catalogProductsCompanyBranchSelected?.length"
                        class="text-sm border border-[#9A9A9A] rounded-[5px] py-2 px-3 w-[550px] relative">
                        <h3 class="flex items-center justify-center mb-2 text-base font-bold">
                            Productos de este cliente
                        </h3>
                        
                        <!-- Boton para crear nuevo producto al cliente -->
                        <button @click="openCompanyEdit" type="button" title="Agregar nuevo producto al cliente" class="absolute top-3 right-7 border border-primary rounded-full size-5 flex items-center justify-center text-primary">
                            <i class="fa-solid fa-plus"></i>
                        </button>

                        <!-- Refrescar productos del cliente -->
                        <button @click="fetchCatalogProductsCompanyBanch" type="button" title="Refrescar productos" class="absolute top-3 left-7 border border-primary rounded-full size-5 flex items-center justify-center text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>

                        <div v-if="loadingCompanyBranchProducts" class="flex items-center justify-center mt-10">
                            <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
                        </div>
                        <section v-else class="max-h-[500px] overflow-auto">
                            <div class="rounded-md border border-gray-400 p-2 my-2 relative"
                                v-for="catalog_product in catalogProductsCompanyBranchSelected" :key="catalog_product">
                                <p :title="catalog_product.name" class="truncate text-center">{{ catalog_product.name }}
                                </p>

                                <body class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-300 mt-2">Precio anterior: <span
                                                class="font-bold text-black dark:text-white ml-2">{{
                                                    catalog_product.pivot.old_price ?? '-'
                                                }} {{ catalog_product.pivot.old_currency ?? '' }}</span></p>
                                        <p class="text-gray-500 dark:text-gray-300">Fecha de cambio: <span
                                                class="font-bold text-black dark:text-white ml-2">{{
                                                    formatDate(catalog_product.pivot.old_date) ?? '-' }}</span></p>
                                        <p class="text-gray-500 dark:text-gray-300 mt-2">Precio actual: <span
                                                class="font-bold text-black dark:text-white ml-2">
                                                {{ catalog_product.pivot.new_price }}
                                                {{ catalog_product.pivot.new_currency ?? '' }}
                                                <span v-if="priceChangePercentage(catalog_product.pivot) !== null"
                                                    :class="priceChangeClass(catalog_product.pivot)">
                                                    <template v-if="priceChangePercentage(catalog_product.pivot) !== 0">
                                                        (<i :class="priceChangeIcon(catalog_product.pivot)" class="text-[10px]"></i>{{
                                                            priceChangePercentage(catalog_product.pivot) }}%)
                                                    </template>
                                                </span>
                                            </span>
                                        </p>
                                        <p class="text-gray-500 dark:text-gray-300">Fecha de cambio: <span
                                                class="font-bold text-black dark:text-white ml-2">{{
                                                    formatDate(catalog_product.pivot.new_date) }}</span></p>
                                    </div>

                                    <figure
                                        class="rounded-md m-2 h-32 cursor-zoom-in bg-[#d9d9d9] dark:bg-[#202020] flex items-center justify-center">
                                        <img v-if="catalog_product.media?.length" class="object-contain h-full" @click="handlePictureCardPreview(catalog_product.media[0])" :src="catalog_product.media[0].original_url" alt="">
                                        <i v-else class="fa-regular fa-image text-4xl text-gray-400"></i>
                                    </figure>
                                </body>
                                <p :class="[formattedLastUpdate(catalog_product.pivot).bgClass, 'text-gray-500 dark:text-gray-800 mt-2 inline-block pr-2']">
                                    Último cambio de precio:
                                    <span class="font-bold text-black ml-2">
                                        {{ formattedLastUpdate(catalog_product.pivot).text }}
                                    </span>
                                </p>

                                <!-- botones de acción -->
                                <div class="absolute bottom-2 right-1 flex items-center space-x-1">
                                    <el-tooltip content="Agendar cambio de precio" placement="top">
                                        <button type="button" @click="handleScheduleUpdateProductPrice(catalog_product)"
                                            class="rounded-full size-7 flex items-center justify-center bg-gray2 text-black">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                                            </svg>
                                        </button>
                                    </el-tooltip>
                                    <el-tooltip content="Cambiar precio" placement="top">
                                        <button type="button" @click="handleUpdateProductPrice(catalog_product)"
                                            class="rounded-full size-7 flex items-center justify-center bg-gray2 text-black">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>
                                    </el-tooltip>
                                </div>
                            </div>
                        </section>

                        <el-dialog v-model="dialogVisible">
                            <img class="mx-auto w-full object-contain" :src="dialogImageUrl" alt="Preview Image" />
                        </el-dialog>
                    </div>
                </div>
                
                <div class="md:w-full lg:w-1/2 mx-7 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg px-9 py-5 shadow-md"
                    :class="{
                        'md:left-auto md:ml-32': catalogProductsCompanyBranchSelected?.length,
                        'md:mx-auto': !catalogProductsCompanyBranchSelected?.length
                    }">
                    <p v-if="!form.opportunity_id && !$page.props.auth.user.permissions.includes('Crear ordenes de venta sin cotizacion')" class="text-xs text-primary mb-3">
                        Por indicaciones de dirección, sólo se puede crear una OV desde una cotización.
                    </p>
                    <el-radio-group v-model="form.is_sale_production" size="small">
                        <el-radio v-if="form.opportunity_id || $page.props.auth.user.permissions.includes('Crear ordenes de venta sin cotizacion')" :value="1">Orden de venta</el-radio>
                        <el-radio :value="0">Orden de stock</el-radio>
                    </el-radio-group>
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div v-if="form.is_sale_production" class="col-span-full">
                            <InputLabel value="Selecciona la cotización relacionada a la OV en caso de tenerla (sólo cot autorizadas)" />
                            <el-select @change="handleRelatedQuote()" v-model="form.quote_id"
                                filterable placeholder="Selecciona una cotización">
                                <el-option v-for="item in quotes" :key="item.id" :label="'C-' + item.id + ' - ' + item.company_branch?.name"
                                    :value="item.id" />
                            </el-select>
                        </div>
                        <div>
                            <InputLabel value="Cliente*" />
                            <el-select @change="handleCompanyBranchIdChange" v-model="form.company_branch_id"
                                filterable placeholder="Selecciona un cliente">
                                <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                    :value="item.id" />
                            </el-select>
                        </div>
                        <div>
                            <InputLabel value="Contacto*" />
                            <el-select v-model="form.contact_id" clearable filterable
                                placeholder="Selecciona el contacto"
                                no-data-text="Primero selecciona al cliente o al prospecto"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="contact in company_branches.find(cb => cb.id ==
                                    form.company_branch_id)?.contacts" :key="contact"
                                    :label="`${contact.charge}: ${contact.name} (${contact.email}, ${contact.additional_emails?.join(', ')})`"
                                    :value="contact.id">
                                    {{ contact.charge }}: {{ contact.name }} ({{ contact.email }}, {{
                                        contact.additional_emails?.join(', ') }})
                                </el-option>
                            </el-select>
                            <InputError :message="form.errors.contact_id" />
                        </div>
                    </div>
                    <!-- productos -->
                    <section>
                        <el-divider content-position="left">Productos</el-divider>
                        <p v-if="!form.company_branch_id"
                            class="flex items-center justify-center space-x-3 text-[#373737] py-2">
                            <i class="fa-solid fa-arrow-up text-sm"></i>
                            <span>Selecciona el cliente para visualizar los productos disponibles.</span>
                        </p>
                        <div v-else>
                            <InputError :message="form.errors.products" />
                            <!-- seleccion de producto -->
                            <article v-if="form.company_branch_id"
                                class="flex space-x-4 rounded-[20px] border border-[#999999] p-3">
                                <div class="w-2/3">
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="col-span-2">
                                            <InputLabel value="Producto" />
                                            <el-select @change="fetchCatalogProductData(); checkIfProductHasSale()"
                                                v-model="product.catalog_product_company_id" class="w-full"
                                                no-data-text="No hay productos registrados a este cliente"
                                                placeholder="Selecciona un producto *">
                                                <el-option
                                                    v-for="item in company_branches.find(cb => cb.id == form.company_branch_id)?.catalog_products"
                                                    :key="item.pivot.id" :label="item.name" :value="item.pivot.id" />
                                            </el-select>
                                        </div>
                                        <div>
                                            <InputLabel value="Cantidad*" />
                                            <el-input-number :disabled="loading || !product.catalog_product_company_id"
                                                @change="validateQuantity()" v-model="product.quantity" :min="1"
                                                class="!w-full" />
                                        </div>
                                    </div>
                                    <p v-if="alertMaxQuantity" class="text-red-600 text-xs">
                                        Sólo hay material para producir
                                        {{ alertMaxQuantity?.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                        unidades.
                                        No olvides reportar la adquisición de más mercancía
                                    </p>
                                    <div class="mt-3">
                                        <InputLabel value="Notas del producto" />
                                        <textarea v-model="product.notes" class="textarea" autocomplete="off"
                                            placeholder="Ingresa las notas sean relevantes sobre la producción producto"></textarea>
                                    </div>
                                    <div class="mt-3">
                                        <InputLabel value="Especificaciones" />
                                        <div class="flex items-center space-x-2 w-full ml-2 mt-1">
                                            <label class="flex items-center">
                                                <Checkbox v-model:checked="product.confusion_alert"
                                                    class="bg-transparent" />
                                                <span class="ml-2 text-xs">Riesgo de confusión de producto</span>
                                            </label>
                                            <el-tooltip placement="top">
                                                <template #content>
                                                    <p>
                                                        Selecciona esta opción si el producto puede generar confusión
                                                        <br>
                                                        debido a su similitud con otros. Esto permitirá que el operador
                                                        <br>
                                                        preste mayor atención al manejarlo.
                                                    </p>
                                                </template>
                                                <div
                                                    class="rounded-full border border-primary size-3 flex items-center justify-center">
                                                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                                </div>
                                            </el-tooltip>
                                        </div>
                                        <label v-if="product.part_number?.split('-')[1] == 'LL'"
                                            class="inline ml-2 mt-1">
                                            <Checkbox v-model:checked="product.requires_medallion"
                                                class="bg-transparent" />
                                            <span class="ml-2 text-xs">Requiere medallón</span>
                                        </label>
                                        <div v-if="form.is_sale_production"
                                            class="flex items-center space-x-2 w-1/2 ml-2 mt-1">
                                            <label class="flex items-center">
                                                <Checkbox v-model:checked="product.is_new_design"
                                                    class="bg-transparent" />
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
                                                    class="rounded-full border border-primary size-3 flex items-center justify-center">
                                                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                                </div>
                                            </el-tooltip>
                                        </div>
                                    </div>
                                    <div class="col-span-full">
                                        <div class="flex items-center space-x-6 ml-5 w-full">
                                            <!-- Descomentar cuando se implenmente lo de formato de autorizacion  -->
                                <!-- <div v-if="selectedCatalogProductHasSale === false" class="w-full">
                                        <div class="flex items-center space-x-3">
                                            <span @click="$inertia.get(route('design-authorizations.create', {company_id: selectedCompanyId}))" class="text-xs text-primary cursor-pointer inline">Crear formato</span>
                                            <el-tooltip placement="top">
                                                <template #content>
                                                    <p>
                                                        Es primera venta de este producto, <br>
                                                        es necesario seleccionar el formato de autorización <br>
                                                        de diseño para poder agregarlo a la orden de venta. <br>
                                                        Si aún no tienes un formato creado, da clic en 'crear formato'.
                                                    </p>
                                                </template>
                                                <div
                                                    class="rounded-full border border-primary size-3 flex items-center justify-center">
                                                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                                </div>
                                            </el-tooltip>
                                            <span v-if="product.design_authorization_id" @click="openDesignAuthorization()" class="text-xs text-secondary cursor-pointer">Ver formato<i class="fa-regular fa-eye ml-2"></i></span>
                                        </div>
                                    <el-select v-model="product.design_authorization_id" clearable
                                        filterable placeholder="Formato de autorización de diseño" no-data-text="No hay formatos autorizados">
                                        <el-option v-for="item in design_authorizations" :key="item.id" :label="item.name"
                                            :value="item.id" />
                                    </el-select>
                                </div> -->
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <SecondaryButton @click="addProduct"
                                            :disabled="form.processing || !product.catalog_product_company_id || !product.quantity">
                                            {{ editIndex !== null ? 'Actualizar producto' : 'Agregar producto a lista'
                                            }}
                                        </SecondaryButton>
                                        <!-- <SecondaryButton @click="addProduct" descomentar cuando se implemente lo del formato de autorizacion
                                :disabled="form.processing || !product.catalog_product_company_id || !product.quantity || (selectedCatalogProductHasSale ? false : !product.design_authorization_id) ">
                                {{ editIndex !== null ? 'Actualizar producto' : 'Agregar producto a lista' }}
                            </SecondaryButton> -->
                                    </div>
                                </div>
                                <div class="w-1/3">
                                    <div v-if="loading"
                                        class="rounded-[10px] border border-[#999999] text-xs text-gray-500 text-center p-4 min-h-24">
                                        <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                                        cargando imagen...
                                    </div>
                                    <div v-else-if="selectedCatalogProduct">
                                        <figure class="rounded-[10px] border border-[#999999] w-full min-h-24">
                                            <img :src="selectedCatalogProduct.media[0]?.original_url"
                                                class="rounded-[10px] min-h-24 w-full object-contain">
                                        </figure>
                                        <div
                                            class="rounded-[10px] border border-[#999999] text-[#373737] dark:text-gray-400 text-xs px-2 py-1 mt-1">
                                            <h2>Almacén - Producto terminado</h2>
                                            <p>
                                                Stock mínimo: <span class="text-black dark:text-white">{{
                                                    selectedCatalogProduct.min_quantity.toLocaleString('en-US', {
                                                        minimumFractionDigits: 2
                                                    }) }} unidades</span>
                                            </p>
                                            <p class="flex items-center space-x-1">
                                                Stock disponible:
                                                <b class="ml-1">{{ availableStock ?
                                                    availableStock.quantity.toLocaleString('en-US',
                                                        {
                                                            minimumFractionDigits: 2
                                                        }) : 0 }} unidades.</b>
                                                <el-tooltip v-if="form.is_sale_production" placement="top">
                                                    <template #content>
                                                        Se descontarán de estas existencias para despachar la orden.
                                                        <br>
                                                        Se refiere a las piezas ya procesadas para tener el producto
                                                        final, <br>
                                                        no se refiere a la materia prima.
                                                    </template>
                                                    <div
                                                        class="rounded-full border border-primary size-3 flex items-center justify-center">
                                                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                                    </div>
                                                </el-tooltip>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            <!-- lista de productos -->
                            <h2 v-if="form.products.length" class="font-bold mt-3 ml-2">Lista de productos</h2>
                            <ol v-if="form.products.length"
                                class="rounded-lg bg-[#CCCCCC] text-black px-5 py-3 col-span-full space-y-1 mt-3 divide-y-[1px]">
                                <template v-for="(item, index) in form.products" :key="index">
                                    <li class="flex justify-between items-center border-[#999999] py-1">
                                        <p class="text-[13px]">
                                            <span class="text-primary">{{ index + 1 }}.</span>
                                            {{ company_branches.find(cb => cb.id ==
                                                form.company_branch_id)?.catalog_products?.find(prd => prd.pivot.id ===
                                                    item.catalog_product_company_id)?.name
                                            }}
                                            ({{ item.quantity?.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                                            unidades)
                                        </p>
                                        <div class="flex space-x-2 items-center">
                                            <el-tag v-if="editIndex == index">En edición</el-tag>
                                            <button @click="editProduct(index)" type="button"
                                                class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                </svg>
                                            </button>
                                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No"
                                                icon-color="#0355B5" title="¿Remover?" @confirm="deleteProduct(index)">
                                                <template #reference>
                                                    <button type="button"
                                                        class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </template>
                                            </el-popconfirm>
                                        </div>
                                    </li>
                                </template>
                            </ol>
                            <div v-if="loading"
                                class="text-primary text-center p-4 min-h-24">
                                <i class="fa-solid fa-circle-notch fa-spin mr-2 text-xl"></i>
                                Cargando productos de cotización...
                            </div>
                        </div>
                    </section>
                    <!-- logistica -->
                    <section v-if="form.is_sale_production && !loading">
                        <el-divider content-position="left">Logistica</el-divider>
                        <p v-if="!form.products.length"
                            class="flex items-center justify-center space-x-3 text-[#373737] py-2">
                            <i class="fa-solid fa-arrow-up text-sm"></i>
                            <span>Agrega al menos un producto a la lista para llenar los datos de logística.</span>
                        </p>
                        <div v-else>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <InputLabel value="Opciones de envío" />
                                    <el-select v-model="form.shipping_option" @change="handleChangeShippingOption"
                                        placeholder="Selecciona">
                                        <el-option v-for="item in shippingOptions" :key="item" :label="item"
                                            :value="item" />
                                    </el-select>
                                    <InputError :message="form.errors.shipping_option" />
                                </div>
                                <div>
                                    <InputLabel value="Pago de flete*" />
                                    <el-select v-model="form.freight_option" @change="handleFreightOption"
                                        placeholder="Seleccionar" :fit-input-width="true">
                                        <el-option v-for="item in freightOptions" :key="item" :label="item" :value="item" />
                                    </el-select>
                                    <InputError :message="form.errors.freight_option" />
                                </div>
                                <div v-if="form.freight_option == 'Cargo del flete prorrateado en producto' || form.freight_option == 'Cargo flete normal de costo al cliente'">
                                    <InputLabel>
                                        <div class="flex items-center">
                                            <span>Costo de flete cotizado*</span>
                                            <el-tooltip placement="top">
                                                <template #content>
                                                    <p>
                                                        Es el monto especificado en la cotización.<br>
                                                        Si en la cotización se registra como texto <br>
                                                        el monto aqui aparecerá como 0, debido a que <br>
                                                        este campo debe ser numérico para poder hacer <br>
                                                        cálculos
                                                    </p>
                                                </template>
                                                <div
                                                    class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                                                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                                </div>
                                            </el-tooltip>
                                        </div>
                                    </InputLabel>
                                    <el-input v-model="form.freight_cost" placeholder="Ej. 800" />
                                    <InputError :message="form.errors.freight_cost" />
                                </div>
                            </div>
                            <div v-for="(partiality, index) in form.partialities" :key="index"
                                class="md:grid grid-cols-2 gap-3">
                                <h2 v-if="form.shipping_option != 'Entrega única'" class="mt-3 col-span-full font-bold">
                                    Parcialidad {{ (index + 1) }}
                                </h2>
                                <div class="mt-3">
                                    <InputLabel>
                                        <div class="flex items-center">
                                            <span>Fecha de embarque esperado</span>
                                            <el-tooltip placement="top">
                                                <template #content>
                                                    <p>
                                                        Esta aparecerá en producción para dar prioridad<br>
                                                        a ventas cercanas a su fecha de entrega
                                                    </p>
                                                </template>
                                                <div
                                                    class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                                                    <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                                </div>
                                            </el-tooltip>
                                        </div>
                                    </InputLabel>
                                    <el-date-picker v-model="partiality.promise_date" type="date"
                                        placeholder="Fecha de entrega esperada" format="YYYY/MM/DD"
                                        value-format="YYYY-MM-DD" :disabled-date="disabledDate" class="!w-full" />
                                    <InputError :message="form.errors.promise_date" />
                                </div>
                                <div class="mt-3">
                                    <InputLabel value="Proveedor de paquetería*" />
                                    <el-select v-model="partiality.shipping_company" placeholder="Paquetería">
                                        <el-option v-for="shippingCompany in shippingCompanies" :key="shippingCompany"
                                            :label="shippingCompany" :value="shippingCompany" />
                                    </el-select>
                                    <InputError :message="form.errors.shipping_company" />
                                </div>
                                <div>
                                    <InputLabel value="Guía" />
                                    <el-input v-model="partiality.tracking_guide" placeholder="Ingresa la guía" />
                                    <InputError :message="form.errors.tracking_guide" />
                                </div>
                                <div>
                                    <InputLabel value="Acuse" />
                                    <FileUploader @files-selected="this.form.acuse = $event" :multiple="false" />
                                    <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                                        PDF, PNG, JPG,(MAX 50MB)
                                    </p>
                                </div>
                                <br>
                                <InputLabel v-if="form.shipping_option != 'Entrega única'"
                                    value="Productos para esta parcialidad" />
                                <InputLabel v-if="form.shipping_option != 'Entrega única'"
                                    value="Cantidad de piezas a enviar" />
                                <div v-if="form.shipping_option != 'Entrega única'" class="col-span-full space-y-1">
                                    <div v-for="(prd, index2) in form.products" :key="index2"
                                        class="grid grid-cols-2 gap-3">
                                        <label v-if="partiality.productsSelected[index2]" class="inline ml-2">
                                            <Checkbox v-model:checked="partiality.productsSelected[index2].selected"
                                                class="bg-transparent" />
                                            <span class="ml-2 text-xs">{{ prd.catalogProduct.name }}</span>
                                        </label>
                                        <el-input-number v-if="partiality.productsSelected[index2]"
                                            v-model="partiality.productsSelected[index2].quantity" :min="1"
                                            :max="prd.quantity" size="small" class="!w-1/2 self-start"
                                            :disabled="!partiality.productsSelected[index2]?.selected" />
                                    </div>
                                </div>
                                <div class="flex space-x-2 bg-yellow-200 pl-3 col-span-full">
                                    <p class="text-[#999999] w-48">Cantidad de cajas:</p>
                                    <p class="text-gray-700">{{ totalBoxes[index] ?? '- Sin información -' }}</p>
                                </div>
                                <div class="flex space-x-2 bg-yellow-200 pl-3 col-span-full">
                                    <p class="text-[#999999] w-48">Costo total de envío:</p>
                                    <p class="text-gray-700">${{ totalCost[index] ?? '- Sin información -' }}</p>
                                </div>
                                <h2 v-if="form.products.length" class="ml-2 mt-6 font-bold col-span-full">
                                    Detalles sobre las cajas
                                </h2>
                                <ShippingCard class="col-span-full"
                                    v-for="(shippProduct, index3) in partiality.productsSelected.filter(p => p?.selected)"
                                    :key="index3"
                                    :product="form.products.find(e => e.catalogProduct.name == shippProduct.name).catalogProduct"
                                    :quantity="shippProduct.quantity" :routePage="'sales.create'"
                                    @total-boxes="totalBoxes[index] = totalBoxes[index] + $event"
                                    @total-cost="totalCost[index] = totalCost[index] + $event" />
                            </div>
                        </div>
                    </section>
                    <!-- Datos de la orden -->
                    <section v-if="form.is_sale_production && !loading">
                        <el-divider content-position="left">Datos de la órden</el-divider>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <InputLabel value="Medio de petición*" />
                                <el-select v-model="form.order_via" placeholder="Medio de petición *">
                                    <el-option v-for="item in orderVias" :key="item" :label="item" :value="item" />
                                </el-select>
                                <InputError :message="form.errors.order_via" />
                            </div>
                            <div>
                                <InputLabel value="Factura" />
                                <el-input v-model="form.invoice" placeholder="Ingresa folio de factura" />
                                <InputError :message="form.errors.invoice" />
                            </div>
                            <div class="flex items-center space-x-2 col-span-full">
                                <label class="flex items-center">
                                    <Checkbox v-model:checked="form.is_high_priority" class="bg-transparent" />
                                    <span class="ml-2 text-xs">Orden con prioridad alta</span>
                                </label>
                                <el-tooltip
                                    content="Al seleccionar esta opción, se recordará diariamente por notificación si no se ha creado una orden de producción"
                                    placement="top">
                                    <div
                                        class="rounded-full border border-primary size-3 flex items-center justify-center">
                                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                    </div>
                                </el-tooltip>
                            </div>
                            <div class="col-span-full">
                                <InputLabel value="Notas de la orden" />
                                <el-input v-model="form.notes" :rows="3" maxlength="900" placeholder="Notas de la órden"
                                    show-word-limit type="textarea" />
                                <InputError :message="form.errors.notes" />
                            </div>
                            <h2 class="ml-2 col-span-full pt-4"><b>OCE</b> (Orden de compra externa)</h2>
                            <div>
                                <InputLabel value="Nombre o folio " />
                                <el-input v-model="form.oce_name" placeholder="Escribe el nombre o folio de la OCE" />
                                <InputError :message="form.errors.oce_name" />
                            </div>
                            <div>
                                <InputLabel value="Archivo(s)" />
                                <FileUploader @files-selected="this.form.media = $event" :multiple="true" />
                                <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                                    PDF, PNG, JPG,(MAX 50 MB)
                                </p>
                            </div>
                            <div>
                                <InputLabel value="Otros" />
                                <FileUploader @files-selected="this.form.anotherFiles = $event" :multiple="true" />
                                <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                                    PDF, PNG, JPG,(MAX 50 MB)
                                </p>
                            </div>
                        </div>
                    </section>
                    <div class="mt-6 mx-3 md:text-right">
                        <PrimaryButton
                            :disabled="form.processing || !form.products.length || (form.is_sale_production && !form.partialities.length)">
                            Crear
                        </PrimaryButton>
                    </div>
                </div>
            </form>

            <!-- modal para agendar actualización de precio de producto en calendario -->
            <DialogModal :show="showScheduleUpdatingPrice" @close="showScheduleUpdatingPrice = false" maxWidth="2xl">
                <template #title>
                    <h1>Agendar actualización de precio</h1>
                </template>
                <template #content>
                    <div class="my-3">
                        <InputLabel value="Fecha de recordatorio*" />
                        <el-date-picker v-model="scheduleForm.start_date" type="date" placeholder="Fecha*"
                            :disabled-date="disabledDate" />
                        <InputError :message="scheduleForm.errors.start_date" />
                    </div>
                    <div class="my-3">
                        <InputLabel value="Título de tarea*" />
                        <el-input v-model="scheduleForm.title" maxlength="255" placeholder="Agregar título" />
                        <InputError :message="scheduleForm.errors.title" />
                    </div>
                    <div class="my-3">
                        <InputLabel value="Descripción de tarea" />
                        <el-input v-model="scheduleForm.description" maxlength="255"
                            placeholder="Agrega una descripción (opcional)" show-word-limit type="textarea" />
                        <InputError :message="scheduleForm.errors.description" />
                    </div>
                </template>
                <template #footer>
                    <div class="flex justify-end space-x-1">
                        <CancelButton @click="showScheduleUpdatingPrice = false" :disabled="scheduleForm.processing">
                            Cancelar</CancelButton>
                        <PrimaryButton type="button" @click="scheduleUpdatePrice" :disabled="scheduleForm.processing">
                            Agendar</PrimaryButton>
                    </div>
                </template>
            </DialogModal>

            <!-- modal para actualizar precio de producto -->
            <DialogModal :show="showUpdatePriceModal" @close="showUpdatePriceModal = false" maxWidth="2xl">
                <template #title>
                    <h1>Actualizar precio de {{ itemToUpdatePrice.name }}</h1>
                </template>
                <template #content>
                    <section class="grid grid-cols-3 gap-3 mt-3">
                        <div>
                            <InputLabel value="Precio nuevo en porcentaje*" />
                            <el-input
                                @change="calculateNewPrice"
                                v-model="new_price_percentage" type="number" :max="100" :min="5" step="0.1"
                                placeholder="Ej. 5.8%"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')"
                                >
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
                            <el-select v-model="priceForm.new_currency" placeholder="Seleccionar"
                                :fit-input-width="true">
                                <el-option v-for="item in newPriceCurrencies" :key="item.value" :label="item.label"
                                    :value="item.value">
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
                        <p v-if="priceForm.new_price && (priceForm.new_price - itemToUpdatePrice.pivot.new_price) < (itemToUpdatePrice.pivot.new_price * 0.04)"
                            class="text-xs text-red-600 col-span-full">El incremento de precio no debe ser menor al 4%
                            del precio actual</p>
                    </section>
                </template>
                <template #footer>
                    <div class="flex justify-end space-x-1">
                        <CancelButton @click="showUpdatePriceModal = false" :disabled="priceForm.processing">Cancelar
                        </CancelButton>
                        <PrimaryButton type="button" @click="updatePrice"
                            :disabled="priceForm.processing
                                || !priceForm.new_price
                                || (priceForm.new_price - itemToUpdatePrice.pivot.new_price) < (itemToUpdatePrice.pivot.new_price * 0.04)">Actualizar precio
                        </PrimaryButton>
                    </div>
                </template>
            </DialogModal>

            <DialogModal :show="showImportantNotesModal" @close="showImportantNotesModal = false">
                <template #title>
                    {{ editIMportantNotes ? 'Editar' : 'Agregar' }} notas importantes para
                    {{ company_branches.find(item => item.id == form.company_branch_id).name }}
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

            <!-- modal para crear proyecto -->
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

            <!-- modal para agregar nuevo producto al cliente -->
            <Modal :show="showCreateNewCatalogProductCompanyModal" @close="showCreateNewCatalogProductCompanyModal = false">
                <section class="mx-7 my-4 space-y-4">
                    <div>
                        <p class="text-secondary text-center text-lg font-bold">
                            Agregar nuevo producto al catálogo del cliente
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3 pt-5 pb-1">
                        <CancelButton @click="showCreateNewCatalogProductCompanyModal = false">Cancelar</CancelButton>
                        <PrimaryButton @click="storeCatalogProductCompany">Agregar producto</PrimaryButton>
                    </div>
                </section>
            </Modal>

            <!-- modal de producto de cotizacion no encontrado en productos del cliente -->
            <Modal :show="showProductNotFoundModal" @close="handleModalClose">
                <section class="mx-7 my-4 space-y-4">
                    <div>
                        <p class="text-amber-700 text-center mt-10 font-bold flex items-center justify-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>

                            <span>Producto no encontrado en el catálogo del cliente</span>
                        </p>
                        <p class="text-center text-gray-500">
                            El siguiente producto no está registrado en el catálogo del cliente. ¿Deseas agregarlo?
                        </p>
                    </div>
                    <section class="bg-gray-100 dark:bg-gray-600 p-4 rounded-md shadow-md dark:shadow-gray-600 text-sm grid grid-cols-2 gap-3 h-48">
                        <div class="space-y-1">
                            <p><strong>Nombre:</strong> {{ missingProduct?.name }}</p>
                            <p><strong>Número de parte:</strong> {{ missingProduct?.part_number }}</p>
                            <p><strong>Descripción:</strong> {{ missingProduct?.description || 'Sin descripción' }}</p>
                        </div>
                        
                        <div v-if="loading"
                            class="rounded-[10px] border border-[#999999] text-xs text-gray-500 dark:text-gray-300 text-center p-4 min-h-24">
                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                            cargando imagen...
                        </div>

                        <figure v-else class="rounded-xl flex items-center justify-center h-40">
                            <img v-if="missingProduct?.media?.length"
                                class="h-full object-contain rounded-lg" :src="missingProduct?.media[0].original_url" :alt="missingProduct?.name">

                            <div v-else class="flex flex-col items-center text-gray-300">
                                <i class="fa-solid fa-image text-4xl"></i>
                                <small class="font-bold text-lg">Sin imagen</small>
                            </div>
                        </figure>
                    </section>

                    <section class="grid grid-cols-3 gap-3">
                        <div>
                            <InputLabel value="Precio del producto*" />
                            <el-input v-model="priceForm.new_price" type="text"
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
                            <el-select v-model="priceForm.new_currency" placeholder="Seleccionar"
                                :fit-input-width="true">
                                <el-option v-for="item in newPriceCurrencies" :key="item" :label="item"
                                    :value="item" />
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
                    </section>
                    <div class="flex justify-end space-x-3 pt-5 pb-1">
                        <SecondaryButton @click="skipProduct()">Omitir</SecondaryButton>
                        <PrimaryButton @click="addProductToCustomer()">Agregar producto</PrimaryButton>
                    </div>
                </section>
            </Modal>

            <!-- Modal para crear tarea en calendario -->
            <DialogModal :show="showCalendarTaskModal" @close="showCalendarTaskModal = false">
                <template #title>
                    <h1>¿Deseas crear un recordatorio de embarque en tu calendario?</h1>
                </template>
                <template #content>
                    <p class="my-4 text-center">Se creará un recordatorio de la fecha de embarque de cada parcialidad de tu orden de venta. <br>
                        Asegúrate de que las fechas de embarque esperado sean correctas, ya que es la fecha en la que se creará el recordatorio.
                    </p>
                </template>
                <template #footer>
                    <CancelButton @click="handleStore(false)">No crear</CancelButton>
                    <PrimaryButton @click="handleStore(true)">Crear recordatorio</PrimaryButton>
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
import Checkbox from "@/Components/Checkbox.vue";
import Modal from "@/Components/Modal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Back from "@/Components/MyComponents/Back.vue";
import InputLabel from "@/Components/InputLabel.vue";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import ShippingCard from "@/Components/MyComponents/Shipping/ShippingCard.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { differenceInMonths, differenceInDays, format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        const form = useForm({
            company_branch_id: null,
            opportunity_id: null,
            contact_id: null,
            shipping_option: null,
            freight_cost: null,
            // shipping_company: null,
            // tracking_guide: null,
            // promise_date: null,
            invoice: null,
            oce_name: null,
            order_via: null,
            notes: null,
            is_high_priority: false,
            products: [],
            acuse: null, //archivo de acuse en sección de logística
            media: null, //archivo OCE
            anotherFiles: null, //otros archivos 
            partialities: [],
            is_sale_production: 0, //seleccionado stock porque se necesita cotizacion para crear venta
            create_calendar_task: false, //bandera para crear o no recordatorio en calendario
            freight_option: null,
            quote_id: null //cotizacion relacionada con la ov
        });

        const priceForm = useForm({
            new_price: null,
            new_currency: null,
            new_date: new Date().toISOString().split('T')[0], // Fecha actual en formato YYYY-MM-DD,
            product_company_id: null,
            catalog_product_id: null, //para agregar un nuevo prudcto de catalogo al cliente
            company_branch_id: null, //para agregar un nuevo prudcto de catalogo al cliente
        });

        const scheduleForm = useForm({
            title: null,
            start_date: null,
            description: null,
        });

        return {
            form,
            priceForm,
            scheduleForm,
            showProductNotFoundModal: false, //modal de producto de catalogo no encontrado en productos del cliente
            missingProduct: null, // producto de cotizacion no encontrado en catalogo de productos del cliente.
            quote: null, // informacion de la cotizacion seleccionada relacionada a la ov.
            loadingCompanyBranchProducts: false,
            loading: false,
            importantNotes: null,
            showImportantNotesModal: false,
            showCalendarTaskModal: false,
            importantNotesToStore: null,
            isEditImportantNotes: false,
            showCreateProjectModal: false,
            showCreateNewCatalogProductCompanyModal: false, //Modal para agregar nuevo producto al cliente
            availableStock: null,
            new_price_percentage: 5, //porcentaje de aumento de precio
            showUpdatePriceModal: false, //modal para actualizar precio
            showScheduleUpdatingPrice: false, //modal para agendar actualización de precio
            itemToUpdatePrice: null, //producto seleccionado para cambiar precio
            dialogImageUrl: null,
            dialogVisible: false, //imagen element-plus
            catalogProductsCompanyBranchSelected: null, //productos de cliente seleccionado
            company_id : null, //id de la empresa seleccionada
            product: {
                design_authorization_id: null, //formato de autorización seleccionado
                catalogProduct: null,
                catalog_product_company_id: null,
                quantity: null,
                notes: null,
                part_number: null,
                is_new_design: false,
                confusion_alert: false,
                requires_medallion: false,
            },
            editIndex: null,
            alertMaxQuantity: 0,
            selectedCatalogProduct: null,
            commitedUnits: null,
            selectedCatalogProductHasSale: null, //valida si Producto seleccionado tiene al menos una venta. (bool)
            design_authorizations: null, //formatos de autorizaión de diseño del cliente
            selectedCompanyId: null, //guarda el id de la matriz que contiene la sucursal seleccionada
            newPriceCurrencies: [
                { value: "$MXN", label: "MXN" },
                { value: "$USD", label: "USD" },
            ],
            shippingCompanies: [
                'PAQUETEXPRESS',
                'LOCAL',
                'DHL',
                'FEDEX',
                'TRES GUERRAS',
            ],
            shippingOptions: [
                'Entrega única',
                '2 parcialidades',
                '3 parcialidades',
                '4 parcialidades',
            ],
            orderVias: [
                'Correo electrónico',
                'WhatsApp',
                'Llamada telefónica',
                'Resurtido programado',
                'Otro',
            ],
            freightOptions: [
                'Cargo flete normal de costo al cliente',
                'Cargo del flete prorrateado en producto',
                'Emblems3d absorbe el costo del flete',
                'El cliente envía la guía',
            ],
            totalBoxes: [0],
            totalCost: [0],
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        PrimaryButton,
        InputError,
        InputLabel,
        CancelButton,
        DialogModal,
        Checkbox,
        Modal,
        Back,
        Link,
        FileUploader,
        ShippingCard
    },
    props: {
        company_branches: Array,
        opportunityId: Number,
        sample: Object,
        quotes: Array,
    },
    watch: {
        'form.partialities'() {
            this.totalBoxes = new Array(this.form.partialities?.length).fill(0);
            this.totalCost = new Array(this.form.partialities?.length).fill(0);
        },
    },
    methods: {
        addProductToCustomer() {
            this.priceForm.post(route('companies.attach-catalog-product'), {
                 onSuccess: async () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Producto agregado al catálogo del cliente",
                        type: "success",
                    });
                    this.showProductNotFoundModal = false;
                    await this.fetchCatalogProductsCompanyBanch();

                     // Buscar el producto recién agregado en el catálogo actualizado
                    const updatedProduct = this.company_branches
                        .find(cb => cb.id == this.form.company_branch_id)
                        ?.catalog_products.find(p => p.name === this.missingProduct.name);

                    // Busca el producto en la cotizacion para obtener la cantidad y las notas.
                    const quoteProduct = this.quote.catalog_products.find(p => p.id === this.missingProduct.id);

                    // Si se encuentra el producto, asigna los valores necesarios
                    if (updatedProduct) {
                        this.product = {
                            catalog_product_company_id: updatedProduct.pivot.id,
                            catalogProduct: null,
                            quantity: quoteProduct.pivot.quantity,
                            notes: quoteProduct.pivot.notes,
                            part_number: quoteProduct.part_number,
                            is_new_design: false,
                            confusion_alert: false,
                            requires_medallion: quoteProduct.pivot.requires_med,
                        };

                        await this.fetchCatalogProductData(); // Obtener detalles del producto
                        this.addProduct(); // Agregar a la cotización y orden de venta
                    }

                    this.modalResponseCallback(); // Resuelve la promesa para continuar el flujo
                },
            });
        },
        async handleRelatedQuote() {
            await this.fetchQuoteInfo();
        },
        async fetchQuoteInfo() {
            try {
                const response = await axios.get(route('quotes.fetch-data', this.form.quote_id));
                if (response.status === 200) {
                    this.quote = response.data.quote;

                    if (this.quote) {
                        this.form.company_branch_id = this.quote.company_branch.id; //guarda el id del cliente
                        this.priceForm.company_branch_id = this.quote.company_branch.id; //guarda el id del cliente
                        this.handleCompanyBranchIdChange();

                        // Obtener los productos de la sucursal
                        const companyProducts = this.company_branches.find(cb => cb.id == this.form.company_branch_id)?.catalog_products || [];

                        // Agregar productos de la cotización a la orden de venta
                        for (const product of this.quote.catalog_products) {
                            // Buscar el producto en el catálogo de la sucursal
                            const item = companyProducts.find(p => p.name === product.name);
                            
                            if (item) {
                                this.product = {
                                    catalog_product_company_id: item.pivot.id,
                                    catalogProduct: null,
                                    quantity: product.pivot.quantity,
                                    notes: product.pivot.notes,
                                    part_number: product.part_number,
                                    is_new_design: false,
                                    confusion_alert: false,
                                    requires_medallion: product.pivot.requires_med,
                                };
                                await this.fetchCatalogProductData(); // Ahora sí esperará a que se ejecute, ya que el for si acepta funciones asíncronas
                                this.addProduct();
                            } else {
                                this.missingProduct = product;
                                this.priceForm.catalog_product_id = product.id; //guarda el id del producto de catalogo
                                this.fetchCatalogProductData(product.id);
                                await this.waitForUserResponse(); // Espera la respuesta del usuario antes de continuar
                            }
                        }

                        // Agregar información general de cotización
                        this.form.freight_option = this.quote.freight_option;
                        this.form.notes = this.quote.notes;
                        this.form.freight_cost = this.quote.freight_cost;
                        this.resetProductForm();
                        this.store(); // ejecuta el metodo para resaltar las validaciones en los campos obligatorios
                    }
                }
            } catch (error) {
                console.error("Error al obtener la cotización:", error);
            }
        },
        waitForUserResponse() {
            return new Promise(resolve => {
                this.showProductNotFoundModal = true;
                this.modalResponseCallback = resolve; // Guarda la referencia de la función que resolverá la promesa
            });
        },
        skipProduct() {
            this.showProductNotFoundModal = false;
            this.modalResponseCallback(); // Resuelve la promesa aunque se omita el producto
        },

        handleModalClose() {
            this.showProductNotFoundModal = false;
            this.modalResponseCallback(); // Asegura que el flujo continúa si el modal se cierra
        },
        handleStoreSale() {
            if (this.form.partialities.some(item => item.promise_date !== null) && this.form.partialities.length > 1) {
                this.showCalendarTaskModal = true;
            } else {
                this.store();
            }
        },
        handleFreightOption() {
            if (this.form.freight_option == 'El cliente envía la guía' || this.form.freight_option == 'Emblems3d absorbe el costo del flete') {
                this.form.freight_cost = 0;
            } else {
                this.form.freight_cost = null;
            }
        },
        calculateNewPrice() {
            // factor para calcular porcentaje del precio
            const factor = 1 + this.new_price_percentage * .01;
            // guarda el precio calculado con el porcentaje seleccionado
            this.priceForm.new_price = (factor * this.itemToUpdatePrice.pivot.new_price).toFixed(2);
        },
        calculateNewPercentage() {
            if (!this.priceForm.new_price || !this.itemToUpdatePrice.pivot.new_price) {
                this.new_price_percentage = 0;
                console.log(this.itemToUpdatePrice);
                return;
            }

            // Convierte los valores a número
            const oldPrice = parseFloat(this.itemToUpdatePrice.pivot.new_price);
            const newPrice = parseFloat(this.priceForm.new_price);

            // Calcula el porcentaje de aumento
            this.new_price_percentage = (((newPrice / oldPrice) - 1) * 100).toFixed(2);
        },
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.original_url;
            this.dialogVisible = true;
        },
        formattedLastUpdate(productData) {
            const { new_date, old_date, new_updated_by } = productData;
            const lastDate = new_date || old_date;

            if (!lastDate) {
                return { text: 'No disponible', bgClass: 'bg-gray-200' };
            }

            const now = new Date();
            const lastUpdate = new Date(lastDate);

            // Calcula la diferencia en meses
            const monthsDifference = differenceInMonths(now, lastUpdate);
            const daysDifference = differenceInDays(now, lastUpdate);

            let timeText = '';
            if (monthsDifference > 0) {
                timeText = `hace ${monthsDifference} mes${monthsDifference !== 1 ? 'es' : ''}`;
            } else {
                timeText = `hace ${daysDifference} día${daysDifference !== 1 ? 's' : ''}`;
            }

            // Determina la clase de fondo basada en los meses
            const bgClass = monthsDifference >= 7 ? 'bg-red-300' : 'bg-yellow-200';

            return {
                text: new_updated_by ? `${timeText} por ${new_updated_by}` : timeText,
                bgClass,
            };
        },
        formatDate(date) {
            if (date) {
                const parsedDate = new Date(date);
                return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
            }
        },
        priceChangePercentage(pivot) {
            const oldPrice = pivot.old_price;
            const newPrice = pivot.new_price;

            if (oldPrice && newPrice) {
                const percentageChange = ((newPrice - oldPrice) / oldPrice) * 100;
                return percentageChange.toFixed(2);
            }

            return null;
        },
        priceChangeClass(pivot) {
            if (this.priceChangePercentage(pivot) > 0) return 'text-green-700';
            if (this.priceChangePercentage(pivot) < 0) return 'text-red-700';
            return 'text-gray-600'; // color gris si no hay cambio en el precio
        },
        priceChangeIcon(pivot) {
            if (this.priceChangePercentage(pivot) > 0) return 'fa-solid fa-arrow-up-long';
            if (this.priceChangePercentage(pivot) < 0) return 'fa-solid fa-arrow-down-long';
            return null; // sin icono si el precio no cambia
        },
        handleScheduleUpdateProductPrice(catalogProduct) {
            //agregar titulo predeterminado
            this.scheduleForm.title = 'Cambiar precio a ' + catalogProduct.name
            this.showScheduleUpdatingPrice = true;
        },
        handleUpdateProductPrice(catalogProduct) {
            //guarda el producto al cual se actualizará el precio
            this.itemToUpdatePrice = catalogProduct;
            // asigna el id del producto al formulario de cambio de precio
            this.priceForm.product_company_id = catalogProduct.pivot.id;
            // calcular el 5% del precio actual y agregarlo a nuevo precio como cantidad por defecto
            this.priceForm.new_price = (catalogProduct.pivot.new_price * 1.05).toFixed(2);
            // Agregar moneda del producto seleccionado
            this.priceForm.new_currency = catalogProduct.pivot.new_currency;
            // Abrir modal
            this.showUpdatePriceModal = true;
        },
        scheduleUpdatePrice() {
            this.scheduleForm.post(route('quotes.schedule-update-product-price'), {
                onSuccess: () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Tarea agendada",
                        type: "success",
                    });
                    this.showScheduleUpdatingPrice = false;
                    this.scheduleForm.reset();
                },
            });
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
                    this.fetchCatalogProductsCompanyBanch();
                },
            });
        },
        handleStore(create_calendar_task) {
            this.form.create_calendar_task = create_calendar_task;
            this.store();
        },
        handleCompanyBranchIdChange() {
            this.getImportantNotes();
            this.getDesignAuthorizations();
            this.fetchCatalogProductsCompanyBanch();
            this.cleanShippingData();
        },
        handleChangeShippingOption() {
            this.form.partialities = [];
            const numberOfShippings = this.shippingOptions?.findIndex(i => i === this.form.shipping_option) + 1;
            for (let index = 0; index < numberOfShippings; index++) {
                this.addPartial(numberOfShippings == 1);
                // console.log(numberOfShippings);
            }
        },
        openDesignAuthorization() {
            const url = route('design-authorizations.show', this.product.design_authorization_id);
            window.open(url, '_blank');
        },
        addPartial(fillAllProducts = false) {
            let partiality = {
                promise_date: null,
                shipping_cost: null,
                shipping_company: null,
                sent_at: null,
                sent_by: null,
                number_of_packages: null,
                tracking_guide: null,
                status: 'Pendiente de envío',
                productsSelected: [],
            };

            // llenar productos seleccionados para parcialidad
            this.form.products.forEach(product => {
                const prd = {
                    id: product.catalogProduct.id,
                    name: product.catalogProduct.name,
                    selected: fillAllProducts,
                    quantity: fillAllProducts ? product.quantity : 0,
                };

                partiality.productsSelected.push({ ...prd });
            });

            this.form.partialities.push({ ...partiality });
        },
        removePartial(index) {
            this.form.partialities.splice(index, 1);
        },
        store() {
            // agrega el costo a cada parcialidad
            if (this.totalCost?.length) {
                this.form.partialities.forEach((element, index) => {
                    element.shipping_cost = this.totalCost[index];
                });
            }
            this.form.post(route('sales.store'), {
                onSuccess: () => {
                    this.$notify({
                        title: 'Éxito',
                        message: 'Orden de venta creada. Se han descontado las cantidades del stock automáticamente',
                        type: 'success'
                    });
                    this.showCalendarTaskModal = false;
                    this.showCreateProjectModal = true;
                }
            });
        },
        cleanShippingData() {
            // limpiar informacion de logistica
            this.form.contact_id = null;
            this.form.products = [];
            this.form.partialities = [];
            this.form.shipping_option = null;
            this.resetProductForm();
        },
        getImportantNotes() {
            this.importantNotes = this.company_branches.find(item => item.id == this.form.company_branch_id)?.important_notes;
            //obtiene el id de la matriz para pasarla como parametro en creacion de formato de autorización de diseño.
            this.selectedCompanyId = this.company_branches.find(item => item.id == this.form.company_branch_id).company_id;
        },
        editImportantNotes() {
            this.isEditImportantNotes = true;
            this.showImportantNotesModal = true;
            this.importantNotesToStore = this.importantNotes;
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
        addProduct() {
            if (this.editIndex !== null) {
                const product = { ...this.product };
                this.form.products[this.editIndex] = product;
                this.editIndex = null;
            } else {
                const product = { ...this.product, catalogProduct: this.selectedCatalogProduct };
                this.form.products.push(product);
                // si se agrega un producto a ultimo momento, actualizar parcialidades
            }

            if (this.form.shipping_option) {
                if (this.form.shipping_option == 'Entrega única') {
                    this.handleChangeShippingOption();
                } else {
                    this.syncPartialitiesProducts(); // Sincronizar productos
                }
            }
            //quitar formato seleccionado de auto. de diseño de la lista para que no se pueda volver seleccionar 
            // this.refreshDesignAuthorizationsList(this.product.design_authorization_id);

            // resetear variables
            this.resetProductForm();
            this.selectedCatalogProduct = null;
            this.alertMaxQuantity = 0;
        },
        syncPartialitiesProducts() {
            this.form.partialities.forEach(partiality => {
                // Crear un nuevo array de productsSelected con los productos sincronizados
                const updatedProductsSelected = [];

                // Iterar sobre los productos actuales
                this.form.products.forEach(product => {
                    // Buscar si el producto ya existe en productsSelected de la parcialidad
                    const existingProduct = partiality.productsSelected.find(p => p.id === product.catalogProduct.id);

                    // Si el producto ya existe, conservar 'selected' y 'quantity'
                    if (existingProduct) {
                        updatedProductsSelected.push({
                            ...existingProduct, // Mantener los datos capturados
                            name: product.catalogProduct.name // Actualizar el nombre si ha cambiado
                        });
                    } else {
                        // Si es un nuevo producto, agregarlo con 'selected: false' o con valores por defecto
                        updatedProductsSelected.push({
                            id: product.catalogProduct.id,
                            name: product.catalogProduct.name,
                            selected: false, // Valor por defecto para nuevos productos
                            quantity: 0 // Cantidad por defecto
                        });
                    }
                });

                // Reemplazar productsSelected con el nuevo array sincronizado
                partiality.productsSelected = updatedProductsSelected;
            });
        },
        deleteProduct(index) {
            this.form.products.splice(index, 1);
            this.syncPartialitiesProducts(); // Sincronizar productos
        },
        editProduct(index) {
            const product = { ...this.form.products[index] };
            this.product = product;
            this.editIndex = index;
            this.fetchCatalogProductData();
        },
        resetProductForm() {
            this.product.catalog_product_company_id = null;
            this.product.quantity = null;
            this.product.notes = null;
            this.product.part_number = null;
            this.product.design_authorization_id = null; //formato de autorización.
            this.product.is_new_design = false;
            this.product.confusion_alert = false;
            this.product.requires_medallion = false;
        },
        //quitar formato seleccionado de auto. de diseño de la lista para que no se pueda volver seleccionar 
        refreshDesignAuthorizationsList(designAuthorizationId) {
            const itemIndex = this.design_authorizations.findIndex(item => item.id == designAuthorizationId);
            if (itemIndex != -1) {
                this.design_authorizations.splice(itemIndex, 1);
            }
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
            return time < today;
        },
        setOpportunityIdFromUrl() {
            // Obtener la URL actual
            const currentURL = new URL(window.location.href);
            // Extraer el valor de 'opportunityId' de los parámetros de búsqueda
            const opportunityIdFromURL = currentURL.searchParams.get('opportunityId');

            if (opportunityIdFromURL) {
                this.form.opportunity_id = opportunityIdFromURL;
                this.form.company_branch_id = parseInt(this.opportunityId.company_branch_id);
                this.form.is_sale_production = 1;
            }
        },
        openCompanyEdit() {
            const url = route('companies.edit', this.company_id);
            window.open(url, '_blank');
        },
        async fetchCatalogProductData(productId = null) {
            if (productId) {
                try {
                    this.loading = true;
                    const response = await axios.get(route('catalog-products.get-info', productId));

                    if (response.status === 200) {
                        this.missingProduct = response.data.item;
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
            } else {
                this.alertMaxQuantity = 0;
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
            }
        },
        //Check if catalog product company has sales. if dont, ask for design authorization.
        async checkIfProductHasSale() {
            try {
                const response = await axios.get(route('sales.check-if-has-sale', this.product.catalog_product_company_id));
                if (response.status === 200) {
                    this.selectedCatalogProductHasSale = response.data.has_sale;
                }
            } catch (error) {
                console.log(error);
            }
        },
        async getDesignAuthorizations() {
            try {
                const response = await axios.get(route('design-authorizations.fetch-for-company-branch', this.form.company_branch_id));
                if (response.status === 200) {
                    this.design_authorizations = response.data.items;
                }
            } catch (error) {
                console.log(error);
            }
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
        async fetchCatalogProductsCompanyBanch() {
            this.loadingCompanyBranchProducts = true;
            try {
                const response = await axios.get(route('quotes.fetch-catalog-products-company-branch', this.form.company_branch_id));

                if (response.status === 200) {
                    this.catalogProductsCompanyBranchSelected = response.data.items;
                    this.company_id = response.data.companyId;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'error',
                    message: error,
                    type: 'error'
                });
            } finally {
                this.loadingCompanyBranchProducts = false;
            }
        },
    },
    mounted() {
        this.setOpportunityIdFromUrl();

        if (this.sample) {
            this.form.company_branch_id = parseInt(this.sample.company_branch_id);
        }
    }
};
</script>
