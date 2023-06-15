<template>
    <div>
        <AppLayout title="Catalogo de productos">
        <template #header>
        <div class="flex justify-between">
            <div class="flex items-center space-x-2 text-gray-600">
                <h2 class="font-semibold text-xl leading-tight">Catálogo de productos</h2>
            </div>
            <div>
            <Link :href="route('catalog-products.create')">
                <SecondaryButton>+ Nuevo</SecondaryButton>
            </Link>
            </div>
        </div>
        </template>

    <!-- tabla -->
    <div class="lg:w-5/6 mx-auto mt-6">
            <div class="flex space-x-2 justify-end">
                <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#FF0000"
                    title="Continuar con la eliminacion?" @confirm="deleteSelections">
                    <template #reference>
                        <el-button type="danger" plain class="mb-3" :disabled="disableMassiveActions">Eliminar</el-button>
                    </template>
                </el-popconfirm>
            </div>
        <el-table :data="filteredTableData" max-height="450" style="width: 100%" @selection-change="handleSelectionChange"
                ref="multipleTableRef" :row-class-name="tableRowClassName">
                <el-table-column type="selection" width="45" />
                <el-table-column prop="name" label="Nombre" width="85" />
                <el-table-column prop="part_number" label="Num de parte" />
                <el-table-column prop="measure_unity" label="Unidad de medida" />
                <el-table-column prop="quantity" label="Cantidad" />
                <el-table-column prop="description" label="Descripción" />
                <el-table-column align="right" fixed="right">
                    <template #header>
                        <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
                    </template>
                    <template #default="scope">
                        <el-button size="small" type="primary"
                            @click="createQuote(scope.$index, scope.row)">Clonar</el-button>
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
import { Link, useForm } from "@inertiajs/vue3";


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
    Link
  },
  props: {
    catalog_products: Array
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
            async deleteSelections() {
            try {
                const response = await axios.post(route('messages.massive-delete', {
                    messages: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    // let indexes = [];
                    this.toast.success(response.data.message);

                    // update list of messages
                    let deletedIndexes = [];
                    this.messages.forEach((message, index) => {
                        if (this.$refs.multipleTableRef.value.includes(message)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar mensajes por índice
                    for (const index of deletedIndexes) {
                        this.messages.splice(index, 1);
                    }

                } else {
                    this.toast.error(response.data.message);
                }

            } catch (err) {
                this.toast.error(err);
                console.log(err);
            }
        },
  },
};
</script>
