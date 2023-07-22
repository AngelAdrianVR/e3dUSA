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
                    @selection-change="handleSelectionChange" @row-click="handleRowClick" ref="multipleTableRef" :row-class-name="tableRowClassName">
                    <el-table-column type="selection" width="45" />
                    <el-table-column prop="subject" label="Título" width="150" />
                    <el-table-column prop="date" label="Fecha" width="150" />
                    <el-table-column prop="start['format']" label="Hora" />
                    <el-table-column prop="location" label="lugar" width="150" />
                    <el-table-column prop="url" label="URL" width="150" />
                    <el-table-column prop="status" label="Estatus" width="100" />
                    <el-table-column align="right" fixed="right">
                        <template #header>
                            <TextInput v-model="search" type="search" class="w-full text-gray-600" placeholder="Buscar" />
                        </template>
                        <template #default="scope">
                            <el-dropdown trigger="click" @command="handleCommand">
                                <span @click.stop class="el-dropdown-link mr-3 justify-center items-center p-2">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </span>
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item @click="showMeetingModal = true"><i class="fa-solid fa-eye"></i>
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

            <!-- -------------- Modal starts----------------------- -->
      <Modal :show="showMeetingModal" @close="showMeetingModal = false">
        <div class="p-3">
          <i
            @click="showMeetingModal = false"
            class="fa-solid fa-xmark cursor-pointer w-5 h-5 rounded-full border border-black flex items-center justify-center absolute right-3"
          ></i>

        <div class="m-4">
          <div class="border-b border-black mb-7">
            <h1 class="text-lg text-center font-bold text-gray-600">{{subject}}</h1>
          </div>

          <div class="mb-3 flex items-center">
          <el-tooltip content="Fecha de reunión" placement="left">
          <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-calendar"></i>
              </span>
        </el-tooltip>
            <p class="text-gray-600">{{ date }}</p>
          </div>

          <div class="flex mb-3 w-full">
            <div>
            <el-tooltip content="Hora de la reunión" placement="left">
          <p
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md">
                <i class="fa-solid fa-clock"></i>
              </p>
        </el-tooltip>
            </div>
              <p class="text-gray-600 px-2">{{ start['format'] }}</p>
            a
            <div class="ml-2">
              <p class="text-gray-600">{{ end['format'] }}</p>
            </div>
          </div>

          <div class="flex items-center mb-3">
            <el-tooltip content="Participantes de la reunion" placement="left">
              <i class="fa-solid fa-users text-gray-700 mr-3"></i>
            </el-tooltip>
            <p class="text-gray-600">{{ participants }}</p>
          </div>

          <div class="flex items-center w-3/5 mb-3">
              <el-tooltip content="Lugar de la reunión" placement="left">
                <i class="fa-solid fa-location-dot text-gray-700"></i>
              </el-tooltip>
              <p class="text-gray-600 ml-3">{{ location }}</p>
          </div>

          <div class="w-full flex mb-3">
              <el-tooltip content="URL de meeting en caso de ser en linea" placement="top">
                <i class="fa-solid fa-chain text-gray-700"></i>
              </el-tooltip>
              <a v-if="url != '--'" :href="url" target="_blank" class="text-secondary ml-3">{{ url }}</a>
              <p v-else class="text-gray-600 ml-3">{{ url }}</p>
          </div>

          <div class="flex col-span-full">
            <el-tooltip content="Descripción" placement="left">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
              >
                ...
              </span>
            </el-tooltip>
            <textarea
              v-model="description"
              class="textarea"
              autocomplete="off"
              placeholder="Descripción"
              disabled
            ></textarea>
          </div>
        </div>



          <div class="flex justify-start space-x-3 pt-5 pb-1">
            <PrimaryButton @click="showMeetingModal = false">Listo</PrimaryButton>
          </div>
        </div>
      </Modal>
      <!-- --------------------------- Modal ends ------------------------------------ -->


      </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link } from "@inertiajs/vue3";
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import Modal from "@/Components/Modal.vue";

export default {
    data(){

        return{
            showMeetingModal: false,
            subject: null,
            location: null,
            url: null,
            description: null,
            date: null,
            start: null,
            end: null,
            participants: null,
        }
    },
    props:{
        meetings: Array,
    },
    components:{
        AppLayout,
        PrimaryButton,
        SecondaryButton,
        Link,
        TextInput,
        Modal,
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
        handleRowClick(row) {
            this.subject = row.subject,
            this.location = row.location,
            this.url = row.url,
            this.description = row.description,
            this.date = row.date,
            this.start = row.start,
            this.end = row.end,
            this.participants = row.participants,
            this.showMeetingModal = true;
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
            if (row.status == 'Pendiente') {
                return 'text-amber-600 cursor-pointer';
            }else if(row.status == 'Autorizada'){
                return 'text-green-600 cursor-pointer';
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