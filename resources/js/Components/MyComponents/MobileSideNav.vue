<template>
    <i @click="options = null" v-if="options" class="fa-solid fa-chevron-left text-xs ml-4 mb-3"></i>
    <template v-for="(menu, index) in options ?? menus" :key="index">
        <ResponsiveNavLink v-if="!menu?.options?.length && menu.show" :href="menu.route" :active="menu.active">
            <div class="flex justify-between items-center">
                <p class="space-x-2 flex items-center">
                    <span v-html="menu.icon"></span>
                    <span>{{ menu.label }}</span>
                </p>
                <i v-if="menu?.options?.length" class="fa-solid fa-chevron-right text-xs"></i>
            </div>
        </ResponsiveNavLink>
        <ResponsiveNavLink v-else-if="menu.show" as="button" @click="showOptions(index)" :active="menu.active">
            <div class="flex justify-between items-center">
                <p class="space-x-2 flex items-center">
                    <span v-html="menu.icon"></span>
                    <span>{{ menu.label }}</span>
                </p>
                <i v-if="menu?.options?.length" class="fa-solid fa-chevron-right text-xs"></i>
            </div>
        </ResponsiveNavLink>
    </template>
</template>

<script>

import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

export default {
    data() {
        return {
            options: null,
            menus: [
                {
                    label: 'Inicio',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Catálogo de productos',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>',
                    route: route('catalog-products.index'),
                    active: route().current('catalog-products.*'),
                    options: [],
                    dropdown: false,
                    show: this.$page.props.auth.user.permissions.includes('Ver catalogo de productos')
                },
                {
                    label: 'CRM',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" /></svg>',
                    active:  route().current('crm.*') || route().current('quotes.*') || route().current('companies.*') ||
                     route().current('sales.*') || route().current('oportunities.*') || route().current('oportunity-tasks.*') ||
                     route().current('client-monitors.*') || route().current('sale-analitics.*') ||
                     route().current('prospects.*'),
                    options: [
                        {
                            label: 'Inicio CRM',
                            route: route('sale-analitics.index'),
                            show: this.$page.props.auth.user.permissions.includes('Analisis de ventas'),
                            notifications: false,
                        },
                        {
                            label: 'Prospectos',
                            route: route('prospects.index'),
                            active: route().current('prospects.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver prospectos')
                        },
                        {
                            label: 'Clientes',
                            route: route('companies.index'),
                            active: route().current('companies.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver clientes')
                        },
                        {
                            label: 'Oportunidades',
                            route: route('oportunities.index'),
                            active: route().current('oportunities.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver oportunidades')
                        },
                        {
                            label: 'Seguimiento integral',
                            route: route('client-monitors.index'),
                            active: route().current('client-monitors.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver seguimiento integral')
                        },
                        {
                            label: 'Cotizaciones',
                            route: route('quotes.index'),
                            active: route().current('quotes.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones')
                        },
                        {
                            label: 'Órdenes de venta / stock',
                            route: route('sales.index'),
                            active: route().current('sales.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver ordenes de venta')
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones') ||
                        this.$page.props.auth.user.permissions.includes('Ver clientes') ||
                        this.$page.props.auth.user.permissions.includes('Ver ordenes de venta') ||
                        this.$page.props.auth.user.permissions.includes('Ver oportunidades')||
                        this.$page.props.auth.user.permissions.includes('Ver seguimiento integral') ||
                        this.$page.props.auth.user.permissions.includes('Ver prospectos')
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
                    options: [],
                    dropdown: false,
                    show: this.$page.props.auth.user.permissions.includes('Ver proyectos')
                },
                {
                    label: 'Compras',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>',
                    active: route().current('suppliers.*') || route().current('purchases.*'),
                    options: [
                        {
                            label: 'Proveedores',
                            route: route('suppliers.index'),
                            active: route().current('suppliers.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver proveedores')
                        },
                        {
                            label: 'Órdenes de compra',
                            route: route('purchases.index'),
                            active: route().current('purchases.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver ordenes de compra')
                        },

                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver proveedores') ||
                        this.$page.props.auth.user.permissions.includes('Ver ordenes de compra')
                },
                {
                    label: 'Almacén',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>',
                    active: route().current('storages.*'),
                    options: [
                        {
                            label: 'Materia prima',
                            route: route('storages.raw-materials.index'),
                            active: route().current('storages.raw-materials.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver materia prima')
                        },
                        {
                            label: 'Insumos',
                            route: route('storages.consumables.index'),
                            active: route().current('storages.consumables.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver insumos')
                        },
                        {
                            label: 'Producto terminado',
                            route: route('storages.finished-products.index'),
                            active: route().current('storages.finished-products.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver producto terminado')
                        },
                        {
                            label: 'Scrap',
                            route: route('storages.scraps.index'),
                            active: route().current('storages.scraps.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver scrap')
                        },
                        {
                            label: 'Obsoletos',
                            route: 'storages.obsolete.index',
                            active: route().current('storages.obsolete.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver producto obsoleto'),
                        },
                        {
                            label: 'Seguimiento',
                            route: 'storages.samples.index',
                            active: route().current('storages.samples.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver producto de seguimiento'),
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
                    options: [
                        {
                            label: 'Nóminas',
                            route: route('payrolls.index'),
                            active: route().current('payrolls.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver nominas')
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: route('admin-additional-times.index'),
                            active: route().current('admin-additional-times.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver solicitudes de tiempo adicional')
                        },
                        {
                            label: 'Personal',
                            route: route('users.index'),
                            active: route().current('users.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver personal')
                        },
                        {
                            label: 'Roles y permisos',
                            route: route('role-permission.index'),
                            active: route().current('role-permission.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver roles y permisos')
                        },
                        {
                            label: 'Bonos',
                            route: route('bonuses.index'),
                            active: route().current('bonuses.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver bonos')
                        },
                        {
                            label: 'Descuentos',
                            route: 'discounts.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver descuentos')
                        },
                        {
                            label: 'Dias festivos',
                            route: route('holidays.index'),
                            active: route().current('holidays.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver dias festivos')
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
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Producción',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" /></svg>',
                    route: route('productions.index'),
                    active: route().current('productions.*'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Mercadotecnia',
                    icon: '<i class="fa-solid fa-lightbulb text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    options: [],
                    dropdown: false,
                    show: false
                },
                {
                    label: 'Más',
                    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-[19px]"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>',
                    active: route().current('machines.*') || route().current('more-additional-times.*') || route().current('meetings.*') || route().current('audits.*')
                    || route().current('samples.*') || route().current('production-costs.*'),
                    options: [
                        {
                            label: 'Tutoriales y manuales',
                            route: route('manuals.index'),
                            active: route().current('manuals.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver manuales')
                        },
                        {
                            label: 'Máquinas',
                            route: route('machines.index'),
                            active: route().current('machines.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver maquinas')
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: route('more-additional-times.index'),
                            active: route().current('more-additional-times.*'),
                            show: this.$page.props.auth.user.permissions.includes('Solicitudes de tiempo adicional personal')
                        },
                        {
                            label: 'Reuniones',
                            route: route('meetings.index'),
                            active: route().current('meetings.*'),
                            // show: this.$page.props.auth.user.permissions.includes('Reuniones personal')
                            show: false,
                        },
                        {
                            label: 'Biblioteca de medios',
                            route: route('dashboard'),
                            active: route().current('dashboard'),
                            show: this.$page.props.auth.user.permissions.includes('Ver medios')
                        },
                        {
                            label: 'Seguimiento de muestras',
                            route: route('samples.index'),
                            active: route().current('samples'),
                            show: this.$page.props.auth.user.permissions.includes('Ver muestra')
                        },
                        {
                            label: 'Costos de producción',
                            route: route('production-costs.index'),
                            show: this.$page.props.auth.user.permissions.includes('Ver costos de produccion')
                        },
                        {
                            label: 'Historial de acciones',
                            route: route('audits.index'),
                            show: this.$page.props.auth.user.permissions.includes('Ver historial de acciones')
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver maquinas') ||
                        this.$page.props.auth.user.permissions.includes('Solicitudes de tiempo adicional personal') ||
                        this.$page.props.auth.user.permissions.includes('Reuniones personal') ||
                        this.$page.props.auth.user.permissions.includes('Ver muestra') ||
                        this.$page.props.auth.user.permissions.includes('Ver biblioteca de medios') ||
                        this.$page.props.auth.user.permissions.includes('Ver historial de acciones') ||
                        this.$page.props.auth.user.permissions.includes('Ver costos de produccion')
                },
                {
                    label: 'Configuración',
                    icon: '<i class="fa-solid fa-gears text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    options: [],
                    dropdown: false,
                    show: false
                },
            ],
        }
    },
    components: {
        ResponsiveNavLink,
    },
    methods: {
        showOptions(index) {
            this.options = this.menus[index].options;
        },
    }
}
</script>