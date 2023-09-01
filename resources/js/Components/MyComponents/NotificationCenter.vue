<template>
    <!-- close notification screen layer -->
    <div v-if="showNotifications" @click="toggleNotifications" class="absolute top-0 right-0 inset-0 z-10"></div>

    <div class="absolute top-5 right-0 flex z-20">
        <button @click="toggleNotifications" class="bg-[#D9D9D9] rounded-tl-xl rounded-bl-xl pl-3 pr-4 py-1 self-start"
            style="width: 40px;">
            <i class="fa-regular fa-bell text-[#9A9A9A] text-xl"></i>
        </button>
        <transition name="slide">
            <div v-if="showNotifications" class="w-56 h-[300px] bg-[#D9D9D9] rounded-bl-xl py-1">
                <h2 class="text-center text-[#9A9A9A] font-bold mt-1">
                    Notificaciones
                </h2>
                <div class="mx-5 mt-2 flex justify-between items-center border-b border-[#9A9A9A] pb-3">
                    <Checkbox v-if="userNotifications.length" v-model="selectAll" name="select-all"
                        class="bg-transparent ml-3 border-gray-600" />
                    <div>
                        <button @click="markNotificationsAsRead" v-if="selectedNotifications.length"
                            class="text-primary text-xs mr-1 w-6 border-[#d90537] border rounded-[5px] px-[2px] py-[2px]">
                            <el-tooltip content="Marcar como leído" placement="top">
                                <i class="fa-regular fa-eye"></i>
                            </el-tooltip>
                        </button>
                        <el-popconfirm v-if="selectedNotifications.length" confirm-button-text="Si" cancel-button-text="No"
                            icon-color="#0355B5" title="Seguro(a) que desea eliminar?" @confirm="deleteNotifications">
                            <template #reference>
                                <button v-if="selectedNotifications.length"
                                    class="text-primary text-xs border-[#d90537] w-6 border rounded-[5px] px-[2px] py-[2px]">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </template>
                        </el-popconfirm>
                    </div>
                </div>
                <!-- loading state -->
                <div v-if="loading" class="flex justify-center items-center pt-10">
                    <i class="fa-solid fa-spinner fa-spin text-2xl text-primary"></i>
                </div>
                <!-- content of notifications -->
                <div v-else class="overflow-y-auto overflow-x-hidden h-[215px]">
                    <div v-for="notification in userNotifications" :key="notification.id"
                        class="px-2 mx-3 mt-1 mb-2 py-1 hover:bg-[#cccccc] rounded-[10px] relative">
                        <label class="flex cursor-pointer ml-3">
                            <i v-if="!notification.read_at"
                                class="absolute top-1 left-1 fa-solid fa-circle text-primary text-[8px] mt-1"></i>
                            <input type="checkbox" v-model="selectedNotifications" :value="notification.id"
                                class="rounded border-gray-600 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                            <div class="ml-3" :class="!notification.read_at ? 'font-bold' : ''">
                                <p class="text-xs">{{ notification.data.description }}</p>
                                <p class="text-xs">{{ notification.data.additional_info }}</p>
                                <p class="text-[10px]">{{ notification.created_at.humans }}</p>
                            </div>
                        </label>
                    </div>
                    <p v-if="!userNotifications.length" class="text-xs text-[#9a9a9a] text-center px-6 py-4">No hay
                        notificaciones para mostrar</p>
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
import axios from 'axios';
import Checkbox from "@/Components/Checkbox.vue";


export default {
    components: {
        Checkbox,
    },
    data() {
        return {
            showNotifications: true,
            selectedNotifications: [],
            userNotifications: [],
            loading: false,
            selectAll: false,
        };
    },
    props: {
        module: String,
    },
    watch: {
        selectAll(newVal) {
            if (newVal) {
                // Si selectAll es verdadero, selecciona todas las notificaciones
                this.selectedNotifications = this.userNotifications.map(notification => notification.id);
            } else {
                // Si selectAll es falso, deselecciona todas las notificaciones
                this.selectedNotifications = [];
            }
        },
    },
    methods: {
        toggleNotifications() {
            this.showNotifications = !this.showNotifications;
        },
        async getNotifications() {
            this.loading = true;
            try {
                const response = await axios.post(route('users.get-notifications'), {module: this.module});

                if (response.status === 200) {
                    this.userNotifications = response.data.items;
                }
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        },
        async markNotificationsAsRead() {
            try {
                const response = await axios.post(route('users.read-notifications'), {notifications_ids: this.selectedNotifications});

                if(response.status === 200) {
                    this.getNotifications();
                    this.selectedNotifications = [];
                }
            } catch (error) {
                console.log(error);
            }
        },
        async deleteNotifications() {
            try {
                const response = await axios.post(route('users.delete-notifications'), {notifications_ids: this.selectedNotifications});

                if(response.status === 200) {
                    this.getNotifications();
                    this.selectedNotifications = [];
                }
            } catch (error) {
                console.log(error);
            }
        },
    },
    mounted() {
        this.getNotifications();
    }
}
</script>

<style>
.slide-enter-active,
.slide-leave-active {
    transition: right 0.5s, width 0.5s;
}

.slide-enter,
.slide-leave-to {
    right: 56px;
    width: 0;
}
</style>