<template>
    <div v-for="(product, index) in ordered_products" :key="index" class="mx-10 grid grid-cols-4 gap-3 mb-7 border-b-2 ">
        <figure class="rounded-[10px]">
            <el-image style="height: 100%; border-radius: 10px;"
                :src="product.catalog_product_company.catalog_product.media[0]?.original_url" fit="contain">
                <template #error>
                    <div class="flex justify-center items-center text-[#ababab]">
                        <i class="fa-solid fa-image text-6xl"></i>
                    </div>
                </template>
            </el-image>
        </figure>
        <div class="col-span-3">
            <div class="flex justify-between font-bold uppercase">
                <h2>{{ product.catalog_product_company.catalog_product.name }}</h2>
                <span>{{ product.catalog_product_company.catalog_product.part_number }}</span>
            </div>
            <div class="mt-2 text-sm flex justify-between">
                <p class="text-primary">OCE: <span class="text-black ml-3">{{ product.sale.oce ?? 'No especificado' }}</span></p>
                <p class="text-primary">Cliente: <span class="text-black ml-3">{{ product.sale.company_branch.name }}</span></p>
            </div>
            <div class="mt-2 text-sm flex justify-between">
                <p class="text-primary">Solicitado por: <span class="text-black ml-3">{{ product.sale.user.name }}</span></p>
                <p class="text-primary">Solicitado el: <span class="text-black ml-3">{{ product.sale.created_at.split('T')[0] }}</span></p>
            </div>
            <div class="mt-2 text-sm">
                <p class="text-primary">Unidades ordenadas: <span class="text-black ml-3">{{ product.quantity }}</span></p>
            </div>
            <div class="mt-2 text-sm">
                <p class="text-primary">Operadores asignados:
                    <p v-for="task in product.productions" :key="task.id" class="text-black ml-3 text-xs flex items-center">
                        -{{ task.operator.name }} <i class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Estimado de {{ task.estimated_time_hours }}h {{ task.estimated_time_minutes }}m <i class="fa-solid fa-circle text-[3px] mx-2"></i>
                        Tareas {{ task.tasks }}
                    </p>
                </p>
            </div>
            <div class="mt-2 text-sm">
                <p class="text-primary">Paquetería:
                    <span class="text-black ml-3">
                        {{ product.sale?.shipping_company }}
                    </span>
                </p>
            </div>
            <div class="mt-2 text-sm">
                <p class="text-primary">Notas:
                    <span class="text-black ml-3">
                        {{ product.notes }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</template>
<script>
export default {

    props: {
        ordered_products: Array,
    }
}
</script>
