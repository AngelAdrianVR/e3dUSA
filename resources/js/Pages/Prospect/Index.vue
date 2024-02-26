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
                    placeholder="Buscar proyecto" />
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
                        <th class="font-bold pb-5">Nombre<i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                        <th class="font-bold pb-5">Creado el <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                        <th class="font-bold pb-5">Contacto <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                        <th class="font-bold pb-5">Teléfono <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                        <th class="font-bold pb-5">Estatus <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                        <th class="font-bold pb-5">Responsable <i class="fa-solid fa-arrow-down-long ml-3"></i></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="prospect in prospects.data" :key="prospect.id"
                        class="mb-4 cursor-pointer hover:bg-[#dfdbdba8]"
                        @click="$inertia.get(route('prospects.show', prospect.id))">
                        <td :title="prospect.prospect_name"
                            class="text-left py-2 px-2 rounded-l-full max-w-[230px] truncate">
                            {{ prospect.name }}
                        </td>
                        <td class="text-left py-2 px-2">
                            {{ prospect.created_at }}
                        </td>
                        <td class="text-left py-2 px-2">
                            {{ prospect.contact_name }}
                        </td>
                        <td class="text-left py-2 px-2">
                            {{ prospect.contact_phone }}
                        </td>
                        <td class="text-left py-2 px-2">
                            {{ prospect.status }}
                        </td>
                        <td class="text-left py-2 px-2">
                            {{ prospect.seller.name }}
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

export default {
    data() {
        return {
            search: '',
            loading: false,
            // inputSearch: '',
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
        deleteprospect(prospect) {
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
    // computed: {
    //     filteredTableData() {
    //         if (!this.search) {
    //             return this.prospects.data;
    //         } else {
    //             return this.prospects.data.filter(
    //                 (prospect) =>
    //                     prospect.name.toLowerCase().includes(this.search.toLowerCase()) ||
    //                     prospect.contact_name.toLowerCase().includes(this.search.toLowerCase()) ||
    //                     prospect.contact_phone.toLowerCase().includes(this.search.toLowerCase()) ||
    //                     prospect.seller.name.toLowerCase().includes(this.search.toLowerCase())
    //             )
    //         }
    //     }
    // },
}
</script>
  