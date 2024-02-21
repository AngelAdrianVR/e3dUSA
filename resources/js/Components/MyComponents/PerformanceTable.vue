<template>
  <div>
    <table class="default border border-[#cccccc] w-full">
      <thead class="border border-[#cccccc]">
        <tr class="border border-[#cccccc] p-3">
          <th></th>

          <th class="border border-[#cccccc] p-3 text-secondary font-bold" colspan="2">Personal</th>

          <th class="border border-[#cccccc] p-3 text-secondary font-bold" colspan="2">Producción</th>
        </tr>

        <tr class="border border-[#cccccc] p-3">
          <th class="border border-[#cccccc] p-3 text-secondary font-bold" scope="col">Día</th>

          <th class="border border-[#cccccc] p-3 text-secondary font-bold">Retardos</th>

          <th class="border border-[#cccccc] p-3 text-secondary font-bold">Puntualidad</th>

          <th class="border border-[#cccccc] p-3 text-secondary font-bold">Tiempo</th>

          <th class="border border-[#cccccc] p-3 text-secondary font-bold">Eficiencia</th>

          <th class="border border-[#cccccc] p-3 text-secondary font-bold" scope="col">Puntos totales</th>
        </tr>
      </thead>

      <tbody>
        <tr class="border border-[#cccccc] p-3 text-center">
          <td class="border border-[#cccccc] p-3">Vie, 09 Junio</td>

          <td class="border border-[#cccccc] p-3">25</td>

          <td class="border border-[#cccccc] p-3">0</td>

          <td class="border border-[#cccccc] p-3">325 min</td>

          <td class="border border-[#cccccc] p-3">50</td>

          <td class="border border-[#cccccc] p-3">50</td>
        </tr>


      </tbody>

      <tfoot>
        <tr class="p-3 text-center">
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td class="p-3 font-bold">Semana x</td>

          <td class=" p-3 font-bold">50</td>

        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {

    }
  },
  props: {

  },
  components: {

  },
  methods: {

  }
}
</script>