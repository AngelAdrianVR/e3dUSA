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
                                <span v-html="menu.icon" class="relative"></span>
                                <span class="leading-none font-normal text-center">
                                    {{ menu.label }}
                                </span>
                                <div class="bg-primary w-[5px] h-[5px] rounded-full absolute top-3 right-7"></div>
                            </template>
                            <template #content>
                                <template v-for="option in menu.options" :key="option">
                                    <DropdownNavLink v-if="option.show" :href="route(option.route)" :notifications="option.notifications">
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
            <p class="text-sm text-primary mt-3 mx-20">Recuerda que tu tiempo trabajado deja de contar a la hora de tu
                salida. Si registras después de esta hora ya no se tomará en cuenta</p>
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
                    label: 'Ventas',
                    icon: '<i class="fa-solid fa-shop text-xs"></i>',
                    active: route().current('quotes.*') || route().current('companies.*') || route().current('sales.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'quote' || notification.data.module === 'sale';
                    }),
                    options: [
                        {
                            label: 'Cotizaciones',
                            route: 'quotes.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver cotizaciones'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'quote';
                            }),
                        },
                        {
                            label: 'Clientes',
                            route: 'companies.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver clientes'),
                            notifications: false,
                        },
                        {
                            label: 'Órdenes de venta',
                            route: 'sales.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver ordenes de venta'),
                            notifications: false,
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
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'po' || notifications.data.module === 'suppliers';
                    }),
                    options: [
                        {
                            label: 'Proveedores',
                            route: 'suppliers.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver proveedores'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'suppler';
                            }),
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
                    label: 'Almacén',
                    icon: '<i class="fa-solid fa-warehouse text-xs"></i>',
                    active: route().current('storages.*') || route().current('raw-materials.*'),
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'raw-material' || notification.data.module === 'consumable'
                            || notification.data.module === 'finished-product' || notification.data.module === 'scrap';
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
                                return notification.data.module === 'finished-product';
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
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'payroll' || notification.data.module === 'admin-additional-time'
                            || notification.data.module === 'user';
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
                    notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'design';
                    }),
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
                        return notification.data.module === 'machine' || notifications.data.module === 'meeting' ||
                            notification.data.module === 'sample' || notification.data.module === 'media-library';
                    }),
                    options: [
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
                            show: this.$page.props.auth.user.permissions.includes('Reuniones personal'),
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
                            show: this.$page.props.auth.user.permissions.includes('Ver scrap'),
                            notifications: this.$page.props.auth.user?.notifications?.some(notification => {
                                return notification.data.module === 'sample';
                            }),
                        },
                        {
                            label: 'Costos de producción',
                            route: 'production-costs.index',
                            show: this.$page.props.auth.user.permissions.includes('Ver costos de produccion'),
                            notifications: false,
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
                    notifications: false,
                    show: false
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