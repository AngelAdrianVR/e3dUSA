<template>
    <div>
        <AppLayout title="Catalogo de productos">
        <template #header>
        <div class="flex justify-between">
            <div class="flex items-center space-x-2 text-gray-800">
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
                <el-table-column prop="" label="Imagen" width="80" />
                <el-table-column prop="part_number" label="Num de parte" width="200" />
                <el-table-column prop="name" label="Nombre" width="200" />
                <el-table-column prop="cost" label="Costo $" width="150" />
                <el-table-column prop="description" label="Descripción" />
                <el-table-column align="right" fixed="right" width="200">
                    <template #header>
                        <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
                    </template>
                    <template #default="scope">
                        <el-button size="small" type="primary"
                            @click="edit(scope.$index, scope.row)">Editar</el-button>
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
import { Link, useForm } from "@inertiajs/vue3";
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
                const response = await axios.post(route('catalog-products.massive-delete', {
                    catalog_products: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.catalog_products.forEach((catalog_product, index) => {
                        if (this.$refs.multipleTableRef.value.includes(catalog_product)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.catalog_products.splice(index, 1);
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
        edit(index, catalog_product) {
            this.$inertia.get(route('catalog-products.edit', catalog_product));
        }
  },

  computed: {
        filteredTableData() {
            return this.catalog_products.filter(
                (catalog_product) =>
                    !this.search ||
                    catalog_product.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    catalog_product.part_number.toLowerCase().includes(this.search.toLowerCase()) ||
                    catalog_product.measure_unit.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
