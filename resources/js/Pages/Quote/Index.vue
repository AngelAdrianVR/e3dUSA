<template>
    <AppLayout title="Cotizaciones">
        <!-- close notification screen layer -->
        <div v-if="showNotifications" @click="toggleNotifications" class="absolute top-0 right-0 inset-0 z-10"></div>

        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl leading-tight">
                    Cotizaciones
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')"
                    :href="route('quotes.create')">
                <SecondaryButton>+ Nuevo</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="relative overflow-hidden">
            <!-- notifications -->
            <div class="absolute top-5 right-0 flex z-20">
                <button @click="toggleNotifications"
                    class="bg-[#D9D9D9] rounded-tl-xl rounded-bl-xl pl-3 pr-4 py-1 self-start" style="width: 40px;">
                    <i class="fa-regular fa-bell text-[#9A9A9A] text-xl"></i>
                </button>
                <transition name="slide">
                    <div v-if="showNotifications" class="w-56 h-[300px] bg-[#D9D9D9] rounded-bl-xl py-1">
                        <h2 class="text-center text-[#9A9A9A] font-bold mt-1">
                            Notificaciones
                        </h2>
                        <div class="mx-5 mt-2 flex justify-between items-center border-b border-[#9A9A9A] pb-3">
                            <Checkbox v-model="selectAll" name="select-all" class="bg-transparent" />
                            <div>
                                <button v-if="selectedNotifications.length" class="text-primary text-xs mr-1 w-6 border-[#d90537] border rounded-[5px] px-[2px] py-[2px]">
                                    <el-tooltip content="Marcar como leído" placement="top">
                                        <i class="fa-regular fa-eye"></i>
                                    </el-tooltip>
                                </button>
                                <el-popconfirm v-if="selectedNotifications.length" confirm-button-text="Si"
                                    cancel-button-text="No" icon-color="#FF0000" title="Seguro(a) que desea eliminar?"
                                    @confirm="deleteSelections">
                                    <template #reference>
                                        <button v-if="selectedNotifications.length" class="text-primary text-xs border-[#d90537] w-6 border rounded-[5px] px-[2px] py-[2px]">
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
                        <div v-else class="overflow-y-auto overflow-x-hidden divide-y-2 h-[215px]">
                            <div v-for="notification in userNotifications" :key="notification.id"
                                :class="notification.read_at ? '' : 'bg-red-300'"
                                class="px-2 mx-3 mt-1 py-1 rounded-[10px]">
                                <label class="flex">
                                    <input type="checkbox" v-model="selectedNotifications" :value="notification.id"
                                        class="rounded border-gray-600 text-[#D90537] shadow-sm focus:ring-[#D90537] bg-transparent" />
                                    <div class="ml-3">
                                        <p class="text-xs">{{ notification.data.description }}</p>
                                        <p class="text-xs">{{ notification.data.additional_info }}</p>
                                        <p class="text-[10px]">{{ notification.created_at.humans }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
            <div class="lg:w-5/6 mx-auto mt-6">

                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="quotes.data.length" />
                    </div>

                    <!-- buttons -->
                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar cotizaciones')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                </div>
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="450" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="folio" label="Folio" width="100" />
                    <el-table-column prop="user.name" label="Creado por" />
                    <el-table-column prop="receiver" label="Receptor" />
                    <el-table-column prop="companyBranch.name" label="Cliente" />
                    <el-table-column prop="authorized_user_name" label="Autorizado por" />
                    <el-table-column prop="created_at" label="Creado el" width="180" />
                    <el-table-column align="right" fixed="right" width="190">
                        <template #header>
                            <div class="flex space-x-2">
                                <TextInput v-model="inputSearch" type="search" class="w-full text-gray-600"
                                    placeholder="Buscar" />
                                <el-button @click="handleSearch" type="primary" plain class="mb-3"><i
                                        class="fa-solid fa-magnifying-glass"></i></el-button>
                            </div>
                        </template>
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </span>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id"><i class="fa-solid fa-eye"></i>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar cotizaciones')
                                            || scope.row.user.id == $page.props.auth.user.id"
                                            :command="'edit-' + scope.row.id">
                                            <i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear cotizaciones')"
                                            :command="'clone-' + scope.row.id"><i class="fa-solid fa-clone"></i>
                                            Clonar</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Crear ordenes de venta')"
                                            :command="'make_so-' + scope.row.id"><i
                                                class="fa-solid fa-arrows-turn-to-dots"></i>Convertir a
                                            OV</el-dropdown-item>
                                        <el-dropdown-item
                                            v-if="$page.props.auth.user.permissions.includes('Autorizar cotizaciones') && !scope.row.authorized_at"
                                            :command="'authorize-' + scope.row.id"><i
                                                class="fa-solid fa-check"></i>Autoizar</el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </AppLayout>
</template>
<script>

import AppLayout from '@/Layouts/AppLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import Checkbox from "@/Components/Checkbox.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";

export default {
    components: {
        AppLayout,
        TextInput,
        Checkbox,
        SecondaryButton,
        Link,
    },
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            // pagination
            itemsPerPage: 10,
            start: 0,
            end: 10,
            showNotifications: true,
            selectedNotifications: [],
            userNotifications: [],
            loading: false,
            selectAll: false,
        };
    },
    props: {
        quotes: {
            type: Object,
            default: []
        },
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
        handleSearch() {
            this.search = this.inputSearch;
        },
        handleSelectionChange(val) {
            this.$refs.multipleTableRef.value = val;

            if (!this.$refs.multipleTableRef.value.length) {
                this.disableMassiveActions = true;
            } else {
                this.disableMassiveActions = false;
            }
        },
        handlePagination(val) {
            this.start = (val - 1) * this.itemsPerPage;
            this.end = val * this.itemsPerPage;
        },
        async deleteSelections() {
            try {
                const response = await axios.post(route('quotes.massive-delete', {
                    quotes: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.quotes.data.forEach((quote, index) => {
                        if (this.$refs.multipleTableRef.value.includes(quote)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.quotes.data.splice(index, 1);
                    }

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },

        handleRowClick(row) {
            this.$inertia.get(route('quotes.show', row));
        },

        tableRowClassName({ row, rowIndex }) {
            if (row.status === 1) {
                return 'text-green-600 cursor-pointer';
            }

            return 'cursor-pointer';
        },
        async clone(quote_id) {
            try {
                const response = await axios.post(route('quotes.clone', {
                    quote_id: quote_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    this.quotes.data.unshift(response.data.newItem);

                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }

            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        async createSO(quote_id) {
            try {
                const response = await axios.post(route('quotes.create-so', {
                    quote_id: quote_id
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }
            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        async authorize(quote_id) {
            try {
                const response = await axios.put(route('quotes.authorize', quote_id));

                if (response.status == 200) {
                    const index = this.quotes.data.findIndex(item => item.id == quote_id);
                    this.quotes.data[index].authorized_at = response.data.item.authorized_at;
                    this.quotes.data[index].authorized_user_name = response.data.item.authorized_user_name;
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });
                } else {
                    this.$notify({
                        title: 'Algo salió mal',
                        message: response.data.message,
                        type: 'error'
                    });
                }
            } catch (err) {
                this.$notify({
                    title: 'Algo salió mal',
                    message: err.message,
                    type: 'error'
                });
                console.log(err);
            }
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                this.createSO(rowId);
            } else if (commandName == 'authorize') {
                this.authorize(rowId);
            } else {
                this.$inertia.get(route('quotes.' + commandName, rowId));
            }
        },
        async getNotifications() {
            this.loading = true;
            try {
                const response = await axios.post(route('users.get-notifications'));

                if (response.status === 200) {
                    this.userNotifications = response.data.items;
                }
            } catch (error) {
                console.log(error);
            }
            this.loading = false;
        }
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.quotes.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.quotes.data.filter(
                    (quote) =>
                        quote.folio.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.receiver.toLowerCase().includes(this.search.toLowerCase()) ||
                        quote.companyBranch.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
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