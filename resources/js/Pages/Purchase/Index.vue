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
          <!-- buttons -->
          <div>
            <el-popconfirm v-if="$page.props.auth.user.permissions.includes('Eliminar ordenes de compra')"
              confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
              @confirm="deleteSelections">
              <template #reference>
                <el-button type="danger" plain class="mb-3" :disabled="disableMassiveActions">Eliminar</el-button>
              </template>
            </el-popconfirm>
          </div>
          <!-- buscador -->
          <IndexSearchBar @search="handleSearch" />
        </div>

        <el-table :data="filteredTableData" @row-click="handleRowClick" max-height="670" style="width: 100%"
          @selection-change="handleSelectionChange" ref="multipleTableRef" :row-class-name="tableRowClassName">
          <el-table-column type="selection" width="30" />
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
          <el-table-column prop="supplier_name" label="Proveedor" />
          <el-table-column align="right">
            <template #default="scope">
              <el-dropdown trigger="click" @command="handleCommand">
                <button @click.stop
                  class="el-dropdown-link mr-3 justify-center items-center size-8 rounded-full text-primary hover:bg-gray2 transition-all duration-200 ease-in-out">
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <template #dropdown>
                  <el-dropdown-menu>
                    <el-dropdown-item :command="'show-' + scope.row.id">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      </svg>
                      Ver</el-dropdown-item>
                    <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Editar ordenes de compra') ||
                      scope.row.user.id == $page.props.auth.user.id" :command="'edit-' + scope.row.id">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                      </svg>
                      Editar</el-dropdown-item>
                    <el-dropdown-item v-if="$page.props.auth.user.permissions.includes('Crear ordenes de compra')"
                      :command="'clone-' + scope.row.id">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                      </svg>
                      Clonar</el-dropdown-item>
                    <el-dropdown-item
                      v-if="$page.props.auth.user.permissions.includes('Autorizar ordenes de compra') && scope.row.status == 'Pendiente'"
                      :command="'authorize-' + scope.row.id"><i
                        class="fa-solid fa-check"></i>Autorizar</el-dropdown-item>
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
import IndexSearchBar from "@/Components/MyComponents/IndexSearchBar.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

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
    TextInput,
    IndexSearchBar,
  },
  props: {
    purchases: Array,
  },
  methods: {
    handleSearch(search) {
      this.search = search;
    },
    handleSelectionChange(val) {
      this.$refs.multipleTableRef.value = val;

      if (!this.$refs.multipleTableRef.value.length) {
        this.disableMassiveActions = true;
      } else {
        this.disableMassiveActions = false;
      }
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
      console.log(row)
    },

    async clone(purchase_id) {
      try {
        const response = await axios.post(
          route("purchases.clone", {
            purchase_id: purchase_id,
          })
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          this.purchases.unshift(response.data.newItem);
        } else {
          this.$notify({
            title: "Algo salió mal",
            message: response.data.message,
            type: "error",
          });
        }
      } catch (err) {
        this.$notify({
          title: "Algo salió mal",
          message: err.message,
          type: "error",
        });
        console.log(err);
      }
    },
    async deleteSelections() {
      try {
        const response = await axios.post(
          route("purchases.massive-delete", {
            purchases: this.$refs.multipleTableRef.value,
          })
        );

        if (response.status == 200) {
          this.$notify({
            title: "Éxito",
            message: response.data.message,
            type: "success",
          });

          // update list of companies
          let deletedIndexes = [];
          this.purchases.forEach((purchase, index) => {
            if (this.$refs.multipleTableRef.value.includes(purchase)) {
              deletedIndexes.push(index);
            }
          });

          // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
          deletedIndexes.sort((a, b) => b - a);

          // Eliminar clientes por índice
          for (const index of deletedIndexes) {
            this.purchases.splice(index, 1);
          }
        } else {
          this.$notify({
            title: "Algo salió mal",
            message: response.data.message,
            type: "error",
          });
        }
      } catch (err) {
        this.$notify({
          title: "Algo salió mal",
          message: err.message,
          type: "error",
        });
        console.log(err);
      }
    },
    async authorize(purchase_id) {
      try {
        const response = await axios.put(route('purchases.authorize', purchase_id));

        if (response.status === 200) {
          const index = this.purchases.findIndex(item => item.id == purchase_id);
          this.purchases[index].authorized_at = response.data.item.authorized_at;
          this.purchases[index].authorized_user_name = response.data.item.authorized_user_name;
          this.purchases[index].status = response.data.item.status;
          // window.location.reload();
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
    },
    handleCommand(command) {
      const commandName = command.split("-")[0];
      const rowId = command.split("-")[1];

      if (commandName == "clone") {
        this.clone(rowId);
      } else if (commandName == "make_so") {
        console.log("SO");
      } else if (commandName == 'authorize') {
        this.authorize(rowId);
      } else {
        this.$inertia.get(route("purchases." + commandName, rowId));
      }
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
