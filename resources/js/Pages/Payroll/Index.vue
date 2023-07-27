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
        <div class="flex justify-between">
          <!-- pagination -->
          <div class="mb-3">
            <el-pagination @current-change="handlePagination" layout="prev, pager, next" :total="payrolls.data.length" />
          </div>

          <!-- buttons -->
          <div>
            <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Crear nominas')" confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000" title="¿Continuar?"
              @confirm="closePayroll">
              <template #reference>
                <PrimaryButton :disabled="!isThursdayAfter8PM">
                  Cerrar nómina
                </PrimaryButton>
              </template>
            </el-popconfirm>
          </div>
        </div>
        <el-table @row-click="handleRowClick" :data="filteredTableData" max-height="450" style="width: 100%"
          class="cursor-pointer" @selection-change="handleSelectionChange" ref="multipleTableRef"
          :row-class-name="tableRowClassName">
          <el-table-column type="selection" width="45" />
          <el-table-column prop="id" label="ID" width="85" />
          <el-table-column prop="week" label="Semana" />
          <el-table-column prop="start_date" label="Inicio" />
          <el-table-column prop="end_date" label="Fin" />
          <el-table-column align="right" fixed="right" width="200">
            <template #header>
              <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
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
import PrimaryButton from "@/Components/PrimaryButton.vue";
import EmptyTable from "@/Components/MyComponents/EmptyTable.vue";
import Table from "@/Components/MyComponents/Table.vue";
import TextInput from '@/Components/TextInput.vue';
import { Link } from "@inertiajs/vue3";
import axios from 'axios';
import moment from 'moment';
import 'moment/locale/es';
moment.locale('es');


export default {
  data() {


    return {
      disableMassiveActions: true,
      search: '',
      // pagination
      itemsPerPage: 25,
      start: 0,
      end: 25,
      isThursdayAfter8PM: false
    };
  },
  components: {
    AppLayout,
    Table,
    EmptyTable,
    SecondaryButton,
    Link,
    TextInput,
    PrimaryButton
  },
  props: {
    payrolls: Object
  },
  methods: {
    tableRowClassName({ row, rowIndex }) {
      if (row.is_active == true) {
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
    handlePagination(val) {
      this.start = (val - 1) * this.itemsPerPage;
      this.end = val * this.itemsPerPage;
    },
    handleRowClick(row) {
      this.$inertia.get(route('payrolls.show', row));
    },
    async closePayroll() {
      try {
        const response = await axios.get(route('payrolls.close-current'));

        if (response.status == 200) {
          this.payrolls.data.unshift(response.data.item);
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
    }
  },
  mounted() {
    const today = moment();
    const isThursday = today.isoWeekday() === 4;
    const isAfter8PM = today.isAfter(moment().set('hour', 20));//8:00 PM

    this.isThursdayAfter8PM = isThursday && isAfter8PM;
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
