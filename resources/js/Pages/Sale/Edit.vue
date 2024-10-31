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
            <form @submit.prevent="handleUpdateSale" class="relative overflow-x-hidden dark:text-white">
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
                <div class="md:w-1/2 md:mx-auto mx-3 my-5 bg-[#D9D9D9] dark:bg-[#202020] rounded-lg p-9 shadow-md">
                    <el-radio-group v-model="form.is_sale_production" size="small">
                        <el-radio :value="1">Orden de venta</el-radio>
                        <el-radio :value="0">Orden de stock</el-radio>
                    </el-radio-group>
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <div>
                            <InputLabel value="Cliente*" />
                            <el-select @change="handleCompanyBranchIdChange" v-model="form.company_branch_id" clearable
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
                                        </div>
                                    </div>
                                    <div class="mt-5">
                                        <SecondaryButton @click="addProduct"
                                            :disabled="form.processing || !product.catalog_product_company_id || !product.quantity">
                                            {{ editIndex !== null
                                                ? 'Actualizar producto'
                                                : 'Agregar producto a lista' }}
                                        </SecondaryButton>
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
                                            <el-tag v-if="editIndex == index"
                                                @close="editIndex = null; resetProductForm()" closable>En
                                                edición</el-tag>
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
                        </div>
                    </section>
                    <!-- logistica -->
                    <section v-if="form.is_sale_production">
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
                                class="md:grid grid-cols-2 gap-3 mt-3">
                                <h2 v-if="form.shipping_option != 'Entrega única'" class="mt-3 col-span-full font-bold">
                                    Parcialidad {{ (index + 1) }}
                                </h2>
                                <div>
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
                                <div>
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
                                            :disabled="!partiality.productsSelected[index2].selected" />
                                    </div>
                                </div>

                                <div class="flex space-x-2 bg-yellow-200 pl-3">
                                    <p class="text-[#999999] w-48">Cantidad de cajas:</p>
                                    <p class="text-black">{{ totalBoxes[index] ?? '- Sin información -' }}</p>
                                </div>

                                <div class="flex space-x-2 bg-yellow-200 pl-3">
                                    <p class="text-[#999999] w-48">Costo total de envío:</p>
                                    <p class="text-black">${{ totalCost[index] ?? '- Sin información -' }}</p>
                                </div>

                                <h2 v-if="form.products.length" class="ml-2 mt-6 font-bold">
                                    Detalles sobre las cajas
                                </h2>
                                <ShippingCard class="col-span-full"
                                    v-for="(shippProduct, index3) in partiality.productsSelected.filter(p => p.selected)"
                                    :key="index3"
                                    :product="form.products.find(e => e.catalogProduct.name == shippProduct.name).catalogProduct"
                                    :quantity="shippProduct.quantity"
                                    @total-boxes="totalBoxes[index] = totalBoxes[index] + $event"
                                    @total-cost="totalCost[index] = totalCost[index] + $event" />
                            </div>
                        </div>
                    </section>
                    <!-- Datos de la orden -->
                    <section v-if="form.is_sale_production">
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
                                <InputLabel value="Archivo" />
                                <FileUploader @files-selected="handleMediaSelected" :multiple="false"
                                    :existingFileUrls="media_oce_url ? [media_oce_url] : []" />
                                <p class="mt-1 text-xs text-right text-gray-500" id="file_input_help">
                                    PDF, PNG, JPG,(MAX 4 GB)
                                </p>
                            </div>
                        </div>
                    </section>
                    <div class="mt-10 mx-3 md:text-right">
                        <PrimaryButton
                            :disabled="form.processing || !form.products.length || (form.is_sale_production && !form.partialities.length)">
                            Guardar cambios
                        </PrimaryButton>
                    </div>
                </div>
            </form>

            <!-- Modal para crear notas -->
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
                    <CancelButton @click="handleUpdate(false)">No crear</CancelButton>
                    <PrimaryButton @click="handleUpdate(true)">Crear recordatorio</PrimaryButton>
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
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import FileUploader from "@/Components/MyComponents/FileUploader.vue";
import ShippingCard from "@/Components/MyComponents/Shipping/ShippingCard.vue";
import InputLabel from "@/Components/InputLabel.vue";

export default {
    data() {
        const form = useForm({
            company_branch_id: this.sale.company_branch_id,
            contact_id: this.sale.contact_id,
            shipping_option: this.sale.shipping_option,
            freight_cost: this.sale.freight_cost,
            // shipping_company: this.sale.shipping_company,
            // tracking_guide: this.sale.tracking_guide,
            // promise_date: this.sale.promise_date,
            invoice: this.sale.invoice,
            oce_name: this.sale.oce_name,
            order_via: this.sale.order_via,
            notes: this.sale.notes,
            is_high_priority: Boolean(this.sale.is_high_priority),
            products: [],
            media: null,
            partialities: this.sale.partialities,
            is_sale_production: this.sale.is_sale_production,
            create_calendar_task: false //bandera para crear tarea en calendario de embarque de venta
        });

        return {
            form,
            loading: false,
            importantNotes: null,
            showImportantNotesModal: false,
            showCalendarTaskModal: false,
            importantNotesToStore: null,
            isEditImportantNotes: false,
            showCreateProjectModal: false,
            availableStock: null,
            product: {
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
            mediaEdited: false,
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
            totalBoxes: [0],
            totalCost: [0],
        };
    },
    components: {
        SecondaryButton,
        PrimaryButton,
        FileUploader,
        ShippingCard,
        CancelButton,
        DialogModal,
        InputError,
        InputLabel,
        AppLayout,
        Checkbox,
        Back,
        Link,
    },
    props: {
        company_branches: Array,
        catalog_products_company_sale: Array,
        sale: Object,
        media: Array,
        media_oce_url: String,
    },
    watch: {
        'form.partialities'() {
            this.totalBoxes = new Array(this.form.partialities?.length).fill(0);
            this.totalCost = new Array(this.form.partialities?.length).fill(0);
        },
    },
    methods: {
        handleUpdateSale() {
            if (this.form.partialities.some(item => item.promise_date !== null)) {
                this.showCalendarTaskModal = true;
            } else {
                this.update();
            }
        },
        handleUpdate(create_calendar_task) {
            this.form.create_calendar_task = create_calendar_task;
            this.update();
        },
        handleMediaSelected(files, mediaUpdated) {
            this.form.media = files;
            this.mediaEdited = mediaUpdated;
        },
        handleCompanyBranchIdChange() {
            this.getImportantNotes();
            this.getDesignAuthorizations();
            this.cleanShippingData();
        },
        handleChangeShippingOption() {
            this.form.partialities = [];
            const numberOfShippings = this.shippingOptions.findIndex(i => i === this.form.shipping_option) + 1;
            for (let index = 0; index < numberOfShippings; index++) {
                this.addPartial(numberOfShippings == 1);
            }
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
            if (this.mediaEdited) {
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
                this.form.media = null;
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
            this.product.id = null;
            this.product.catalog_product_company_id = null;
            this.product.quantity = null;
            this.product.notes = null;
            this.product.part_number = null;
            this.product.is_new_design = false;
            this.product.requires_medallion = false;
            this.product.confusion_alert = false;
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Establece la hora a las 00:00:00.000
            return time < today;
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
        async getCatalogProduct(PartNumber) {
            try {
                const response = await axios.get(route('catalog-products.get-by-part-number', PartNumber));

                if (response.status === 200) {
                    return response.data.item;
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
    },
    mounted() {
        this.catalog_products_company_sale.forEach(element => {
            const product = {
                id: element.id,
                catalogProduct: element.catalog_product_company.catalog_product,
                catalog_product_company_id: element.catalog_product_company_id,
                quantity: element.quantity,
                notes: element.notes,
                part_number: element.catalog_product_company.catalog_product.part_number,
                is_new_design: Boolean(element.is_new_design),
                requires_medallion: Boolean(element.requires_medallion),
                confusion_alert: Boolean(element.confusion_alert),
            }
            this.form.products.push(product);
        });

        this.form.partialities.forEach(partiality => {
            partiality.productsSelected.forEach(prd => {
                prd.selected = prd.selected == '1' ? true : false
            });
        });

        this.getImportantNotes();
    }
};
</script>
