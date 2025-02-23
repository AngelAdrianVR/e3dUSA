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
           <el-table-column prop="folio" label="Folio">
              <template #default="scope">
                <div class="flex">
                  <p class="mr-2">
                    <el-tooltip v-if="scope.row.is_for_production" content="Para producción" placement="top">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 mt-1 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                      </svg>
                    </el-tooltip>
                    <el-tooltip v-else content="Para muestras" placement="top">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 mt-1 text-rose-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                      </svg>
                    </el-tooltip>
                  </p>
                  <p class="flex-0 w-[80%]">{{ scope.row.folio }}</p>
                </div>
              </template>
            </el-table-column>
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
