<template>
    <AppLayout title="Reuniones">
      <template #header>
        <div class="flex justify-between">
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Reuniones
            </h2>
          </div>
          <div>
          <Link :href="route('meetings.create')">
            <SecondaryButton>+ Nuevo</SecondaryButton>
            </Link>
          </div>
        </div>
      </template>

      <!-- tabla -->
            <div class="lg:w-5/6 mx-auto mt-6">
                <div class="flex justify-between">
                    <!-- pagination -->
                    <div>
                        <el-pagination @current-change="handlePagination" layout="prev, pager, next"
                            :total="meetings.data.length" />
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
                <el-table :data="meetings.data" max-height="450" style="width: 100%"
                    @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="subject" label="Título" width="150" />
                    <el-table-column prop="location" label="lugar" width="150" />
                    <el-table-column prop="url" label="URL" width="200" />
                    <el-table-column prop="status" label="Estatus" width="100" />
                    <el-table-column prop="date" label="Fecha" width="100" />
                    <el-table-column prop="description" label="Descripción" />
                    <el-table-column align="right" fixed="right">
                        <template #header>
                            <TextInput v-model="search" type="search" class="w-full text-gray-600" placeholder="Buscar" />
                        </template>
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <span class="el-dropdown-link mr-3">
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
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';

export default {
    data(){

        return{

        }
    },
    props:{
        meetings: Array
    },
    components:{
        AppLayout,
        SecondaryButton,
        Link,
        TextInput
    },
    methods:{
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
                const response = await axios.post(route('meetings.massive-delete', {
                    meetings: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.meetings.data.forEach((meeting, index) => {
                        if (this.$refs.multipleTableRef.value.includes(meeting)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.meetings.data.splice(index, 1);
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
            if (row.status === 1) {
                return 'text-green-600';
            }

            return '';
        },

        handleCommand(command) {
            const commandName = command.split('-')[0];
            const rowId = command.split('-')[1];

            if (commandName == 'clone') {
                this.clone(rowId);
            } else if (commandName == 'make_so') {
                console.log('SO');
            } else {
                this.$inertia.get(route('meetings.' + commandName, rowId));
            }
        },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.meetings.data.filter((item, index) => index >= this.start && index < this.end);
            } else {
                return this.meetings.data.filter(
                    (meeting) =>
                        meeting.subject.toLowerCase().includes(this.search.toLowerCase()) ||
                        meeting.status.toLowerCase().includes(this.search.toLowerCase()) ||
                        meeting.description.toLowerCase().includes(this.search.toLowerCase())
                )
            }
        }
    },
}
</script>