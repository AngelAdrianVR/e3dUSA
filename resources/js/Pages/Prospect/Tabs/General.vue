<template>
    <section class="flex justify-between mt-3 text-sm">
        <div class="w-[45%] grid grid-cols-3 gap-x-6 gap-y-2">
            <span class="text-[#9a9a9a]">ID</span>
            <span class="col-span-2">{{ String(prospect.id).padStart(2, '0') }}</span>
            <span class="text-[#9a9a9a]">Creado por</span>
            <span class="col-span-2">{{ prospect.user.name }}</span>
            <span class="text-[#9a9a9a]">Creado el</span>
            <span class="col-span-2">{{ dateFormat(prospect.created_at) }}</span>
            <span class="text-[#9a9a9a]">Nombre de la empresa</span>
            <span class="col-span-2">{{ prospect.name }}</span>
            <span class="text-[#9a9a9a]">Domicilio</span>
            <span class="col-span-2">{{ prospect.address }} - {{ prospect.state }}</span>
            <span class="text-[#9a9a9a]">Nombre del contacto</span>
            <span class="col-span-2">{{ prospect.contact_name }}</span>
            <span class="text-[#9a9a9a]">Puesto</span>
            <span class="col-span-2">{{ prospect.contact_charge }}</span>
            <span class="text-[#9a9a9a]">Correo electrónico</span>
            <span class="col-span-2">{{ prospect.contact_email }}</span>
            <span class="text-[#9a9a9a]">Teléfono</span>
            <p>
                {{ prospect.contact_phone }}
                <span v-if="prospect.contact_phone_extension">
                    <span class="text-[#9a9a9a] mx-2">ext.</span> {{ prospect.contact_phone_extension }}
                </span>
            </p>
        </div>
        <div class="w-px border border-[#9a9a9a]"></div>
        <div class="w-[45%] grid grid-cols-3 gap-x-6 gap-y-2 self-start">
            <span class="text-[#9a9a9a]">Estatus</span>
            <el-tooltip :content="statuses.find(item => item.label == prospect.status).tooltip" placement="top">
                <span class="px-2 py-1 rounded-full justify-self-start col-span-2" :style="{
                    color: statuses.find(item => item.label == prospect.status).color,
                    backgroundColor: statuses.find(item => item.label == prospect.status).bg
                }">{{ prospect.status }}</span>
            </el-tooltip>
            <span class="text-[#9a9a9a]">Número de sucursales</span>
            <span class="col-span-2">{{ prospect.branches_number }}</span>
            <span class="text-[#9a9a9a]">Resumen</span>
            <span class="col-span-2">{{ prospect.abstract }}</span>
        </div>
    </section>
</template>
<script>
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
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
    methods: {
        dateFormat(date) {
            const formattedDate = format(new Date(date), 'dd MMM yyyy', { locale: es });

            return formattedDate;
        },
    },
    props: {
        prospect: Object,
    }
}
</script>