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
                                <div v-if="menu.notifications"
                                    class="bg-primary w-[5px] h-[5px] rounded-full absolute top-3 right-7"></div>
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
                    icon: '<i class="fa-solid fa-house text-xs"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    notifications: false,
                    options: [],
                    dropdown: false,
                    show: true
                },
                {
                    label: 'Catálogo de productos',
                    icon: '<i class="fa-solid fa-book-open text-xs"></i>',
                    route: route('catalog-products.index'),
                    active: route().current('catalog-products.*'),
                    notifications: false,
                    options: [],
                    dropdown: false,
                    show: this.$page.props.auth.user.permissions.includes('Ver catalogo de productos')
                },
                {
                    label: 'CRM',
                    icon: '<i class="fa-solid fa-chart-line text-sm"></i>',
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
                            label: 'Análisis de ventas',
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
                            notifications: false,
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
                    icon: '<i class="fa-solid fa-cart-shopping text-xs"></i>',
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
                    label: 'Proyectos',
                    icon: '<i class="fa-solid fa-check"></i>',
                    route: route('projects.index'),
                    active: route().current('projects.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'projects';
                    }),
                    show: this.$page.props.auth.user.permissions.includes('Ver proyectos')
                },
                {
                    label: 'Almacén',
                    icon: '<i class="fa-solid fa-warehouse text-xs"></i>',
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
                    icon: '<i class="fa-solid fa-user-group text-xs"></i>',
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
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'production';
                    }),
                    show: true
                },
                {
                    label: 'Calidad',
                    icon: '<i class="fa-solid fa-clipboard-check text-sm"></i>',
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
                    icon: '<i class="fa-solid fa-ellipsis text-xs"></i>',
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
                    icon: '<i class="fa-solid fa-gears text-sm"></i>',
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