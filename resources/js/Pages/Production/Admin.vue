<template>
    <div>
        <AppLayout title="Departamento de producción">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl leading-tight">Todas las órdenes de producción</h2>
                    </div>
                    <div>
                        <Link :href="route('productions.create')">
                        <SecondaryButton>+ Nuevo</SecondaryButton>
                        </Link>
                    </div>
                </div>
            </template>

            <div class="flex space-x-6 items-center justify-center text-xs mt-2">
                <p class="text-amber-500"><i class="fa-solid fa-circle mr-1"></i>Producción sin iniciar</p>
                <p class="text-blue-500"><i class="fa-solid fa-circle mr-1"></i>Producción en proceso</p>
                <p class="text-green-500"><i class="fa-solid fa-circle mr-1"></i>Producción terminada</p>
            </div>

            <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="productions.data.length" />
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
                    <el-table-column type="selection" width="55" />
                    <el-table-column prop="user.name" label="Creador" />
                    <el-table-column prop="company_branch.name" label="Cliente" />
                    <el-table-column prop="created_at" label="Creada el" />
                    <el-table-column prop="status['label']" label="Estatus" />
                    <el-table-column prop="productions.length" label="N° operadores" width="120"  />
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
                                        <el-dropdown-item :command="'edit-' + scope.row.id"><i class="fa-solid fa-pen"></i>
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
import { Link } from "@inertiajs/vue3";
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
    },
    props: {
        productions: Array
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
                const response = await axios.post(route('productions.massive-delete', {
                    sales: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of productions
                    let deletedIndexes = [];
                    this.productions.data.forEach((production, index) => {
                        if (this.$refs.multipleTableRef.value.includes(production)) {
                            deletedIndexes.push(index);
                        }
                    });

                    deletedIndexes.sort((a, b) => b - a);

                    for (const index of deletedIndexes) {
                        this.productions.data.splice(index, 1);
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
            
            if(row.status['label'] == 'Producción sin iniciar'){
                return 'cursor-pointer text-amber-500';
            }else if(row.status['label'] == 'Producción en proceso'){
                return 'cursor-pointer text-blue-500';
            }else if(row.status['label'] == 'Producción terminada'){
                return 'cursor-pointer text-green-500';
            }
            return 'cursor-pointer';
        },
        handleRowClick(row) {
            this.$inertia.get(route('productions.show', row));
        },
        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('productions.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.productions.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.productions.data.filter(
                    (production) =>
                        production.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.status.label.toLowerCase().includes(this.search.toLowerCase()) ||
                        production.company_branch.name.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
};
</script>
