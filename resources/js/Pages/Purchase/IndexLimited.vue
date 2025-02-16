<template>
  <div class="dark:text-white">
    <AppLayout title="Compras">
      <template #header>
        <div class="flex justify-between">
          <div class="flex items-center space-x-2">
            <h2 class="font-semibold text-xl leading-tight">
              Órdenes de compra
            </h2>
          </div>
          <div>
            <Link :href="route('purchases.create')">
            <SecondaryButton>+ Nuevo</SecondaryButton>
            </Link>
          </div>
        </div>
      </template>

      <div class="flex space-x-6 items-center justify-center text-xs mt-2">
        <p><i class="fa-solid fa-circle mr-1 text-red-500"></i>Sin autorización</p>
        <p><i class="fa-solid fa-circle mr-1 text-yellow-500"></i>Autorizado.Compra no realizada</p>
        <p><i class="fa-solid fa-circle mr-1 text-blue-600"></i>Compra realizada</p>
        <p><i class="fa-solid fa-circle mr-1 text-green-600"></i>Producto recibido</p>
      </div>

      <!-- tabla -->
      <div class="lg:w-5/6 mx-auto mt-6">
        <div class="flex justify-between">
          <!-- pagination -->
          <div>
            <el-pagination @current-change="handlePagination" layout="prev, pager, next" :total="purchases.length" />
          </div>
          <!-- buscador -->
          <IndexSearchBar @search="handleSearch" />
        </div>

        <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
           ref="multipleTableRef" :row-class-name="tableRowClassName">
          <el-table-column prop="folio" label="Folio" />
          <el-table-column prop="user" label="Creador" />
          <el-table-column prop="created_at" label="Creado el" />
          <el-table-column prop="authorized_user_name" label="Autorizado por" />
          <el-table-column prop="status" label="Estatus">
            <template #default="scope">
              <div class="flex">
                <p class="mr-2 mt-px text-[6px]" :class="getStatusColor(scope.row.status)">
                  <i class="fa-solid fa-circle"></i>
                </p>
                <span>{{ scope.row.status }}</span>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="Productos">
            <template #default="scope">
              <p>
                {{ scope.row.products.map(item => item.name).join(', ') }}
              </p>
            </template>
          </el-table-column>
          <el-table-column prop="emited_at" label="Pedido el" />
          <el-table-column prop="recieved_at" label="Recibido el" />
          <el-table-column prop="supplier_nickname" label="Alias de Proveedor" />
        </el-table>
      </div>
      <!-- tabla -->
    </AppLayout>
  </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import { Link } from "@inertiajs/vue3";

export default {
  data() {
    return {
      disableMassiveActions: true,
      inputSearch: '',
      search: "",
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
    IndexSearchBar,
  },
  props: {
    purchases: Array,
  },
  methods: {
    handleSearch(search) {
      this.search = search;
    },
    getStatusColor(status) {
      if (status == 'Pendiente') {
        return 'text-red-500';
      } else if (status == 'Autorizado') {
        return 'text-yellow-500';
      } else if (status == 'Emitido') {
        return 'text-blue-500';
      } else if (status == 'Recibido') {
        return 'text-green-500';
      } else {
        return 'text-gray-500';
      }
    },
    handlePagination(val) {
      this.start = (val - 1) * this.itemsPerPage;
      this.end = val * this.itemsPerPage;
    },
    tableRowClassName({ row, rowIndex }) {
      return 'cursor-pointer text-xs';
    },

    handleRowClick(row) {
      this.$inertia.get(route('purchases.show', row));
    },
  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.purchases.filter(
          (item, index) => index >= this.start && index < this.end
        );
      } else {
        return this.purchases.filter(
          (purchase) =>
            purchase.user?.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.products.map(prd => prd.name).join(', ')?.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.status?.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.folio?.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.created_at?.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.supplier_name?.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.authorized_user_name?.toLowerCase().includes(this.search.toLowerCase())
        );
      }
    },
  },
};
</script>
