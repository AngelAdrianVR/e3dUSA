<template>
  <Loading v-if="loading" class="my-10" />
  <div v-else class="overflow-x-auto text-sm">
    <table v-if="items.length" class="w-full table-fixed">
      <thead>
        <tr class="text-left">
          <th class="font-bold pb-5 px-2">
            Nombre del documento
          </th>
          <th class="font-bold pb-5 px-2">
            Fecha de creación
          </th>
          <th class="font-bold pb-5 px-2">
            Creador
          </th>
          <th class="font-bold pb-5 px-2">
            Tipo
          </th>
          <th class="font-bold pb-5 px-2">
            Descripción
          </th>
          <th class="font-bold pb-5 px-2">
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="item.id" class="border-[#9A9A9A]">
          <td class="truncate py-2 px-2" :title="item.name">
            <div>
              <!-- <img class="w-14 object-contain" src="@/../../public/images/pdf.png" alt=""> -->
              <a :href="item.media[0]?.original_url" target="_blank" class="text-secondary underline">{{ item.name }}
                (.{{ item.media[0]?.file_name.split('.')[1] }})</a>
            </div>
          </td>
          <td class="truncate py-2 px-2">
            {{ formatDate(item.created_at) }}
          </td>
          <td class="py-2 px-2">
            {{ item.user?.name }}
          </td>
          <td class="py-2 px-2">
            {{ item.type }}
          </td>
          <td class="truncate py-2 px-2 rounded-r-full" :title="item.description">
            {{ item.description }}
          </td>
          <td v-if="$page.props.auth.user.permissions.includes('Eliminar diseños exclusivos')"
            class="text-left py-2 px-2 rounded-r-full">
            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537" title="¿Eliminar?"
              @confirm="deleteDesign(index)">
              <template #reference>
                <button class="text-primary cursor-pointer pl-3">
                  <i class="fa-regular fa-trash-can"></i>
                </button>
              </template>
            </el-popconfirm>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-else class="flex flex-col text-center justify-center mt-8 text-gray-400">
      <p class="text-sm text-center">No hay diseños de este cliente</p>
      <i class="fa-regular fa-folder-open text-9xl mt-10 text-gray-400/30"></i>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Loading from '@/Components/MyComponents/Loading.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
  data() {
    return {
      items: [],
      loading: true,
    }
  },
  components: {
    Loading,
  },
  props: {
    companyId: Number,
  },
  emits: ['item-deleted'],
  methods: {
    formatDate(date) {
      const parsedDate = new Date(date);
      return format(parsedDate, 'dd MMM Y, h:mm a', { locale: es }); // Formato personalizado
    },
    async deleteDesign(itemIndex) {
      try {
        const response = await axios.delete(route('exclusive-designs.destroy', this.items[itemIndex].id));
        if (response.status === 200) {
          this.$notify({
            title: "Correcto",
            message: "Se ha eliminado el registro",
            type: "success",
          });

          this.items.splice(itemIndex, 1);
        }
      } catch (error) {
        console.log(error);
        this.$notify({
          title: "Algo salió mal",
          message: "No se pudo eliminar registro. Inténta más tarde",
          type: "error",
        });
      }
    },
    async fetchExclusiveDesigns() {
      try {
        this.loading = true;
        const response = await axios.get(route("companies.get-exclusive-designs", this.companyId));

        if (response.status === 200) {
          this.items = response.data.items;
        }
      } catch (err) {
        this.$notify({
          title: "Algo salió mal",
          message: "El servidor no pudo enviar los diseños exclusivos de este cliente",
          type: "error",
        });
        console.log(err);
      } finally {
        this.loading = false;
      }
    },
  },
  async mounted() {
    await this.fetchExclusiveDesigns();
  }
}
</script>
