<template>
    <!-- sidebar -->
    <div class="h-screen hidden lg:block shadow-lg relative">
        <div class="bg-[#D9D9D9] h-full">
            <nav class="pt-14">
                <div>
                    <div class="mb-16 items-center font-semibold text-[#0355B5] flex flex-col">
                        <span>Work</span>
                        <span>01:37:27</span>
                    </div>
                    <template v-for="(menu, index) in menus" :key="index">
                        <SideNavLink :href="menu.route" :active="menu.active">
                            <span v-html="menu.icon"></span>
                            <span class="text-sm font-normal">
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
                    icon: '<i class="fa-solid fa-house"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
                    // show: this.$page.props.auth.user.is_admin
                },
                {
                    label: 'Catálogo',
                    icon: '<i class="fa-solid fa-book-open"></i>',
                    route: route('dashboard'),
                    active: route().current('dashboard'),
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