<template>
    <div v-if="loading" class="absolute z-30 left-0 top-0 inset-0 bg-black opacity-50 flex items-center justify-center">
    </div>
    <div v-if="loading"
        class="absolute z-40 top-1/2 left-[35%] lg:left-1/2 w-32 h-32 rounded-lg bg-white flex items-center justify-center">
        <i class="fa-solid fa-spinner fa-spin text-5xl text-primary"></i>
    </div>
    <AppLayoutNoHeader title="Prospectos">
        <div class="flex justify-between text-lg mx-14 mt-11">
            <span>Prospectos</span>
        </div>

        <div class="flex justify-between mt-5 mx-1 lg:mx-14">
            <div class="w-1/3 relative ">
                <input @keyup.enter="fetchMatches" v-model="search" class="input outline-none pr-8"
                    placeholder="Buscar prospecto" />
                <i class="fa-solid fa-magnifying-glass absolute top-2 right-4 text-xs text-[#9A9A9A]"></i>
            </div>
            <div>
                <PrimaryButton @click="$inertia.get(route('prospects.create'))">Nuevo prospecto</PrimaryButton>
            </div>
        </div>

        <NotificationCenter module="prospects" />
        <div v-if="!search" class="overflow-auto mx-1 lg:mx-14">
            <Pagination :pagination="prospects" class="mt-6 py-2" />
        </div>
        <div class="lg:px-14 pb-7 pt-10 text-sm overflow-x-scroll">
            <table v-if="prospects.data.length > 0" class="w-full mx-auto">
                <thead>
                    <tr class="text-left">
                        <th class="font-bold pb-5 pl-2">Nombre</th>
                        <th class="font-bold pb-5">Creado el </th>
                        <th class="font-bold pb-5">Contacto </th>
                        <th class="font-bold pb-5">Teléfono </th>
                        <th class="font-bold pb-5">Estatus </th>
                        <th class="font-bold pb-5 pr-2">Responsable </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="prospect in prospects.data" :key="prospect.id"
                        class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
                        @click="$inertia.get(route('prospects.show', prospect.id))">
                        <td class="text-left py-2 rounded-l-full pl-2 max-w-[230px] truncate">
                            {{ prospect.name }}
                        </td>
                        <td class="text-left py-2">
                            {{ dateFormat(prospect.created_at) }}
                        </td>
                        <td class="text-left py-2">
                            {{ prospect.contact_name }}
                        </td>
                        <td class="text-left py-2">
                            {{ prospect.contact_phone }}
                            {{ prospect.contact_phone_extension ? ' Ext. ' + prospect.contact_phone_extension : null }}
                        </td>
                        <td class="text-left py-2">
                            <el-tooltip :content="statuses.find(item => item.label == prospect.status).tooltip"
                                placement="top">
                                <span class="px-2 py-1 rounded-full" :style="{
                                    color: statuses.find(item => item.label == prospect.status).color,
                                    backgroundColor: statuses.find(item => item.label == prospect.status).bg
                                }">{{ prospect.status }}</span>
                            </el-tooltip>
                        </td>
                        <td class="text-left py-2">
                            {{ prospect.seller?.name ?? prospect.user?.name }}
                        </td>
                        <td v-if="$page.props.auth.user.permissions.includes('Eliminar prospectos')"
                            class="text-left pr-2 rounded-r-full">
                            <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#D90537"
                                title="¿Eliminar?" @confirm="deleteProspect(prospect)">
                                <template #reference>
                                    <i @click.stop="" class="fa-regular fa-trash-can text-primary cursor-pointer p-2"></i>
                                </template>
                            </el-popconfirm>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-else class="text-xs text-center text-gray-600">No hay prospectos para mostrar</p>
        </div>
    </AppLayoutNoHeader>
</template>
  
<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Pagination from "@/Components/MyComponents/Pagination.vue";
import NotificationCenter from "@/Components/MyComponents/NotificationCenter.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            search: '',
            loading: false,
            statuses: [
                {
                    label: "Contacto inicial",
                    bg: "#F1F996",
                    color: "#B1B402",
                    tooltip: "El prospecto entra en contacto con la empresa por primera vez",
                },
                {
                    label: "Asignado",
                    bg: "#F9BA96",
                    color: "#F07209",
                    tooltip: "Se asignó a un responsable para gestionar el seguimiento con el prospecto",
                },
                {
                    label: "En proceso de conversión",
                    bg: "#BCF996",
                    color: "#37A305",
                    tooltip: "El responsable esta trabajando para convertir el prospecto en nuevo cliente",
                },
                {
                    label: "Perdido",
                    bg: "#F7B7FC",
                    color: "#9E0FA9",
                    tooltip: "El prospecto no se convierte en cliente ",
                },
            ],
        }
    },
    components: {
        AppLayoutNoHeader,
        PrimaryButton,
        SecondaryButton,
        Pagination,
        NotificationCenter,
    },
    props: {
        prospects: Object
    },
    methods: {
        dateFormat(date) {
            const formattedDate = format(new Date(date), 'dd MMM yy', { locale: es });

            return formattedDate;
        },
        async fetchMatches() {
            this.loading = true;
            try {
                if (!this.search) {
                    this.$inertia.get(route('prospects.index'));
                } else {
                    const response = await axios.get(route('prospects.get-matches', { query: this.search }));

                    if (response.status === 200) {
                        this.prospects.data = response.data.items;
                    }
                }

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        deleteProspect(prospect) {
            this.$inertia.delete(route('prospects.destroy', prospect));
            this.$notify({
                title: "Éxito",
                message: "Prospecto eliminado",
                type: "success",
            });
        },
        handleSearch() {
            this.search = this.inputSearch;
        },
        handlePageChange(newPage) {
            this.$inertia.get(route('prospects.index', { page: newPage }));
        },
    },
}
</script>
  