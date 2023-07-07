<template>
  <div>
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

      <!-- tabla -->
      <div class="lg:w-5/6 mx-auto mt-6">
        <div class="flex justify-between">
          <!-- pagination -->
          <div>
            <el-pagination
              @current-change="handlePagination"
              layout="prev, pager, next"
              :total="purchases.length"
            />
          </div>

          <!-- buttons -->
          <div>
            <el-popconfirm
              confirm-button-text="Si"
              cancel-button-text="No"
              icon-color="#FF0000"
              title="¿Continuar?"
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
        </div>

        <el-table
          :data="filteredTableData"
          max-height="450"
          style="width: 100%"
          @selection-change="handleSelectionChange"
          ref="multipleTableRef"
          :row-class-name="tableRowClassName"
        >
          <el-table-column type="selection" width="45" />
          <el-table-column prop="id" label="ID" width="45" />
          <el-table-column prop="user.name" label="Creador" width="150" />
          <el-table-column prop="created_at" label="Creado el" width="120" />
          <el-table-column
            prop="authorized_user_name"
            label="Autorizado por"
            width="130"
          />
          <el-table-column prop="status" label="Estatus" width="100" />
          <el-table-column prop="emited_at" label="Pedido el" width="120" />
          <el-table-column prop="recieved_at" label="Recibido el" width="120" />
          <el-table-column prop="supplier.name" label="Proveedor" width="120" />
          <el-table-column align="right" fixed="right">
            <template #header>
              <TextInput
                v-model="search"
                type="search"
                class="w-full text-gray-600"
                placeholder="Buscar"
              />
            </template>
            <template #default="scope">
              <el-dropdown trigger="click" @command="handleCommand">
                <span class="el-dropdown-link mr-3">
                  <i class="fa-solid fa-ellipsis-vertical"></i>
                </span>
                <template #dropdown>
                  <el-dropdown-menu>
                    <el-dropdown-item :command="'show-' + scope.row.id"
                      ><i class="fa-solid fa-eye"></i> Ver</el-dropdown-item
                    >
                    <el-dropdown-item :command="'edit-' + scope.row.id"
                      ><i class="fa-solid fa-pen"></i> Editar</el-dropdown-item
                    >
                    <el-dropdown-item :command="'clone-' + scope.row.id"
                      ><i class="fa-solid fa-clone"></i>
                      Clonar</el-dropdown-item
                    >
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
import TextInput from "@/Components/TextInput.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

export default {
  data() {
    return {
      disableMassiveActions: true,
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
  },
  props: {
    purchases: Array,
  },
  methods: {
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
    tableRowClassName({ row, rowIndex }) {
      if (row.status === 'Recibido') {
        return "text-green-600";
      } else {
        return "text-amber-600";
      }

      return "";
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

          this.purchases.data.unshift(response.data.newItem);
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
          this.purchases.data.forEach((purchase, index) => {
            if (this.$refs.multipleTableRef.value.includes(purchase)) {
              deletedIndexes.push(index);
            }
          });

          // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
          deletedIndexes.sort((a, b) => b - a);

          // Eliminar clientes por índice
          for (const index of deletedIndexes) {
            this.purchases.data.splice(index, 1);
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

    handleCommand(command) {
      const commandName = command.split("-")[0];
      const rowId = command.split("-")[1];

      if (commandName == "clone") {
        this.clone(rowId);
      } else if (commandName == "make_so") {
        console.log("SO");
      } else {
        this.$inertia.get(route("purchases." + commandName, rowId));
      }
    },
  },
  computed: {
    filteredTableData() {
      if (!this.search) {
        return this.purchases.data.filter(
          (item, index) => index >= this.start && index < this.end
        );
      } else {
        return this.purchases.data.filter(
          (purchase) =>
            purchase.user.name.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.status.toLowerCase().includes(this.search.toLowerCase()) ||
            purchase.authorized_user_name.toLowerCase().includes(this.search.toLowerCase()) 
        );
      }
    },
  },
};
</script>
