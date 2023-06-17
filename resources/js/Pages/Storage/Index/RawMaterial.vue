<template>
    <div>
        <AppLayout title="Materia prima">
        <template #header>
        <div class="flex justify-between">
            <div class="flex items-center space-x-2">
                <h2 class="font-semibold text-xl leading-tight">Materia prima</h2>
            </div>
            <div>
            <Link :href="route('raw-materials.create')">
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
                <el-table-column prop="id" label="ID" width="45" />
                <el-table-column prop="name" label="Nombre" width="250" />
                <el-table-column prop="part_number" label="N° parte" width="120" />
                <el-table-column prop="description" label="Descripción" width="350" />
                <el-table-column align="right" fixed="right">
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
    raw_materials: Array
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
                const response = await axios.post(route('raw-materials.massive-delete', {
                    raw_materials: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.raw_materials.forEach((raw_material, index) => {
                        if (this.$refs.multipleTableRef.value.includes(raw_material)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.raw_materials.splice(index, 1);
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
        edit(index, raw_material) {
            this.$inertia.get(route('raw-materials.edit', raw_material));
        }
  },

  computed: {
        filteredTableData() {
            return this.raw_materials.filter(
                (raw_material) =>
                    !this.search ||
                    raw_material.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    raw_material.part_number.toLowerCase().includes(this.search.toLowerCase()) ||
                    raw_material.description.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
