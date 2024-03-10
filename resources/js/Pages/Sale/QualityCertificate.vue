<template>
  <div class="relative lg:py-2 lg:px-12 p-3 overflow-auto text-sm h-screen">
    <div class="w-72 md:w-96 mx-auto">
      <ApplicationLogo />
    </div>
    <figure class="hidden lg:block absolute right-7 top-0">
      <img class="h-3/4" src="../../../../public/images/lineaEmblems.png" alt="" />
    </figure>
    <figure class="absolute left-0 bottom-0">
      <img class="w-1/2" src="../../../../public/images/lineasEmblems.png" alt="" />
    </figure>

    <!-- Contenido del certificado -->
    <section class="lg:px-28 mt-10">
      <!-- Header  -->
      <h1 class="text-right font-bold mb-2">CERTIFICADO DE CALIDAD</h1>
      <div class="border-b border-black mb-3"></div>
      <div class="border-b border-black mb-1"></div>
      <p class="text-right font-bold mb-1">Folio: {{ sale.data.folio }}</p>
      <p class="text-right font-bold mb-2">{{ sale.data.created_at.split(' ').slice(0, 3).join(' ') }}</p>

      <!-- Body -->
      <div class="lg:w-1/2 grid grid-cols-2">
        <!-- lado izquierdo -->
        <div class="font-bold">
          <p>Cliente:</p>
          <p>Ordern de compra:</p>
          <p>Producto:</p>
          <p>Descripción:</p>
          <p>Número de factura:</p>
          <p>Cantidad:</p>
        </div>
        <!-- lado derecho -->
        <div>
            <p>{{ sale.data.company_branch?.name }}</p>
            <p>{{ sale.data.folio }}</p>
            <p>
                {{ sale.data.catalogProductCompanySales
                    .map(item => item?.catalog_product_company?.catalog_product?.name)
                    .filter(name => name)
                    .join(', ')
                }}
            </p>
            <p>
                {{ sale.data.catalogProductCompanySales
                    .map(item => item?.catalog_product_company?.catalog_product?.description)
                    .filter(description => description)
                    .join(', ')
                }}
            </p>
            <p>{{ 'Numero de factura' }}</p>
            <p>
                {{ sale.data.catalogProductCompanySales
                    .map(item => item?.quantity)
                    .filter(quantity => quantity)
                    .join(', ')
                }} PIEZAS
            </p>
        </div>
      </div>

      <p class="mt-7">
        Este producto ha sido manufacturado bajo lineamientos de un sistema de gestión de
        calidad de <strong>Emblems 3D USA S.A. de C.V.</strong> y certificado bajo el mismo; cumpliendo con
        cada una de las especificaciones requeridas para su uso.
      </p>
      <p class="mt-3">INDICACIONES DE ALMACENAMIENTO:</p>
      <ul class="*:ml-4">
        <li>• Apilar <strong class="text-primary">verticalmente</strong> los paquetes en un máximo de cuatro cajas.</li>
        <li>• No colocar objetos pesados sobre las plantillas de emblemas.</li>
        <li>• 1 año de estabilidad en almacén bajo las siguientes condiciones:</li>
        <li class="ml-3">- Temperatura entre 15 y 30 °C </li>
        <li class="ml-3">- 50% humedad relativa </li>
      </ul>

      <p class="mt-3">INDICACIONES DE APLICACIÓN:</p>
      <ul class="*:ml-4">
        <li>• <strong class="text-primary">Limpie y desengrase la superficie donde desea aplicar el emblema.</strong> Asegúrese que ésta se encuentre a una temperatura no menor a 10 °C.</li>
        <li>• <strong class="text-primary">Desprenda la película plástica del respaldo y maneje siempre el emblema por los bordes. No toque el adhesivo con los dedos o este podría perder fuerza de adherencia.</strong></li>
        <li>• Aplique el emblema cuidadosamente sobre la superficie y presione sobre toda la pieza sacando el aire atrapado. </li>
      </ul>

      <p class="mt-7 mb-9">Vo.Bo</p>
      <span class="border-t border-black pt-1">Ing. Gustavo Gómez. Encargado de Producción</span>
    </section>

    <figure class="mt-5">
        <img class="lg:w-[60%] mx-auto" src="../../../../public/images/contacto.png" alt="">
    </figure>

    <!-- footer -->
    <footer class="lg:flex items-center border-t-4 border-black mt-4 py-2 lg:px-4 text-base">
        <p class="text-gray-400 w-11/12">Emblemas de alta calidad. Somos los mejores fabricantes ramo automotriz, electrodomésticos, electrónica, mueble y juguetes.
            En división automotriz somos fabricantesespecialistas en emblemas cromados, porta placas, llaveros, porta documentos, placas de estireno y tapetes de hule.
        </p>
        <figure>
            <img class="mx-auto m-3 lg:m-0 w-[30%]" src="../../../../public/images/qrPagina.png" alt="">
        </figure>
        <figure>
            <img class="mx-auto m-3 lg:m-0 w-1/2 lg:w-full" src="../../../../public/images/escudoSherman.png" alt="">
        </figure>
    </footer>
  </div>
</template>

<script>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

export default {
  data() {
    return {};
  },
  components: {
    ApplicationLogo,
  },
  props: {
    sale: Object
  },
  methods: {},
};
</script>
