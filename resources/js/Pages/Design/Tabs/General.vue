<template>
    <div class="md:grid grid-cols-2 border-b-2 border-[#cccccc] text-sm">
        <div class="grid grid-cols-2 gap-3 text-left p-4 md:ml-10 border-r-2 border-gray-[#cccccc] items-center self-start">
            <span class="text-gray-500">ID</span>
            <span>{{ design.id }}</span>
            <span class="text-gray-500">Solicitada por</span>
            <span>{{ design.user.name }}</span>
            <span class="text-gray-500">Solicitada el</span>
            <span>{{ design.created_at }}</span>
            <span class="text-gray-500">Autorizado</span>
            <span>{{ design.authorized_user_name }} -
                {{ design.authorized_at }}</span>
            <span class="text-gray-500">Diseñador(a)</span>
            <span>{{ design.designer.name }}</span>
            <span class="text-gray-500">Estimado de entrega</span>
            <span>{{ design.expected_end_at }}</span>
            <span class="text-gray-500">Iniciado el</span>
            <span>{{ design.started_at }}</span>
            <span class="text-gray-500">¿Requiere formato de autorización para cliente?</span>
            <span>{{ design.needs_authorization ? 'Si' : 'No' }}</span>
            <span class="text-gray-500">Estatus</span>
            <span class="rounded-full border text-center" :class="design.status['text-color'] +
                ' ' +
                design.status['border-color']
                ">{{ design.status["label"] }}</span>
            <span v-if="design.has_priority" class="text-gray-500">Prioridad</span>
            <span v-if="design.has_priority" class="text-primary"><i
                    class="fa-solid fa-triangle-exclamation mr-2"></i>{{
                        'Alta' }}</span>
            <span v-if="design.results.length" class="text-gray-500 self-start">Archivos de resultados</span>
            <div v-if="design.results.length" class="flex flex-col">
                <a class="hover:underline text-primary hover:text-secondary" v-for="file in design.results"
                    :key="file.id" :href="file.original_url" target="_blank">• {{ file.file_name }}</a>
            </div>
            <span v-if="design.afImage.length" class="text-gray-500 self-start">Imagen para formato de autorización</span>
            <div v-if="design.afImage.length" class="flex flex-col">
                <a class="hover:underline text-primary hover:text-secondary" v-for="file in design.afImage"
                    :key="file.id" :href="file.original_url" target="_blank">• {{ file.file_name }}</a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-left p-4 md:ml-10 items-center self-start">
            <span class="text-gray-500">Cliente</span>
            <span>{{ design.company_branch_name }}</span>
            <span class="text-gray-500">Contacto</span>
            <span>{{ design.contact_name }}</span>
            <span class="text-gray-500">Nombre del diseño</span>
            <span>{{ design.name }}</span>
            <span class="text-gray-500">Clasificación</span>
            <span>{{ design.design_type.name }}</span>

            <p class="text-secondary col-span-2 mt-7">Especificaciones</p>

            <span class="text-gray-500 self-start">Requerimientos</span>
            <span style="white-space: pre-line;">{{ design.specifications }}</span>

            <span class="text-gray-500">Unidad</span>
            <span>{{ design.measure_unit }}</span>
            <span class="text-gray-500">Dimensiones</span>
            <span>{{ design.dimensions }}</span>
            <span class="text-gray-500">Pantones</span>
            <span>{{ design.pantones }}</span>
            <span class="text-gray-500">Plano</span>
            <a class="text-primary hover:text-secondary hover:underline" target="_blank"
                :href="design.media_plano[0]?.original_url">{{ design.media_plano[0]?.file_name ?? '-' }}</a>
            <span class="text-gray-500">Logo</span>
            <a class="text-primary hover:text-secondary hover:underline" target="_blank"
                :href="design.media_logo[0]?.original_url">{{ design.media_logo[0]?.file_name ?? '-' }}</a>
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
        design: Object,
    },
    components: {

    },
    methods: {
    },
}
</script>