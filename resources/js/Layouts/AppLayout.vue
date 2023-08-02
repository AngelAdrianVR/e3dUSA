<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import MobileSideNav from '@/Components/MyComponents/MobileSideNav.vue';
import SideNav from '@/Components/MyComponents/SideNav.vue';
import axios from 'axios';
import { ElNotification } from 'element-plus';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const nextAttendance = ref('');
const temporalFlag = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

const getAttendanceTextButton = async () => {
    try {
        const response = await axios.get(route('users.get-next-attendance'));
        nextAttendance.value = response.data.next;
    } catch (error) {
        console.error(error);
    }
};

const createKiosk = async () => {
    try {
        const response = await axios.post(route('kiosk.store'));

        if (response.status === 200) {
            // Set cookie in browser
            document.cookie = 'kioskToken=' + response.data.token + '; path=/; expires=Fri, 31 Dec 9999 23:59:59 GMT';
            ElNotification.success({
                title: 'Éxito',
                message: 'Dispositivo agregado como kiosco',
            });

            temporalFlag.value = true;
        }
    } catch (error) {
        console.error(error);
    }
};

const setAttendance = async () => {
    try {
        const response = await axios.get(route('users.set-attendance'));
        if (response.status === 200) {
            nextAttendance.value = response.data.next;
            ElNotification.success({
                title: 'Éxito',
                message: 'Registro correcto',
            });
        }
    } catch (error) {
        console.error(error);
        ElNotification.error({
            message: 'error:' + error.message,
            type: 'error'
        });
    }
};

const currentTime = ref(new Date().getHours());

    const greeting = computed(() => {
      if (currentTime.value >= 0 && currentTime.value < 12) {
        return {text: "Buenos días ", class: "fa-solid fa-sun text-yellow-500 mr-2"};
      } else if (currentTime.value >= 12 && currentTime.value < 19) {
        return {text: "Buenas tardes ", class: "fa-solid fa-cloud-sun text-orange-500 mr-2"};
      } else {
        return {text: "Buenas noches ", class: "fa-solid fa-moon text-purple-500 mr-2"};
      }
    });

    onMounted(() => {
    getAttendanceTextButton();

    setInterval(() => {
       currentTime.value = new Date().getHours();
     }, 60000); // 60000 ms = 1 minute
    });

</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <div class="overflow-hidden h-screen bg-[#F2F2F2] md:grid md:grid-cols-12">
            <aside>
                <SideNav />
            </aside>

            <main class="md:col-span-11">
                <nav class="bg-[#F2F2F2] border-b border-[#D9D9D9]">
                    <!-- Primary Navigation Menu -->
                    <div class="w-11/12 mx-auto">
                        <div class="flex justify-between h-14">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationMark class="w-1/3" />
                                    </Link>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <div class="ml-3 relative">
                                    <!-- Teams Dropdown -->
                                    <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    {{ $page.props.auth.user.current_team.name }}

                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <div class="w-60">
                                                <!-- Team Management -->
                                                <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        Manage Team
                                                    </div>

                                                    <!-- Team Settings -->
                                                    <DropdownLink
                                                        :href="route('teams.show', $page.props.auth.user.current_team)">
                                                        Team Settings
                                                    </DropdownLink>

                                                    <DropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                        :href="route('teams.create')">
                                                        Create New Team
                                                    </DropdownLink>

                                                    <!-- Team Switcher -->
                                                    <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                        <div class="border-t border-gray-200" />

                                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                                            Switch Teams
                                                        </div>

                                                        <template v-for="team in $page.props.auth.user.all_teams"
                                                            :key="team.id">
                                                            <form @submit.prevent="switchToTeam(team)">
                                                                <DropdownLink as="button">
                                                                    <div class="flex items-center">
                                                                        <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                                            class="mr-2 h-5 w-5 text-green-400"
                                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                            viewBox="0 0 24 24" stroke-width="1.5"
                                                                            stroke="currentColor">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>

                                                                        <div>{{ team.name }}</div>
                                                                    </div>
                                                                </DropdownLink>
                                                            </form>
                                                        </template>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                    </Dropdown>
                                </div>

                                <p class="mr-24">
                                    <i :class="greeting.class"></i>
                                    {{ greeting.text }} <strong>{{ $page.props.auth.user.name.split(' ')[0] }}</strong>
                                </p>

                                <el-popconfirm
                                    v-if="$page.props.isKiosk && nextAttendance && $page.props.auth.user.permissions.includes('Registrar asistencia')"
                                    confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                                    title="¿Continuar?" @confirm="setAttendance">
                                    <template #reference>
                                        <SecondaryButton v-if="nextAttendance != 'Dia terminado'" class="mr-14">
                                            {{ nextAttendance }}
                                        </SecondaryButton>
                                        <span v-else class="bg-[#75b3f9] text-[#0355B5] mr-14 rounded-md px-3 py-1">
                                            {{ nextAttendance }}
                                        </span>
                                    </template>
                                </el-popconfirm>

                                <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Crear kiosco')"
                                    confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                                    title="¿Continuar?" @confirm="createKiosk">
                                    <template #reference>
                                        <el-tooltip v-if="$page.props.isKiosk || temporalFlag"
                                            content="Se puede registrar asistencias desde este dispositivo">
                                            <span class="bg-[#75b3f9] text-[#0355B5] mr-14 rounded-md px-3 py-1  text-xs">
                                                Kiosco
                                            </span>
                                        </el-tooltip>
                                        <SecondaryButton v-else class="mr-14">
                                            Hacer kiosco
                                        </SecondaryButton>
                                    </template>
                                </el-popconfirm>

                                <el-tooltip v-if="$page.props.auth.user.permissions.includes('Chatear')" content="Chat"
                                    placement="bottom">
                                    <a :href="route('chatify')" target="_blank" class="mr-8">
                                        <i class="fa-solid fa-comments text-[#9A9A9A]"></i>
                                    </a>
                                </el-tooltip>

                                <!-- <i class="fa-solid fa-bell text-[#9A9A9A] mr-8"></i> -->

                                <!-- reminders -->
                                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-alarm-fill text-[#9A9A9A] mr-3" viewBox="0 0 16 16">
                                    <path
                                        d="M6 .5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H9v1.07a7.001 7.001 0 0 1 3.274 12.474l.601.602a.5.5 0 0 1-.707.708l-.746-.746A6.97 6.97 0 0 1 8 16a6.97 6.97 0 0 1-3.422-.892l-.746.746a.5.5 0 0 1-.707-.708l.602-.602A7.001 7.001 0 0 1 7 2.07V1h-.5A.5.5 0 0 1 6 .5zm2.5 5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9V5.5zM.86 5.387A2.5 2.5 0 1 1 4.387 1.86 8.035 8.035 0 0 0 .86 5.387zM11.613 1.86a2.5 2.5 0 1 1 3.527 3.527 8.035 8.035 0 0 0-3.527-3.527z" />
                                </svg> -->

                                <!-- Settings Dropdown -->
                                <div class="ml-3 relative">
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <button v-if="$page.props.jetstream.managesProfilePhotos"
                                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                    :src="$page.props.auth.user.profile_photo_url"
                                                    :alt="$page.props.auth.user.name">
                                            </button>

                                            <span v-else class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                    {{ $page.props.auth.user.name }}

                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <!-- Account Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Administrador de cuenta
                                            </div>

                                            <DropdownLink :href="route('profile.show')">
                                                Perfil
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                                :href="route('api-tokens.index')">
                                                API Tokens
                                            </DropdownLink>

                                            <div class="border-t border-gray-200" />

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    Cerrar sesión
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-mr-2 flex items-center sm:hidden">
                            <el-tooltip v-if="$page.props.auth.user.permissions.includes('Chatear')" content="Chat"
                                    placement="bottom">
                                    <a :href="route('chatify')" target="_blank" class="mr-8">
                                        <i class="fa-solid fa-comments text-[#9A9A9A]"></i>
                                    </a>
                                </el-tooltip>
                                <button
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                    @click="showingNavigationDropdown = !showingNavigationDropdown">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path
                                            :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path
                                            :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                            </div>
                        </div>
                    </div>
                    <!-- Responsive Navigation Menu -->
                    <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                        class="sm:hidden bg-[#d9d9d9] w-4/6 absolute right-0 top-14 z-10 max-h-[90%] overflow-y-scroll overflow-x-hidden shadow-lg border border-[#cccccc] pt-4">
                        <MobileSideNav />

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                    <img class="h-10 w-10 rounded-full object-cover"
                                        :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                </div>

                                <div>
                                    <div class="font-medium text-base text-gray-800">
                                        {{ $page.props.auth.user.name }}
                                    </div>
                                    <div class="font-medium text-sm text-gray-500">
                                        {{ $page.props.auth.user.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                    Perfil
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                    API Tokens
                                </ResponsiveNavLink>

                                <!-- Authentication -->
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        Cerrar sesión
                                    </ResponsiveNavLink>
                                </form>

                                <!-- Team Management -->
                                <template v-if="$page.props.jetstream.hasTeamFeatures">
                                    <div class="border-t border-gray-200" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Team
                                    </div>

                                    <!-- Team Settings -->
                                    <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)"
                                        :active="route().current('teams.show')">
                                        Team Settings
                                    </ResponsiveNavLink>

                                    <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                                        :href="route('teams.create')" :active="route().current('teams.create')">
                                        Create New Team
                                    </ResponsiveNavLink>

                                    <!-- Team Switcher -->
                                    <template v-if="$page.props.auth.user.all_teams.length > 1">
                                        <div class="border-t border-gray-200" />

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Switch Teams
                                        </div>

                                        <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                            <form @submit.prevent="switchToTeam(team)">
                                                <ResponsiveNavLink as="button">
                                                    <div class="flex items-center">
                                                        <svg v-if="team.id == $page.props.auth.user.current_team_id"
                                                            class="mr-2 h-5 w-5 text-green-400"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>{{ team.name }}</div>
                                                    </div>
                                                </ResponsiveNavLink>
                                            </form>
                                        </template>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </nav>
                <header v-if="$slots.header" class="bg-gray-800 border-t-2 border-gray-400">
                    <div class="mx-auto py-2 px-4 sm:px-6 lg:px-8 text-white">
                        <slot name="header" />
                    </div>
                </header>
                <div class="overflow-y-auto h-[calc(100vh-8rem)] bg-[#F2F2F2]">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
