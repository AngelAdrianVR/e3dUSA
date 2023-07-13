<template>
    <div>
        <AppLayout title="Almacén de scrap">
        <template #header>
        <div class="flex justify-between">
            <div class="flex items-center space-x-2">
                <h2 class="font-semibold text-xl leading-tight">Almacén de scrap</h2>
            </div>
            <div>
            <Link :href="route('storages.scraps.create')">
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
                <el-table-column prop="storageable.name" label="Nombre" width="250" />
                <el-table-column prop="storageable.part_number" label="N° parte" width="120" />
                <el-table-column prop="location" label="Ubicación" width="120" />
                <el-table-column prop="quantity" label="Cantidad" width="100" />
                <el-table-column align="right" fixed="right" >
                    <template #header>
                        <TextInput v-model="search" type="search" class="w-full" placeholder="Buscar" />
                    </template>
                    <!-- <template #default="scope">
                        <el-button size="small" type="primary"
                            @click="edit(scope.$index, scope.row)">Editar</el-button>
                    </template> -->
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
    SecondaryButton,
    Link,
    TextInput,
  },
  props: {
    scraps: Array
  },
  methods:{
    tableRowClassName({row, rowIndex}){
            return 'text-red-600';
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
                const response = await axios.post(route('storages.scraps.massive-delete', {
                    scraps: this.$refs.multipleTableRef.value
                }));

                if (response.status == 200) {
                    this.$notify({
                        title: 'Éxito',
                        message: response.data.message,
                        type: 'success'
                    });

                    // update list of quotes
                    let deletedIndexes = [];
                    this.scraps.forEach((scrap, index) => {
                        if (this.$refs.multipleTableRef.value.includes(scrap)) {
                            deletedIndexes.push(index);
                        }
                    });

                    // Ordenar los índices de forma descendente para evitar problemas de desplazamiento al eliminar elementos
                    deletedIndexes.sort((a, b) => b - a);

                    // Eliminar cotizaciones por índice
                    for (const index of deletedIndexes) {
                        this.scraps.splice(index, 1);
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
        edit(index, scrap) {
            console.log(scrap);
            this.$inertia.get(route('storages.finished-products.edit', scrap.storageable));
        }
  },

  computed: {
        filteredTableData() {
            return this.scraps.filter(
                (scrap) =>
                    !this.search ||
                    scrap.storageable.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    scrap.storageable.part_number.toLowerCase().includes(this.search.toLowerCase())
            )
        }
    },
};
</script>
