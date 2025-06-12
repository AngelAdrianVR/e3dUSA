<template>
    <div v-if="loading" class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
    </div>
    <div v-if="loading"
        class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
        <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
    </div>
    <AppLayout title="Cotizaciones">
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl leading-tight">
                    Cotizaciones
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')"
                    :href="route('quotes.create')">
                <SecondaryButton>+ Nuevo</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="relative overflow-hidden min-h-[60vh] dark:text-white">
            <div class="flex justify-center space-x-5">
                <p class="mr-2 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle text-green-500"></i>
                    <span>Clilentes</span>
                </p>
                <p class="mr-2 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-circle text-blue-500"></i>
                    <span>Prospectos</span>
                </p>
            </div>
            <div class="flex justify-center space-x-5 mt-1">
                <p class="mr-2 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-square text-orange-400"></i>
                    <span>Creado por el cliente sin autorización</span>
                </p>
                <p class="mr-2 text-xs flex items-center space-x-2">
                    <i class="fa-solid fa-square text-green-400"></i>
                    <span>Creado por el cliente autorizado</span>
                </p>
            </div>
            <div class="flex justify-center space-x-5 mt-1">
                <p class="mr-2 text-xs flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 text-amber-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <span>Editar cotización para autorizar</span>
                </p>
            </div>
            <NotificationCenter module="quote" />
            <div class="lg:w-[93%] mx-auto mt-6">

                <div class="flex justify-between">
                    <!-- pagination -->
                    <div v-if="!search" class="overflow-auto mb-2">
                        <PaginationWithNoMeta :pagination="quotes" class="py-2" />
                    </div>

                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar cotizaciones')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>

                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>
                <el-table :data="filteredQuotes" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="folio" label="Folio" width="135">
                        <template #default="scope">
                            <div class="flex items-center space-x-1">
                                <el-tooltip v-if="scope.row.quote_acepted" placement="top">
                                    <template #content>
                                        <p>
                                            El cliente firmó la cotización <br>
                                            el {{ formatDate(scope.row.responded_at) }}
                                        </p>
                                    </template>
                                    <i class="fa-solid fa-check text-base text-green-700 mr-1"></i>
                                </el-tooltip>
                                <el-tooltip v-else-if="scope.row.rejected_razon" placement="top">
                                    <template #content>
                                        <p>
                                            El cliente rechazó la cotización <br>
                                            el {{ formatDate(scope.row.responded_at) }} <br>
                                            Motivo: <b>{{ scope.row.rejected_razon }}</b>
                                        </p>
                                    </template>
                                    <i class="fa-solid fa-xmark text-xs text-red-700 mr-1"></i>
                                </el-tooltip>
                                <el-tooltip
                                    v-if="scope.row.early_payment_discount && $page.props.auth.user.permissions.includes('Descuentos cotizaciones')"
                                    placement="top">
                                    <template #content>
                                        <p v-if="scope.row.early_paid_at">
                                            Descuento de pago por anticipado aplicado: %{{ scope.row.discount }} <br>
                                            <span>Pagado el: <strong class="text-blue-400">{{
                                                    formatDate(scope.row.early_paid_at) }}</strong></span>
                                        </p>
                                        <p v-else>
                                            Descuento de pago por anticipado: %{{ scope.row.discount }}
                                        </p>
                                    </template>
                                    <svg v-if="scope.row.early_paid_at" class="mr-1" width="16" height="19"
                                        viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.15527 0C9.57271 0.953908 11.3524 2.23003 12.2705 7.08301C12.6323 7.25786 13.0912 6.56184 13.9756 5.11523C18.2995 13.5451 15.0251 18.2319 10.4346 18.3633H5.97461C2.30572 18.3633 -0.842483 14.5589 0.203125 8.52539C1.11282 9.8512 1.48847 9.99154 2.04004 9.70605C0.85957 5.11534 8.598 4.06604 7.15527 0ZM10.5732 6.05664C10.3949 5.99143 10.1997 6.06337 10.1035 6.21875L10.0684 6.29102C9.30024 8.39053 8.70097 10.0296 8.10156 11.668L6.13379 17.0459C6.05912 17.25 6.16407 17.4761 6.36816 17.5508C6.54675 17.616 6.74192 17.5436 6.83789 17.3877L6.87305 17.3164C7.64129 15.2165 8.24131 13.5771 8.84082 11.9385L10.8076 6.56055C10.8817 6.35675 10.7769 6.13129 10.5732 6.05664ZM10.7002 12.0654C9.68635 12.0656 8.86447 12.8885 8.86426 13.9023C8.86438 14.9163 9.6863 15.7381 10.7002 15.7383C11.7141 15.7381 12.537 14.9163 12.5371 13.9023C12.5369 12.8885 11.714 12.0656 10.7002 12.0654ZM10.7002 12.8525C11.2794 12.8528 11.7498 13.3231 11.75 13.9023C11.7499 14.4816 11.2795 14.951 10.7002 14.9512C10.1209 14.951 9.65149 14.4816 9.65137 13.9023C9.65158 13.3231 10.121 12.8528 10.7002 12.8525ZM6.50293 7.60645C5.48915 7.60667 4.6673 8.42863 4.66699 9.44238C4.66699 10.4564 5.48897 11.2791 6.50293 11.2793C7.5169 11.2791 8.33984 10.4564 8.33984 9.44238C8.33954 8.42862 7.51671 7.60666 6.50293 7.60645ZM6.50293 8.39355C7.08207 8.39377 7.55243 8.86326 7.55273 9.44238C7.55273 10.0218 7.08226 10.492 6.50293 10.4922C5.9236 10.492 5.4541 10.0218 5.4541 9.44238C5.45441 8.86326 5.92379 8.39378 6.50293 8.39355Z"
                                            fill="#22c55e" />
                                    </svg>
                                    <svg v-else class="mr-1" width="16" height="19" viewBox="0 0 16 19" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.15527 0C9.57271 0.953908 11.3524 2.23003 12.2705 7.08301C12.6323 7.25786 13.0912 6.56184 13.9756 5.11523C18.2995 13.5451 15.0251 18.2319 10.4346 18.3633H5.97461C2.30572 18.3633 -0.842483 14.5589 0.203125 8.52539C1.11282 9.8512 1.48847 9.99154 2.04004 9.70605C0.85957 5.11534 8.598 4.06604 7.15527 0ZM10.5732 6.05664C10.3949 5.99143 10.1997 6.06337 10.1035 6.21875L10.0684 6.29102C9.30024 8.39053 8.70097 10.0296 8.10156 11.668L6.13379 17.0459C6.05912 17.25 6.16407 17.4761 6.36816 17.5508C6.54675 17.616 6.74192 17.5436 6.83789 17.3877L6.87305 17.3164C7.64129 15.2165 8.24131 13.5771 8.84082 11.9385L10.8076 6.56055C10.8817 6.35675 10.7769 6.13129 10.5732 6.05664ZM10.7002 12.0654C9.68635 12.0656 8.86447 12.8885 8.86426 13.9023C8.86438 14.9163 9.6863 15.7381 10.7002 15.7383C11.7141 15.7381 12.537 14.9163 12.5371 13.9023C12.5369 12.8885 11.714 12.0656 10.7002 12.0654ZM10.7002 12.8525C11.2794 12.8528 11.7498 13.3231 11.75 13.9023C11.7499 14.4816 11.2795 14.951 10.7002 14.9512C10.1209 14.951 9.65149 14.4816 9.65137 13.9023C9.65158 13.3231 10.121 12.8528 10.7002 12.8525ZM6.50293 7.60645C5.48915 7.60667 4.6673 8.42863 4.66699 9.44238C4.66699 10.4564 5.48897 11.2791 6.50293 11.2793C7.5169 11.2791 8.33984 10.4564 8.33984 9.44238C8.33954 8.42862 7.51671 7.60666 6.50293 7.60645ZM6.50293 8.39355C7.08207 8.39377 7.55243 8.86326 7.55273 9.44238C7.55273 10.0218 7.08226 10.492 6.50293 10.4922C5.9236 10.492 5.4541 10.0218 5.4541 9.44238C5.45441 8.86326 5.92379 8.39378 6.50293 8.39355Z"
                                            fill="#BC0B0B" />
                                    </svg>
                                </el-tooltip>
                                <span>{{ scope.row.folio }}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="Orden de venta" width="100">
                        <template #default="scope">
                            <div>
                                <p v-if="scope.row.sale_id" @click.stop="handleShowSale(scope.row.sale_id)"
                                    class="text-blue-400 dark:text-blue-300 hover:underline">OV-{{
                                        String(scope.row.sale_id).padStart('0', 4) }}</p>
                                <p v-else>N/A</p>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column v-if="$page.props.auth.user.permissions.includes('Ver utilidades')"
                        label="Utilidad" width="130">
                        <template #default="scope">
                            <SaleProfit :profit="scope.row.profit" />
                        </template>
                    </el-table-column>
                    <el-table-column prop="user.name" label="Creado por">
                        <template #default="scope">
                            <div v-if="scope.row.created_by_customer" class="flex items-center space-x-2">
                                <el-tooltip v-if="scope.row.user?.name" placement="top">
                                    <template #content>
                                        <p v-if="scope.row.authorized_at">
                                            Revisado por: {{ scope.row.user?.name }} y autorizado
                                        </p>
                                        <p v-else>
                                            Revisado por: {{ scope.row.user?.name }} sin autorización
                                        </p>
                                    </template>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </el-tooltip>
                                <el-tooltip v-else placement="top">
                                    <template #content>
                                        <p>
                                            Editar cotización para autorizar
                                        </p>
                                    </template>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                </el-tooltip>
                                <p>{{ 'Solicitado desde portal de clientes' }}</p>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column width="100" prop="receiver" label="Receptor" />
                    <el-table-column width="175" prop="companyBranch.name" label="Cliente/Prospecto">
                        <template #default="scope">
                            <div class="flex">
                                <p class="mr-2 mt-px text-[6px]"
                                    :class="scope.row.companyBranch.name ? 'text-green-500' : 'text-blue-500'">
                                    <i class="fa-solid fa-circle"></i>
                                </p>
                                <span>{{ scope.row.companyBranch.name ?? scope.row.prospect.name }}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column width="120" prop="authorized_user_name" label="Autorizado por" />
                    <el-table-column label="Productos" width="210">
                        <template v-slot="scope">
                            <p v-for="(p, index) in scope.row.catalog_products.map(product => product.name)"
                                :key="index">
                                • {{ p }}
                            </p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="created_at" label="Creado el" width="150" />
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar cotizaciones')
                                            || scope.row.user.id == $page.props.auth.user.id"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')"
                                            :command="'clone-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                            </svg>
                                            Clonar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta') && !scope.row.sale_id"
                                            :command="'make_so-' + scope.row.id" :disabled="!scope.row.authorized_at">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                                            </svg>
                                            Convertir a OV
                                        </el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Autorizar cotizaciones') && !scope.row.authorized_at"
                                            :disabled="!scope.row.user?.name" :command="'authorize-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            Autorizar
                                        </el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="scope.row.early_payment_discount && !scope.row.early_paid_at"
                                            :disabled="!scope.row.user?.name" :command="'earlyPayment-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                            </svg>
                                            Marcar pago anticipado
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>

        <ConfirmationModal :show="showConversionConfirm" @close="showConversionConfirm = true">
            <template #title>
                <h1>Convertir cotización a orden de venta</h1>
            </template>
            <template #content>
                <p>
                    Al hacer la conversión se te redireccionará a la OV creada ya que
                    quedarán varios campos sin llenar. <br>
                    Es importante llenar toda la información correspondiente, para evitar
                    futuros problemas.
                </p>
                <div class="flex mt-3">
                    <p class="mr-2 mt-px text-[6px] text-blue-500">
                        <i class="fa-solid fa-circle"></i>
                    </p>
                    <p>
                        Si la cotización fue creada para un prospecto, se convertirá a cliente en automático, lo cual
                        dejará también información pendiente del cliente por llenar.
                    </p>
                </div>
            </template>
            <template #footer>
                <div class="flex items-center justify-end space-x-1">
                    <CancelButton @click="showConversionConfirm = false" :disabled="converting">Cancelar</CancelButton>
                    <PrimaryButton @click="createSO" :disabled="converting">Convertir</PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>
<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import LoadingLogo from "@/Components/MyComponents/LoadingLogo.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import SaleProfit from "@/Components/MyComponents/SaleProfit.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CancelButton from '@/Components/MyComponents/CancelButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    components: {
        PaginationWithNoMeta,
        NotificationCenter,
        SecondaryButton,
        PrimaryButton,
        CancelButton,
        IndexSearchBar,
        LoadingLogo,
        SaleProfit,
        AppLayout,
        Link,
        ConfirmationModal,
    },
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            loading: false,
            pagination: null,
            filteredQuotes: this.quotes.data,
            showConversionConfirm: false,
            converting: false,
            selectedQuoteId: null,
        };
    },
    props: {
        quotes: {
            type: Object,
            default: []
        },
    },
    methods: {
        handleShowSale(saleId) {
            const url = this.route('sales.show', saleId);
            window.open(url, '_blank');
        },
        formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMM, Y  • H:mm a', { locale: es }); // Formato personalizado
        },
        async handleSearch(search) {
            this.search = search;
            this.loading = true;
            try {
                if (!this.search) {
                    this.filteredQuotes = this.quotes.data;
                } else {
                    const response = await axios.post(route('quotes.get-matches', { query: this.search }));

                    if (response.status === 200) {
                        this.filteredQuotes = response.data.items;
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('quotes.massive-delete', {
                    quotes: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.quotes.data.forEach((quote, index) => {
                        if (this.$refs.multipleTableRef.value.includes(quote)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.quotes.data.splice(index, 1);
                    }

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        handleRowClick(row) {
            // const url = this.route('quotes.show', row);
            // window.open(url, '_blank');
            this.$inertia.get(route('quotes.show', row.id));
        },
        tableRowClassName({ row, rowIndex }) {
            if (row.created_by_customer) {
                if (row.authorized_at) {
                    return 'cursor-pointer text-xs dark:!bg-green-600 !bg-green-200'
                } else {
                    return 'cursor-pointer text-xs dark:!bg-orange-500 !bg-orange-200'
                }
            } else {
                return 'cursor-pointer text-xs';
            }
        },
        async clone(quote_id) {
            try {
                const response = await axios.post(route('quotes.clone', {
                    quote_id: quote_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.quotes.data.unshift(response.data.newItem);

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        async createSO() {
            try {
                this.converting = true;
                const response = await axios.post(route('quotes.create-so', {
                    quote_id: this.selectedQuoteId
                }));

                if (response.status == 200) {
                    this.$inertia.visit(route('sales.edit', response.data.sale_id));
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }
            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        async authorize(quote_id) {
            try {
                const response = await axios.put(route('quotes.authorize', quote_id));

                if (response.status == 200) {
                    const index = this.quotes.data.findIndex(item => item.id == quote_id);
                    this.quotes.data[index].authorized_at = response.data.item.authorized_at;
                    this.quotes.data[index].authorized_user_name = response.data.item.authorized_user_name;
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }
            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        async markEarlyPayment(quote_id) {
            try {
                const response = await axios.put(route('quotes.mark-early-payment', quote_id));

                if (response.status == 200) {
                    const index = this.quotes.data.findIndex(item => item.id == quote_id);
                    this.quotes.data[index].early_paid_at = response.data.quote.early_paid_at;
                    this.$notify({
                        title: 'Éxito',
                        message: 'Pago anticipado marcado',
                        type: 'success'
                    });
                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }
            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                this.selectedQuoteId = rowId;
                this.showConversionConfirm = true;
            } else if (commandName == 'authorize') {
                this.authorize(rowId);
            } else if (commandName == 'earlyPayment') {
                this.markEarlyPayment(rowId);
            } else {
                this.$inertia.get(route('quotes.' + commandName, rowId));
            }
        },
    },
}
</script>