<template>
    <section class="p-7 dark:text-white">
        <div v-if="!loading">
            <div v-if="rawMaterials?.length > 0" class="lg:grid grid-cols-4 mt-7 gap-5">
                <SupplierProductCard v-for="product in rawMaterials" :key="product" :product="product" />
            </div>
            <p v-else class="text-gray-500 text-center text-sm">No hay productos registrados a este proveedor</p>
        </div>
        <div v-else class="flex justify-center items-center pt-10">
            <i class="fa-solid fa-spinner fa-spin text-4xl text-primary"></i>
        </div>
    </section>
</template>
<script>
import SupplierProductCard from '@/Components/MyComponents/SupplierProductCard.vue';

export default {
    data() {
        return {
            loading: false,
            rawMaterials: [],
        }
    },
    props: {
        purchase: Object,
    },
    components: {
        SupplierProductCard,
    },
    methods: {
        async fetchSupplierItems() {
            if (!this.rawMaterials.length) { //solo hace la peticion si no se han cargado
                this.loading = true;
                try {
                    const response = await axios.get(route('raw-materials.fetch-supplier-items', {
                        raw_materials_ids: this.purchase.products.map(item => item.id).join(',')
                    }));

                    if (response.status === 200) {
                        this.rawMaterials = response.data.items;
                        //Agrega a los productos la cantidad comprada
                        this.rawMaterials = this.rawMaterials.map((item, index) => {
                            return {
                                ...item,
                                quantity: this.purchase.products[index].quantity,
                                currency: this.purchase.currency,
                            };
                        });
                    }
                } catch (error) {
                    console.log(error);
                } finally {
                    this.loading = false;
                }
            }
        },
    },
    mounted() {
        this.fetchSupplierItems();
    }
}
</script>