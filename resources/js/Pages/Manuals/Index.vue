<template>
    <AppLayoutNoHeader title="Tutoriales y manuales">
        <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1 dark:text-white">
            <div class="flex justify-between">
                <label class="text-lg">Tutoriales y manuales</label>
            </div>
            <div class="flex justify-between">
                <div class="flex items-center space-x-2 w-1/3">
                    <input @keyup.enter="handleSearch" v-model="inputSearch" type="search" class="input"
                        placeholder="Buscar tutorial / manual" />
                </div>
                <div class="flex items-center space-x-2">
                    <Link v-if="$page.props.auth.user.permissions.includes('Crear manuales')"
                        :href="route('manuals.create')">
                    <PrimaryButton class="rounded-xl">Crear</PrimaryButton>
                    </Link>
                </div>
            </div>
        </div>
        <div class="w-11/12 mx-8 my-16 dark:text-white">
            <div v-if="filteredTableData.length" class="w-full mx-auto text-sm">
                <ManualPresentation v-for="item in filteredTableData" :key="item.id" :manual="item" />
            </div>
            <div v-else>
                <p class="text-sm text-center text-gray-400">No hay registros para mostrar</p>
            </div>
        </div>
        <!-- <div class="mt-4">
              <Pagination :pagination="qualities" />
          </div> -->
    </AppLayoutNoHeader>
</template>
    
<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Pagination from "@/Components/MyComponents/Pagination.vue";
import ManualPresentation from "@/Components/MyComponents/Manual/ManualPresentation.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    data() {
        return {
            search: "",
            inputSearch: "",
        }
    },
    components: {
        AppLayoutNoHeader,
        Dropdown,
        DropdownLink,
        PrimaryButton,
        SecondaryButton,
        Pagination,
        Link,
        ManualPresentation,
    },
    props: {
        manuals: Object,
    },
    methods: {
        handleSearch() {
            this.search = this.inputSearch;
        },
        // async deleteManual(manual) {
        //     try {
        //         const response = await axios.delete(route('manuals.destroy', manual.id));

        //         if (response.status === 200) {
        //             this.$notify({
        //                 title: "Éxito",
        //                 message: "Se ha eliminado correctamente",
        //                 type: "success",
        //             });
        //             const index = this.manuals.findIndex(item => item.id === manual.id);

        //             if (index !== -1) {
        //                 this.manuals.splice(index, 1);
        //             }
        //         }
        //     } catch (error) {
        //         console.log(error);
        //     }
        // },
    },
    computed: {
        filteredTableData() {
            if (!this.search) {
                return this.manuals;
            } else {
                return this.manuals.filter((manual) =>
                    manual.title.toString().toLowerCase().includes(this.search.toLowerCase()) ||
                    manual.type.toLowerCase().includes(this.search.toLowerCase()) ||
                    manual.user.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        },
    },
}
</script>
    