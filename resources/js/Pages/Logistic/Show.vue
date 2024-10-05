<template>
    <AppLayoutNoHeader title="Logística">
        <main class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
            <div class="flex justify-between mb-5">
                <label class="text-lg">Logística</label>

                <LogisticTimeLine />

                <Link :href="route('logistics.index')"
                    class="cursor-pointer size-7 rounded-full hover:bg-[#D9D9D9] flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </Link>
            </div>

            <div class="flex justify-between">
                <div class="w-1/2 md:w-1/3 mr-2">
                    <el-select @change="$inertia.get(route('logistics.show', logisticSelected))" v-model="logisticSelected" clearable
                    filterable placeholder="Buscar registro de logística" no-data-text="No hay opciones registradas"
                    no-match-text="No se encontraron coincidencias">
                    <el-option v-for="item in logistics" :key="item.id" :label="'EV-' + item.id.toString().padStart(4, '0') + '/ ' + 'OV-' + item.sale.id" :value="item.id" />
                    </el-select>
                </div>
                <div class="flex items-center space-x-2">
                    <PrimaryButton @click="openPrintPage">Confirmar envío completo</PrimaryButton>
                    <!-- <el-tooltip v-if="$page.props.auth.user.permissions.includes('Editar ordenes de venta') ||
                    sale.data.user_id == $page.props.auth.user.id" content="Editar" placement="top">
                    <Link :href="route('logistics.edit', sale.data.id)">
                    <button class="size-9 flex items-center justify-center rounded-lg bg-[#D9D9D9]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>
                    </Link>
                    </el-tooltip> -->
                </div>
            </div>

            <section class="pt-10 md:w-[80%] md:mx-auto border">
                <p>Folio de la orden: <strong class="ml-7">OP-{{ logistic.sale.id.toString().padStart(4, '0') }}</strong></p>

                <!-- informacion de la orden -->
                <div class="border border-[#999999] rounded-xl p-4 mt-5">
                    <h2 class="font-bold">Información de la orden</h2>

                    <section class="grid grid-cols-2">
                        <!-- lado izquierdo del grid -->
                        <article class="mt-4 border-r border-[#999999] space-y-1">
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Solicitada por:</p>
                                <p>{{ logistic.sale.user.name }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Solicitada el:</p>
                                <p>{{ formatDate(logistic.sale.created_at) }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Prioridad:</p>
                                <p :class="logistic.sale.is_high_priority ? 'text-red-600 font-bold' : 'text-black'">{{ logistic.sale.is_high_priority ? 'Urgente' : 'Normal' }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Fecha prevista entrega:</p>
                                <p>{{ formatDate(logistic.sale.promise_date) ?? 'Sin fecha promesa' }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Notas:</p>
                                <p>{{ logistic.sale.notes ?? '-' }}</p>
                            </div>
                        </article>

                        <!-- lado derecho del grid -->
                        <article class="mt-4 space-y-1 ml-5">
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Cliente:</p>
                                <p>{{ logistic.sale.company_branch?.company?.business_name }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Sucursal:</p>
                                <p>{{ logistic.sale.company_branch?.name }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Contacto:</p>
                                <p>{{ logistic.sale.contact.name }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Email:</p>
                                <p>{{ logistic.sale.contact.email }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Teléfono:</p>
                                <p>{{ logistic.sale.contact.phone }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <p class="text-[#999999] w-48">Dirección:</p>
                                <p class="w-full">{{ logistic.sale.company_branch?.address }}</p>
                            </div>
                        </article>
                    </section>
                </div>
            </section>

        </main>
    </AppLayoutNoHeader>
</template>

<script>
import AppLayoutNoHeader from "@/Layouts/AppLayoutNoHeader.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import LogisticTimeLine from "@/Components/MyComponents/LogisticTimeLine.vue";
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
data(){
    return {
        logisticSelected: this.logistic.id
    }
},
components:{
    AppLayoutNoHeader,
    LogisticTimeLine,
    PrimaryButton,
},
props:{
    logistic: Object,
    logistics: Array
},
methods:{
    formatDate(date) {
      const parsedDate = new Date(date);
      return format(parsedDate, 'dd MMMM yyyy', { locale: es }); // Formato personalizado
    },
}
}
</script>
