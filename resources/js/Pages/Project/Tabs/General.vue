<template>
    <div class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center">
            <p class="text-secondary col-span-2 mb-2">Información del proyecto</p>

            <span class="text-gray-500">Creado por</span>
            <span>{{ project.user?.name }}</span>
            <span class="text-gray-500 my-2">Creado el</span>
            <span>{{ project.created_at }}</span>
            <span class="text-gray-500 my-2">Responsable del proyecto</span>
            <span>{{ project.owner?.name }}</span>
            <span class="text-gray-500 my-2">Fecha de inicio</span>
            <span>{{ project.start_date }}</span>
            <span class="text-gray-500 my-2">Fecha final</span>
            <span>{{ project.limit_date }}</span>

            <div class="flex items-start my-2">
                <span class="text-gray-500">Proyecto estricto</span>
                <el-tooltip
                    content="Las tareas no pueden comenzar ni finalizar fuera de las fechas programadas de un proyecto  "
                    placement="top">
                    <i class="fa-regular fa-circle-question text-primary text-[10px] ml-1"></i>
                </el-tooltip>
            </div>
            <span>
                <i v-if="project.is_strict_project" class="fa-solid fa-check text-green-500"></i>
                <i v-else class="fa-solid fa-minus"></i>
            </span>
            <span class="text-gray-500 my-2">Descripción</span>
            <span v-html="project.description"></span>
            <span class="text-gray-500 my-2">Proyecto interno</span>
            <span>
                <i v-if="project.is_internal_project" class="fa-solid fa-check text-green-500"></i>
                <i v-else class="fa-solid fa-minus"></i>
            </span>
            <span class="text-gray-500 my-2">Grupo</span>
            <span>{{ project.projectGroup.name }}</span>

            <p v-if="!project.is_internal_project" class="text-secondary col-span-2 mb-2 mt-8">
                Campos adicionales
            </p>

            <span v-if="!project.is_internal_project" class="text-gray-500">Cliente</span>
            <span v-if="!project.is_internal_project">
                {{ project.company?.business_name }}
                </span>
            <span v-if="!project.is_internal_project" class="text-gray-500 my-2">Sucursal</span>
            <span v-if="!project.is_internal_project">
                {{ project.companyBranch.name }}
                </span>
            <span v-if="!project.is_internal_project" class="text-gray-500 my-2">Dirección</span>
            <span v-if="!project.is_internal_project">
                {{ project.shipping_address }}</span>

            <span v-if="!project.is_internal_project" class="text-gray-500 my-2">OV</span>
            <Link :href="route('sales.show', project.sale?.id ?? 1)" class="text-secondary underline">
            <span v-if="!project.is_internal_project">{{
                'OV-' + project.sale?.id
            }}</span>
            </Link>
        </div>

        <div class="grid grid-cols-2 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center self-start">
            <p class="text-secondary col-span-2 mb-2">Presupuestos</p>

            <span class="text-gray-500">Moneda</span>
            <span class="mb-6">{{ project.currency }}</span>
            <span class="text-gray-500 mt-2">Monto</span>
            <span>${{ project.budget?.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}</span>

            <p class="text-secondary col-span-2 mb-2 mt-5">Documentos adjuntos</p>
            <li v-for="file in project.media" :key="file" class="flex items-center justify-between col-span-full">
                <a :href="file.original_url" target="_blank" class="flex items-center">
                    <i :class="getFileTypeIcon(file.file_name)"></i>
                    <span class="ml-2">{{ file.file_name }}</span>
                </a>
            </li>

            <p v-if="project.tags.length" class="text-secondary col-span-full mt-7 mb-2">Etiquetas</p>
            <div class="col-span-full flex space-x-3">
                <Tag v-for="(item, index) in project.tags" :key="index" :name="item.name" :color="item.color" />
            </div>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {

        }
    },
    props: {
        project: Object,
    },
    components: {

    },
    methods: {
        getFileTypeIcon(fileName) {
            // Asocia extensiones de archivo a iconos
            const fileExtension = fileName.split('.').pop().toLowerCase();
            switch (fileExtension) {
                case 'pdf':
                    return 'fa-regular fa-file-pdf text-red-700';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'fa-regular fa-image text-blue-300';
                default:
                    return 'fa-regular fa-file-lines';
            }
        },
    },
}
</script>