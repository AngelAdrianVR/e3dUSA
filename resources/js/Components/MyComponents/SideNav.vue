<template>
    <!-- sidebar -->
    <div class="h-screen hidden lg:block shadow-lg relative">
        <div class="bg-[#D9D9D9] h-full overflow-auto">
            <nav class="pt-2">
                <div>
                    <div class="mb-4 items-center font-semibold text-[#0355B5] flex flex-col">
                        <span>Work</span>
                        <span>01:37:27</span>
                    </div>
                    <template v-for="(menu, index) in menus" :key="index">
                        <SideNavLink :href="menu.route" :active="menu.active" :dropdown="menu.dropdown">
                            <template #trigger>
                                <span v-html="menu.icon"></span>
                                <span class="leading-none font-normal text-center">
                                    {{ menu.label }}
                                </span>
                            </template>
                            <template #content>
                                <DropdownNavLink v-for="option in menu.options" :key="option" :href="route(option.route)">
                                    {{ option.label }}
                                </DropdownNavLink>
                            </template>
                        </SideNavLink>
                    </template>
                </div>
            </nav>
        </div>
    </div>
</template>

<script>

import SideNavLink from "@/Components/MyComponents/SideNavLink.vue";
import DropdownNavLink from "@/Components/MyComponents/DropdownNavLink.vue";

export default {
    data() {
        return {
            menus: [
                {
                    label: 'Inicio',
                    icon: '<i class="fa-solid fa-house text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    options: [],
                    dropdown: false,
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Catálogo de productos',
                    icon: '<i class="fa-solid fa-book-open text-xs"></i>',
                    route: route('catalog-products.index'),
                    active: route().current('catalog-products.*'),
                    options: [],
                    dropdown: false,
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Ventas',
                    icon: '<i class="fa-solid fa-shop text-xs"></i>',
                    active: route().current('quotes.*') || route().current('companies.*') || route().current('sales.*'),
                    options: [
                        {
                            label: 'Cotizaciones',
                            route: 'quotes.index'
                        },
                        {
                            label: 'Clientes',
                            route: 'companies.index'
                        },
                        {
                            label: 'Órdenes de venta',
                            route: 'sales.index'
                        },

                    ],
                    dropdown: true,
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Compras',
                    icon: '<i class="fa-solid fa-cart-shopping text-xs"></i>',
                    active: route().current('suppliers.*') || route().current('purchases.*'),
                    options: [
                        {
                            label: 'Proveedores',
                            route: 'suppliers.index'
                        },
                        {
                            label: 'Órdenes de compra',
                            route: 'purchases.index'
                        },

                    ],
                    dropdown: true,
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Almacén',
                    icon: '<i class="fa-solid fa-warehouse text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    options: [
                        {
                            label: 'Materia prima',
                            route: 'storages.raw-materials.index'
                        },
                        {
                            label: 'Insumos',
                            route: 'dashboard'
                        },
                        {
                            label: 'Producto terminado',
                            route: 'dashboard'
                        },
                        {
                            label: 'Scrap',
                            route: 'dashboard'
                        },
                    ],
                    dropdown: true
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Recursos Humanos',
                    icon: '<i class="fa-solid fa-user-group text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    options: [
                        {
                            label: 'Nóminas',
                            route: 'dashboard'
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'dashboard'
                        },
                        {
                            label: 'Personal',
                            route: 'dashboard'
                        },
                    ],
                    dropdown: true
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Diseño',
                    icon: '<i class="fa-solid fa-pen-ruler text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Producción',
                    icon: '<i class="fa-solid fa-helmet-safety text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Mercadotecnia',
                    icon: '<i class="fa-solid fa-lightbulb text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Más',
                    icon: '<i class="fa-solid fa-ellipsis text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    options: [
                        {
                            label: 'Máquinas',
                            route: 'dashboard'
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'dashboard'
                        },
                        {
                            label: 'Reuniones',
                            route: 'dashboard'
                        },
                        {
                            label: 'Biblioteca de medios',
                            route: 'dashboard'
                        },
                    ],
                    dropdown: true
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Configuración',
                    icon: '<i class="fa-solid fa-gears text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
            ],
        }
    },
    components: {
        SideNavLink,
        DropdownNavLink
    }
}
</script>