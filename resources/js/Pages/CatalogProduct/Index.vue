<template>
    <div>
        <AppLayout title="Catalogo de productos">
        <template #header>
        <div class="flex justify-between">
            <div class="flex items-center space-x-2 text-gray-600">
                <!-- <i class="fa-brands fa-product-hunt text-xl"></i> -->
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
    <div class="mx-4 py-3">
        <Table />
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
       
    };
  },
  components: {
    AppLayout,
    Table,
    EmptyTable,
    SecondaryButton,
    Link
  },
  props: {},
};
</script>
