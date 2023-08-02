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
                            $page.props.auth.user?.employee_properties?.hours_per_week ?? 0 }}h</span>
                    </div>
                    <template v-for="(menu, index) in menus" :key="index">
                        <SideNavLink v-if="menu.show" :href="menu.route" :active="menu.active" :dropdown="menu.dropdown"
                            class="mb-px">
                            <template #trigger>
                                <span v-html="menu.icon"></span>
                                <span class="leading-none font-normal text-center">
                                    {{ menu.label }}
                                </span>
                            </template>
                            <template #content>
                                <template v-for="option in menu.options" :key="option">
                                    <DropdownNavLink v-if="option.show" :href="route(option.route)">
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
                    label: 'Ventas',
                    icon: '<i class="fa-solid fa-shop text-xs"></i>',
                    active: route().current('quotes.*') || route().current('companies.*') || route().current('sales.*'),
                    options: [
                        {
                            label: 'Cotizaciones',
                            route: 'quotes.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones')
                        },
                        {
                            label: 'Clientes',
                            route: 'companies.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver clientes')
                        },
                        {
                            label: 'Órdenes de venta',
                            route: 'sales.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver ordenes de venta')
                        },

                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones') ||
                        this.$page.props.auth.user.permissions.includes('Ver clientes') ||
                        this.$page.props.auth.user.permissions.includes('Ver ordenes de venta')
                },
                {
                    label: 'Compras',
                    icon: '<i class="fa-solid fa-cart-shopping text-xs"></i>',
                    active: route().current('suppliers.*') || route().current('purchases.*'),
                    options: [
                        {
                            label: 'Proveedores',
                            route: 'suppliers.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver proveedores')
                        },
                        {
                            label: 'Órdenes de compra',
                            route: 'purchases.index',
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
                    active: route().current('storages.*') || route().current('raw-materials.*'),
                    options: [
                        {
                            label: 'Materia prima',
                            route: 'storages.raw-materials.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver materia prima')
                        },
                        {
                            label: 'Insumos',
                            route: 'storages.consumables.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver insumos')
                        },
                        {
                            label: 'Producto terminado',
                            route: 'storages.finished-products.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver producto terminado')
                        },
                        {
                            label: 'Scrap',
                            route: 'storages.scraps.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver scrap')
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
                        || route().current('holidays.*'),
                    options: [
                        {
                            label: 'Nóminas',
                            route: 'payrolls.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver nominas')
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'admin-additional-times.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver solicitudes de tiempo adicional')
                        },
                        {
                            label: 'Personal',
                            route: 'users.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver personal')
                        },
                        {
                            label: 'Roles y permisos',
                            route: 'role-permission.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver roles y permisos')
                        },
                        {
                            label: 'Bonos',
                            route: 'bonuses.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver bonos')
                        },
                        {
                            label: 'Dias festivos',
                            route: 'holidays.index',
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
                    show: true
                },
                {
                    label: 'Producción',
                    icon: '<i class="fa-solid fa-helmet-safety text-xs"></i>',
                    route: route('productions.index'),
                    active: route().current('productions.*'),
                    show: true
                },
                {
                    label: 'Mercadotecnia',
                    icon: '<i class="fa-solid fa-lightbulb text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    show: false
                },
                {
                    label: 'Más',
                    icon: '<i class="fa-solid fa-ellipsis text-xs"></i>',
                    active: route().current('machines.*') || route().current('more-additional-times.*') || route().current('meetings.*') ||
                        route().current('samples.*') || route().current('production-costs.*'),
                    options: [
                        {
                            label: 'Máquinas',
                            route: 'machines.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver maquinas')
                        },
                        {
                            label: 'Solicitudes de tiempo adicional',
                            route: 'more-additional-times.index',
                            show: this.$page.props.auth.user.permissions.includes('Solicitudes de tiempo adicional personal')
                        },
                        {
                            label: 'Reuniones',
                            route: 'meetings.index',
                            show: this.$page.props.auth.user.permissions.includes('Reuniones personal')
                        },
                        {
                            label: 'Biblioteca de medios',
                            route: 'dashboard',
                            // show: this.$page.props.auth.user.permissions.includes('Ver medios')
                            show: false
                        },
                        {
                            label: 'Seguimiento de muestras',
                            route: 'samples.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver scrap')
                        },
                        {
                            label: 'Costos de producción',
                            route: 'production-costs.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver costos de produccion')
                        },
                    ],
                    dropdown: true,
                    show: this.$page.props.auth.user.permissions.includes('Ver maquinas') ||
                        this.$page.props.auth.user.permissions.includes('Solicitudes de tiempo adicional personal') ||
                        this.$page.props.auth.user.permissions.includes('Reuniones personal') ||
                        this.$page.props.auth.user.permissions.includes('Ver biblioteca de medios') ||
                        this.$page.props.auth.user.permissions.includes('Ver costos de produccion')
                },
                {
                    label: 'Configuración',
                    icon: '<i class="fa-solid fa-gears text-sm"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboar'),
                    show: false
                },
            ],
            showModal: false,
            payrollId: null,
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
        async getCurrentPayroll() {
            try {
                const response = await axios.post(route('payrolls.get-current-payroll'));

                if (response.status === 200) {
                    this.payrollId = response.data.item.id;
                }

            } catch (error) {
                console.log(error.message);
            }
        }
    },
    mounted() {
        this.getCurrentPayroll();
    }
}
</script>