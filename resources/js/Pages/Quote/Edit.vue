<template>
    <div>
        <AppLayout title="Editar cotización">
            <template #header>
                <div class="flex justify-between">
                    <Back />
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Editar cotización</h2>
                    </div>
                </div>
            </template>
            <!-- Form -->
            <form @submit.prevent="edit" class="relative overflow-x-hidden dark:text-white">
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

                <!-- company branch productos -->
                <div class="absolute top-5 -right-1 mt-36">
                    <div v-if="catalogProductsCompanyBranchSelected?.length"
                        class="text-sm border border-[#9A9A9A] rounded-[5px] py-2 px-3 w-[550px]">
                        <h3 class="flex items-center justify-center mb-2 text-base font-bold">
                            Productos de este cliente
                        </h3>

                        <div v-if="loading" class="flex items-center justify-center mt-10">
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
                            <img class="mx-auto" w-full :src="dialogImageUrl" alt="Preview Image" />
                        </el-dialog>
                    </div>
                </div>

                <div class="md:w-full lg:w-1/2 mx-7 my-5 bg-[#D9D9D9] dark:bg-[#202020] dark:text-white rounded-lg px-9 py-5 shadow-md"
                    :class="{
                        'md:left-auto md:ml-32': catalogProductsCompanyBranchSelected?.length,
                        'md:mx-auto': !catalogProductsCompanyBranchSelected?.length
                    }">
                    <div class="md:grid gap-3 gap-y-2 mb-6 grid-cols-2">
                        <div class="col-span-2 flex justify-between mb-7">
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
                            <InputLabel>
                                <div class="flex items-center space-x-2">
                                    <span>Moneda*</span>
                                    <el-tooltip placement="top">
                                        <template #content>
                                            <p>
                                                La moneda que se elija se usará para productos <br>
                                                y costos de flete y herramental
                                            </p>
                                        </template>
                                        <div
                                            class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                                            <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                        </div>
                                    </el-tooltip>
                                </div>
                            </InputLabel>
                            <el-select v-model="form.currency" placeholder="Selecciona" :fit-input-width="true">
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
                            <InputError :message="form.errors.currency" />
                        </div>
                        <div>
                            <div>
                                <InputLabel>
                                    <div class="flex items-center space-x-2">
                                        <span>
                                            {{ form.is_customer ? 'Cliente' : 'Prospecto' }}*
                                        </span>
                                        <el-tooltip placement="top">
                                            <template #content>
                                                <p>
                                                    Para poder cotizar, los clientes (sucursales) <br>
                                                    o prospectos deben de estar registrados.
                                                </p>
                                            </template>
                                            <div
                                                class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                                                <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                            </div>
                                        </el-tooltip>
                                    </div>
                                </InputLabel>
                                <el-select v-if="form.is_customer" @change="handleSelectCompanyBranch()"
                                    v-model="form.company_branch_id" clearable filterable placeholder="Busca el cliente"
                                    no-data-text="No hay clientes registrados"
                                    no-match-text="No se encontraron coincidencias">
                                    <el-option v-for="item in company_branches" :key="item.id" :label="item.name"
                                        :value="item.id" />
                                </el-select>
                                <div v-else class="w-full flex items-center space-x-3">
                                    <el-select v-model="form.prospect_id" @change="handleSelectProspect()" clearable
                                        filterable placeholder="Busca el prospecto"
                                        no-data-text="No hay prospectos registrados"
                                        no-match-text="No se encontraron coincidencias">
                                        <el-option v-for="item in prospects" :key="item.id" :label="item.name"
                                            :value="item.id" />
                                    </el-select>
                                    <el-tooltip content="Creación rápida de prospecto" placement="top">
                                        <i @click="showProspectFormModal = true"
                                            class="fa-solid fa-circle-plus text-primary mr-2 text-lg cursor-pointer"></i>
                                    </el-tooltip>
                                </div>
                            </div>
                            <InputError :message="form.errors.company_branch_id" />
                            <InputError :message="form.errors.prospect_id" />
                        </div>
                        <div>
                            <InputLabel value="Nombre de quien recibe*" />
                            <el-input v-model="form.receiver" placeholder="Ej. Lic Manuel Avila" />
                            <InputError :message="form.errors.receiver" />
                        </div>
                        <div>
                            <InputLabel value="Departamento o puesto*" />
                            <el-input v-model="form.department" placeholder="Ej. Gerente de mercadotecnia" />
                            <InputError :message="form.errors.department" />
                        </div>
                        <div>
                            <InputLabel value="Costo de herramental*" />
                            <el-input v-model="form.tooling_cost" placeholder="Ej. 800" />
                            <InputError :message="form.errors.tooling_cost" />
                        </div>
                        <div>
                            <InputLabel value="Moneda" />
                            <el-select v-model="form.tooling_currency" placeholder="Sin moneda (texto libre)"
                                :fit-input-width="true">
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
                        </div>
                        <div class="flex items-center space-x-2 col-span-full">
                            <label class="flex items-center text-gray-600 dark:text-gray-500">
                                <input type="checkbox" v-model="form.tooling_cost_stroked"
                                    class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                                <span class="ml-2 text-sm">Tachar:</span>
                            </label>
                            <span class="text-gray-700 dark:text-gray-400 text-xs mt-1"
                                :class="form.tooling_cost_stroked ? 'line-through' : ''">
                                {{ form.tooling_cost }} {{ form.tooling_currency }}
                            </span>
                        </div>
                        <div>
                            <InputLabel value="Pago de flete*" />
                            <el-select v-model="form.freight_option" @change="handleFreightOption"
                                placeholder="Seleccionar" :fit-input-width="true">
                                <el-option v-for="item in freightOptions" :key="item" :label="item" :value="item" />
                            </el-select>
                            <InputError :message="form.errors.freight_option" />
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <InputLabel v-if="form.freight_option == 'Cargo del flete prorrateado en producto'"
                                    value="Costo de flete cargado a precio de producto*" />
                                <InputLabel
                                    v-else-if="['Cargo flete normal de costo al cliente'].includes(form.freight_option)"
                                    value="Costo de flete*" />
                                <InputLabel
                                v-else-if="['Emblems3d absorbe el costo del flete'].includes(form.freight_option)"
                                value="Costo de flete que absorbe Emblems3d*" />
                                <el-tooltip v-if="form.freight_option == 'Cargo del flete prorrateado en producto'"
                                    placement="top">
                                    <template #content>
                                        <p>
                                            Es necesario ingresar el costo del flete <br>
                                            aunque se cargue al costo del producto <br>
                                            Para contemplarlo en el reporte de gastos de envío
                                        </p>
                                    </template>
                                    <div
                                        class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                    </div>
                                </el-tooltip>
                            </div>
                            <el-input v-model="form.freight_cost"
                                v-if="form.freight_option == 'Cargo del flete prorrateado en producto' || form.freight_option == 'Cargo flete normal de costo al cliente' || form.freight_option == 'Emblems3d absorbe el costo del flete'" placeholder="Ej. 550" />
                            <InputError :message="form.errors.freight_cost" />
                        </div>
                        <div class="flex items-center space-x-2 col-span-full">
                            <label class="flex items-center text-gray-600 dark:text-gray-500">
                                <input type="checkbox" v-model="form.freight_cost_stroked"
                                    class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                                <span class="ml-2 text-sm">Tachar:</span>
                            </label>
                            <span class="text-gray-700 dark:text-gray-400 text-xs mt-1"
                                :class="form.freight_cost_stroked ? 'line-through' : ''">
                                {{ form.freight_cost }} {{ form.tooling_currency }}
                            </span>
                        </div>
                        <div>
                            <InputLabel value="Dias para primera producción*" />
                            <el-select v-model="form.first_production_days" placeholder="Selecciona">
                                <el-option v-for="(item, index) in form.is_spanish_template ? firstProductionDaysList : firstProductionDaysListEnglish" :key="item" :label="item"
                                    :value="item" />
                            </el-select>
                            <InputError :message="form.errors.first_production_days" />
                        </div>
                        <div class="col-span-full">
                            <InputLabel value="Notas" />
                            <el-input v-model="form.notes" :rows="3" maxlength="800" placeholder="..." show-word-limit
                                type="textarea" />
                            <InputError :message="form.errors.notes" />
                        </div>
                        <div class="flex items-center space-x-2 col-span-full">
                            <label class="flex items-center text-gray-600 dark:text-gray-500">
                                <input type="checkbox" v-model="form.show_breakdown"
                                    class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                                <span class="ml-2 text-sm">Mostrar total sumando productos, flete y herramental</span>
                            </label>
                        </div>
                        <el-divider content-position="left" class="col-span-full">Productos</el-divider>
                        <!-- products -->
                        <!-- Seleccion de tipo de producto -->
                        <el-radio-group class="col-span-full" v-model="productType" size="small">
                            <el-radio-button label="Producto de catálogo" value="Producto de catálogo" />
                            <el-radio-button label="Materia prima" value="Materia prima" />
                        </el-radio-group>
                        <div v-if="productType === 'Producto de catálogo'" class="col-span-full">
                            <InputLabel value="Producto de catálogo*" />
                            <el-select v-model="product.id" clearable filterable
                                placeholder="Busca el producto de catálogo" no-data-text="No hay productos registrados"
                                no-match-text="No se encontraron coincidencias">
                                <el-option class="w-[820px]" @click="handleSelectedProduct(item)"
                                    v-for="item in catalog_products" :key="item.id"
                                    :label="item.name + ' (' + item.part_number + ')'" :value="item.id" />
                            </el-select>
                        </div>
                        <div v-else class="col-span-full">
                            <InputLabel value="Materia prima*" />
                            <el-select v-model="product.id" clearable filterable
                                placeholder="Busca el producto sin componentes"
                                no-data-text="No hay productos registrados"
                                no-match-text="No se encontraron coincidencias">
                                <el-option v-for="item in raw_materials" :key="item.id"
                                    :label="item.name + ' (' + item.part_number + ')'" :value="item.id" />
                            </el-select>
                        </div>
                        <div>
                            <figure class="border border-[#9a9a9a] rounded-md min-h-20 max-h-44 w-full">
                                <img v-if="product.id"
                                    :src="catalog_products.find(p => p.id == product.id).media[0].original_url"
                                    class="object-contain min-h-20 max-h-44 rounded-md">
                                <p v-else
                                    class="flex items-center space-x-2 justify-center text-sm text-[#373737] dark:text-gray-500 mt-3">
                                    <i class="fa-solid fa-arrow-up"></i>
                                    <span>Selecciona un producto para ver imagen</span>
                                </p>
                            </figure>
                        </div>
                        <div>
                            <InputLabel value="¿Mostrar imagen en cotización?" />
                            <el-switch v-model="product.show_image" inline-prompt size="large"
                                style="--el-switch-on-color: #0355B5; --el-switch-off-color: #CCCCCC" active-text="Si"
                                inactive-text="No" />
                        </div>
                        <div>
                            <InputLabel value="Cantidad a cotizar*" />
                            <el-input v-model="product.quantity" type="text"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 500" />
                        </div>
                        <div>
                            <InputLabel value="Precio unitario*" />
                            <el-input v-model="product.price" type="text"
                                :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 16.85" />
                        </div>
                        <div v-if="isKeyChain">
                            <label class="flex items-center text-gray-600">
                                <input @change="handleRequiredMed(product.requires_med)" type="checkbox"
                                    v-model="product.requires_med"
                                    class="rounded border-gray-400 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                                <span class="ml-2 text-sm">Requiere medallón</span>
                            </label>
                        </div>
                        <div class="col-span-full">
                            <InputLabel value="Notas" />
                            <el-input v-model="product.notes" :rows="3" maxlength="800" placeholder="..."
                                show-word-limit type="textarea" />
                        </div>
                        <div>
                            <div>
                                <SecondaryButton @click="addProduct" type="button"
                                    :disabled="!product.id || !product.quantity || !product.price || form.processing">
                                    {{ editIndex !== null ? 'Actualizar producto' : 'Agregar producto a lista' }}
                                </SecondaryButton>
                            </div>
                        </div>
                        <InputError :message="form.errors.products" class="col-span-full" />
                        <ol v-if="form.products.length"
                            class="rounded-lg bg-[#CCCCCC] dark:text-black px-5 py-3 col-span-full space-y-1 divide-y-[1px]">
                            <template v-for="(item, index) in form.products" :key="index">
                                <li class="flex justify-between items-center border-[#999999] py-1">
                                    <!-- Si es producto de catalogo lo busca en esos productos -->
                                    <p v-if="item.isCatalogProduct" class="text-xs">
                                        <span class="text-primary">{{ index + 1 }}.</span>
                                        {{ catalog_products.find(prd => prd.id === item.id)?.name }}
                                        (x{{ item.quantity }} unidades) <span class="text-gray1">-> Producto de
                                            catálogo</span>
                                    </p>
                                    <!-- Si es materia prima lo busca en materias primas -->
                                    <p v-else class="text-sm">
                                        <span class="text-primary">{{ index + 1 }}.</span>
                                        {{ raw_materials.find(prd => prd.id === item.id)?.name }}
                                        (x{{ item.quantity }} unidades) <span class="text-gray1">-> Materia prima
                                        </span>
                                    </p>
                                    <div class="flex space-x-2 items-center">
                                        <el-tag v-if="editIndex == index" @close="editIndex = null; resetProductForm()"
                                            closable>En edición</el-tag>
                                        <button @click="editProduct(index)" type="button"
                                            class="size-7 bg-[#B7B4B4] rounded-full flex items-center justify-center text-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>
                                        <el-popconfirm confirm-button-text="Si" cancel-button-text="No"
                                            icon-color="#0355B5" title="¿Continuar?" @confirm="deleteProduct(index)">
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
                    <el-divider />
                    <!-- buttons -->
                    <div class="md:text-right">
                        <PrimaryButton :disabled="form.processing">
                            <i v-if="form.processing"
                                class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                            Guardar cambios
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
                                @change="calculateNewPrice()"
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
                    {{ editIMportantNotes ? 'Editar' : 'Agregar' }}
                    notas importantes para
                    {{ company_branches.find(item => item.id == form.company_branch_id).name }}
                </template>
                <template #content>
                    <div>
                        <InputLabel>
                            <div class="flex items-center space-x-2">
                                <span>Notas</span>
                                <el-tooltip placement="top">
                                    <template #content>
                                        <p>
                                            Estas notas se mostraran cuando se seleccione <br>
                                            esta sucursal para crear cotizacion, orden <br>
                                            de venta u otro movimiento
                                        </p>
                                    </template>
                                    <div
                                        class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
                                        <i class="fa-solid fa-info text-primary text-[7px]"></i>
                                    </div>
                                </el-tooltip>
                            </div>
                        </InputLabel>
                        <el-input v-model="importantNotesToStore" :rows="3" maxlength="1000"
                            placeholder="Ej. Precio acordado de 'x' producto en siguiente cotizacion $45.30"
                            show-word-limit type="textarea" />
                    </div>
                </template>
                <template #footer>
                    <CancelButton @click="showImportantNotesModal = false">Cancelar</CancelButton>
                    <PrimaryButton @click="storeImportantNotes()" :disabled="!importantNotesToStore">
                        Guardar notas
                    </PrimaryButton>
                </template>
            </DialogModal>

            <!-- prospect form -->
            <DialogModal :show="showProspectFormModal" @close="showProspectFormModal = false">
                <template #title>Creación rápida de nuevo prospecto </template>
                <template #content>
                    <form class="grid grid-cols-2 gap-3 mt-5">
                        <div>
                            <InputLabel value="Nombre de la empresa*" class="ml-3 mb-1" />
                            <el-input v-model="prospectForm.name" placeholder="Escribe el nombre de la empresa"
                                :maxlength="100" clearable />
                            <InputError :message="prospectForm.errors.name" />
                        </div>
                        <div>
                            <InputLabel value="Nombre del contacto*" class="ml-3 mb-1" />
                            <el-input v-model="prospectForm.contact_name" placeholder="Escribe el nombre del contacto"
                                :maxlength="100" clearable />
                            <InputError :message="prospectForm.errors.contact_name" />
                        </div>
                        <div>
                            <InputLabel value="Puesto*" class="ml-3 mb-1" />
                            <el-input v-model="prospectForm.contact_charge" placeholder="Ej. Supervisor"
                                :maxlength="100" clearable />
                            <InputError :message="prospectForm.errors.contact_charge" />
                        </div>
                        <div>
                            <InputLabel value="Correo electrónico*" class="ml-3 mb-1" />
                            <el-input v-model="prospectForm.contact_email"
                                placeholder="Escribe el correo electrónico del contacto" :maxlength="100" required
                                clearable />
                            <InputError :message="prospectForm.errors.contact_email" />
                        </div>
                        <div>
                            <InputLabel value="Teléfono*" class="ml-3 mb-1" />
                            <el-input v-model="prospectForm.contact_phone" :formatter="(value) => `${value}`.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3')
                                " :parser="(value) => value.replace(/\D/g, '')" maxlength="10" clearable
                                placeholder="Escribe el número de teléfono del contacto" />
                            <InputError :message="prospectForm.errors.contact_phone" />
                        </div>
                    </form>
                </template>
                <template #footer>
                    <div class="flex items-center space-x-2">
                        <CancelButton @click="showProspectFormModal = false" :disabled="prospectForm.processing">
                            Cancelar
                        </CancelButton>
                        <PrimaryButton @click="storeProspect()" :disabled="prospectForm.processing">
                            <i v-if="prospectForm.processing"
                                class="fa-sharp fa-solid fa-circle-notch fa-spin mr-2 text-white"></i>
                            Crear
                        </PrimaryButton>
                    </div>
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
import IconInput from "@/Components/MyComponents/IconInput.vue";
import DialogModal from "@/Components/DialogModal.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Back from "@/Components/MyComponents/Back.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { formatDistanceToNow } from 'date-fns'
import { differenceInMonths, differenceInDays, format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from "axios";

export default {
    data() {
        const form = useForm({
            receiver: this.quote.receiver,
            department: this.quote.department,
            tooling_cost: this.quote.tooling_cost,
            tooling_cost_stroked: Boolean(this.quote.tooling_cost_stroked),
            freight_cost_stroked: Boolean(this.quote.freight_cost_stroked),
            tooling_currency: this.quote.tooling_currency,
            freight_cost: this.quote.freight_cost,
            freight_option: this.quote.freight_option,
            freight_cost_charged_in_product: !!this.quote.freight_cost_charged_in_product,
            first_production_days: this.quote.first_production_days,
            notes: this.quote.notes,
            currency: this.quote.currency,
            is_spanish_template: this.quote.is_spanish_template,
            show_breakdown: !!this.quote.show_breakdown,
            company_branch_id: this.quote.company_branch_id,
            is_customer: this.quote.company_branch_id ? 1 : 0,
            prospect_id: this.quote.prospect_id,
            products: [],
        });

        const prospectForm = useForm({
            name: null,
            contact_name: null,
            contact_charge: null,
            contact_email: null,
            contact_phone: null,
            status: "Contacto inicial",
            quick_creation: true,
            abstract: "Agregado en creación rápida en formulario de creación de cotización",
        });

        const priceForm = useForm({
            new_price: null,
            new_currency: null,
            product_company_id: null,
        });

        const scheduleForm = useForm({
            title: null,
            start_date: null,
            description: null,
        });

        return {
            form,
            priceForm,
            prospectForm,
            scheduleForm,
            new_price_percentage: 5, //porcentaje de aumento de precio
            isKeyChain: false, //bandera para saber si es llavero y activar el check de requiere medallon
            editIndex: null,
            productType: "Producto de catálogo",
            importantNotes: null,
            showImportantNotesModal: false,
            importantNotesToStore: null,
            isEditImportantNotes: false,
            showProspectFormModal: false,
            dialogVisible: false, //imagen element-plus
            catalogProductsCompanyBranchSelected: null, //productos de cliente seleccionado
            showScheduleUpdatingPrice: false, //modal para agendar actualización de precio
            showUpdatePriceModal: false, //modal para actualizar precio
            product: {
                id: null,
                quantity: null,
                price: null,
                isCatalogProduct: null,
                show_image: true,
                requires_med: false, //bandera para guardar si el llavero requiero medallon
                notes: null,
            },
            firstProductionDaysList: [
                'Inmediata',
                '1 a 2 días',
                '2 a 3 días',
                '3 a 4 días',
                '4 a 5 días',
                '5 a 6 días',
                '1 a 2 semanas',
                '2 a 3 semanas',
                '3 a 4 semanas',
                '4 a 5 semanas',
                '5 a 6 semanas',
            ],
            firstProductionDaysListEnglish: [
                'Immediate',
                '1 to 2 days',
                '2 to 3 days',
                '3 to 4 days',
                '4 to 5 days',
                '5 to 6 days',
                '1 to 2 weeks',
                '2 to 3 weeks',
                '3 to 4 weeks',
                '4 to 5 weeks',
                '5 to 6 weeks',
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
            freightOptions: [
                'Cargo flete normal de costo al cliente',
                'Cargo del flete prorrateado en producto',
                'Emblems3d absorbe el costo del flete',
                'El cliente envía la guía',
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
            newPriceCurrencies: [
                { value: "$MXN", label: "MXN" },
                { value: "$USD", label: "USD" },
            ],
        };
    },
    components: {
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        InputError,
        InputLabel,
        IconInput,
        CancelButton,
        DialogModal,
        Back,
        Link
    },
    props: {
        catalog_products: Array,
        raw_materials: Array,
        company_branches: Array,
        quote: Object,
        prospects: Array,
    },
    methods: {
        calculateNewPrice() {
            // factor para calcular porcentaje del precio
            const factor = 1 + this.new_price_percentage * .01;
            // guarda el precio calculado con el porcentaje seleccionado
            this.priceForm.new_price = (factor * this.itemToUpdatePrice.pivot.new_price).toFixed(2);
        },
        calculateNewPercentage() {
            if (!this.priceForm.new_price || !this.itemToUpdatePrice.pivot.new_price) {
                this.new_price_percentage = 0;
                return;
            }

            // Convierte los valores a número
            const oldPrice = parseFloat(this.itemToUpdatePrice.pivot.new_price);
            const newPrice = parseFloat(this.priceForm.new_price);

            // Calcula el porcentaje de aumento
            this.new_price_percentage = (((newPrice / oldPrice) - 1) * 100).toFixed(2);
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
        formatDate(date) {
            if (date) {
                const parsedDate = new Date(date);
                return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
            }
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
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.original_url;
            this.dialogVisible = true;
        },
        disabledDate(time) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return time.getTime() < today.getTime();
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
        handleScheduleUpdateProductPrice(catalogProduct) {
            //agregar titulo predeterminado
            this.scheduleForm.title = 'Cambiar precio a ' + catalogProduct.name
            this.showScheduleUpdatingPrice = true;
        },
        handleSelectCompanyBranch() {
            this.getImportantNotes();

            if (this.form.company_branch_id) {
                this.fetchCatalogProductsCompanyBanch();
            } else {
                this.catalogProductsCompanyBranchSelected = null;
            }
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
        async fetchCatalogProductsCompanyBanch() {
            this.loading = true;
            try {
                const response = await axios.get(route('quotes.fetch-catalog-products-company-branch', this.form.company_branch_id));

                if (response.status === 200) {
                    this.catalogProductsCompanyBranchSelected = response.data.items;
                }
            } catch (error) {
                console.log(error);
                this.$notify({
                    title: 'error',
                    message: error,
                    type: 'error'
                });
            } finally {
                this.loading = false;
            }
        },
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
        handleSelectedProduct(product) {
            if (product.part_number.includes("LL")) {
                this.isKeyChain = true;
            } else {
                this.isKeyChain = false;
            }
        },
        handleFreightOption() {
            if (this.form.freight_option == 'El cliente envía la guía' || this.form.freight_option == 'Emblems3d absorbe el costo del flete') {
                this.form.freight_cost = 0;
            } else {
                this.form.freight_cost = null;
            }
        },
        handleRequiredMed(requires_med) {
            if (requires_med) {
                this.product.notes = 'Incluye medallón'
            } else {
                this.product.notes = null;
            }
        },
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
        storeProspect() {
            this.prospectForm.post(route("prospects.store"), {
                onSuccess: () => {
                    this.$notify({
                        title: "Éxito",
                        message: "Nueva prospecto registrado",
                        type: "success",
                    });

                    this.showProspectFormModal = false;
                    this.prospectForm.reset();
                },
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

            //agrega una bandera para saber si es producto de catalogo o materia prima
            if (this.productType === 'Producto de catálogo') {
                product.isCatalogProduct = true;
            } else {
                product.isCatalogProduct = false;
            }

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

            //revisa si el producto es llavero para mostrar el check de requiere medallon.
            const product_part_number = this.catalog_products.find(item => item.id === this.product.id)?.part_number;
            if (product_part_number.includes("LL")) {
                this.isKeyChain = true;
            } else {
                this.isKeyChain = false;
            }
            this.editIndex = index;
        },
        resetProductForm() {
            this.product.id = null;
            this.product.quantity = null;
            this.product.notes = null;
            this.product.price = null;
            this.product.show_image = true;
            this.product.requires_med = false;
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
    },
    mounted() {
        //Carga al formulario los productos de catalogo de la cotizacion
        this.quote.catalog_products.forEach(element => {
            const product = {
                id: element.pivot.catalog_product_id,
                quantity: element.pivot.quantity,
                isCatalogProduct: true,
                notes: element.pivot.notes,
                price: element.pivot.price,
                show_image: Boolean(element.pivot.show_image),
                requires_med: Boolean(element.pivot.requires_med),
            }

            this.form.products.push(product);
        });

        //Carga al formulario las materias primas de la cotizacion
        this.quote.raw_materials.forEach(element => {
            const product = {
                id: element.pivot.raw_material_id,
                quantity: element.pivot.quantity,
                isCatalogProduct: false,
                notes: element.pivot.notes,
                price: element.pivot.price,
                show_image: Boolean(element.pivot.show_image),
            }

            this.form.products.push(product);
        });
        this.getImportantNotes();
        this.fetchCatalogProductsCompanyBanch();
    }
};
</script>
