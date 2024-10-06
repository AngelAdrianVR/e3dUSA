<template>
    <AppLayout title="Logística">
        <template #header>
            <div class="flex justify-between mt-4">
                <h2 class="font-semibold text-xl leading-tight">Logística</h2>
                
                <!-- <div class="flex space-x-2">
                    <button class="rounded-full bg-[#D9D9D9] text-[#4B5563] size-8 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </button>
                </div> -->
            </div>
        </template>

        <div class="flex space-x-6 items-center justify-center text-xs">
            <p class="flex items-center space-x-2">
                <svg width="16" height="16" class="size-5" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_13713_230" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
                        <rect width="16" height="16" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_13713_230)">
                    <path d="M12.433 6.26634L12.933 5.76634L11.6663 4.53301V2.66634H10.9997V4.79967L12.433 6.26634ZM2.66634 14.6663C2.47745 14.6663 2.31912 14.6025 2.19134 14.4747C2.06356 14.3469 1.99967 14.1886 1.99967 13.9997V12.633C1.77745 12.3997 1.61079 12.1413 1.49967 11.858C1.38856 11.5747 1.33301 11.2886 1.33301 10.9997V4.66634C1.33301 3.67745 1.79134 2.98579 2.70801 2.59134C3.62467 2.1969 5.22745 1.99967 7.51634 1.99967C7.3719 2.19967 7.2469 2.41079 7.14134 2.63301C7.03579 2.85523 6.94412 3.08301 6.86634 3.31634C5.73301 3.33856 4.85801 3.40523 4.24134 3.51634C3.62467 3.62745 3.19967 3.78856 2.96634 3.99967H6.71634C6.68301 4.2219 6.66634 4.44412 6.66634 4.66634C6.66634 4.88856 6.68301 5.11079 6.71634 5.33301H2.66634V7.33301H7.51634C7.70523 7.59967 7.91634 7.8469 8.14967 8.07467C8.38301 8.30245 8.64412 8.49967 8.93301 8.66634H2.66634V10.6663C2.66634 11.033 2.7969 11.3469 3.05801 11.608C3.31912 11.8691 3.63301 11.9997 3.99967 11.9997H9.33301C9.69967 11.9997 10.0136 11.8691 10.2747 11.608C10.5358 11.3469 10.6663 11.033 10.6663 10.6663V9.28301C10.8886 9.31634 11.1108 9.33301 11.333 9.33301C11.5552 9.33301 11.7775 9.31634 11.9997 9.28301V10.9997C11.9997 11.2886 11.9441 11.5747 11.833 11.858C11.7219 12.1413 11.5552 12.3997 11.333 12.633V13.9997C11.333 14.1886 11.2691 14.3469 11.1413 14.4747C11.0136 14.6025 10.8552 14.6663 10.6663 14.6663H9.99967C9.81079 14.6663 9.65245 14.6025 9.52467 14.4747C9.3969 14.3469 9.33301 14.1886 9.33301 13.9997V13.333H3.99967V13.9997C3.99967 14.1886 3.93579 14.3469 3.80801 14.4747C3.68023 14.6025 3.5219 14.6663 3.33301 14.6663H2.66634ZM11.333 7.99967C10.4108 7.99967 9.62467 7.67467 8.97467 7.02467C8.32467 6.37467 7.99967 5.58856 7.99967 4.66634C7.99967 3.75523 8.3219 2.9719 8.96634 2.31634C9.61078 1.66079 10.3997 1.33301 11.333 1.33301C12.2552 1.33301 13.0413 1.65801 13.6913 2.30801C14.3413 2.95801 14.6663 3.74412 14.6663 4.66634C14.6663 5.58856 14.3413 6.37467 13.6913 7.02467C13.0413 7.67467 12.2552 7.99967 11.333 7.99967ZM4.33301 11.333C4.61079 11.333 4.8469 11.2358 5.04134 11.0413C5.23579 10.8469 5.33301 10.6108 5.33301 10.333C5.33301 10.0552 5.23579 9.81912 5.04134 9.62467C4.8469 9.43023 4.61079 9.33301 4.33301 9.33301C4.05523 9.33301 3.81912 9.43023 3.62467 9.62467C3.43023 9.81912 3.33301 10.0552 3.33301 10.333C3.33301 10.6108 3.43023 10.8469 3.62467 11.0413C3.81912 11.2358 4.05523 11.333 4.33301 11.333ZM8.99967 11.333C9.27745 11.333 9.51356 11.2358 9.70801 11.0413C9.90245 10.8469 9.99967 10.6108 9.99967 10.333C9.99967 10.0552 9.90245 9.81912 9.70801 9.62467C9.51356 9.43023 9.27745 9.33301 8.99967 9.33301C8.7219 9.33301 8.48579 9.43023 8.29134 9.62467C8.0969 9.81912 7.99967 10.0552 7.99967 10.333C7.99967 10.6108 8.0969 10.8469 8.29134 11.0413C8.48579 11.2358 8.7219 11.333 8.99967 11.333Z" fill="#F68C0F"/>
                    </g>
                </svg>
                <span>Pendiente de envío</span>
            </p>
            <p class="flex space-x-2">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_13713_236" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
                    <rect width="16" height="16" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_13713_236)">
                    <path d="M3.33333 5.33333V12.6667H12.6667V5.33333H10.6667V10.6667L8 9.33333L5.33333 10.6667V5.33333H3.33333ZM3.33333 14C2.96667 14 2.65278 13.8694 2.39167 13.6083C2.13056 13.3472 2 13.0333 2 12.6667V4.35C2 4.19444 2.025 4.04444 2.075 3.9C2.125 3.75556 2.2 3.62222 2.3 3.5L3.13333 2.48333C3.25556 2.32778 3.40833 2.20833 3.59167 2.125C3.775 2.04167 3.96667 2 4.16667 2H11.8333C12.0333 2 12.225 2.04167 12.4083 2.125C12.5917 2.20833 12.7444 2.32778 12.8667 2.48333L13.7 3.5C13.8 3.62222 13.875 3.75556 13.925 3.9C13.975 4.04444 14 4.19444 14 4.35V12.6667C14 13.0333 13.8694 13.3472 13.6083 13.6083C13.3472 13.8694 13.0333 14 12.6667 14H3.33333ZM3.6 4H12.4L11.8333 3.33333H4.16667L3.6 4ZM6.66667 5.33333V8.5L8 7.83333L9.33333 8.5V5.33333H6.66667Z" fill="#10498B"/>
                    </g>
                </svg>
                <span>Parcialmente enviado</span>
            </p>
            <p class="flex space-x-2 ">
                <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.93141 12.096C5.93141 12.423 5.80152 12.7366 5.57031 12.9678C5.33911 13.199 5.02553 13.3289 4.69856 13.3289C4.37159 13.3289 4.05801 13.199 3.8268 12.9678C3.5956 12.7366 3.46571 12.423 3.46571 12.096M5.93141 12.096C5.93141 11.7691 5.80152 11.4555 5.57031 11.2243C5.33911 10.9931 5.02553 10.8632 4.69856 10.8632C4.37159 10.8632 4.05801 10.9931 3.8268 11.2243C3.5956 11.4555 3.46571 11.7691 3.46571 12.096M5.93141 12.096H10.8628M3.46571 12.096H1.92465C1.67942 12.096 1.44424 11.9986 1.27083 11.8252C1.09743 11.6518 1.00002 11.4166 1.00002 11.1714V8.39748M10.8628 12.096H12.7121M10.8628 12.096V8.39748M1.00002 8.39748V2.12229C0.998709 1.89722 1.08094 1.67968 1.2308 1.51176C1.38065 1.34384 1.58747 1.23748 1.81123 1.21327C4.55065 0.928911 7.31216 0.928911 10.0516 1.21327C10.516 1.26094 10.8628 1.65545 10.8628 2.12229V2.90966M1.00002 8.39748H10.8628M15.1778 12.096C15.1778 12.423 15.0479 12.7366 14.8167 12.9678C14.5855 13.199 14.2719 13.3289 13.9449 13.3289C13.6179 13.3289 13.3044 13.199 13.0732 12.9678C12.842 12.7366 12.7121 12.423 12.7121 12.096M15.1778 12.096C15.1778 11.7691 15.0479 11.4555 14.8167 11.2243C14.5855 10.9931 14.2719 10.8632 13.9449 10.8632C13.6179 10.8632 13.3044 10.9931 13.0732 11.2243C12.842 11.4555 12.7121 11.7691 12.7121 12.096M15.1778 12.096H16.1024C16.6128 12.096 17.0303 11.6818 16.9983 11.1722C16.8331 8.45801 15.919 5.84266 14.3575 3.6165C14.2088 3.40796 14.0146 3.23597 13.7896 3.11352C13.5646 2.99107 13.3148 2.92136 13.0589 2.90966H10.8628M10.8628 2.90966V8.39748" stroke="#2F941E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Enviado</span>
            </p>
        </div>

        <!-- Filtro -->
        <div class="flex items-center space-x-5 lg:mx-28 mx-4 mt-5">
            <div class="w-1/4 flex flex-col">
                <label class="text-sm ml-2 mb-1">Filtro por propietario</label>
                <el-select @change="fetchItemsFiltered" v-model="filter" class="!w-full"
                    placeholder="Selecciona una opción">
                    <el-option v-for="item in options" :key="item" :label="item" :value="item" />
                </el-select>
            </div>
            <!-- <div class="w-1/3 flex flex-col">
                <label class="text-sm ml-2 mb-1">Filtro por estatus</label>
                <el-cascader @change="applyStatusFilter()" v-model="statusfilter" :options="cascaderOptions"
                    :props="cascaderProps" collapse-tags class="!w-full" />
            </div> -->
        </div>

        <LoadingLogo v-if="loading" />

        <div v-else class="relative overflow-hidden min-h-[60vh]">
            <!-- <NotificationCenter module="shippings" /> -->
            <div class="w-11/12 lg:w-5/6 mx-auto mt-6">
                <div class="lg:flex justify-between items-center">
                    <!-- pagination -->
                    <div v-if="!search" class="overflow-auto mb-2">
                        <PaginationWithNoMeta :pagination="shippings" class="py-2" />
                    </div>
                    <!-- buttons -->
                    <div>
                        <el-popconfirm
                            v-if="$page.props.auth.user.permissions.includes('Eliminar ordenes de venta')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                            title="¿Continuar?" @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>
    
                <el-table :data="filteredShippings" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column label="Folio" width="100">
                        <template #default="scope">
                            <p @click.stop="openInNewTab(scope.row.id)" class="hover:underline !text-blue-500">EV-{{ scope.row.id.toString().padStart(4, '0') }}</p>
                        </template>
                    </el-table-column>
                    <!-- <el-table-column label="Folio de ov" width="100">
                        <template #default="scope">
                            <p @click.stop="openInNewTab(scope.row.sale.id)" class="hover:underline">
                                OV-{{ scope.row.sale.id.toString().padStart(4, '0') }}
                            </p>
                        </template>
                    </el-table-column> -->
                    <el-table-column prop="user.name" label="Vendedor" />
                    <el-table-column label="Creado el">
                        <template #default="scope">
                            <p>{{ formatDate(scope.row.created_at) }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column prop="company_branch.name" label="Cliente" />
                    <el-table-column label="Productos" width="150">
                        <template #default="scope">
                            <p>{{ scope.row.productions?.length > 1 ? scope.row.productions.map(p => p.catalog_product_company_sale.catalog_product_company?.catalog_product?.name).join(', ') : '- Sin producción -' }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Tipo de entrega">
                        <template #default="scope">
                            <div class="flex items-center space-x-1">
                                <span v-html="getStatusIcon(scope.row.status)"></span>
                                <p>{{ scope.row.shipping_type ?? '-' }}</p>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="Guía" width="100">
                        <template #default="scope">
                            <p>{{ scope.row.tracking_guide ?? '-' }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Fecha de entrega" width="115">
                        <template #default="scope">
                            <p>{{ scope.row.promise_date ?? '-' }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Fecha de envío" width="120">
                        <template #default="scope">
                            <p>{{ formatDate(scope.row.sent_at) ?? '-' }}</p>
                        </template>
                    </el-table-column>
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
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <!-- <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
                                            scope.row.user.id == $page.props.auth.user.id"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item> -->
                                        <el-dropdown-item v-if="scope.row.status !== 'Parcialmente enviado' && scope.row.status !== 'Enviado'"
                                            :command="'Enviado-' + scope.row.id">
                                            <svg width="18" height="15" class="size-4 mr-2" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.93141 12.096C5.93141 12.423 5.80152 12.7366 5.57031 12.9678C5.33911 13.199 5.02553 13.3289 4.69856 13.3289C4.37159 13.3289 4.05801 13.199 3.8268 12.9678C3.5956 12.7366 3.46571 12.423 3.46571 12.096M5.93141 12.096C5.93141 11.7691 5.80152 11.4555 5.57031 11.2243C5.33911 10.9931 5.02553 10.8632 4.69856 10.8632C4.37159 10.8632 4.05801 10.9931 3.8268 11.2243C3.5956 11.4555 3.46571 11.7691 3.46571 12.096M5.93141 12.096H10.8628M3.46571 12.096H1.92465C1.67942 12.096 1.44424 11.9986 1.27083 11.8252C1.09743 11.6518 1.00002 11.4166 1.00002 11.1714V8.39748M10.8628 12.096H12.7121M10.8628 12.096V8.39748M1.00002 8.39748V2.12229C0.998709 1.89722 1.08094 1.67968 1.2308 1.51176C1.38065 1.34384 1.58747 1.23748 1.81123 1.21327C4.55065 0.928911 7.31216 0.928911 10.0516 1.21327C10.516 1.26094 10.8628 1.65545 10.8628 2.12229V2.90966M1.00002 8.39748H10.8628M15.1778 12.096C15.1778 12.423 15.0479 12.7366 14.8167 12.9678C14.5855 13.199 14.2719 13.3289 13.9449 13.3289C13.6179 13.3289 13.3044 13.199 13.0732 12.9678C12.842 12.7366 12.7121 12.423 12.7121 12.096M15.1778 12.096C15.1778 11.7691 15.0479 11.4555 14.8167 11.2243C14.5855 10.9931 14.2719 10.8632 13.9449 10.8632C13.6179 10.8632 13.3044 10.9931 13.0732 11.2243C12.842 11.4555 12.7121 11.7691 12.7121 12.096M15.1778 12.096H16.1024C16.6128 12.096 17.0303 11.6818 16.9983 11.1722C16.8331 8.45801 15.919 5.84266 14.3575 3.6165C14.2088 3.40796 14.0146 3.23597 13.7896 3.11352C13.5646 2.99107 13.3148 2.92136 13.0589 2.90966H10.8628M10.8628 2.90966V8.39748" stroke="#2F941E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            Enviado</el-dropdown-item>
                                        <el-dropdown-item v-if="scope.row.status !== 'Parcialmente enviado' && scope.row.status !== 'Enviado'"
                                            :command="'Parcialmente enviado-' + scope.row.id">
                                            <svg width="16" height="16" class="size-4 mr-2" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <mask id="mask0_13713_236" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
                                                <rect width="16" height="16" fill="#D9D9D9"/>
                                                </mask>
                                                <g mask="url(#mask0_13713_236)">
                                                <path d="M3.33333 5.33333V12.6667H12.6667V5.33333H10.6667V10.6667L8 9.33333L5.33333 10.6667V5.33333H3.33333ZM3.33333 14C2.96667 14 2.65278 13.8694 2.39167 13.6083C2.13056 13.3472 2 13.0333 2 12.6667V4.35C2 4.19444 2.025 4.04444 2.075 3.9C2.125 3.75556 2.2 3.62222 2.3 3.5L3.13333 2.48333C3.25556 2.32778 3.40833 2.20833 3.59167 2.125C3.775 2.04167 3.96667 2 4.16667 2H11.8333C12.0333 2 12.225 2.04167 12.4083 2.125C12.5917 2.20833 12.7444 2.32778 12.8667 2.48333L13.7 3.5C13.8 3.62222 13.875 3.75556 13.925 3.9C13.975 4.04444 14 4.19444 14 4.35V12.6667C14 13.0333 13.8694 13.3472 13.6083 13.6083C13.3472 13.8694 13.0333 14 12.6667 14H3.33333ZM3.6 4H12.4L11.8333 3.33333H4.16667L3.6 4ZM6.66667 5.33333V8.5L8 7.83333L9.33333 8.5V5.33333H6.66667Z" fill="#10498B"/>
                                                </g>
                                            </svg>
                                            Parcialmente enviado</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
        </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LoadingLogo from '@/Components/MyComponents/LoadingLogo.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import { Link } from "@inertiajs/vue3";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';

export default {
data() {
    return {
        disableMassiveActions: true,
        // filtro
        // cascaderProps: { multiple: true },
        filter: 'Mis órdenes',
        statusfilter: [["Pendiente de envío"], ["Parcialmente enviado"], ["Enviado"]],
        options: ['Mis órdenes', 'Todas las órdenes'],
        filteredShippings: this.shippings.data,
        search: '',
        loading: false,
        // pagination: this.shippings,
    }
},
components:{
    PaginationWithNoMeta,
    NotificationCenter,
    SecondaryButton,
    IndexSearchBar,
    PrimaryButton,
    LoadingLogo,
    AppLayout,
    Link
},  
props:{
    shippings: Object
},
methods:{
    formatDate(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMM yyyy', { locale: es }); // Formato personalizado
        }
    },
    formatDateTime(date) {
        if ( date ) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd MMM yyyy, a las h:mm', { locale: es }); // Formato personalizado
        }
    },
    openInNewTab(saleId) {
        const url = this.route('sales.show', saleId);
        window.open(url, '_blank');
    },
    getStatusIcon(status) {
        if ( status === 'Enviado' ) {
            return '<svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.93141 12.096C5.93141 12.423 5.80152 12.7366 5.57031 12.9678C5.33911 13.199 5.02553 13.3289 4.69856 13.3289C4.37159 13.3289 4.05801 13.199 3.8268 12.9678C3.5956 12.7366 3.46571 12.423 3.46571 12.096M5.93141 12.096C5.93141 11.7691 5.80152 11.4555 5.57031 11.2243C5.33911 10.9931 5.02553 10.8632 4.69856 10.8632C4.37159 10.8632 4.05801 10.9931 3.8268 11.2243C3.5956 11.4555 3.46571 11.7691 3.46571 12.096M5.93141 12.096H10.8628M3.46571 12.096H1.92465C1.67942 12.096 1.44424 11.9986 1.27083 11.8252C1.09743 11.6518 1.00002 11.4166 1.00002 11.1714V8.39748M10.8628 12.096H12.7121M10.8628 12.096V8.39748M1.00002 8.39748V2.12229C0.998709 1.89722 1.08094 1.67968 1.2308 1.51176C1.38065 1.34384 1.58747 1.23748 1.81123 1.21327C4.55065 0.928911 7.31216 0.928911 10.0516 1.21327C10.516 1.26094 10.8628 1.65545 10.8628 2.12229V2.90966M1.00002 8.39748H10.8628M15.1778 12.096C15.1778 12.423 15.0479 12.7366 14.8167 12.9678C14.5855 13.199 14.2719 13.3289 13.9449 13.3289C13.6179 13.3289 13.3044 13.199 13.0732 12.9678C12.842 12.7366 12.7121 12.423 12.7121 12.096M15.1778 12.096C15.1778 11.7691 15.0479 11.4555 14.8167 11.2243C14.5855 10.9931 14.2719 10.8632 13.9449 10.8632C13.6179 10.8632 13.3044 10.9931 13.0732 11.2243C12.842 11.4555 12.7121 11.7691 12.7121 12.096M15.1778 12.096H16.1024C16.6128 12.096 17.0303 11.6818 16.9983 11.1722C16.8331 8.45801 15.919 5.84266 14.3575 3.6165C14.2088 3.40796 14.0146 3.23597 13.7896 3.11352C13.5646 2.99107 13.3148 2.92136 13.0589 2.90966H10.8628M10.8628 2.90966V8.39748" stroke="#2F941E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        } else if ( status === 'Parcialmente enviado' ) {
            return '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_13713_236" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#mask0_13713_236)"><path d="M3.33333 5.33333V12.6667H12.6667V5.33333H10.6667V10.6667L8 9.33333L5.33333 10.6667V5.33333H3.33333ZM3.33333 14C2.96667 14 2.65278 13.8694 2.39167 13.6083C2.13056 13.3472 2 13.0333 2 12.6667V4.35C2 4.19444 2.025 4.04444 2.075 3.9C2.125 3.75556 2.2 3.62222 2.3 3.5L3.13333 2.48333C3.25556 2.32778 3.40833 2.20833 3.59167 2.125C3.775 2.04167 3.96667 2 4.16667 2H11.8333C12.0333 2 12.225 2.04167 12.4083 2.125C12.5917 2.20833 12.7444 2.32778 12.8667 2.48333L13.7 3.5C13.8 3.62222 13.875 3.75556 13.925 3.9C13.975 4.04444 14 4.19444 14 4.35V12.6667C14 13.0333 13.8694 13.3472 13.6083 13.6083C13.3472 13.8694 13.0333 14 12.6667 14H3.33333ZM3.6 4H12.4L11.8333 3.33333H4.16667L3.6 4ZM6.66667 5.33333V8.5L8 7.83333L9.33333 8.5V5.33333H6.66667Z" fill="#10498B"/></g></svg>';
        } else {
            return '<svg width="16" height="16" class="size-5" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_13713_230" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#mask0_13713_230)"><path d="M12.433 6.26634L12.933 5.76634L11.6663 4.53301V2.66634H10.9997V4.79967L12.433 6.26634ZM2.66634 14.6663C2.47745 14.6663 2.31912 14.6025 2.19134 14.4747C2.06356 14.3469 1.99967 14.1886 1.99967 13.9997V12.633C1.77745 12.3997 1.61079 12.1413 1.49967 11.858C1.38856 11.5747 1.33301 11.2886 1.33301 10.9997V4.66634C1.33301 3.67745 1.79134 2.98579 2.70801 2.59134C3.62467 2.1969 5.22745 1.99967 7.51634 1.99967C7.3719 2.19967 7.2469 2.41079 7.14134 2.63301C7.03579 2.85523 6.94412 3.08301 6.86634 3.31634C5.73301 3.33856 4.85801 3.40523 4.24134 3.51634C3.62467 3.62745 3.19967 3.78856 2.96634 3.99967H6.71634C6.68301 4.2219 6.66634 4.44412 6.66634 4.66634C6.66634 4.88856 6.68301 5.11079 6.71634 5.33301H2.66634V7.33301H7.51634C7.70523 7.59967 7.91634 7.8469 8.14967 8.07467C8.38301 8.30245 8.64412 8.49967 8.93301 8.66634H2.66634V10.6663C2.66634 11.033 2.7969 11.3469 3.05801 11.608C3.31912 11.8691 3.63301 11.9997 3.99967 11.9997H9.33301C9.69967 11.9997 10.0136 11.8691 10.2747 11.608C10.5358 11.3469 10.6663 11.033 10.6663 10.6663V9.28301C10.8886 9.31634 11.1108 9.33301 11.333 9.33301C11.5552 9.33301 11.7775 9.31634 11.9997 9.28301V10.9997C11.9997 11.2886 11.9441 11.5747 11.833 11.858C11.7219 12.1413 11.5552 12.3997 11.333 12.633V13.9997C11.333 14.1886 11.2691 14.3469 11.1413 14.4747C11.0136 14.6025 10.8552 14.6663 10.6663 14.6663H9.99967C9.81079 14.6663 9.65245 14.6025 9.52467 14.4747C9.3969 14.3469 9.33301 14.1886 9.33301 13.9997V13.333H3.99967V13.9997C3.99967 14.1886 3.93579 14.3469 3.80801 14.4747C3.68023 14.6025 3.5219 14.6663 3.33301 14.6663H2.66634ZM11.333 7.99967C10.4108 7.99967 9.62467 7.67467 8.97467 7.02467C8.32467 6.37467 7.99967 5.58856 7.99967 4.66634C7.99967 3.75523 8.3219 2.9719 8.96634 2.31634C9.61078 1.66079 10.3997 1.33301 11.333 1.33301C12.2552 1.33301 13.0413 1.65801 13.6913 2.30801C14.3413 2.95801 14.6663 3.74412 14.6663 4.66634C14.6663 5.58856 14.3413 6.37467 13.6913 7.02467C13.0413 7.67467 12.2552 7.99967 11.333 7.99967ZM4.33301 11.333C4.61079 11.333 4.8469 11.2358 5.04134 11.0413C5.23579 10.8469 5.33301 10.6108 5.33301 10.333C5.33301 10.0552 5.23579 9.81912 5.04134 9.62467C4.8469 9.43023 4.61079 9.33301 4.33301 9.33301C4.05523 9.33301 3.81912 9.43023 3.62467 9.62467C3.43023 9.81912 3.33301 10.0552 3.33301 10.333C3.33301 10.6108 3.43023 10.8469 3.62467 11.0413C3.81912 11.2358 4.05523 11.333 4.33301 11.333ZM8.99967 11.333C9.27745 11.333 9.51356 11.2358 9.70801 11.0413C9.90245 10.8469 9.99967 10.6108 9.99967 10.333C9.99967 10.0552 9.90245 9.81912 9.70801 9.62467C9.51356 9.43023 9.27745 9.33301 8.99967 9.33301C8.7219 9.33301 8.48579 9.43023 8.29134 9.62467C8.0969 9.81912 7.99967 10.0552 7.99967 10.333C7.99967 10.6108 8.0969 10.8469 8.29134 11.0413C8.48579 11.2358 8.7219 11.333 8.99967 11.333Z" fill="#F68C0F"/></g></svg>';
        }
    },
    applyStatusFilter() {
        // Convertir el array de arrays a un array plano de strings
        const flatSelectedStatuses = this.statusfilter.flat();

        // Filtrar las ventas basadas en los estados seleccionados
        this.filteredShippings = this.shippings.data.filter(sale =>
            flatSelectedStatuses.includes(sale.status.label)
        );
    },
    removeLeftZeros() {
        // Verificar si el valor ingresado es un número
        const isNumber = /^\d+$/.test(this.search);

        if (isNumber) {
            // Eliminar ceros a la izquierda
            return parseInt(this.search, 10).toString();
        }

        return this.search;
    },
    handleSelectionChange(val) {
        this.$refs.multipleTableRef.value = val;

        if (!this.$refs.multipleTableRef.value.length) {
            this.disableMassiveActions = true;
        } else {
            this.disableMassiveActions = false;
        }
    },
    handleCommand(command) {
        const commandName = command.split('-')[0];
        const rowId = command.split('-')[1];

        if (commandName == 'clone') {
            this.clone(rowId);
        } else if (commandName == 'make_so') {
            console.log('SO');
        } else {
            this.$inertia.get(route('shippings.' + commandName, rowId));
        }
    },
    fetchItemsFiltered() {
        if ( this.filter === 'Todas las órdenes' ) {
            this.$inertia.get(route('shippings.index-all'));
        }
    },
    tableRowClassName({ row, rowIndex }) {
        return 'cursor-pointer text-xs';
    },
    handleRowClick(row) {
        this.$inertia.get(route('shippings.show', row));
    },
    async deleteSelections() {
        if (!this.$refs.multipleTableRef.value.length) {
            this.disableMassiveActions = true;
        } else {
            this.disableMassiveActions = false;
        }
    },
    async deleteSelections() {
        try {
            const response = await axios.post(route('shippings.massive-delete', {
                shippings: this.$refs.multipleTableRef.value
            }));

            if (response.status == 200) {
                this.$notify({
                    title: 'Éxito',
                    message: response.data.message,
                    type: 'success'
                });

                // update list of shippings
                let deletedIndexes = [];
                this.shippings.data.forEach((sale, index) => {
                    if (this.$refs.multipleTableRef.value.includes(sale)) {
                        deletedIndexes.push(index);
                    }
                });

                // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                deletedIndexes.sort((a, b) => b - a);

                // Eliminar OV por índice
                for (const index of deletedIndexes) {
                    this.shippings.data.splice(index, 1);
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
    async handleSearch(search) {
        this.search = search;
        this.loading = true;
        try {
            if (!this.search) {
                this.filteredShippings = this.shippings.data;
            } else {
                const response = await axios.post(route('shippings.get-matches'), { query: this.search });
 
                if (response.status === 200) {
                    this.filteredShippings = response.data.items.data;
                }
            }
        } catch (error) {
            console.log(error);
        } finally {
            this.loading = false;
        }
    },
},
    mounted() {
        // this.applyStatusFilter();
    }
}
</script>
