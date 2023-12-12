<script setup>
import { ref, onMounted, computed } from "vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import MobileSideNav from "@/Components/MyComponents/MobileSideNav.vue";
import SideNav from "@/Components/MyComponents/SideNav.vue";
import Modal from "@/Components/Modal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import axios from "axios";
import { ElNotification } from "element-plus";
import dayjs from 'dayjs';

defineProps({
  title: String,
});

const barCodeRef = ref("");
const showingNavigationDropdown = ref(false);
const nextAttendance = ref("");
const isPaused = ref(null);
const temporalFlag = ref(false);
const qrScan = ref(false);
const is_product = ref(true);
const loading = ref(false);
const partNumberInput = ref(null);
const machineFound = ref(null);
const productFound = ref(null);
const catalogProductFound = ref(null);
const unseenMessages = ref(null);
const openPasswordModal = ref(false);
const daysSinceNewDate = ref(0);
const superPassword = ref(null);


const form = useForm({
  barCode: null,
  scanType: "Entrada",
});


const getUnseenMessages = async () => {
  try {
    const response = await axios.post(route("users.get-unseen-messages"));

    if (response.status === 200) {
      unseenMessages.value = response.data.count;
    }
  } catch (error) {
    console.log(error);
  }
};

const scanMachineForm = async () => {
  try {
    machineFound.value = null;
    loading.value = true;
    const response = await axios.post(route("machines.QR-search-machine"), {
      barCode: form.barCode,
    });

    if (response.status === 200) {
      if (response.data.item == null) {
        ElNotification.error({
          title: "Error",
          message: "No se encontró ninguna máquina",
        });
      } else {
        machineFound.value = response.data.item;
        form.barCode = null;
        partNumberInput.value.focus();
      }
    }
  } catch (error) {
    ElNotification.error({
      title: "Error",
      message: "Formato de código inválido",
    });
    console.log(error);
  } finally {
    form.barCode = null;
    loading.value = false;
  }
}

const scanForm = async () => {
  if (form.scanType == "Buscar materia prima") {
    try {
      productFound.value = null;
      loading.value = true;
      const response = await axios.post(route("storages.QR-search-product"), {
        barCode: form.barCode,
        scanType: form.scanType,
      });

      if (response.status === 200) {
        if (response.data.item == null) {
          ElNotification.error({
            title: "Error",
            message: "No se encontró ningun producto",
          });
        } else {
          productFound.value = response.data.item;
          form.barCode = null;
          partNumberInput.value.focus();
        }
      }
    } catch (error) {
      ElNotification.error({
        title: "Error",
        message: "Formato de código inválido",
      });
      console.log(error);
    } finally {
      form.barCode = null;
      loading.value = false;
    }
    // ------------- productos de catalogo para admin ----------------------
  } else if (form.scanType == "Producto de catalogo") {
    try {
      productFound.value = null;
      loading.value = true;
      const response = await axios.post(route("catalog-products.QR-search-catalog-product"), {
        barCode: form.barCode,
        scanType: form.scanType,
      });

      if (response.status === 200) {
        if (response.data.item == null) {
          ElNotification.error({
            title: "Error",
            message: "No se encontró ningun producto de catálogo",
          });
        } else {
          catalogProductFound.value = response.data.item;
          form.barCode = null;
          partNumberInput.value.focus();
        }
      }
    } catch (error) {
      ElNotification.error({
        title: "Error",
        message: "Formato de código inválido",
      });
      console.log(error);
    } finally {
      form.barCode = null;
      loading.value = false;
    }
  } else {
    try {
      const response = await axios.post(route("storages.QR"), {
        barCode: form.barCode,
        scanType: form.scanType,
      });
      if (response.status === 200) {
        ElNotification.success({
          title: "Éxito",
          message: response.data.message,
        });
        form.barCode = null;
        partNumberInput.value.focus();
      }
    } catch (error) {
      if (error.response.status === 422) {
        ElNotification.error({
          title: "Error",
          message: error.response.data.message,
        });
      } else {
        ElNotification.error({
          title: "Error",
          message: "Formato de código inválido",
        });
        console.log("error:", error);
      }
    } finally {
      form.barCode = null;
    }
  }
};

const switchToTeam = (team) => {
  router.put(
    route("current-team.update"),
    {
      team_id: team.id,
    },
    {
      preserveState: false,
    }
  );
};

const logout = () => {
  router.post(route("logout"));
};

const getAttendanceTextButton = async () => {
  try {
    const response = await axios.get(route("users.get-next-attendance"));
    nextAttendance.value = response.data.next;
  } catch (error) {
    console.error(error);
  }
};

const getPauseStatus = async () => {
  try {
    const response = await axios.get(route("users.get-pause-status"));
    isPaused.value = response.data.status;
  } catch (error) {
    console.error(error);
  }
};

const createKiosk = async () => {
  try {
    const response = await axios.post(route("kiosk.store"));

    if (response.status === 200) {
      // Set cookie in browser
      document.cookie =
        "kioskToken=" +
        response.data.token +
        "; path=/; expires=Fri, 31 Dec 9999 23:59:59 GMT";
      ElNotification.success({
        title: "Éxito",
        message: "Dispositivo agregado como kiosco",
      });

      temporalFlag.value = true;
    }
  } catch (error) {
    console.error(error);
  }
};

const setAttendance = async () => {
  try {
    const response = await axios.get(route("users.set-attendance"));
    if (response.status === 200) {
      nextAttendance.value = response.data.next;
      ElNotification.success({
        title: "Éxito",
        message: "Registro correcto",
      });
    }
  } catch (error) {
    console.error(error);
    if (error?.response.status === 422) {
      ElNotification.error({
        message: error.response.data.message,
        type: "error",
      });
    } else {
      ElNotification.error({
        message: 'Hubo algún problema en el servior, repórtalo con soporte',
        type: "error",
      });
    }
  }
};

const setPause = async () => {
  try {
    const response = await axios.get(route("users.set-pause"));
    if (response.status === 200) {
      isPaused.value = response.data.status;
      ElNotification.success({
        title: "Éxito",
        message: response.data.message,
      });
    }
  } catch (error) {
    console.error(error);
    if (error?.response.status === 422) {
      ElNotification.error({
        message: error.response.data.message,
        type: "error",
      });
    } else {
      ElNotification.error({
        message: 'Hubo algún problema en el servior, repórtalo con soporte',
        type: "error",
      });
    }
  }
};

const QRScan = () => {
  qrScan.value = true;
  is_product.value = true;
};

const timeSinceNewPrice = (company_info) => {
  const currentDate = dayjs();
  const newDate = dayjs(company_info.pivot.new_date);

  const diff = currentDate.diff(newDate, 'day');
  const formattedDifference = diff < 31 ? `${diff} días` : `${currentDate.diff(newDate, 'month')} mes(es)`;

  return formattedDifference;
};

const QRMachineScan = () => {
  qrScan.value = true;
  is_product.value = false;
};

const currentTime = ref(new Date().getHours());

const greeting = computed(() => {
  if (currentTime.value >= 0 && currentTime.value < 12) {
    return {
      text: "Buenos días ",
      class: "fa-solid fa-sun text-yellow-500 mr-2",
    };
  } else if (currentTime.value >= 12 && currentTime.value < 19) {
    return {
      text: "Buenas tardes ",
      class: "fa-solid fa-cloud-sun text-orange-500 mr-2",
    };
  } else {
    return {
      text: "Buenas noches ",
      class: "fa-solid fa-moon text-purple-500 mr-2",
    };
  }
});

onMounted(() => {
  getAttendanceTextButton();
  getPauseStatus();
  getUnseenMessages();

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

                          <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                          <DropdownLink :href="route(
                            'teams.show',
                            $page.props.auth.user.current_team
                          )
                            ">
                            Team Settings
                          </DropdownLink>

                          <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                            Create New Team
                          </DropdownLink>

                          <!-- Team Switcher -->
                          <template v-if="$page.props.auth.user.all_teams.length > 1">
                            <div class="border-t border-gray-200" />

                            <div class="block px-4 py-2 text-xs text-gray-400">
                              Switch Teams
                            </div>

                            <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                              <form @submit.prevent="switchToTeam(team)">
                                <DropdownLink as="button">
                                  <div class="flex items-center">
                                    <svg v-if="team.id ==
                                      $page.props.auth.user.current_team_id
                                      " class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
                                      fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round"
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

                <!-- <el-tooltip content="Escanear máquina con código QR">
                  <SecondaryButton @click="QRMachineScan" class="mr-2">
                    <i class="fa-solid fa-qrcode mr-1"></i> Máquinas
                  </SecondaryButton>
                </el-tooltip> -->

                <el-tooltip content="Escanear producto con código QR">
                  <PrimaryButton @click="QRScan" class="mr-10">
                    <i class="fa-solid fa-qrcode"></i>
                  </PrimaryButton>
                </el-tooltip>

                <p class="mr-4">
                  <i :class="greeting.class"></i>
                  {{ greeting.text }}
                  <strong>{{
                    $page.props.auth.user.name.split(" ")[0]
                  }}</strong>
                </p>

                <!-- pause work time -->
                <el-popconfirm v-if="$page.props.isKiosk && isPaused !== null &&
                    nextAttendance &&
                    $page.props.auth.user.permissions.includes(
                      'Registrar asistencia'
                    )
                    " confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
                  :title="isPaused ? '¿Reanudar tiempo?' : 'Pausar tiempo?'" @confirm="setPause">
                  <template #reference>
                    <button v-if="nextAttendance == 'Registrar salida'"
                      class="w-8 h-8 mr-5 rounded-full border-2 border-[#0355B5] text-secondary">
                      <i v-if="isPaused" class="fa-solid fa-play"></i>
                      <i v-else class="fa-solid fa-pause"></i>
                    </button>
                  </template>
                </el-popconfirm>

                <!-- attendances -->
                <div v-if="$page.props.isKiosk &&
                  nextAttendance &&
                  $page.props.auth.user.permissions.includes(
                    'Registrar asistencia'
                  ) && !isPaused">
                  <el-popconfirm
                    v-if="nextAttendance != 'Registrar salida' && $page.props.auth.user.employee_properties.job_position != 'Auxiliar de producción'"
                    confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                    @confirm="setAttendance">
                    <template #reference>
                      <SecondaryButton v-if="nextAttendance != 'Dia terminado'" class="mr-14">
                        {{ nextAttendance }}
                      </SecondaryButton>
                      <span v-else class="bg-[#75b3f9] text-[#0355B5] mr-14 rounded-md px-3 py-1">
                        {{ nextAttendance }}
                      </span>
                    </template>
                  </el-popconfirm>
                  <div v-else>
                    <SecondaryButton @click="openPasswordModal = true" v-if="nextAttendance != 'Dia terminado'"
                      class="mr-14">
                      {{ nextAttendance }}
                    </SecondaryButton>
                    <span v-else class="bg-[#75b3f9] text-[#0355B5] mr-14 rounded-md px-3 py-1">
                      {{ nextAttendance }}
                    </span>
                  </div>
                </div>
                
                <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Crear kiosco')
                  " confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                  @confirm="createKiosk">
                  <template #reference>
                    <el-tooltip v-if="$page.props.isKiosk || temporalFlag"
                      content="Se puede registrar asistencias desde este dispositivo">
                      <span class="bg-[#75b3f9] text-[#0355B5] mr-14 rounded-md px-3 py-1 text-xs">
                        Kiosco
                      </span>
                    </el-tooltip>
                    <SecondaryButton v-else class="mr-14">
                      Hacer kiosco
                    </SecondaryButton>
                  </template>
                </el-popconfirm>

                <div class="mr-9 relative">
                  <el-tooltip content="Calendario">
                    <Link :href="route('calendars.index')">
                    <i class="fa-solid fa-calendar-days text-[#9A9A9A]"></i>
                    </Link>
                  </el-tooltip>
                  <div v-if="$page.props.auth.user?.notifications?.some(notification => {
                    return notification.data.module === 'calendar';
                  })" class="bg-primary w-[10px] h-[10px] border border-white rounded-full absolute -top-1 -right-2">
                  </div>
                </div>

                <div class="relative">
                  <el-tooltip v-if="$page.props.auth.user.permissions.includes('Chatear')" content="Chat"
                    placement="bottom">
                    <a :href="route('chatify')" target="_blank" class="mr-8">
                      <i class="fa-solid fa-comments text-[#9A9A9A]"></i>
                    </a>
                  </el-tooltip>
                  <div v-if="unseenMessages > 0"
                    class="absolute bottom-4 right-5 bg-primary text-white w-4 h-4 flex items-center justify-center text-[10px] rounded-full">
                    {{ unseenMessages }}
                  </div>
                </div>

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
                        <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                          :alt="$page.props.auth.user.name" />
                      </button>

                      <span v-else class="inline-flex rounded-md">
                        <button type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                          {{ $page.props.auth.user.name }}

                          <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
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

                      <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                        API Tokens
                      </DropdownLink>

                      <div class="border-t border-gray-200" />

                      <!-- Authentication -->
                      <form @submit.prevent="logout">
                        <DropdownLink as="button"> Cerrar sesión </DropdownLink>
                      </form>
                    </template>
                  </Dropdown>
                </div>
              </div>

              <!-- Hamburger -->
              <div class="-mr-2 flex items-center sm:hidden">
                <div class="relative">
                  <el-tooltip v-if="$page.props.auth.user.permissions.includes('Chatear')" content="Chat"
                    placement="bottom">
                    <a :href="route('chatify')" target="_blank" class="mr-8">
                      <i class="fa-solid fa-comments text-[#9A9A9A]"></i>
                    </a>
                  </el-tooltip>
                  <div v-if="unseenMessages > 0"
                    class="absolute bottom-4 right-5 bg-primary text-white w-4 h-4 flex items-center justify-center text-[10px] rounded-full">
                    {{ unseenMessages }}
                  </div>
                </div>
                <button
                  class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                  @click="
                    showingNavigationDropdown = !showingNavigationDropdown
                    ">
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <!-- Responsive Navigation Menu -->
          <div :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
            class="z-40 sm:hidden bg-[#d9d9d9] w-4/6 absolute right-0 top-14 max-h-[90%] overflow-y-scroll overflow-x-hidden shadow-lg border border-[#cccccc] pt-4">
            <MobileSideNav />

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
              <div class="flex items-center px-4">
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                  <img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name" />
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

                <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')"
                  :active="route().current('api-tokens.index')">
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
                  <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)
                    " :active="route().current('teams.show')">
                    Team Settings
                  </ResponsiveNavLink>

                  <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')"
                    :active="route().current('teams.create')">
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
                            <svg v-if="team.id == $page.props.auth.user.current_team_id
                              " class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
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
  <!-- Password modal -->
  <DialogModal :show="openPasswordModal" @close="openPasswordModal = false">
    <template #title>
      Contraseña de supervisor
    </template>
    <template #content>
      <p class="text-center text-sm my-4">Para garantizar la precisión en nuestros registros de producción, se solicita
        que obtengan la autorización del
        supervisor antes de registrar su salida. Asegúrense de proporcionar el estatus de cualquier trabajo pendiente. La
        contraseña del supervisor es necesaria para completar este proceso.
        Gracias por su colaboración.</p>
      <InputLabel value="Contraseña de supervisor" />
      <input type="password" class="input" v-model="superPassword" placeholder="Ingresar contraseña">
    </template>
    <template #footer>
      <PrimaryButton @click="setAttendance(); openPasswordModal = false;" :disabled="superPassword != 'Supervisor'">
        Registrar salida</PrimaryButton>
    </template>
  </DialogModal>

  <!-- -------------- QR modal starts----------------------- -->
  <Modal :show="qrScan" @close="qrScan = false">
    <div class="mx-7 my-4 space-y-4 relative">
      <div class="flex justify-center mb-4">
        <h2 v-if="is_product" class="font-bold text-center mr-2">Movimientos y detalles de producto</h2>
        <h2 v-else class="font-bold text-center mr-2">Búsqueda de maquinaria</h2>
        <div @click="
          qrScan = false;
        form.reset();
        "
          class="cursor-pointer w-5 h-5 rounded-full border-2 border-black flex items-center justify-center absolute top-0 right-0">
          <i class="fa-solid fa-xmark"></i>
        </div>
      </div>

      <form v-if="is_product" @submit.prevent="scanForm">
        <div style="margin-top: 20px">
          <el-radio-group v-model="form.scanType">
            <el-radio-button v-if="$page.props.auth.user.permissions.includes('Crear entradas')
              " label="Entrada" />
            <el-radio-button v-if="$page.props.auth.user.permissions.includes('Crear salidas')" label="Salida" />
            <el-radio-button label="Buscar materia prima" />
            <el-radio-button v-if="$page.props.auth.user.permissions.includes(
              'QR producto de catalogo'
            )
              " label="Producto de catalogo" />
          </el-radio-group>
        </div>

        <div class="mt-6">
          <div class="flex col-span-full ml-3 mt-2">
            <el-tooltip content="Código QR *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                <i class="fa-solid fa-qrcode ml-2"></i>
              </span>
            </el-tooltip>
            <input ref="partNumberInput" v-model="form.barCode" class="input" autocomplete="off" placeholder="Código QR *"
              type="text" />
            <InputError :message="form.errors.barCode" />
          </div>
        </div>
        <div v-if="loading" class="mt-5 z-20 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i>
        </div>

        <!-- -------------- Product found in search raw material starts--------------------- -->
        <div v-if="(productFound && !loading) && form.scanType == 'Buscar materia prima'" class="flex space-x-2 mt-4">
          <figure class="w-1/3 h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center border">
            <el-image style="height: 100%" :src="productFound.storageable?.media[0]?.original_url" fit="contain">
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image>
          </figure>
          <div class="w-2/3 border text-sm">
            <h1 class="text-sm font-bold text-center mt-1">
              {{ productFound.storageable?.name }}
            </h1>
            <ul class="px-4 mt-2">
              <li>
                <label class="text-primary">Número de parte: </label>
                {{ productFound.storageable?.part_number }}
              </li>
              <li>
                <label class="text-primary">Ubicación: </label>
                {{ productFound.location }}
              </li>
              <li>
                <label class="text-primary">Descripción: </label>
                {{ productFound.storageable?.description }}
              </li>
              <li>
                <label class="text-primary">Stock: </label>
                {{
                  productFound.quantity
                    .toFixed(2)
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                }}
                {{ productFound.storageable?.measure_unit }}
              </li>
              <li>
                <label class="text-primary">costo: </label> ${{
                  productFound.storageable?.cost
                    .toFixed(2)
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                }}
              </li>
            </ul>
            <Link class="text-center mt-5" :href="route('storages.show', productFound.id)">
            <p class="text-secondary hover:underline cursor-pointer">
              Ver producto
            </p>
            </Link>
          </div>
        </div>
        <!-- -------------- Product found in search raw material ends--------------------- -->


        <!-- -------------- Catalog Product found in search starts--------------------- -->
        <div v-if="(catalogProductFound && !loading) && form.scanType == 'Producto de catalogo'"
          class="flex space-x-2 mt-4">
          <figure class="w-1/3 h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center border">
            <el-image style="height: 100%" :src="catalogProductFound.media[0]?.original_url" fit="contain">
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image>
          </figure>
          <div class="w-2/3 border text-sm">
            <h1 class="text-sm font-bold text-center mt-1">
              {{ catalogProductFound.name }}
            </h1>
            <ul class="px-4 mt-2">
              <li>
                <label class="text-primary">Número de parte: </label>
                {{ catalogProductFound.part_number }}
              </li>
              <li>
                <label class="text-primary">Descripción: </label>
                {{ catalogProductFound.description }}
              </li>
              <li>
                <label class="text-primary">Características: </label>
                {{ catalogProductFound.features?.raw }}
              </li>
              <li>
                <label class="text-primary">Unidad de medida: </label>
                {{ catalogProductFound.measure_unit }}
              </li>
              <li>
                <label class="text-primary">costo: </label> ${{
                  catalogProductFound.cost.number_format
                }}
              </li>
            </ul>
            <Link class="text-center my-5" :href="route('catalog-products.show', catalogProductFound.id)">
            <p class="text-secondary hover:underline cursor-pointer">
              Ver producto
            </p>
            </Link>
            <h1 class="text-sm font-bold text-center mt-1">
              Clientes
            </h1>

            <div>
              <div v-for="company_info in catalogProductFound.companies" :key="company_info"
                class="p-3 flex flex-col border rounde-lg">
                <p class="text-secondary font-bold">Razon social: <span class="text-gray-600 font-thin">{{
                  company_info.business_name }}</span></p>
                <p class="text-secondary font-bold">Precio anterior: <span class="text-gray-600 font-thin">{{
                  company_info.pivot.old_price }} {{ company_info.pivot.new_currency
  }}</span></p>
                <p class="text-secondary font-bold">Fecha de cambio: <span class="text-gray-600 font-thin">{{
                  company_info.pivot.old_date }}</span></p>
                <p class="text-secondary font-bold">Precio actual: <span class="text-gray-600 font-thin">{{
                  company_info.pivot.new_price }} {{ company_info.pivot.new_currency
  }}</span></p>
                <p class="text-secondary font-bold">Fecha de cambio: <span class="text-gray-600 font-thin">{{
                  company_info.pivot.new_date }}</span></p>
                <p class="text-secondary font-bold">Último ajuste de precio hace:
                  <span class="text-gray-600 font-thin">{{ timeSinceNewPrice(company_info) }}</span>
                </p>
              </div>
            </div>

          </div>
        </div>
        <!-- -------------- Catalog Product found in search ends--------------------- -->

        <div class="flex justify-between items-center">
          <button type="button" @click="QRMachineScan()" class="text-primary text-sm flex items-center">Escanear máquinas
            <i class="fa-solid fa-arrow-right-long ml-2 mt-1"></i></button>
          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <CancelButton @click="
              qrScan = false;
            form.reset();
            ">Cancelar</CancelButton>
            <PrimaryButton :disabled="form.processing">Buscar</PrimaryButton>
          </div>
        </div>
      </form>

      <!------------------------ Machine form starts ------------------------>
      <form v-if="!is_product" @submit.prevent="scanMachineForm">
        <div class="mt-6">
          <div class="flex col-span-full ml-3 mt-2">
            <el-tooltip content="Código QR *" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                <i class="fa-solid fa-qrcode ml-2"></i>
              </span>
            </el-tooltip>
            <input ref="partNumberInput" v-model="form.barCode" class="input" autocomplete="off" placeholder="Código QR *"
              type="text" />
            <InputError :message="form.errors.barCode" />
          </div>
        </div>

        <div v-if="loading" class="mt-5 z-20 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i>
        </div>

        <div v-if="machineFound" class="flex space-x-2 mt-4">
          <figure class="w-1/3 h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center border">
            <el-image style="height: 100%" :src="machineFound.media[0]?.original_url" fit="contain">
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image>
          </figure>
          <div class="w-2/3 border text-sm">
            <h1 class="text-sm font-bold text-center mt-1">
              {{ machineFound.name }}
            </h1>
            <ul class="px-4 mt-2">
              <li>
                <label class="text-primary">ID: </label>
                {{ machineFound.id }}
              </li>
              <li>
                <label class="text-primary">Número de serie: </label>
                {{ machineFound.serial_number }}
              </li>
              <li>
                <label class="text-primary">Peso: </label>
                {{ machineFound.weight }} kg.
              </li>
              <li>
                <label class="text-primary">Alto: </label>
                {{ machineFound.height }} cm.
              </li>
              <li>
                <label class="text-primary">Largo: </label>
                {{ machineFound.large }} cm.
              </li>
              <li>
                <label class="text-primary">Ancho: </label>
                {{ machineFound.width }} cm.
              </li>
              <li>
                <label class="text-primary">Mantenimiento cada: </label>
                {{ machineFound.days_next_maintenance }}
              </li>
              <li>
                <label class="text-primary">Proveedor: </label>
                {{ machineFound.supplier }}
              </li>
              <li>
                <label class="text-primary">Fecha de adquisición: </label>
                {{ machineFound.aquisition_date }}
              </li>
              <li>
                <label class="text-primary">costo: </label> ${{
                  machineFound.cost
                    .toFixed(2)
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                }}
              </li>
              <li>
                <label class="text-primary mt-2">Archivos: </label>
                <div class="flex flex-col">
                  <p class="text-secondary hover:underline" v-for="file in machineFound.files" :key="file"><a
                      :href="file.original_url" target="_blank">{{ file.name ?? 'No hay archivos de esta máquina' }}</a>
                  </p>
                </div>
              </li>
            </ul>
            <Link class="text-center mt-5" :href="route('machines.show', machineFound.id)">
            <p class="text-secondary hover:underline cursor-pointer mt-4">
              Ver máquina
            </p>
            </Link>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <button @click="QRScan()" class="text-primary text-sm flex items-center"><i
              class="fa-solid fa-arrow-left-long mr-2 mt-1"></i> Escanear productos</button>
          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <CancelButton @click="
              qrScan = false;
            form.reset();
            ">Cancelar</CancelButton>
            <PrimaryButton :disabled="form.processing">Buscar</PrimaryButton>
          </div>
        </div>

      </form>
      <!-- ---------------------- Machine form ends ---------------------- -->
    </div>
  </Modal></template>
