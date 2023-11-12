<template>
<AppLayoutNoHeader title="(CRM) Seguimiento integral">
    <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div class="flex justify-between">
          <label class="text-lg">Seguimiento integral</label>
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
            <Link v-if="$page.props.auth.user.permissions.includes('Enviar correos en seguimiento integral')" :href="route('email-monitors.create')">
              <PrimaryButton class="rounded-xl">Enviar correo</PrimaryButton>
            </Link>
          <Dropdown
            align="right"
            width="48"
            v-if="$page.props.auth.user.permissions.includes('Enviar correos en seguimiento integral') ||
            $page.props.auth.user.permissions.includes('Registrar pagos en seguimiento integral') ||
            $page.props.auth.user.permissions.includes('Agendar citas en seguimiento integral')"
          >
            <template #trigger>
              <button class="h-9 px-3 rounded-lg bg-[#D9D9D9] flex items-center text-sm">
                Más <i class="fa-solid fa-chevron-down text-[11px] ml-2"></i>
              </button>
            </template>
            <template #content>
              <DropdownLink
              :href="route('payment-monitors.create')"
                v-if="
                  $page.props.auth.user.permissions.includes('Registrar pagos en seguimiento integral')
                "
              >
                Registrar pago o transacción
              </DropdownLink>
              <DropdownLink
              :href="route('meeting-monitors.create')"
                v-if="
                  $page.props.auth.user.permissions.includes('Agendar citas en seguimiento integral')
                "
              >
                Agendar cita
              </DropdownLink>
              <DropdownLink
              :href="route('whatsapp-monitors.create')"
                v-if="
                  $page.props.auth.user.permissions.includes('Registrar interaccion whatsapp en seguimiento integral')
                "
              >
                Interacción WhatsApp
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>
    </div>

    <!-- ----------- Client monitor table ----------- -->
    <div class="w-11/12 mx-8 my-16">
      <table v-if="filteredTableData.length" class="w-full mx-auto text-sm">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-3 pl-2 text-left">
              Folio <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Cliente <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Tipo que interacciones <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Fecha <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Concepto <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th class="font-bold pb-3 text-left">
              Vededor <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr  v-for="monitor in filteredTableData" :key="monitor.id"
          @click="showMonitorType(monitor)"
            class="mb-4 hover:bg-[#dfdbdba8] cursor-pointer"
          >
            <td class="py-2 pl-2 rounded-l-full">
              {{ monitor.folio}}
            </td>
            <td class="py-2">
              {{ monitor.company?.business_name ? monitor.company?.business_name : 'Oportunidad: ' + monitor.oportunity?.name }}
            </td>
            <td class="py-2">
              <span
                class="py-1 rounded-full"
                >{{ monitor.type }}</span
              >
            </td>
            <td class="py-2">
              <span
                class="py-1 rounded-full"
                >{{ monitor.date }}</span
              >
            </td>
            <td class="py-2">
              <p :title="monitor.concept" class="w-36 truncate">{{ monitor.concept }}</p>
            </td>
            <td class="py-2">
              {{ monitor.seller?.name }}
            </td>
            <td
              v-if="$page.props.auth.user.permissions.includes('Eliminar seguimiento integral')"
              class="py-2 pr-2 rounded-r-full"
            >
              <el-popconfirm
                confirm-button-text="Si"
                cancel-button-text="No"
                icon-color="#D90537"
                title="¿Eliminar?"
                @confirm="deleteClientMonitor(monitor)"
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
    client_monitors: Object,
},
methods:{
    showMonitorType(monitor) {
      if (monitor.type == 'Correo') {
        this.$inertia.get(route('email-monitors.show', monitor.emailMonitor?.id));
      } else if (monitor.type == 'Pago') {
        this.$inertia.get(route('payment-monitors.show', monitor.paymentMonitor?.id));
      } else if (monitor.type == 'Reunión') {
        this.$inertia.get(route('meeting-monitors.show', monitor.mettingMonitor?.id));
      } else if (monitor.type == 'WhatsApp') {
        this.$inertia.get(route('whatsapp-monitors.show', monitor.whatsappMonitor?.id));
      }
    },
    handleSearch() {
      this.search = this.inputSearch;
    },
    async deleteClientMonitor(monitor) {
      try {
        const response = await axios.delete(route('client-monitors.destroy', monitor.id));

      if (response.status === 200) {
           this.$notify({
            title: "Éxito",
            message: "Se ha eliminado correctamente",
            type: "success",
          });
        const index = this.client_monitors.data.findIndex(item => item.id === monitor.id);

        if (index !== -1) {
          this.client_monitors.data.splice(index, 1);
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
        return this.client_monitors.data;
      } else {
        return this.client_monitors.data.filter((monitor) =>
          monitor.folio.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.type.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.concept.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.company?.business_name.toLowerCase().includes(this.search.toLowerCase()) ||
          monitor.seller?.name.toLowerCase().includes(this.search.toLowerCase())
        );
      }
    },
  },
}
</script>

<style>

</style>