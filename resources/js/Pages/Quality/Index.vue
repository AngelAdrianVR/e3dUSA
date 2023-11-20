<template>
<AppLayoutNoHeader title="Calidad">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Departamento de calidad</label>
        </div>
        <div class="flex justify-between">
            <div class="flex items-center space-x-2 w-1/3">
            <input
            @keyup.enter="handleSearch"
            v-model="inputSearch"
            type="search"
            class="input"
            placeholder="Buscar"
            />
            <!-- <SecondaryButton @click="handleSearch" type="submit" class="rounded-lg">
                <i class="fa-solid fa-magnifying-glass"></i>
            </SecondaryButton> -->
            </div>
        <div class="flex items-center space-x-2">
            <Link v-if="$page.props.auth.user.permissions.includes('Crear registro de calidad')" :href="route('qualities.create')">
              <PrimaryButton class="rounded-xl">+ Crear</PrimaryButton>
            </Link>
          
        </div>
      </div>
    </div>

    <!-- ----------- Client monitor table ----------- -->
    <div class="w-11/12 mx-8 my-16">
      <table v-if="filteredTableData.length" class="w-full mx-auto text-sm">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-3 pl-2 text-left">
              ID <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Supervisor <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Fecha y hora <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Folio inspeccionado <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Estado de folio <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr  v-for="quality in filteredTableData" :key="quality.id"
          @click="$inertia.get(route('qualities.show', quality.id))"
            class="mb-4 hover:bg-[#dfdbdba8] cursor-pointer"
          >
            <td class="py-2 pl-2 rounded-l-full">
              {{ quality.id}}
            </td>
            <td class="py-2">
              {{ quality.supervisor.name }}
            </td>
            <td class="py-2">
              <span
                class="py-1 rounded-full"
                >{{ quality.created_at }}</span
              >
            </td>
            <td class="py-2">
              <span
                class="py-1 rounded-full"
                >{{ quality.folio }}</span
              >
            </td>
            <td class="py-2">
             {{ quality.status }}
            </td>
            <td
              v-if="$page.props.auth.user.permissions.includes('Eliminar registro de calidad')"
              class="py-2 pr-2 rounded-r-full"
            >
              <el-popconfirm
                confirm-button-text="Si"
                cancel-button-text="No"
                icon-color="#D90537"
                title="¿Eliminar?"
                @confirm="deleteQuality(quality)"
              >
                <template #reference>
                  <i @click.stop="" class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
                </template>
              </el-popconfirm>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else>
        <p class="text-sm text-center text-gray-400">No hay seguimientos para mostrar</p>
      </div>
    </div>

</AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data(){
    return{
        search: "",
        inputSearch: "",
    }
},
components:{
    AppLayoutNoHeader,
    Dropdown,
    DropdownLink,
    PrimaryButton,
    SecondaryButton,
    Link,
},
props:{
    qualities: Object,
},
methods:{
    handleSearch() {
      this.search = this.inputSearch;
    },
    async deleteQuality(quality) {
      try {
        const response = await axios.delete(route('qualities.destroy', quality.id));

      if (response.status === 200) {
           this.$notify({
            title: "Éxito",
            message: "Se ha eliminado correctamente",
            type: "success",
          });
        const index = this.qualities.findIndex(item => item.id === quality.id);

        if (index !== -1) {
          this.qualities.splice(index, 1);
        }
      }
      } catch (error) {
        console.log(error);
      }
    },
},
computed: {
    filteredTableData() {
      if (!this.search) {
        return this.qualities;
      } else {
        return this.qualities.filter((quality) =>
          quality.id.toString().toLowerCase().includes(this.search.toLowerCase()) ||
          quality.folio.toLowerCase().includes(this.search.toLowerCase()) ||
          quality.status.toLowerCase().includes(this.search.toLowerCase()) ||
          quality.supervisor.name.toLowerCase().includes(this.search.toLowerCase())
        );
      }
    },
  },
}
</script>
