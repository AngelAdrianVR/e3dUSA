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
                        <SideNavLink :href="menu.route" :active="menu.active">
                            <span v-html="menu.icon"></span>
                            <span class="text-xs font-normal text-center">
                                {{ menu.label }}
                            </span>
                        </SideNavLink>
                    </template>
                </div>
            </nav>
        </div>
    </div>
</template>

<script>

import SideNavLink from "@/Components/MyComponents/SideNavLink.vue";

export default {
    data() {
        return {
            menus: [
                {
                    label: 'Inicio',
                    icon: '<i class="fa-solid fa-house text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Catálogo de productos',
                    icon: '<i class="fa-solid fa-book-open text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Ventas',
                    icon: '<i class="fa-solid fa-shop text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Compras',
                    icon: '<i class="fa-solid fa-cart-shopping text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Almacén',
                    icon: '<i class="fa-solid fa-warehouse text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Recursos Humanos',
                    icon: '<i class="fa-solid fa-user-group text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Diseño',
                    icon: '<i class="fa-solid fa-pen-ruler text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Producción',
                    icon: '<i class="fa-solid fa-helmet-safety text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Mercadotecnia',
                    icon: '<i class="fa-solid fa-lightbulb text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Más',
                    icon: '<i class="fa-solid fa-ellipsis text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
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
    }
}
</script>