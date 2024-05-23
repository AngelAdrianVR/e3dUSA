<template>
    <div>
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
                <p class="text-amber-600"><i class="fa-solid fa-circle mr-1"></i>Enviado. Esperando respuesta </p>
                <p class="text-blue-600"><i class="fa-solid fa-circle mr-1"></i>Muestra devuelta/ esperando
                    retroalimentación</p>
                <p class="text-sky-500"><i class="fa-solid fa-circle mr-1"></i>Enviada con modificaciones</p>
                <p class="text-green-500"><i class="fa-solid fa-circle mr-1"></i>Venta cerrada</p>
                <p class="text-primary"><i class="fa-solid fa-circle mr-1"></i>Venta no concretada</p>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <NotificationCenter module="samples" />
                <div class="flex justify-between">
                    <!-- pagination -->
                    <!-- <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="samples.length" />
                    </div> -->
                    <!-- buttons -->
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
                <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
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
                    <el-table-column prop="returned_at" label="devuelto el" />
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
                                        <el-dropdown-item :command="'show-' + scope.row.id"><i
                                                class="fa-solid fa-eye"></i>
                                            Ver</el-dropdown-item>
                                        <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar muestra')
                            && scope.row.status['label'] == 'Enviado. Esperando respuesta'"
                                            :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                            Editar</el-dropdown-item>
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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";

export default {
    data() {
        return {
            disableMassiveActions: true,
            inputSearch: '',
            search: '',
            // pagination
            itemsPerPage: 10,
            start: 0,
            end: 10,
        };
    },
    components: {
        AppLayout,
        SecondaryButton,
        Link,
        TextInput,
        NotificationCenter,
        IndexSearchBar,
    },
    props: {
        samples: Array
    },
    methods: {
        handleSearch(search) {
            this.search = search;
        },
        tableRowClassName({ row, rowIndex }) {
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

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('samples.' + commandName, rowId));
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

                    // update list of quotes
                    let deletedIndexes = [];
                    this.samples.forEach((sample, index) => {
                        if (this.$refs.multipleTableRef.value.includes(sample)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.samples.splice(index, 1);
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
    computed: {
        filteredTableData() {
            return this.samples.filter(
                (sample) =>
                    !this.search ||
                    sample.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    sample.status['label'].toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
