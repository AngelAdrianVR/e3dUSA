<template>
    <i @click="options = null" v-if="options" class="fa-solid fa-chevron-left text-xs ml-4 mb-3"></i>
    <template v-for="(menu, index) in options ?? menus" :key="index">
        <ResponsiveNavLink v-if="!menu?.options?.length && menu.show" :href="menu.route" :active="menu.active">
            <div class="flex justify-between items-center">
                <p class="space-x-2">
                    <span v-html="menu.icon"></span>
                    <span>{{ menu.label }}</span>
                </p>
                <i v-if="menu?.options?.length" class="fa-solid fa-chevron-right text-xs"></i>
            </div>
        </ResponsiveNavLink>
        <ResponsiveNavLink v-else-if="menu.show" as="button" @click="showOptions(index)" :active="menu.active">
            <div class="flex justify-between items-center">
                <p class="space-x-2">
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
                    icon: '<i class="fa-solid fa-house text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Catálogo de productos',
                    icon: '<i class="fa-solid fa-book-open text-xs"></i>',
                    route: route('catalog-products.index'),
                    active: route().current('catalog-products.*'),
                    options: [],
                    dropdown: false,
                    show: this.$page.props.auth.user.permissions.includes('Ver catalogo de productos')
                },
                {
                    label: 'CRM',
                    icon: '<i class="fa-solid fa-chart-line text-xs"></i>',
                    active:  route().current('crm.*') || route().current('quotes.*') || route().current('companies.*') || route().current('sales.*') || route().current('oportunities.*') || route().current('oportunity-tasks.*') || route().current('client-monitors.*'),
                    options: [
                        {
                            label: 'Inicio',
                            route: route('crm.dashboard'),
                            active: route().current('crm.*'),
                            show: this.$page.props.auth.user.permissions.includes('Inicio crm')
                        },
                        {
                            label: 'Cotizaciones',
                            route: route('quotes.index'),
                            active: route().current('quotes.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones')
                        },
                        {
                            label: 'Clientes',
                            route: route('companies.index'),
                            active: route().current('companies.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver clientes')
                        },
                        {
                            label: 'Órdenes de venta',
                            route: route('sales.index'),
                            active: route().current('sales.*'),
                            show: this.$page.props.auth.user.permissions.includes('Ver ordenes de venta')
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
                        

                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones') ||
                        this.$page.props.auth.user.permissions.includes('Ver clientes') ||
                        this.$page.props.auth.user.permissions.includes('Ver ordenes de venta') ||
                        this.$page.props.auth.user.permissions.includes('Ver oportunidades')||
                        this.$page.props.auth.user.permissions.includes('Ver seguimiento integral')
                },
                {
                    label: 'Proyectos',
                    icon: '<i class="fa-solid fa-check"></i>',
                    route: route('projects.index'),
                    active: route().current('projects.*'),
                    options: [],
                    dropdown: false,
                    show: this.$page.props.auth.user.permissions.includes('Ver proyectos')
                },
                {
                    label: 'Compras',
                    icon: '<i class="fa-solid fa-cart-shopping text-xs"></i>',
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
                    icon: '<i class="fa-solid fa-warehouse text-xs"></i>',
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
                    icon: '<i class="fa-solid fa-user-group text-xs"></i>',
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
                    icon: '<i class="fa-solid fa-pen-ruler text-xs"></i>',
                    route: route('designs.index'),
                    active: route().current('designs.*'),
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Producción',
                    icon: '<i class="fa-solid fa-helmet-safety text-xs"></i>',
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
                    icon: '<i class="fa-solid fa-ellipsis text-xs"></i>',
                    active: route().current('machines.*') || route().current('more-additional-times.*') || route().current('meetings.*') || route().current('audits.*')
                    || route().current('samples.*') || route().current('production-costs.*'),
                    options: [
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
                            show: this.$page.props.auth.user.permissions.includes('Reuniones personal')
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