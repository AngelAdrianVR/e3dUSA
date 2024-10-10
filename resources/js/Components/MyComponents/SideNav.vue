<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <div class="bg-[#D9D9D9] h-full overflow-auto">
            <nav class="pt-2 px-2">
                <div>
                    <div @click="showModal = true" v-if="$page.props.auth.user?.employee_properties"
                        class="cursor-pointer hover:underline mb-4 items-center font-semibold text-xs text-[#0355B5] flex flex-col">
                        <span>Horas / semana</span>
                        <span>{{ $page.props.week_time.formatted }} / {{
                            $page.props.auth.user?.employee_properties?.hours_per_week_formatted ?? '0 h' }}</span>
                    </div>
                    <template v-for="(menu, index) in menus" :key="index">
                        <SideNavLink v-if="menu.show" :href="menu.route" :active="menu.active" :dropdown="menu.dropdown"
                            class="mb-px">
                            <template #trigger>
                                <span v-html="menu.icon" class="relative"></span>
                                <span class="leading-none font-normal text-center">
                                    {{ menu.label }}
                                </span>
                                <i v-if="menu.notifications" class="fa-solid fa-circle fa-flip text-primary text-[10px] absolute top-3 right-7"></i>
                            </template>
                            <template #content>
                                <template v-for="option in menu.options" :key="option">
                                    <DropdownNavLink v-if="option.show" :href="route(option.route)"
                                        :notifications="option.notifications">
                                        {{ option.label }}
                                    </DropdownNavLink>
                                </template>
                            </template>
                        </SideNavLink>
                    </template>
                </div>
            </nav>
        </div>
    </div>

    <DialogModal :show="showModal" @close="showModal = false">
        <template #title>
            Nómina semanal
            <!-- <p class="text-sm text-primary mt-3 mx-20">
                Se notifica a todos los colaboradores de emblems3d que a partir del 8 de Septiembre del 2023,
                los registros de salida después de las horas de su jornada diaria no contarán como horas adicionales.
            </p> -->
            <div class="w-1/2 mt-5 mx-10">
                <el-select v-model="payrollId" filterable :reserve-keyword="false" placeholder="Buscar nómina">
                    <el-option v-for="item in payrolls" :key="item.id" :label="'Nómina semana: ' + item.week"
                        :value="item.id" />
                </el-select>
            </div>

        </template>
        <template #content>
            <PayrollTemplate :user="$page.props.auth.user" :payrollId="payrollId" dontShowDetails />
        </template>
        <template #footer>
            <CancelButton @click="showModal = false">Cerrar</CancelButton>
        </template>
    </DialogModal>
</template>

<script>

import SideNavLink from "@/Components/MyComponents/SideNavLink.vue";
import DropdownNavLink from "@/Components/MyComponents/DropdownNavLink.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import PayrollTemplate from "@/Components/MyComponents/payrollTemplate.vue";
import DialogModal from "@/Components/DialogModal.vue";

export default {
    data() {
        return {
            menus: [
                {
                    label: 'Inicio',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    notifications: false,
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Catálogo de productos',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>',
                    route: route('catalog-products.index'),
                    active: route().current('catalog-products.*'),
                    notifications: false,
                    options: [],
                    dropdown: false,
                    show: this.$page.props.auth.user.permissions.includes('Ver catalogo de productos')
                },
                {
                    label: 'CRM',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" /></svg>',
                    active: route().current('crm.*') || route().current('quotes.*') || route().current('companies.*')
                        || route().current('sales.*') || route().current('oportunities.*') || route().current('oportunity-tasks.*')
                        || route().current('client-monitors.*') || route().current('meeting-monitors.*') || route().current('payment-monitors.*')
                        || route().current('sale-analitics.*') || route().current('sale-analitics.*')
                        || route().current('prospects.*') || route().current('prospects.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return ['quote', 'sales', 'opportunities', 'companies', 'prospects'].includes(notification.data.module);
                    }),
                    options: [
                        {
                            label: 'Inicio CRM',
                            route: 'sale-analitics.index',
                            show: this.$page.props.auth.user.permissions.includes('Analisis de ventas'),
                            notifications: false,
                        },
                        {
                            label: 'Prospectos',
                            route: 'prospects.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver prospectos'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'prospects';
                            }),
                        },
                        {
                            label: 'Clientes',
                            route: 'companies.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver clientes'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'companies';
                            }),
                        },
                        {
                            label: 'Oportunidades',
                            route: 'oportunities.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver oportunidades'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'opportunities';
                            }),
                        },
                        {
                            label: 'Seguimiento integral',
                            route: 'client-monitors.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver seguimiento integral'),
                            notifications: false,
                        },
                        {
                            label: 'Cotizaciones',
                            route: 'quotes.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'quote';
                            }),
                        },
                        {
                            label: 'Órdenes de venta / stock',
                            route: 'sales.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver ordenes de venta'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'sales';
                            }),
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones') ||
                        this.$page.props.auth.user.permissions.includes('Ver clientes') ||
                        this.$page.props.auth.user.permissions.includes('Ver ordenes de venta') ||
                        this.$page.props.auth.user.permissions.includes('Ver oportunidades') ||
                        this.$page.props.auth.user.permissions.includes('Ver seguimiento integral')
                },
                {
                    label: 'Compras',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>',
                    active: route().current('suppliers.*') || route().current('purchases.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return ['po', 'suppliers'].includes(notification.data.module);
                    }),
                    options: [
                        {
                            label: 'Proveedores',
                            route: 'suppliers.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver proveedores'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'suppler';
                            }),
                            notifications: false,
                        },
                        {
                            label: 'Órdenes de compra',
                            route: 'purchases.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver ordenes de compra'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'po';
                            }),
                        },

                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver proveedores') ||
                        this.$page.props.auth.user.permissions.includes('Ver ordenes de compra')
                },
                {
                    label: 'Logistica',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>',
                    active: route().current('shippings.*') || route().current('boxes.*') || route().current('shipping-rates.*'),
                    // notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                    //     return notification.data.module === 'shippings';
                    // }),
                     options: [
                        {
                            label: 'Envíos',
                            route: 'shippings.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver logistica'),
                            // notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                            //     return notification.data.module === 'suppler';
                            // }),
                            // notifications: false,
                        },
                        {
                            label: 'Administrador de cajas',
                            route: 'boxes.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver logistica'),
                            // notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                            //     return notification.data.module === 'suppler';
                            // }),
                            // notifications: false,
                        },
                        {
                            label: 'Tarifas',
                            route: 'shipping-rates.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver logistica'),
                            // notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                            //     return notification.data.module === 'suppler';
                            // }),
                            // notifications: false,
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver logistica')
                },
                {
                    label: 'Proyectos',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                    route: route('projects.index'),
                    active: route().current('projects.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'projects';
                    }),
                    show: this.$page.props.auth.user.permissions.includes('Ver proyectos')
                },
                {
                    label: 'Almacén',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>',
                    active: route().current('storages.*') || route().current('raw-materials.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return ['raw-material', 'consumable', 'finished-product', 'scrap'].includes(notification.data.module);
                    }),
                    options: [
                        {
                            label: 'Materia prima',
                            route: 'storages.raw-materials.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver materia prima'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'raw-material';
                            }),
                        },
                        {
                            label: 'Insumos',
                            route: 'storages.consumables.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver insumos'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'consumable';
                            }),
                        },
                        {
                            label: 'Producto terminado',
                            route: 'storages.finished-products.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver producto terminado'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'finished-products';
                            }),
                        },
                        {
                            label: 'Scrap',
                            route: 'storages.scraps.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver scrap'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'scrap';
                            }),
                        },
                        {
                            label: 'Obsoletos',
                            route: 'storages.obsolete.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver producto obsoleto'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'obsoletes';
                            }),
                        },
                        {
                            label: 'Seguimiento',
                            route: 'storages.samples.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver producto de seguimiento'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'samples';
                            }),
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver materia prima') ||
                        this.$page.props.auth.user.permissions.includes('Ver insumos') ||
                        this.$page.props.auth.user.permissions.includes('Ver producto terminado') ||
                        this.$page.props.auth.user.permissions.includes('Ver scrap')
                },
                {
                    label: 'Recursos Humanos',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>',
                    active: route().current('payrolls.*')
                        || route().current('admin-additional-times.*')
                        || route().current('users.*')
                        || route().current('role-permission.*')
                        || route().current('bonuses.*')
                        || route().current('holidays.*')
                        || route().current('discounts.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return ['payroll', 'admin-additional-time', 'user'].includes(notification.data.module);
                    }),
                    options: [
                        {
                            label: 'Nóminas',
                            route: 'payrolls.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver nominas'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'payroll';
                            }),
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'admin-additional-times.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver solicitudes de tiempo adicional'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'admin-additional-time';
                            }),
                        },
                        {
                            label: 'Personal',
                            route: 'users.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver personal'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'user';
                            }),
                        },
                        {
                            label: 'Roles y permisos',
                            route: 'role-permission.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver roles y permisos'),
                            notifications: false,
                        },
                        {
                            label: 'Bonos',
                            route: 'bonuses.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver bonos'),
                            notifications: false,
                        },
                        {
                            label: 'Descuentos',
                            route: 'discounts.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver descuentos')
                        },
                        {
                            label: 'Dias festivos',
                            route: 'holidays.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver dias festivos'),
                            notifications: false,
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver nominas') ||
                        this.$page.props.auth.user.permissions.includes('Ver solicitudes de tiempo adicional') ||
                        this.$page.props.auth.user.permissions.includes('Ver roles y permisos') ||
                        this.$page.props.auth.user.permissions.includes('Ver bonos') ||
                        this.$page.props.auth.user.permissions.includes('Ver dias festivos')
                },
                {
                    label: 'Diseño',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" /></svg>',
                    route: route('designs.index'),
                    active: route().current('designs.*'),
                    show: true
                },
                {
                    label: 'Producción',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" /></svg>',
                    route: route('productions.index'),
                    active: route().current('productions.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'production';
                    }),
                    show: true
                },
                {
                    label: 'Calidad',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" /></svg>',
                    route: route('qualities.index'),
                    active: route().current('qualities.*'),
                    show: this.$page.props.auth.user.permissions.includes('Ver modulo de calidad')
                },
                {
                    label: 'Mercadotecnia',
                    icon: '<i class="fa-solid fa-lightbulb text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'marketing';
                    }),
                    show: false
                },
                {
                    label: 'Más',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                    active: route().current('machines.*') || route().current('more-additional-times.*') || route().current('meetings.*') ||
                        route().current('samples.*') || route().current('production-costs.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return ['machine', 'meeting', 'samples', 'media-library'].includes(notification.data.module);
                    }),
                    options: [
                        {
                            label: 'Tutoriales y manuales',
                            route: 'manuals.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver manuales'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'manual';
                            }),
                        },
                        {
                            label: 'Máquinas',
                            route: 'machines.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver maquinas'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'machine';
                            }),
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'more-additional-times.index',
                            show: this.$page.props.auth.user.permissions.includes('Solicitudes de tiempo adicional personal'),
                            notifications: false,
                        },
                        {
                            label: 'Reuniones',
                            route: 'meetings.index',
                            // show: this.$page.props.auth.user.permissions.includes('Reuniones personal'),
                            show: false,
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'meeting';
                            }),
                        },
                        {
                            label: 'Biblioteca de medios',
                            route: 'dashboard',
                            // show: this.$page.props.auth.user.permissions.includes('Ver medios')
                            show: false,
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'media-library';
                            }),
                        },
                        {
                            label: 'Seguimiento de muestras',
                            route: 'samples.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver muestra'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'samples';
                            }),
                        },
                        {
                            label: 'Costos de producción',
                            route: 'production-costs.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver costos de produccion'),
                            notifications: false,
                        },
                        {
                            label: 'Historial de acciones',
                            route: 'audits.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver historial de acciones')
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver maquinas') ||
                        this.$page.props.auth.user.permissions.includes('Solicitudes de tiempo adicional personal') ||
                        this.$page.props.auth.user.permissions.includes('Reuniones personal') ||
                        this.$page.props.auth.user.permissions.includes('Ver biblioteca de medios') ||
                        this.$page.props.auth.user.permissions.includes('Ver historial de acciones') ||
                        this.$page.props.auth.user.permissions.includes('Ver costos de produccion')
                },
                {
                    label: 'Configuración',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>',
                    route: route('settings.index'),
                    active: route().current('settings.*'),
                    notifications: false,
                    show: this.$page.props.auth.user.permissions.includes('Ver configuraciones')
                },
            ],
            showModal: false,
            payrollId: null,
            payrolls: null,
        }
    },
    components: {
        SideNavLink,
        DropdownNavLink,
        DialogModal,
        CancelButton,
        PayrollTemplate
    },
    methods: {
        async getAllPayrolls() {
            try {
                const response = await axios.post(route('payrolls.get-all-payrolls'));

                if (response.status === 200) {
                    this.payrolls = response.data.items;
                    this.payrollId = this.payrolls[0].id;
                }

            } catch (error) {
                console.log(error.message);
            }
        }
    },
    mounted() {
        this.getAllPayrolls();
    }
}
</script>