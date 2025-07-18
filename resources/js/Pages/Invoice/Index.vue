<template>
    <AppLayout title="Factuaración">
        <main class="p-2 md:py-4 md:px-6">
            <div class="flex justify-between">
                <div class="flex items-center space-x-2">
                    <h2 class="font-semibold text-xl leading-tight">Facturas</h2>
                </div>
                <div>
                    <Link :href="route('invoices.create')">
                        <SecondaryButton>Crear factura</SecondaryButton>
                    </Link>
                </div>
            </div>
        </main>
    </AppLayout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PaginationWithNoMeta from "@/Components/MyComponents/PaginationWithNoMeta.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
data() {
    return {

    }
},
components: {
    PaginationWithNoMeta,
    SecondaryButton,
    AppLayout,
    Link
},
props: {

},
methods: {
    
}
}
</script>
