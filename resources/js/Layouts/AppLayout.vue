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
const showSearchInput = ref(false); //buscador general
const showSearchResults = ref(false); //buscador general
const searchResults = ref(null); //buscador general
const searchInput = ref(null); //buscador general
const loadingSearch = ref(false); //buscador general
const isDarkMode = ref(localStorage.getItem('darkMode') === 'true');// Obtener el estado del modo nocturno desde el localStorage
const darkModeSwitch = ref(localStorage.getItem('darkMode') === 'true');// Obtener el estado del modo nocturno desde el localStorage
const draggableAlert = ref(null); // Asigna el ref a una variable reactiva


const form = useForm({
  barCode: null,
  scanType: "Entrada",
});

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    darkModeSwitch.value = isDarkMode.value;
    localStorage.setItem('darkMode', isDarkMode.value); // Guardar el estado en localStorage+
    document.documentElement.classList.toggle('dark', isDarkMode.value);
};

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
      // evitar registro multiple por muchos clicks
      if (nextAttendance.value != response.data.next) {
        nextAttendance.value = response.data.next;
        ElNotification.success({
          title: "Éxito",
          message: "Registro correcto",
        });
      } else {
        ElNotification.info({
          title: "Debes de esperar por lo menos 1 minuto para registrar salida",
          message: "",
        });
      }
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

const searchStart = () => {
  showSearchInput.value = true;
};

const searchEnd = () => {
  setTimeout(() => {
    showSearchInput.value = false;
    showSearchResults.value = false;
  }, 100);
};

const searching = () => {
  const searchInputRef = document.getElementById('generalInputSearch'); //obtiene el texto del input
  loadingSearch.value = true;
  showSearchResults.value = true;
  // Realizar solicitud Axios a la ruta de búsqueda en Laravel
  axios.get(`/search?query=${searchInputRef.value}`)
    .then(response => {
      searchResults.value = response.data.results;
      loadingSearch.value = false;
    })
    .catch(error => {
      console.error('Error al realizar la búsqueda:', error);
      loadingSearch.value = false;
    });
};

onMounted(() => {
  getAttendanceTextButton();
  getPauseStatus();
  getUnseenMessages();

  setInterval(() => {
    currentTime.value = new Date().getHours();
  }, 60000); // 60000 ms = 1 minute
  
  document.documentElement.classList.toggle('dark', isDarkMode.value);

  const alertBox = draggableAlert.value; // Accede al elemento DOM

  if (!alertBox) return; // Asegúrate de que el elemento existe

  let isDragging = false;
  let offsetX = 0;
  let offsetY = 0;

  alertBox.addEventListener('mousedown', (e) => {
    isDragging = true;
    offsetX = e.clientX - alertBox.offsetLeft;
    offsetY = e.clientY - alertBox.offsetTop;
    alertBox.style.cursor = 'grabbing';
  });

  window.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    const x = e.clientX - offsetX;
    const y = e.clientY - offsetY;
    alertBox.style.left = `${x}px`;
    alertBox.style.top = `${y}px`;
    alertBox.style.position = 'absolute';
  });

  window.addEventListener('mouseup', () => {
    if (isDragging) {
      isDragging = false;
      alertBox.style.cursor = 'pointer';
    }
  });
});
</script>

<template>
  <div>

    <Head :title="title" />

    <Banner />

    <div class="overflow-hidden h-screen bg-[#F2F2F2] dark:bg-[#0D0D0D] md:grid md:grid-cols-12">
      <aside>
        <SideNav />
      </aside>

      <main class="md:col-span-11">
        <nav class="bg-[#F2F2F2] dark:bg-[#0D0D0D] border-b border-[#D9D9D9] transition-all ease-linear duration-500">
          <!-- Primary Navigation Menu -->
          <div class="w-11/12 mx-auto">
            <div class="flex items-center justify-between h-14">
              <div class="flex w-1/5">
                <!-- Logo -->
                <div class="w-44 lg:w-2/3 flex items-center">
                  <Link :href="route('dashboard')">
                  <ApplicationMark class="w-full" />
                  </Link>
                </div>
              </div>

              <!-- Buscador general -->
              <div class="w-full mx-5 lg:mx-0 lg:w-1/4 text-xs lg:text-sm">
                <button v-if="!showSearchInput" @click="searchStart"
                  class="rounded-full size-9 flex justify-center items-center border border-[#9A9A9A]">
                  <i class="fa-solid fa-magnifying-glass text-sm text-[#9A9A9A]"></i>
                </button>
                <div v-else class="relative">
                  <input @input="searching" placeholder="Escribe lo que estas buscando" ref="searchInput"
                    @blur="searchEnd" type="text" id="generalInputSearch"
                    class="input !rounded-full !bg-transparent border-[#9A9A9A] pl-8">
                  <i class="fa-solid fa-magnifying-glass text-sm text-[#9A9A9A] absolute left-3 top-[6px]"></i>

                  <!-- Resultados -->
                  <div v-if="showSearchResults"
                    class="bg-white dark:bg-[#202020] w-80 max-h-80 overflow-auto absolute top-[50px] left-0 shadow-lg rounded-md py-4 z-50">
                    <!-- estado de carga -->
                    <div v-if="loadingSearch" class="flex justify-center items-center">
                      <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
                    </div>
                    <!-- Mostrar los resultados aquí -->
                    <div v-else-if="searchResults">
                      <div v-for="(results, modelName) in searchResults" :key="modelName">
                        <h2 class="font-bold px-4 dark:text-white">{{ modelName }}</h2>
                        <ul>
                          <li @click="$inertia.get(route(result.model + '.show', result.id))"
                            class="text-gray-500 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-primary text-xs px-4 cursor-pointer"
                            v-for="result in results" :key="result.id">
                            {{ result.name }} (ID: {{ result.id }}) <!-- Ajusta según tu estructura de datos -->
                          </li>
                        </ul>
                      </div>
                    </div>
                    <p class="text-sm text-center text-gray-400" v-else>No se encontraron coincidencias</p>
                  </div>
                </div>
              </div>

              <div class="w-1/2">
                <div class="hidden sm:flex sm:items-center sm:ml-1">
                  <el-tooltip content="Escanear producto con código QR">
                    <PrimaryButton @click="QRScan" class="mr-10">
                      <i class="fa-solid fa-qrcode"></i>
                    </PrimaryButton>
                  </el-tooltip>

                  <p class="mr-4 text-xs w-2/3 dark:text-white">
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
                        class="w-20 h-6 text-xs mr-5 rounded-full border-2 border-[#0355B5] text-secondary">
                        <i v-if="isPaused" class="fa-solid fa-play"></i>
                        <i v-else class="fa-solid fa-pause"></i>
                      </button>
                    </template>
                  </el-popconfirm>

                  <div class="w-2/3" v-if="$page.props.isKiosk &&
                    nextAttendance &&
                    $page.props.auth.user.permissions.includes(
                      'Registrar asistencia'
                    ) && !isPaused">
                    <div v-if="nextAttendance == 'Registrar salida' && $page.props.auth.user.has_pendent_production">
                      <SecondaryButton @click="openPasswordModal = true" v-if="nextAttendance != 'Dia terminado'"
                        class="mr-14">
                        {{ nextAttendance }}
                      </SecondaryButton>
                      <span v-else class="bg-[#75b3f9] text-[#0355B5] mr-14 rounded-md px-3 py-1">
                        {{ nextAttendance }}
                      </span>
                    </div>
                    <div v-else>
                      <el-popconfirm v-if="nextAttendance != 'Dia terminado'" confirm-button-text="Si"
                        cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?" @confirm="setAttendance">
                        <template #reference>
                          <SecondaryButton class="mr-14">
                            {{ nextAttendance }}
                          </SecondaryButton>
                        </template>
                      </el-popconfirm>
                      <span v-else class="w-full bg-[#75b3f9] text-[#0355B5] text-xs px-1 mr-14 rounded-md py-1">
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
                      <SecondaryButton v-else class="w-1/2">
                        Hacer kiosco
                      </SecondaryButton>
                    </template>
                  </el-popconfirm>

                  <div class="w-full flex items-center justify-end">
                    <!-- calendario -->
                    <div class="mr-3 relative">
                      <el-tooltip content="Calendario">
                        <Link :href="route('calendars.index')">
                          <button class="flex justify-center items-center rounded-full border border-[#7a7a7a] size-9">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#7a7a7a]">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                          </button>
                        </Link>
                      </el-tooltip>
                      <i v-if="$page.props.auth.user?.notifications?.some(notification => {
                        return notification.data.module === 'calendar';
                      })" class="fa-solid fa-circle fa-flip text-primary text-sm absolute -top-2 -right-0"></i>
                    </div>

                    <!-- chat -->
                    <div class="relative">
                      <el-tooltip v-if="$page.props.auth.user.permissions.includes('Chatear')" content="Chat"
                        placement="bottom">
                        <a :href="route('chatify')" target="_blank" class="mr-5 flex justify-center items-center rounded-full border border-[#7a7a7a] size-9">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#7a7a7a]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                          </svg>
                        </a>
                      </el-tooltip>
                      <div v-if="unseenMessages > 0"
                        class="absolute bottom-6 right-4 bg-primary text-white w-4 h-4 flex items-center justify-center text-[10px] rounded-full">
                        {{ unseenMessages }}
                      </div>
                    </div>

                    <!-- Dark mode toggle -->
                    <div class="mr-7">
                      <el-switch @change="darkModeSwitch = !darkModeSwitch; toggleDarkMode()" v-model="darkModeSwitch" style="--el-switch-on-color: #1e3a8a;">
                        <template #inactive-action>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-700">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                          </svg>
                        </template>
                        <template #active-action>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-700">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                          </svg>
                        </template>
                      </el-switch>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="ml-3 relative">
                      <Dropdown align="right" width="48">
                        <template #trigger>
                          <button v-if="$page.props.jetstream.managesProfilePhotos"
                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                            <img class="h-8 w-8 rounded-full object-cover"
                              :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name" />
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
                          <div class="block px-4 py-2 text-xs rounded-md dark:text-white" :class="{
                            'bg-secondarylight text-secondary': $page.props.auth.user.experience == 'Novato',
                            'text-[#FD8827] bg-[#FEDBBD]': $page.props.auth.user.experience == 'Intermedio',
                            'text-[#9E0FA9] bg-[#F7B7FC]': $page.props.auth.user.experience == 'Experto',
                          }">
                            Nivel {{ $page.props.auth.user.experience }}
                          </div>
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
                </div>
                <!-- Hamburger -->
                <div class="flex items-center justify-end sm:hidden w-full">
                  <!-- calendario -->
                  <!-- <div class="mr-3 relative">
                    <el-tooltip content="Calendario">
                      <Link :href="route('calendars.index')">
                        <button class="flex justify-center items-center rounded-full border border-[#7a7a7a] size-9">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#7a7a7a]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                          </svg>
                        </button>
                      </Link>
                    </el-tooltip>
                    <div v-if="$page.props.auth.user?.notifications?.some(notification => {
                      return notification.data.module === 'calendar';
                    })" class="bg-primary w-[10px] h-[10px] border border-white rounded-full absolute -top-1 -right-2">
                    </div>
                  </div> -->

                  <div class="relative">
                      <el-tooltip v-if="$page.props.auth.user.permissions.includes('Chatear')" content="Chat"
                        placement="bottom">
                        <a :href="route('chatify')" target="_blank" class="mr-12 flex justify-center items-center rounded-full border border-[#7a7a7a] size-9">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-[#7a7a7a]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                          </svg>
                        </a>
                      </el-tooltip>
                      <div v-if="unseenMessages > 0"
                        class="absolute bottom-4 right-5 bg-primary text-white w-4 h-4 flex items-center justify-center text-[10px] rounded-full">
                        {{ unseenMessages }}
                      </div>
                  </div>

                  <!-- darkmode toggle -->
                  <div class="mr-7">
                    <el-switch @change="darkModeSwitch = !darkModeSwitch; toggleDarkMode()" v-model="darkModeSwitch" style="--el-switch-on-color: #1e3a8a;">
                      <template #inactive-action>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-700">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                      </template>
                      <template #active-action>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-700">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                      </template>
                    </el-switch>
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
          </div>
          <!-- Responsive Navigation Menu -->
          <div :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }" class="z-40 sm:hidden bg-[#d9d9d9] w-4/6 absolute right-0 top-14 max-h-[90%] overflow-y-scroll overflow-x-hidden shadow-lg border border-[#cccccc] pt-4">
            <MobileSideNav />

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
              <div class="block px-4 py-2 text-xs" :class="{
                'bg-secondarylight text-secondary': $page.props.auth.user.experience == 'Novato',
                'text-[#FD8827] bg-[#FEDBBD]': $page.props.auth.user.experience == 'Intermedio',
                'text-[#9E0FA9] bg-[#F7B7FC]': $page.props.auth.user.experience == 'Experto',
              }">
                Nivel {{ $page.props.auth.user.experience }}
              </div>
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
                            " class="mr-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                              stroke-width="1.5" stroke="currentColor">
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
        <header v-if="$slots.header" class="">
          <div class="mx-auto py-2 px-4 sm:px-6 lg:px-8 transition-all ease-linear duration-500 dark:text-white">
            <slot name="header" />
          </div>
        </header>
        <div class="overflow-y-auto h-[calc(100vh-6.2rem)] bg-[#F2F2F2] dark:bg-[#0D0D0D] transition-all ease-linear duration-500">

          <!-- aviso de alarma de recordatorios vencidos o próximos a vencer -->
          <div v-if="$page.props.auth.user.has_important_reminder"
            ref="draggableAlert"
            class="border border-red-600 rounded-md bg-red-50 flex justify-between items-center space-x-2 absolute md:right-1/2 py-2 px-4 !cursor-move z-30 w-full md:w-96">
            <figure class="w-24">
              <img src="@/../../public/images/alarm.png" alt="">
            </figure>
            <p class="text-sm">
              Tienes una o más tareas de actualización de precio que <strong>debes completar hoy y/o que tienen atraso.</strong>
            </p>
            <div @click="$inertia.get(route('calendars.index'));" class="text-red-600 flex items-center space-x-3 pl-2 !cursor-pointer">
              <span>Ir</span>
              <i class="fa-solid fa-arrow-right"></i>
            </div>
          </div>

          <slot />
        </div>
      </main>
    </div>
  </div>
  <!-- Password modal -->
  <DialogModal :show="openPasswordModal" @close="openPasswordModal = false">
    <template #title>
      Contraseña de supervisor. Tienes {{ $page.props.auth.user.has_pendent_production }} orden(es) pausadas.
    </template>
    <template #content>
      <p class="text-center text-sm my-4">Para garantizar la precisión en nuestros registros de producción, se solicita
        que obtengan la autorización del
        supervisor antes de registrar su salida. Asegúrense de proporcionar el estatus de cualquier trabajo pendiente.
        La
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
        " class="cursor-pointer w-5 h-5 hover:text-primary rounded-full flex items-center justify-center absolute top-0 right-0">
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
            <input ref="partNumberInput" v-model="form.barCode" class="input" autocomplete="off"
              placeholder="Código QR *" type="text" />
            <InputError :message="form.errors.barCode" />
          </div>
        </div>
        <div v-if="loading" class="mt-5 z-20 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i>
        </div>

        <!-- -------------- Product found in search raw material starts--------------------- -->
        <div v-if="(productFound && !loading) && form.scanType == 'Buscar materia prima'" class="flex space-x-2 mt-4">
          <figure class="w-1/3 h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center border">
            <!-- <el-image style="height: 100%" :src="productFound.storageable?.media[0]?.original_url" fit="contain">
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image> -->
            <img class="object-contain h-40" :src="productFound.storageable?.media[0]?.original_url" alt="">
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
            <!-- <el-image style="height: 100%" :src="catalogProductFound.media[0]?.original_url" fit="contain">
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image> -->
            <img class="object-contain h-40" :src="catalogProductFound.media[0]?.original_url" alt="">
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
          <button type="button" @click="QRMachineScan()" class="text-primary text-sm flex items-center">Escanear
            máquinas
            <i class="fa-solid fa-arrow-right-long ml-2 mt-1"></i></button>
          <div class="flex justify-end space-x-3 pt-5 pb-1">
            <CancelButton type="button" @click="
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
            <input ref="partNumberInput" v-model="form.barCode" class="input" autocomplete="off"
              placeholder="Código QR *" type="text" />
            <InputError :message="form.errors.barCode" />
          </div>
        </div>

        <div v-if="loading" class="mt-5 z-20 rounded-lg flex items-center justify-center">
          <i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i>
        </div>

        <div v-if="machineFound" class="flex space-x-2 mt-4">
          <figure class="w-1/3 h-60 bg-[#D9D9D9] rounded-lg relative flex items-center justify-center border">
            <!-- <el-image style="height: 100%" :src="machineFound.media[0]?.original_url" fit="contain">
              <template #error>
                <div class="flex justify-center items-center text-[#ababab]">
                  <i class="fa-solid fa-image text-6xl"></i>
                </div>
              </template>
            </el-image> -->
            <img class="object-contain h-60" :src="machineFound.media[0]?.original_url" alt="">
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
            <CancelButton type="button" @click="
              qrScan = false;
            form.reset();
            ">Cancelar</CancelButton>
            <PrimaryButton :disabled="form.processing">Buscar</PrimaryButton>
          </div>
        </div>

      </form>
      <!-- ---------------------- Machine form ends ---------------------- -->
    </div>
  </Modal>
</template>
