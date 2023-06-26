<template>
  <div>
    <AppLayout title="Nóminas">
      <template #header>
        <div class="flex justify-between">
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">Nóminas</h2>
          </div>
        </div>
      </template>

      <!-- tabla -->
      <div class="lg:w-5/6 mx-auto mt-6">
        <div class="flex space-x-2 justify-end">
          <el-popconfirm
            confirm-button-text="Si"
            cancel-button-text="No"
            icon-color="#FF0000"
            title="Continuar con la eliminacion?"
            @confirm="deleteSelections"
          >
            <template #reference>
              <el-button
                type="danger"
                plain
                class="mb-3"
                :disabled="disableMassiveActions"
                >Eliminar</el-button
              >
            </template>
          </el-popconfirm>
        </div>
        <el-table
          @row-click="handleRowClick"
          :data="filteredTableData"
          max-height="450"
          style="width: 100%"
          class="cursor-pointer"
          @selection-change="handleSelectionChange"
          ref="multipleTableRef"
          :row-class-name="tableRowClassName"
        >
          <el-table-column type="selection" width="45" />
          <el-table-column prop="id" label="ID" width="70" />
          <el-table-column prop="week" label="Semana" width="130" />
          <el-table-column prop="start_date" label="Inicio" width="130" />
          <el-table-column prop="end_date" label="Fin" width="120" />
          <el-table-column align="right" fixed="right">
            <template #header>
              <TextInput
                v-model="search"
                type="search"
                class="w-full"
                placeholder="Buscar"
              />
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
import EmptyTable from "@/Components/MyComponents/EmptyTable.vue";
import Table from "@/Components/MyComponents/Table.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import axios from 'axios';


export default {
  data() {


    return {
       disableMassiveActions: true,
        search: '',
    };
  },
  components: {
    AppLayout,
    Table,
    EmptyTable,
    SecondaryButton,
    Link,
    TextInput,
  },
  props: {
    payrolls: Array
  },
  methods:{
    tableRowClassName({row, rowIndex}){

            if(row.is_active == true ){
            return 'text-blue-600';
        }


  },
    handleSelectionChange(val) {
                this.$refs.multipleTableRef.value = val;

                if (!this.$refs.multipleTableRef.value.length) {
                    this.disableMassiveActions = true;
                } else {
                    this.disableMassiveActions = false;
                }
            },
            async deleteSelections() {
            try {
                const response = await axios.post(route('raw-materials.massive-delete', {
                    payrolls: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.payrolls.forEach((payroll, index) => {
                        if (this.$refs.multipleTableRef.value.includes(payroll)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.payrolls.splice(index, 1);
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
        edit(index, payroll) {
            console.log(payroll);
            this.$inertia.get(route('raw-materials.edit', payroll.storageable));
        },
       handleRowClick(row){
        this.$inertia.get(route('payrolls.show', row));
    }
  },

  computed: {
        filteredTableData() {
            return this.payrolls.data.filter(
                (payroll) =>
                    !this.search ||
                    payroll.start_date.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
