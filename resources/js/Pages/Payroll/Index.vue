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

      <div class="flex space-x-6 items-center justify-center text-xs mt-2 dark:text-white">
        <p><i class="fa-solid fa-circle mr-1 text-blue-600"></i>Nómina en curso</p>
      </div>
      <!-- tabla -->
      <div class="lg:w-5/6 mx-auto mt-6">
        <div class="flex justify-between">
          <!-- pagination -->
          <div class="mb-3">
            <el-pagination @current-change="handlePagination" layout="prev, pager, next"
              :total="payrolls.data.length" />
          </div>
          <!-- buttons -->
          <!-- <div>
            <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Crear nominas')" confirm-button-text="Si"
              cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?" @confirm="closePayroll">
              <template #reference>
                <PrimaryButton :disabled="!isThursdayAfter8PM">
                  Cerrar nómina
                </PrimaryButton>
              </template>
            </el-popconfirm>
          </div> -->
          <!-- buscador -->
          <IndexSearchBar @search="handleSearch" />
        </div>
        <el-table @row-click="handleRowClick" :data="filteredTableData" max-height="670" style="width: 100%"
          class="cursor-pointer" @selection-change="handleSelectionChange" ref="multipleTableRef"
          :row-class-name="tableRowClassName">
          <el-table-column type="selection" width="30" />
          <el-table-column prop="id" label="ID" width="85" />
          <el-table-column prop="week" label="Semana" width="85">
            <template #default="scope">
              <div class="flex">
                <p v-if="scope.row.is_active" class="mr-2 mt-px text-[6px] text-blue-600">
                  <i class="fa-solid fa-circle"></i>
                </p>
                <span>{{ scope.row.week }}</span>
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="start_date" label="Inicio" />
          <el-table-column prop="end_date" label="Fin" />
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
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import moment from 'moment';
import 'moment/locale/es';

export default {
  data() {
    return {
      disableMassiveActions: true,
      search: '',
      // pagination
      itemsPerPage: 10,
      start: 0,
      end: 10,
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
    PrimaryButton,
    IndexSearchBar,
  },
  props: {
    payrolls: Object
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
    handleRowClick(row) {
      this.$inertia.get(route('payrolls.show', row));
    },
    async closePayroll() {
      try {
        const response = await axios.post(route('payrolls.close-current'));

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
      if (!this.search) {
        return this.payrolls.data.filter((item, index) => index >= this.start && index < this.end);
      } else {
        return this.payrolls.data.filter(
          (payroll) =>
            payroll.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
            payroll.week.toString().toLowerCase().includes(this.search.toLowerCase()) ||
            payroll.start_date.toLowerCase().includes(this.search.toLowerCase()) ||
            payroll.end_date.toLowerCase().includes(this.search.toLowerCase())
        );
      }
    }
  },
};
</script>
