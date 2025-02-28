<template>
    <div class="w-11/12 mx-auto my-8">
      <table v-if="opportunity.survey" class="lg:w-[80%] w-full mx-auto text-sm">
        <thead>
          <tr class="text-center">
            <th class="font-bold pb-5">
              ID <i class="fa-solid fa-arrow-down-long ml-3"></i>
            </th>
            <el-tooltip
              content="En una escala del 0 al 10, ¿qué tan satisfecho/a estás con la calidad de nuestros productos?"
              placement="top">
              <th class="font-bold pb-5">
                P1 <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
            </el-tooltip>
            <el-tooltip content="¿Nuestros productos cumplieron con tus expectativas?" placement="top">
              <th class="font-bold pb-5">
                P2 <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
            </el-tooltip>
            <el-tooltip content="¿Consideras que nuestro equipo de trabajo fue profesional y cortés?" placement="top">
              <th class="font-bold pb-5">
                P3 <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
            </el-tooltip>
            <el-tooltip content="¿Recomendarías nuestros productos a otros?" placement="top">
              <th class="font-bold pb-5">
                P4 <i class="fa-solid fa-arrow-down-long ml-3"></i>
              </th>
            </el-tooltip>
            <th class="font-bold pb-5">Comentario</th>
          </tr>
        </thead>
        <tbody>
          <tr class="mb-4 hover:bg-[#dfdbdba8]">
            <td class="text-center py-2 px-2 rounded-l-full">
              {{ opportunity.survey?.oportunity_id }}
            </td>
            <td class="text-center py-2 px-2">
              <span class="py-1 px-4 rounded-full">{{ opportunity.survey?.p1 }}</span>
            </td>
            <td class="text-center py-2 px-2">
              <span class="py-1 px-2 rounded-full">{{ opportunity.survey?.p2 }}</span>
            </td>
            <td class="text-center py-2 px-2">
              {{ opportunity.survey?.p3 }}
            </td>
            <td class="text-center py-2 px-2">
              {{ opportunity.survey?.p4 }}
            </td>
            <td class="text-center py-2 px-2 rounded-r-full">
              {{ opportunity.survey?.p5 }}
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else>
        <p class="text-sm text-center text-gray-400">No se ha contestado la encuesta</p>
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
        opportunity: Object,
    },
    components: {
    },
    methods: {
    },
}
</script>