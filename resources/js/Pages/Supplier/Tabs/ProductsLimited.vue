<template>
    <section class="p-7 dark:text-white">
        <div v-if="!loading">
            <div v-if="rawMaterials.length" class="lg:grid grid-cols-4 mt-7 gap-5">
                <SupplierProductCardLimited v-for="product in rawMaterials" :key="product" :product="product" />
            </div>
            <p v-else class="text-gray-500 text-center text-sm">No hay productos registrados a este proveedor</p>
        </div>
        <div v-else class="flex justify-center items-center pt-10">
            <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
        </div>
    </section>
</template>
<script>
import SupplierProductCardLimited from '@/Components/MyComponents/SupplierProductCardLimited.vue';

export default {
    data() {
        return {
            loading: false,
            rawMaterials: [],
        }
    },
    props: {
        supplier: Object,
    },
    components: {
        SupplierProductCardLimited,
    },
    methods: {
        async fetchSupplierItems() {
            if (!this.rawMaterials.length) { //solo hace la peticion si no se han cargado
                this.loading = true;
                try {
                    const response = await axios.get(route('raw-materials.fetch-supplier-items', {
                        raw_materials_ids: this.supplier.raw_materials_id.join(',')
                    }));

                    if (response.status === 200) {
                        this.rawMaterials = response.data.items;
                    }
                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                }
            }
        }
    },
    mounted() {
        this.fetchSupplierItems();
    }
}
</script>