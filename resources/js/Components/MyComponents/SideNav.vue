<template>
    <!-- sidebar -->
    <div class="h-screen hidden md:block shadow-lg relative">
        <div class="bg-[#D9D9D9] h-full overflow-auto">
            <nav class="pt-2">
                <div>
                    <div v-if="$page.props.auth.user?.employee_properties" class="mb-4 items-center font-semibold text-[#0355B5] flex flex-col">
                        <span>Horas / semana</span>
                        <span>{{ $page.props.week_time.formatted }} / {{ $page.props.auth.user?.employee_properties?.hours_per_week ?? 0 }}h</span>
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
                    active: route().current('storages.*'),
                    options: [
                        {
                            label: 'Materia prima',
                            route: 'storages.raw-materials.index'
                        },
                        {
                            label: 'Insumos',
                            route: 'storages.consumables.index'
                        },
                        {
                            label: 'Producto terminado',
                            route: 'storages.finished-products.index'
                        },
                        {
                            label: 'Scrap',
                            route: 'storages.scraps.index'
                        },
                    ],
                    dropdown: true
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Recursos Humanos',
                    icon: '<i class="fa-solid fa-user-group text-xs"></i>',
                    active: route().current('payrolls.*') 
                        || route().current('admin-additional-times.*')
                        || route().current('users.*')
                        || route().current('role-permission.*')
                        || route().current('bonuses.*')
                        || route().current('holidays.*'),
                    options: [
                        {
                            label: 'Nóminas',
                            route: 'payrolls.index'
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'admin-additional-times.index'
                        },
                        {
                            label: 'Personal',
                            route: 'users.index'
                        },
                        {
                            label: 'Roles y permisos',
                            route: 'role-permission.index'
                        },
                        {
                            label: 'Bonos',
                            route: 'bonuses.index'
                        },
                        {
                            label: 'Dias festivos',
                            route: 'holidays.index'
                        },
                    ],
                    dropdown: true
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Diseño',
                    icon: '<i class="fa-solid fa-pen-ruler text-xs"></i>',
                    route: route('designs.index'),
                    active: route().current('designs.*'),
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
                    active: route().current('machines.*') || route().current('more-additional-times.*') || route().current('meetings.*'),
                    options: [
                        {
                            label: 'Máquinas',
                            route: 'machines.index'
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'more-additional-times.index'
                        },
                        {
                            label: 'Reuniones',
                            route: 'meetings.index'
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