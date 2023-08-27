<template>
    <div>
        <AppLayout title="Departamento de diseño">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Órdenes de diseño</h2>
                    </div>
                    <div>
                        <Link :href="route('designs.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p class="text-amber-500"><i class="fa-solid fa-circle mr-1"></i>Esperando autorización</p>
                <p class="text-amber-700"><i class="fa-solid fa-circle mr-1"></i>Autorizado. Sin iniciar</p>
                <p class="text-[#0355B5]"><i class="fa-solid fa-circle mr-1"></i>En proceso</p>
                <p class="text-green-500"><i class="fa-solid fa-circle mr-1"></i>Terminado</p>
            </div>

            <!-- tabla -->
            <div class="relative overflow-hidden">
                <NotificationCenter module="design" />
                <div class="lg:w-5/6 mx-auto mt-6 relative">
                    <div class="flex justify-between">
                        <!-- pagination -->
                        <div>
                            <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                                :total="designs.data.length" />
                        </div>
    
                        <!-- buttons -->
                        <div>
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                                title="¿Continuar?" @confirm="deleteSelections">
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
                        <el-table-column prop="user.name" label="Solicitante" />
                        <el-table-column prop="name" label="Deseño" />
                        <el-table-column prop="designer.name" label="Diseñador(a)" />
                        <el-table-column prop="created_at" label="Solicitado el" />
                        <el-table-column prop="status[label]" label="Estatus" />
                        <el-table-column align="right" fixed="right" width="190">
                            <template #header>
                                <div class="flex space-x-2">
                                <TextInput v-model="inputSearch" type="search" class="w-full text-gray-600" placeholder="Buscar" />
                                <el-button @click="handleSearch" type="primary" plain class="mb-3"><i class="fa-solid fa-magnifying-glass"></i></el-button>
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
                                            <el-dropdown-item v-if="(scope.row.status['label'] != 'Terminado' && scope.row.user.id == $page.props.auth.user.id) || 
                                                ($page.props.auth.user.permissions.includes('Ordenes de diseño todas') && scope.row.status['label'] != 'Terminado')"
                                                :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
                                                Editar</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </template>
                        </el-table-column>
                    </el-table>
                </div>
            </div>
            <!-- tabla -->

        </AppLayout>
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import axios from 'axios';


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
        NotificationCenter
    },
    props: {
        designs: Array
    },
    methods: {
        handleSearch(){
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
                const response = await axios.post(route('designs.massive-delete', {
                    designs: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.designs.data.forEach((design, index) => {
                        if (this.$refs.multipleTableRef.value.includes(design)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.designs.data.splice(index, 1);
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
        tableRowClassName({ row, rowIndex }) {

            if (row.status['label'] == 'Esperando Autorización') {
                 return 'cursor-pointer text-amber-500';
            }else if(row.status['label'] == 'Autorizado. Sin iniciar'){
                return 'cursor-pointer text-amber-700';
            }else if(row.status['label'] == 'En proceso'){
                return 'cursor-pointer text-[#0355B5]';
            }else if(row.status['label'] == 'Terminado'){
                return 'cursor-pointer text-green-500';
            }
        },
        handleRowClick(row) {
            this.$inertia.get(route('designs.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('designs.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.designs.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.designs.data.filter(
                    (design) =>
                        design.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        design.status.label.toLowerCase().includes(this.search.toLowerCase()) ||
                        design.user.name.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
