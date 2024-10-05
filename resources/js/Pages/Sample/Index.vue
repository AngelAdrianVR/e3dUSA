<template>
    <div>
        <div v-if="loading"
            class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
        </div>
        <div v-if="loading"
            class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
            <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
        </div>

        <AppLayout title="Seguimiento de muestras">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Seguimiento de muestras</h2>
                    </div>
                    <div>
                        <Link v-if="$page.props.auth.user.permissions.includes('Crear muestra')"
                            :href="route('samples.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p><i class="fa-solid fa-circle mr-1 text-amber-600"></i>Enviado. Esperando respuesta </p>
                <p><i class="fa-solid fa-circle mr-1 text-blue-600"></i>Muestra devuelta/ esperandoretroalimentación</p>
                <p><i class="fa-solid fa-circle mr-1 text-sky-500"></i>Enviada con modificaciones</p>
                <p><i class="fa-solid fa-circle mr-1 text-green-500"></i>Venta cerrada</p>
                <p><i class="fa-solid fa-circle mr-1 text-primary"></i>Venta no concretada</p>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <NotificationCenter module="samples" />
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div v-if="!search" class="overflow-auto mb-2">
                        <PaginationWithNoMeta :pagination="pagination" class="py-2" />
                    </div>

                    <div>
                        <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar muestra')"
                            confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
                            @confirm="deleteSelections">
                            <template #reference>
                                <el-button type="danger" plain class="mb-3"
                                    :disabled="disableMassiveActions">Eliminar</el-button>
                            </template>
                        </el-popconfirm>
                    </div>
                    <!-- buscador -->
                    <IndexSearchBar @search="handleSearch" />
                </div>
                <el-table :data="filteredSamples" @row-click="handleRowClick" max-height="670" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef"
                    :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="30" />
                    <el-table-column prop="folio" label="Folio" width="120">
                        <template #default="scope">
                            <div class="flex">
                                <p class="mr-2 mt-px text-[6px]" :class="scope.row.status['text-color']">
                                    <i class="fa-solid fa-circle"></i>
                                </p>
                                <span>{{ scope.row.folio }}</span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="user.name" label="Creado por" />
                    <el-table-column prop="name" label="Nombre" />
                    <el-table-column prop="company_branch.name" label="Cliente" />
                    <el-table-column prop="quantity" label="Cantidad">
                        <template #default="scope">
                            <span>
                                {{ scope.row.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="sent_at" label="Enviado el" />
                    <el-table-column prop="returned_at" label="Devuelto el" />
                    <el-table-column prop="authorized_at" label="Autorizado el">
                         <template #default="scope">
                            <span :class="{'text-red-500': !scope.row.authorized_at}">{{ scope.row.authorized_at ?? 'No autorizado' }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="comments" label="Comentarios" />
                    <el-table-column align="right">
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <button @click.stop
                                    class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item :command="'show-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar muestra')
                                            && scope.row.status['label'] == 'Enviado. Esperando respuesta'"
                                            :command="'edit-' + scope.row.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Editar</el-dropdown-item>
                                            <el-dropdown-item
                                                v-if="$page.props.auth.user.permissions.includes('Autorizar cotizaciones') && !scope.row.authorized_at"
                                                :command="'authorize-' + scope.row.id">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                                Autorizar
                                            </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <!-- tabla -->
        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            loading: false,
            pagination: this.samples,
            filteredSamples: this.samples.data,
            // pagination
            // itemsPerPage: 10,
            // start: 0,
            // end: 10,
        };
    },
    components: {
        PaginationWithNoMeta,
        NotificationCenter,
        SecondaryButton,
        IndexSearchBar,
        AppLayout,
        TextInput,
        Link,
    },
    props: {
        samples: Array
    },
    methods: {
        async handleSearch(search) {
            this.search = search;
            this.loading = true;
            try {
                if (!this.search) {
                    this.filteredSamples = this.samples.data;
                    this.pagination = this.samples; //cuando se busca con texto vacio s emuestran todas las porduccoines y la paginacion es de todas las producciones
                } else {
                    const response = await axios.post(route('samples.get-matches', { query: this.search }));

                    if (response.status === 200) {
                        this.filteredSamples = response.data.items.data;
                        this.pagination = response.data.items; //se cambia la paginacion a los encontrados
                    }
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        tableRowClassName({ row, rowIndex }) {
            if ( !row.authorized_at ) {
                return 'cursor-pointer text-gray-400 !bg-[#fafafa]';
            } 
            return 'cursor-pointer text-xs';
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

        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'authorize') {
                this.authorize(rowId);
            } else {
                this.$inertia.get(route('samples.' + commandName, rowId));
            }
        },
        async authorize(sample_id) {
            try {
                const response = await axios.put(route('samples.authorize', sample_id));

                if (response.status == 200) {
                    const index = this.samples.data.findIndex(item => item.id == sample_id);
                    this.samples.data[index].authorized_at = response.data.authorized_at;
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
        async deleteSelections() {
            try {
                const response = await axios.post(route('samples.massive-delete', {
                    samples: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of samples
                    let deletedIndexes = [];
                    this.samples.data.forEach((sample, index) => {
                        if (this.$refs.multipleTableRef.value.includes(sample)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.samples.data.splice(index, 1);
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
            this.$inertia.get(route('samples.show', row));
        },
        edit(index, sample) {
            this.$inertia.get(route('samples.edit', sample));
        }
    },
};
</script>
