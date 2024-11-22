<template>
  <div
    class="rounded-xl px-7 py-3 relative text-xs shadow-lg bg-[#cccccc] dark:bg-[#202020] dark:text-white border-[3px] border-transparent"
    :class="{
      '!border-primary': isHighPriority,
    }">
    <!-- low stock message -->
    <div v-if="catalog_product_company_sale.productions.some(item => item.has_low_stock)"
      class="z-20 rounded-md absolute border-2 border-gray-100 bg-[#FDB9C9] py-2 text-primary px-2 -top-3 -right-5 flex items-center justify-center">
      <i class="fa-solid fa-triangle-exclamation mr-2"></i>
      No hay suficiente materia prima para continuar
    </div>
    <!-- high priority message -->
    <div v-if="isHighPriority"
      class="z-20 rounded-[3px] absolute font-bold text-sm border border-gray1 py-1 px-2 -top-10 right-24 flex items-center justify-center">
      <i class="fa-solid fa-exclamation mr-2 text-primary"></i>
      Prioridad alta
    </div>
    <!-- is new design message -->
    <div v-if="catalog_product_company_sale.is_new_design"
      class="z-20 rounded-[3px] absolute font-bold text-sm bg-blue-100 text-blue-500 py-1 px-2 -top-10 -right-4 flex items-center justify-center">
      Diseño nuevo
    </div>
    <!-- selection circle -->
    <div @click="handleSelection" v-if="!is_view_for_seller"
      class="w-5 h-5 border-2 rounded-full absolute top-3 left-3 cursor-pointer flex items-center justify-center"
      :class="selected ? 'border-[#d90537]' : 'border-black'">
      <div class="w-3 h-3 rounded-full" :class="selected ? 'bg-primary' : null"></div>
    </div>

    <!-- card -->
    <p class="text-center font-bold mt-4 text-sm min-h-12">
      {{ catalog_product_company_sale.catalog_product_company?.catalog_product?.name }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{
      catalog_product_company_sale.catalog_product_company?.catalog_product?.part_number
    }}</span>
    <el-tooltip content="Número de parte" placement="top">
      <i
        class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"></i>
    </el-tooltip>

    <div class="grid grid-cols-2 gap-x-4 ">
      <div>
        <figure @mouseover="showOverlay" @mouseleave="hideOverlay"
          class="bg-[#D9D9D9] dark:bg-[#333333] w-full h-28 my-3 rounded-[10px] relative">
          <div v-if="catalog_product_company_sale.confusion_alert" class="absolute flex size-7 cursor-default">
            <span
              class="animate-ping absolute -top-2 -left-2 inline-flex h-full w-full rounded-full bg-gray-700 opacity-75"></span>
            <el-tooltip placement="bottom">
              <template #content>
                <div class="w-48 text-center">
                  <h1 class="font-bold text-white mb-2">Riesgo de confusión</h1>
                  <p>
                    ¡Atención! Este producto es similar a otros y puede causar confusión. <br>
                    Verifica cuidadosamente antes de continuar para evitar errores
                  </p>
                </div>
              </template>
              <div
                class="bg-black rounded-full size-7 absolute -top-2 -left-2 flex items-center justify-center text-lg z-10">
                <span class="text-xs">❗❗</span>
              </div>
            </el-tooltip>
          </div>
          <img class="object-contain w-full h-28 mx-auto"
            :src="catalog_product_company_sale.catalog_product_company?.catalog_product?.media[currentImage]?.original_url"
            alt="">
          <div v-if="imageHovered"
            @click="openImage(catalog_product_company_sale.catalog_product_company?.catalog_product?.media[currentImage]?.original_url)"
            class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
            <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
          </div>
        </figure>
        <div v-if="catalog_product_company_sale.catalog_product_company?.catalog_product?.media?.length > 1"
          class="my-3 flex items-center justify-center space-x-3">
          <i @click="currentImage = index"
            v-for="(image, index) in catalog_product_company_sale.catalog_product_company?.catalog_product?.media?.length"
            :key="index" :class="index == currentImage ? 'text-black' : 'text-white'"
            class="fa-solid fa-circle text-[7px] cursor-pointer"></i>
        </div>
      </div>

      <div class="flex flex-col space-y-3">
        <div>
          <p class="text-primary text-left">Caracteristicas</p>
          <li
            v-for="( feature, index ) in catalog_product_company_sale.catalog_product_company?.catalog_product?.features"
            :key="index" class="text-gray-800 list-disc">{{ feature }}</li>
        </div>

        <!-- Partes que componen el producto  -->
        <div>
          <p class="text-primary text-left">Componentes</p>
          <p v-for="( raw_material, index ) in catalog_product_company_sale.catalog_product_company?.catalog_product?.raw_materials "
            :key="index" class="text-secondary text-[11px] underline cursor-pointer uppercase">
          <p @click.stop="$inertia.get(route('storages.show', comp_storage.id))"
            v-for="comp_storage in raw_material.storages" :key="comp_storage">•{{ comp_storage.storageable.name }}</p>
          </p>
          <el-tag class="mt-2" type="success" v-if="catalog_product_company_sale.requires_medallion">Requiere
            medallón</el-tag>
          <!-- <p v-if="catalog_product_company_sale.requires_medallion" class="text-[#37951F] bg-[#ADFEB5] px-1 rounded-[3px] text-center mt-1">Requiere medallón</p> -->
        </div>

        <!-- boton para ver empaque -->
        <div v-if="loadingShippingRate" class="flex justify-center">
          <i class="fa-solid fa-spinner fa-spin text-xl text-primary"></i>
        </div>
        <button v-else @click="showPackageModal = true"
          class="rounded-md w-ful flex items-center justify-center bg-black text-white py-1 px-3 self-start">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
          </svg>
          <span class="ml-2">Ver empaque</span>
        </button>
      </div>
    </div>

    <div class="border-b-2 border-[#9a9a9a] pb-1">
      <p class="text-black dark:text-white flex justify-between">
      <div class="flex items-center space-x-1">
        <span>Unidades de stock usadas</span>
        <el-tooltip placement="top">
          <template #content>
            <p>
              Cantidad actual de productos listos para su <br>
              envío en el almacén de producto terminado.
            </p>
          </template>
          <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center ml-2">
            <i class="fa-solid fa-info text-primary text-[7px]"></i>
          </div>
        </el-tooltip>
      </div>
      <span class="">{{
        catalog_product_company_sale.finished_product_used.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      }}</span></p>
      <p class="text-primary flex items-center justify-between">
      <div class="flex items-center space-x-1">
        <span>Unidades a producir</span>
        <el-tooltip placement="top">
          <template #content>
            <p>
              Cantidad adicional de productos que deben ser fabricados <br>
              para satisfacer la demanda total.
            </p>
          </template>
          <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center ml-2">
            <i class="fa-solid fa-info text-primary text-[7px]"></i>
          </div>
        </el-tooltip>
      </div>
      <strong class="ml-4 ">{{
        (catalog_product_company_sale.quantity -
          catalog_product_company_sale.finished_product_used).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      }}</strong>
      </p>
      <p class="flex justify-between">
      <div class="flex items-center space-x-1">
        <span>Unidades ordenadas</span>
        <el-tooltip placement="top">
          <template #content>
            <p>
              Cantidad total de productos solicitados.
            </p>
          </template>
          <div class="rounded-full border border-primary w-3 h-3 flex items-center justify-center ml-2">
            <i class="fa-solid fa-info text-primary text-[7px]"></i>
          </div>
        </el-tooltip>
      </div>
      <span>{{
        catalog_product_company_sale.quantity.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      }}</span></p>
    </div>

    <div v-if="!is_view_for_seller" class="border-b-2 border-[#9a9a9a] pb-1 mt-2">
      <div class="flex justify-between items-center">
        <p class="text-primary ">Operadores asignados:</p>
        <button @click="showProgressDetailsModal = true"
          class="bg-primary rounded-full px-1 py-px text-white text-[10px]">Avances</button>
      </div>
      <p v-for="production in catalog_product_company_sale.productions" :key="production.id"
        class="mt-1 flex justify-between items-center">
        <span :class="{
          'text-green-600': $page.props.auth.user.id == production.operator.id,
          'text-yellow-600': production.is_paused,
        }
          ">-{{ production.operator.name }} {{ production.is_paused ? ' (pausado)' : '' }}
        </span>
      <div class="flex items-center space-x-1">
        <!-- Boton de pausa y play de producción -->
        <el-tooltip v-if="getNextAction(production) == 'Finalizar'"
          :content="production.is_paused ? 'Reanudar producción' : 'Pausar producción'" placement="top">
          <button @click="pauseProduction(production)"
            v-if="production.operator_id == $page.props.auth.user.id || $page.props.auth.user.permissions.includes('Cambiar estatus a produccion')"
            class="bg-secondary size-4 rounded-full text-[7px] text-white disabled:opacity-25 disabled:cursor-not-allowed">
            <i v-if="production.is_paused" class="fa-solid fa-play"></i>
            <i v-else class="fa-solid fa-pause"></i>
          </button>
        </el-tooltip>
        <!-- Boton de finalizar y comenzar producción -->
        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
          :title="getNextAction(production) == 'Finalizar' ? 'Finalizar producción' : 'Iniciar producción'"
          @confirm="confirmedChangeStatus(production)">
          <template #reference>
            <button
              v-if="production.operator_id == $page.props.auth.user.id || $page.props.auth.user.permissions.includes('Cambiar estatus a produccion')"
              :disabled="getNextAction(production) == 'Finalizado'"
              class="bg-primary size-4 rounded-full text-[7px] text-white disabled:opacity-25 disabled:cursor-not-allowed">
              <i v-if="getNextAction(production) == 'Finalizar'" class="fa-solid fa-stop"></i>
              <i v-else class="fa-solid fa-play"></i>
            </button>
          </template>
        </el-popconfirm>
        <el-tooltip placement="right">
          <template #content>
            <p> <strong class="text-yellow-500">Tareas: </strong>{{ production.tasks }}</p>
            <p> <strong class="text-yellow-500">Tiempo estimado: </strong>{{ production.estimated_time_hours }}h {{
              production.estimated_time_minutes }}m</p>
            <p> <strong class="text-yellow-500">Empezado el: </strong>{{ production.started_at ?
              getDateFormtted(production.started_at) : 'Sin iniciar' }}</p>
            <p> <strong class="text-yellow-500">Terminado el: </strong>{{ production.finished_at ?
              getDateFormtted(production.finished_at) : 'Sin terminar' }}</p>
            <p> <strong class="text-red-400">Unidades malas: </strong>{{ production.scrap }}</p>
          </template>
          <i class="fa-solid fa-list-check"></i>
        </el-tooltip>
      </div>

      <!-- pause alert -->
      <div v-if="production.is_paused"
        class="absolute w-[90%] top-10 left-[5%] px-2 py-4 text-primary text-2xl flex items-center bg-[#D9D9D9] rounded-[10px] border-4 border-[#D90537]">
        <p class="mr-3 text-center">Producción Pausada</p>
        <el-tooltip content="Reanudar producción" placement="top">
          <button @click="pauseProduction(production)"
            class="border-2 border-[#D90537] rounded-full w-7 h-7 flex items-center justify-center mr-5">
            <i class="fa-solid fa-play text-sm"></i>
          </button>
        </el-tooltip>
      </div>
      </p>
    </div>
    <div class="flex items-center justify-between mt-2">
      <div
        v-if="catalog_product_company_sale.productions.some(item => item.operator_id == $page.props.auth.user.id) && getOrderStatus() != 'Terminado'">
        <!-- <el-tooltip v-if="!catalog_product_company_sale.productions.find(item => item.operator_id ==
          $page.props.auth.user.id)?.has_low_stock"
          content="Con este botón se indica si no es posible continuar con la producción por materia prima insuficiente"
          placement="top">
          <button @click="toggleStockStatus" class="bg-primary rounded-full px-1 py-px text-white text-[10px]">
            No hay materia prima suficiente
          </button>
        </el-tooltip> -->
        <el-popconfirm v-if="!catalog_product_company_sale.productions.find(item => item.operator_id ==
          $page.props.auth.user.id)?.has_low_stock" confirm-button-text="Si" cancel-button-text="No"
          icon-color="#0355B5" title="Se notificará a compras. ¿Continuar?" @confirm="toggleStockStatus">
          <template #reference>
            <button class="bg-primary rounded-full px-1 py-px text-white text-[10px]">
              No hay materia prima suficiente
            </button>
          </template>
        </el-popconfirm>
        <el-tooltip v-else
          content="Con este botón se indica que ya hay suficiente materia prima para continuar con la producción"
          placement="top">
          <button @click="toggleStockStatus" class="bg-primary rounded-full px-1 py-px text-white text-[10px]">
            Ya hay materia prima suficiente
          </button>
        </el-tooltip>
      </div>
      <button @click="showCommentsModal = true" class="text-gray-500 mx-3">
        {{ catalog_product_company_sale.comments?.length }} <i class="fa-regular fa-comment"></i>
      </button>
    </div>

    <!-- Notas del producto -->
    <div class="bg-[#d9d9d9] dark:bg-[#333333] dark:text-white rounded-lg p-2 my-3 flex items-center justify-between">
      <p class="font-bold flex items-center space-x-2">
        <span>Fecha tentativa de término:</span>
        <i v-if="loadingCompletionDate" class="fa-solid fa-spinner fa-spin text-xl text-primary"></i>
        <span v-else class="font-thin">{{ estimatedCompletionDate }}</span>
      </p>
      <el-tooltip placement="top">
        <template #content>
          <p>
            Esta fecha y hora es el cálculo estimado de finalización <br>
            para todas las tareas de producción registradas. <br>
            Se basa en el tiempo estimado total de las tareas, el <br>
            horario laboral del operador (incluyendo su hora de entrada, <br>
            salida y descansos) y omite los días en que el operador no trabaja.
          </p>
        </template>
        <div class="rounded-full border border-primary size-3 flex items-center justify-center ml-2">
          <i class="fa-solid fa-info text-primary text-[7px]"></i>
        </div>
      </el-tooltip>
    </div>
    <div class="bg-[#d9d9d9] dark:bg-[#333333] dark:text-white rounded-lg p-2 my-3">
      <p class="font-bold flex space-x-2">
        <span>Notas:</span>
        <span class="font-thin">{{ catalog_product_company_sale.notes ?? '-' }}</span>
      </p>
    </div>
    <!-- Historial de precios -->
    <div class="bg-[#d9d9d9] dark:bg-[#191919] dark:text-white rounded-lg p-2 grid grid-cols-2 my-3 relative">
      <span class="">Precio Anterior:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company?.old_price ?? 'N/A' }}
        {{ catalog_product_company_sale.catalog_product_company?.old_currency }}</span>
      <span>Establecido el:</span>
      <span class="text-secondary mb-3">
        {{ formatDate(catalog_product_company_sale.catalog_product_company?.old_date) ?? '-' }}
      </span>

      <span>Precio Actual:</span>
      <p class="text-secondary">
        {{ catalog_product_company_sale.catalog_product_company?.new_price }}
        {{ catalog_product_company_sale.catalog_product_company?.new_currency }}
        <span v-if="priceChangePercentage !== null" :class="priceChangeClass">
          <template v-if="priceChangePercentage !== 0">
            (<i :class="priceChangeIcon" class="text-[10px]"></i>{{ priceChangePercentage }}%)
          </template>
        </span>
      </p>
      <span>Establecido el:</span>
      <span class="text-secondary mb-3">{{ formatDate(catalog_product_company_sale.catalog_product_company?.new_date ??
        '-') }}</span>

      <span>Último cambio de precio:</span>
      <span class="text-secondary">{{ formattedLastUpdate }}</span>

      <!-- boton para cambiar el precio -->
      <button @click="showUpdatePriceModal = true"
        class="rounded-full size-6 bg-gray-400  text-black flex items-center justify-center absolute top-1 right-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="size-4">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
        </svg>
      </button>
    </div><br>

    <div class="flex items-center absolute bottom-3 left-4">
      <span class="text-primary">Status:</span>
      <p class="ml-2 px-2 rounded-full border" :class="{
        'border-[#0355B5] text-secondary': getOrderStatus() == 'En proceso',
        'border-green-600 text-green-600': getOrderStatus() == 'Terminado',
        'border-[#9a9a9a] text-[#9a9a9a]': getOrderStatus() == 'Sin iniciar',
      }
        ">{{ getOrderStatus() }}</p>
    </div>

    <div class="absolute bottom-3 right-4">
      <!-- <el-tooltip
        :content="catalog_product_company_sale.productions.find(item => item.operator_id == $page.props.auth.user.id)?.is_paused ? 'Reanudar producción' : 'Pausar producción'"
        placement="top">
        <button @click="pauseProduction"
          v-if="catalog_product_company_sale.productions.some(item => item.operator_id == $page.props.auth.user.id) && getNextAction() == 'Finalizar'"
          class="border-[#D90537] border-2 w-5 h-5 text-[9px] mr-2 rounded-full text-primary disabled:opacity-25 disabled:cursor-not-allowed">
          <i v-if="catalog_product_company_sale.productions.find(item => item.operator_id == $page.props.auth.user.id)?.is_paused"
            class="fa-solid fa-play"></i>
          <i v-else class="fa-solid fa-pause"></i>
        </button>
      </el-tooltip>
      <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
        @confirm="getNextAction() == 'Finalizar' ? showScrapModal = true : changeTaskStatus()">
        <template #reference>
          <button
            v-if="catalog_product_company_sale.productions.some(item => item.operator_id == $page.props.auth.user.id) || true"
            :disabled="getNextAction() == 'Finalizado'"
            class="bg-primary px-2 rounded-md text-white disabled:opacity-25 disabled:cursor-not-allowed">
            {{ getNextAction() }}
          </button>
        </template>
      </el-popconfirm> -->
    </div>
  </div>

  <!-- modal para actualizar precio de producto -->
  <DialogModal :show="showUpdatePriceModal" @close="showUpdatePriceModal = false" maxWidth="lg">
    <template #title>
      <h1>Actualizar precio</h1>
    </template>
    <template #content>
      <section class="grid grid-cols-2 gap-3">
        <div>
          <InputLabel value="Precio nuevo*" />
          <el-input v-model="priceForm.new_price" type="text"
            :formatter="(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
            :parser="(value) => value.replace(/[^\d.]/g, '')" placeholder="Ej. 30.90" />
          <InputError :message="priceForm.errors.new_price" />
        </div>
        <div>
          <InputLabel value="Moneda*" />
          <el-select v-model="priceForm.new_currency" placeholder="Seleccionar" :fit-input-width="true">
            <el-option v-for="item in currencies" :key="item.value" :label="item.label" :value="item.value">
              <span style="float: left">{{ item.label }}</span>
              <span style="float: right; color: #cccccc; font-size: 13px">{{ item.value }}</span>
            </el-option>
          </el-select>
          <InputError :message="priceForm.errors.new_currency" />
        </div>
        <p v-if="priceForm.new_price && (priceForm.new_price - catalog_product_company_sale.catalog_product_company?.new_price) < (catalog_product_company_sale.catalog_product_company?.new_price * 0.04)"
          class="text-xs text-red-600 col-span-full">El incremento de precio no debe ser menor al 4% del precio actual
        </p>
      </section>
    </template>
    <template #footer>
      <div class="flex justify-end space-x-1">
        <CancelButton @click="showUpdatePriceModal = false" :disabled="form.processing">Cancelar</CancelButton>
        <PrimaryButton @click="updatePrice"
          :disabled="form.processing
            || !priceForm.new_price
            || (priceForm.new_price - catalog_product_company_sale.catalog_product_company?.new_price) < (catalog_product_company_sale.catalog_product_company?.new_price * 0.04)">
          Actualizar precio</PrimaryButton>
      </div>
    </template>
  </DialogModal>

  <DialogModal :show="showProgressModal" @close="showProgressModal = false">
    <template #title>
      <h1>Pausar producción</h1>
    </template>
    <template #content>
      <form @submit.prevent="storeProgress()" ref="myForm">
        <div>
          <IconInput v-model="form.task" inputPlaceholder="Tarea en proceso *" inputType="text">
            <el-tooltip content="Ingreasa la tarea que esta en proceso (empaque, grabado, serigrafía, etc.)"
              placement="top">
              <i class="fa-solid fa-thumbtack"></i>
            </el-tooltip>
          </IconInput>
          <InputError :message="form.errors.task" />
        </div>
        <div class="mt-2">
          <IconInput v-model="form.progress" inputPlaceholder="Ingreasa la cantidad de piezas que llevas al momento *"
            inputType="number">
            <el-tooltip content="Ingreasa la cantidad de piezas que llevas al momento (progreso)" placement="top">
              <i class="fa-solid fa-bars-progress"></i>
            </el-tooltip>
          </IconInput>
          <InputError :message="form.errors.progress" />
        </div>
        <div class="mt-2">
          <div class="flex">
            <el-tooltip content="Notas" placement="top">
              <span
                class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
                <i class="fa-solid fa-grip-lines"></i>
              </span>
            </el-tooltip>
            <textarea v-model="form.notes" class="textarea mb-1" autocomplete="off" placeholder="Notas"></textarea>
          </div>
          <InputError :message="form.errors.notes" />
        </div>
      </form>
    </template>
    <template #footer>
      <CancelButton @click="showProgressModal = false; form.reset();" :disabled="form.processing">
        Cancelar</CancelButton>
      <PrimaryButton @click="submitForm" :disabled="form.processing">Pausar</PrimaryButton>
    </template>
  </DialogModal>

  <!-- progress details modal -->
  <DialogModal :show="showProgressDetailsModal" @close="showProgressDetailsModal = false">
    <template #title>
      <h1>Avances registrados</h1>
    </template>
    <template #content>
      <table class="w-full">
        <thead class="text-left">
          <tr>
            <el-tooltip content="Año-Mes-Día" placement="top">
              <th>Fecha</th>
            </el-tooltip>
            <th>Colaborador</th>
            <th>Tarea</th>
            <th>Progreso</th>
            <th>Notas</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="( production, index ) in catalog_product_company_sale.productions " :key="index">
            <tr v-for=" progress in production.progress " :key="progress.id">
              <td>{{ progress.created_at.split('T')[0] }} a las {{ progress.created_at.split('T')[1].split('.')[0] }}
              </td>
              <td>{{ production.operator.name }}</td>
              <td>{{ progress.task }}</td>
              <td>{{ progress.progress }} unidades</td>
              <td>{{ progress.notes ?? '-' }}</td>
            </tr>
          </template>
        </tbody>
      </table>
    </template>
    <template #footer>
      <CancelButton @click="showProgressDetailsModal = false">Cerrar</CancelButton>
    </template>
  </DialogModal>

  <!-- scrap modal -->
  <DialogModal :show="showScrapModal" @close="showScrapModal = false">
    <template #title>
      <h1>Finalización de tarea</h1>
    </template>
    <template #content>
      <el-radio-group v-model="isProduction" class="ml-4">
        <el-radio label="1" size="medium">Es producción</el-radio>
        <el-radio label="2" size="medium">Es empaque</el-radio>
      </el-radio-group>
      <section v-if="isProduction == '1'">
        <div class="border border-[#0355B5] rounded-lg px-4 py-2 mt-5 mb-3 mx-7 relative">
          <p class="text-secondary text-xs">
            Es importante que seas honesto con la cantidad de merma porque se notifica a jefe de producción y a
            dirección.
            La cantidad de merma que debes de ingresar son piezas malas por un error de tu parte en culquier proceso de
            producción,
            no son las piezas que venian con defecto de fabricación. Las piezas con defecto de fábrica, regresarlas al
            encargado de almacén
            para que se den de baja del sistema y agregar al almacén de merma.
          </p>
        </div>
        <div>
          <IconInput v-model="goodUnits" inputPlaceholder="Piezas buenas realizadas *" inputType="number" class="w-1/2">
            <el-tooltip content="Ingreasa la cantidad de piezas buenas que realizaste *" placement="top">
              <i class="fa-regular fa-square-check"></i>
            </el-tooltip>
          </IconInput>
        </div>
        <div>
          <IconInput v-model="scrap" inputPlaceholder="Piezas malas *" inputType="number" class="w-1/2">
            <el-tooltip content="Ingreasa la cantidad de piezas malas *" placement="top">
              <i class="fa-solid fa-prescription-bottle-medical"></i>
            </el-tooltip>
          </IconInput>
        </div>
        <div v-if="scrap > 0" class="flex">
          <el-tooltip content="Motivo de merma *" placement="top">
            <span
              class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600">
              <i class="fa-solid fa-grip-lines"></i>
            </span>
          </el-tooltip>
          <textarea v-model="reason" class="textarea mb-1" autocomplete="off"
            placeholder="Motivo. Ejemplo: Al grabar los medallones, moví el escantillón por accidente"></textarea>
        </div>
        <p class="font-bold text-lg my-2">Menciona si fuiste supervisado durante la producción de este producto.</p>
        <p class="text-secondary">Supervisiones a esta OP</p>
        <!-- Informacion de upervisión. -->
        <div v-for="quality in qualities.data" :key="quality"
          class="grid grid-cols-2 my-3 border border-[#9A9A9A] rounded-md p-4">
          <p>Nombre del supervisor:</p>
          <p>{{ quality.supervisor.name }}</p>
          <p>Número de inspección:</p>
          <p @click="$inertia.get(route('qualities.show', quality.id))" class="cursor-pointer text-secondary">{{
            quality.id }}</p>
          <p>Fecha y hora:</p>
          <p>{{ quality.created_at }}</p>
        </div>
        <div class="block my-4">
          <label class="flex items-center">
            <Checkbox v-model:checked="supervision" name="remember" class="bg-transparent" />
            <span class="ml-2 text-sm text-[#9A9A9A]">Fuí supervisado</span>
          </label>
        </div>
      </section>
      <section v-else class="lg:grid grid-cols-3 gap-2 mt-3">
        <div class="border border-[#0355B5] rounded-lg px-4 py-2 mt-5 mb-3 mx-7 relative col-span-full">
          <p class="text-secondary text-xs">
            Ingresa los datos de cada paquete que hayas realizado y agregalos a la lista.
          </p>
        </div>
        <div>
          <label class="block">Largo de empaque (en centímetros) *</label>
          <el-input-number v-model="package.large" :min="1" />
        </div>
        <div>
          <label class="block">Ancho de empaque (en centímetros) *</label>
          <el-input-number v-model="package.width" :min="1" />
        </div>
        <div>
          <label class="block">Alto de empaque (en centímetros) *</label>
          <el-input-number v-model="package.height" :min="1" />
        </div>
        <div>
          <label class="block">Peso de empaque (en kilogramos) *</label>
          <el-input-number v-model="package.weight" :min="1" />
        </div>
        <div>
          <label class="block">Piezas en empaque *</label>
          <el-input-number v-model="package.quantity" :min="1" />
        </div>
        <div class="col-span-full">
          <SecondaryButton @click="addPackage()"
            :disabled="!package.large || !package.width || !package.height || !package.quantity || !package.weight">
            Agregar paquete a lista</SecondaryButton>
        </div>
        <p class="col-span-full border-t border-gray-400 pt-2">Lista de paquetes o empaques</p>
        <ul class="col-span-full">
          <li v-for="(item, index) in packages" :key="index" class="flex items-center justify-between">
            • Paquete {{ (index + 1) }}:
            {{ item.large + 'cm largo, ' + item.width + 'cm ancho, '
              + item.height + 'cm alto. ' + item.weight + ' Kg.' + item.quantity + ' Pzs.' }}
            <button @click="removePackage(index)" class="size-5 rounded-full hover:text-red-500">x</button>
          </li>
        </ul>
      </section>
    </template>
    <template #footer>
      <CancelButton @click="showScrapModal = false">Cerrar</CancelButton>
      <PrimaryButton v-if="isProduction == '1'" @click="changeTaskStatus(production)"
        :disabled="!goodUnits || !scrap || (scrap > 0 && !reason)">Finalizar
        producción
      </PrimaryButton>
      <PrimaryButton v-else @click="changeTaskStatus(production)" :disabled="!packages.length">Finalizar
        producción
      </PrimaryButton>
    </template>
  </DialogModal>

  <!-- comments modal -->
  <DialogModal :show="showCommentsModal" @close="showCommentsModal = false">
    <template #title>
      <h1>Comentarios</h1>
    </template>
    <template #content>
      <div>
        <figure class="flex space-x-2 mt-4" v-for="comment in catalog_product_company_sale.comments" :key="comment">
          <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
            <img class="h-8 w-8 rounded-full object-cover" :src="comment.user?.profile_photo_url"
              :alt="comment.user?.name" />
          </div>
          <div class="text-sm w-full">
            <p class="font-bold">{{ comment.user?.name }}</p>
            <p v-html="comment.body"></p>
          </div>
        </figure>
        <p v-if="!catalog_product_company_sale.comments?.length" class="text-gray-500 text-center pt-10 text-xs">No hay
          comentarios</p>
        <div class="flex space-x-1 mt-32">
          <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full w-10">
            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
              :alt="$page.props.auth.user.name" />
          </div>
          <RichText @submitComment="storeComment(taskComponentLocal)" @content="updateComment($event)"
            ref="commentEditor" class="flex-1" withFooter :userList="users" :disabled="sendingComments" />
        </div>
      </div>
    </template>
    <template #footer>
      <CancelButton @click="showCommentsModal = false">Cerrar</CancelButton>
      <!-- <PrimaryButton @click="changeTaskStatus" :disabled="!scrap">Finalizar producción</PrimaryButton> -->
    </template>
  </DialogModal>

  <!-- info modal -->
  <DialogModal :show="showInfoModal" @close="showInfoModal = false">
    <template #title>
      <h1>Proceso de producción: ¡Pasos importantes!</h1>
    </template>
    <template #content>
      <p>
        <b class="text-primary">No se puede iniciar y finalizar la tarea de inmediato.</b> Por favor, asegúrate de
        completar la actividad
        correspondiente antes de finalizar para garantizar la precisión
        del tiempo real de trabajo registrado. Esto nos permite obtener datos exactos sobre la duración de la
        producción.
      </p>
    </template>
    <template #footer>
      <PrimaryButton @click="showInfoModal = false">Entendido</PrimaryButton>
    </template>
  </DialogModal>

  <!-- empaque modal -->
  <DialogModal :show="showPackageModal" @close="showPackageModal = false" maxWidth="4xl">
    <template #title>
      <h1 class="!text-left mb-4">
        Empaque
        <strong v-if="catalog_product_company_sale.confusion_alert" class="text-primary ml-3">
          {{ '¡El producto tiene riesgo de confusión. Revisar con vendedor antes de empacar!' }}
        </strong>
      </h1>
    </template>
    <template #content>
      <main class="overflow-auto max-h-[550px]">
        <p>
          Empaca el producto en las cajas mostradas a continuación.
        </p>
        <h2 class="my-5 ml-4 font-bold text-lg text-black">{{ salePartialities.length > 1 ? 'Envío en parcialidades' :
          '' }}</h2>
        <section
          v-for="(partiality, index) in salePartialities.filter(item => item.productsSelected.some(product => product.id === catalogProductShippingRates.id))"
          :key="partiality" class="grid grid-cols-2 gap-3 px-4 my-4 py-4 border-b border-gray-500">
          <article class="space-y-1">
            <div
              v-if="salePartialities.filter(item => item.productsSelected.some(product => product.id === catalogProductShippingRates.id))?.length > 1"
              class="flex space-x-2">
              <p class="text-[#373737] font-bold w-24">Parcialidad:</p>
              <p>{{ index + 1 }}</p>
            </div>
            <div class="flex space-x-2">
              <p class="text-[#373737] font-bold w-24">Producto:</p>
              <p>{{ catalog_product_company_sale.catalog_product_company?.catalog_product?.name }}</p>
            </div>
            <div class="flex space-x-2">
              <p class="text-[#373737] font-bold w-24">N. Parte:</p>
              <p>{{ catalog_product_company_sale.catalog_product_company?.catalog_product?.part_number }}</p>
            </div>
            <div class="flex space-x-2">
              <p class="text-[#373737] font-bold w-24">Cantidad:</p>
              <p>{{ partiality.productsSelected.find(item => item.id === catalogProductShippingRates.id)?.quantity }} {{
                partiality.productsSelected.find(item => item.id === catalogProductShippingRates.id)?.quantity == 1 ?
                  'unidad' : 'unidades' }}</p>
            </div>
            <div class="flex space-x-2">
              <p class="text-[#373737] font-bold w-24">Notas:</p>
              <p>{{ partiality.productsSelected.find(item => item.id === catalogProductShippingRates.id)?.notes
                ? '⚠' + partiality.productsSelected.find(item => item.id === catalogProductShippingRates.id).notes
                : '-' }}</p>
            </div>
          </article>
          <article class="flex flex-col items-end justify-end space-y-3">
            <figure class="rounded-xl flex items-center justify-center bg-gray-200 w-3/4 h-28">
              <img v-if="catalog_product_company_sale.catalog_product_company?.catalog_product?.media?.length"
                class="object-contain h-full"
                :src="catalog_product_company_sale.catalog_product_company?.catalog_product?.media[0]?.original_url">
              <i v-else class="fa-regular fa-image text-gray-300 text-5xl"></i>
            </figure>

            <p v-if="shippingInfo?.is_fragile"
              class="inline-flex rounded-md justify-center items-center px-3 py-[2px] bg-[#FDB9C9] text-primary">
              <svg class="mr-2" width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M2.63137 0H0.0145374C0.12422 5.10188 -0.263825 5.06154 0.404279 6.24615C0.961078 5.11539 1.42056 4.46654 2.35299 3.82308C2.1303 3.33846 1.68488 2.90769 1.35079 2.47692C1.87381 1.53081 2.15193 0.986847 2.63137 0Z"
                  fill="#D90537" />
                <path
                  d="M2.18598 2.42308C2.13029 2.36923 3.52224 0 3.52224 0H7.92076C8.0261 1.75289 8.00576 2.78859 7.97644 4.63077C8.03212 6.78462 6.41747 7.91539 4.58012 7.96923V12.7615H7.25263V14H0.571346V12.7615H3.24386V7.96923C2.01354 7.86996 1.50341 7.61407 0.849733 6.89231C1.51786 5.33077 2.18948 4.63672 3.46657 3.98462C2.96487 3.35599 2.24167 2.47692 2.18598 2.42308Z"
                  fill="#D90537" />
              </svg>
              Frágil
            </p>
          </article>
          <section class="flex justify-between items-center col-span-2 mt-3">
            <span>Especificaciones de la(s) caja(s)</span>
            <PrimaryButton v-if="!shippingInfo"
              @click="$inertia.get(route('shipping-rates.create', { catalog_product_id: catalogProductShippingRates.id, route: 'productions.show', idRoute: catalog_product_company_sale.sale_id }))"
              type="button">
              Agregar cajas
            </PrimaryButton>
          </section>

          <section class="col-span-full" v-if="hasShippingInfo(partiality)">
            <p class="mt-2 px-4">Cajas necesarias: <span class="ml-4">{{ shippingInfo[index].boxes.length }}</span></p>
            <div class="overflow-x-auto mt-4">
              <table class="min-w-full">
                <thead class="bg-[#373737] text-white">
                  <tr class="*:px-4 *:py-2 *:text-left">
                    <th>#Caja</th>
                    <th>Tamaño</th>
                    <th>Dimensiones</th>
                    <th>Peso</th>
                    <th>Piezas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(box, index) in shippingInfo[index].boxes" :key="box" class="*:px-4 *:py-2">
                    <td>{{ (index + 1) }}</td>
                    <td>{{ box.name }}</td>
                    <td>{{ box.length + 'x' + box.width + 'x' + box.height }} cm</td>
                    <td>{{ box.weight }} kg</td>
                    <td>{{ box.quantity }} piezas</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </section>

        <!-- Notas del producto -->
        <div v-if="catalog_product_company_sale.notes" class="mt-5 font-bold">
          <p class="text-black">Notas de producto:</p>
          <p class="text-primary">{{ catalog_product_company_sale.notes }}</p>
        </div>
      </main>
    </template>
    <template #footer>
      <div class="flex items-center justify-end w-full">
        <!-- <ThirthButton class="!text-primary !border-primary">Sin stock de cajas</ThirthButton> -->
        <PrimaryButton @click="showPackageModal = false">Entendido</PrimaryButton>
      </div>
    </template>
  </DialogModal>
</template>

<script>
import axios from 'axios';
import moment from "moment";
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ThirthButton from "@/Components/MyComponents/ThirthButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import InputError from "@/Components/InputError.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { formatDistanceToNow } from 'date-fns';
import { differenceInMonths, differenceInDays } from 'date-fns';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { useForm } from "@inertiajs/vue3";

export default {
  data() {
    const form = useForm({
      task: null,
      progress: null,
      notes: null,
      production_id: null,
    });

    const priceForm = useForm({
      new_price: null,
      new_currency: null,
      product_company_id: this.catalog_product_company_sale.catalog_product_company?.id,
    });

    return {
      form,
      priceForm,
      imageHovered: false, //imagen de tarjeta
      currentImage: 0, //imagen de tarjeta
      selected: false,
      showProgressModal: false,
      showUpdatePriceModal: false,
      showInfoModal: false,
      showPackageModal: false,
      showProgressDetailsModal: false,
      showScrapModal: false,
      showCommentsModal: false,
      sendingComments: false,
      isProduction: '1',
      production: null,
      scrap: null,
      reason: null,
      goodUnits: null,
      supervision: false,
      comment: null,
      catalogProductShippingRates: null, //catalog product con tarifas cargadas
      shippingInfo: [], //tarifa seleccionada para ese producto
      loadingShippingRate: false,
      loadingCompletionDate: false,
      users: [],
      // paquetes
      packages: [],
      package: {
        large: null,
        width: null,
        height: null,
        weight: null,
        quantity: null,
      },
      currencies: [
        { value: "$MXN", label: "MXN" },
        { value: "$USD", label: "USD" },
      ],
      estimatedCompletionDate: null,
    };
  },
  emits: ['selected'],
  props: {
    catalog_product_company_sale: Object,
    is_view_for_seller: {
      type: Boolean,
      default: false
    },
    isHighPriority: {
      type: Boolean,
      default: false
    },
    qualities: Object
  },
  components: {
    SecondaryButton,
    PrimaryButton,
    ThirthButton,
    CancelButton,
    DialogModal,
    InputError,
    InputLabel,
    IconInput,
    RichText,
    Checkbox
  },
  methods: {
    formatDate(date) {
      if (date) {
        const parsedDate = new Date(date);
        return format(parsedDate, 'dd \'de\' MMM, Y', { locale: es }); // Formato personalizado
      }
    },
    confirmedChangeStatus(production) {
      if (this.getNextAction(production) == 'Finalizar') {
        this.showScrapModal = true;
        this.production = production;
      } else {
        this.changeTaskStatus(production);
      }
    },
    // paquetes
    addPackage() {
      const pack = { ...this.package };
      this.packages.push(pack);
      this.package = {
        large: null,
        width: null,
        height: null,
        weight: null,
        quantity: null,
      };
    },
    removePackage(index) {
      this.packages.splice(index, 1)
    },
    hasShippingInfo(partiality) {
      // Buscar el producto en partiality.productsSelected
      const productSelected = partiality.productsSelected.find(product => product.id === this.catalogProductShippingRates.id);

      // Si no se encuentra el producto, retornar false
      if (!productSelected) {
        return false;
      }

      // Buscar la información de envío del producto seleccionado en catalogProductShippingRates
      const productSelectedShippingInfo = this.catalogProductShippingRates.shipping_rates.find(item => item.quantity === productSelected.quantity);
      this.shippingInfo.push(productSelectedShippingInfo);

      // Retornar true si existe la información de envío, de lo contrario false
      return !!productSelectedShippingInfo;
    },
    async fetchUsers() {
      try {
        const response = await axios.get(route('users.get-all'));

        if (response.status === 200) {
          this.users = response.data.items;
        }
      } catch (error) {
        console.log(error);
      }
    },
    updateComment(content) {
      this.comment = content;
    },
    async storeComment() {
      const editor = this.$refs.commentEditor;
      if (this.comment) {
        this.sendingComments = true;
        try {
          const response = await axios.post(route("productions.comment", this.catalog_product_company_sale), {
            comment: this.comment,
            mentions: editor.mentions,
          });
          if (response.status === 200) {
            this.catalog_product_company_sale.comments.push(response.data.item);
            this.comment = null;
            editor.clearContent();
          }
        } catch (error) {
          console.log(error);
        } finally {
          this.sendingComments = false;
        }
      }
    },
    submitForm() {
      this.$refs.myForm.dispatchEvent(new Event('submit', { cancelable: true }));
    },
    updatePrice() {
      this.priceForm.put(route('company-branches.update-product-price', this.priceForm.product_company_id), {
        onSuccess: () => {
          this.$notify({
            title: "Éxito",
            message: "Precio actualizado",
            type: "success",
          });
          this.showUpdatePriceModal = false;
          this.priceForm.reset();
        },
      });
    },
    pauseProduction(production) {
      this.form.production_id = production.id;
      if (production.is_paused) {
        this.continueProduction(production);
      } else {
        this.showProgressModal = true;
      }
    },
    async continueProduction(production) {
      try {
        const response = await axios.put(route('productions.continue-production', production.id));

        if (response.status === 200) {
          this.$notify({
            title: 'Éxito',
            message: response.data.message,
            type: 'success'
          });

          // this.catalog_product_company_sale.productions.find(item => item.id == this.form.production_id).is_paused = 0;
          production.is_paused = 0;
        }
      } catch (error) {
        console.log(error);
      }
    },
    storeProgress() {
      this.form.post(route('production-progress.store'), {
        onSuccess: () => {
          this.$notify({
            title: 'Éxito',
            message: 'Se ha pausado la producción',
            type: 'success'
          });

          this.catalog_product_company_sale.productions.find(item => item.id == this.form.production_id).is_paused = 1;
          this.form.reset();
          this.showProgressModal = false;
        }
      });
    },
    handleSelection() {
      this.selected = !this.selected;
      this.$emit('selected', this.selected);
    },
    getOrderStatus() {
      const productions = this.catalog_product_company_sale.productions;
      const allTasksFinished = productions.every((order) => order.finished_at !== null);
      const someTasksStarted = productions.some((order) => order.started_at !== null && order.finished_at === null);
      const allTasksNotStarted = productions.every((order) => order.started_at === null);

      if (!productions.length) return "Sin iniciar";

      if (allTasksFinished) {
        return "Terminado";
      } else if (someTasksStarted) {
        return "En proceso";
      } else if (allTasksNotStarted) {
        return "Sin iniciar";
      } else {
        return "En proceso";
      }
    },
    getNextAction(task) {
      // const task = this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id);
      if (task.finished_at) return 'Finalizado';
      else if (task.started_at) return 'Finalizar';
      else return 'Iniciar';
    },
    getDateFormtted(dateTime) {
      if (!dateTime) return null;
      return moment(dateTime).format("DD MMM YYYY, hh:mmA");
    },
    async toggleStockStatus() {
      try {
        let task = this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id);
        const response = await axios.put(route('productions.change-stock-status', task.id));

        if (response.status === 200) {
          this.catalog_product_company_sale.productions.find(item => item.operator_id == this.$page.props.auth.user.id).has_low_stock = !task.has_low_stock;

          this.$notify({
            title: 'Éxito',
            message: response.data.message,
            type: 'success'
          });
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    async changeTaskStatus(production) {
      try {
        const response = await axios.put(route('productions.change-status', production.id), {
          scrap: this.scrap, reason: this.reason, good_units: this.goodUnits, packages: this.packages, supervision: this.supervision
        });
        let type = 'success';
        let title = 'Éxito';
        if (response.status === 200) {
          if (response.data.item === null) {
            this.showInfoModal = true;
          } else {
            this.fetchEstimatedCompletionDate();
            production.started_at = response.data.item.started_at;
            production.finished_at = response.data.item.finished_at;
            this.production = null;
            this.showScrapModal = false;
            this.scrap = null;
            this.$notify({
              title: title,
              message: response.data.message,
              type: type
            });
          }

        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error'
        });
      }
    },
    async fetchCatalogProuctShippingRates() {
      this.loadingShippingRate = true;
      try {
        const response = await axios.get(route('productions.fetch-catalog-product-shipping-rates', this.catalog_product_company_sale.catalog_product_company?.catalog_product?.id));

        if (response.status === 200) {
          this.catalogProductShippingRates = response.data.item;
          if (this.catalogProductShippingRates?.shipping_rates?.length) {
            // this.shippingInfo = this.catalogProductShippingRates?.shipping_rates.find(item => item.quantity === this.catalog_product_company_sale.quantity);
          }
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchSaleInfo() {
      try {
        const response = await axios.get(route('productions.fetch-sale-info', this.catalog_product_company_sale.sale_id));

        if (response.status === 200) {
          this.salePartialities = response.data.item.partialities;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loadingShippingRate = false;
      }
    },
    async fetchEstimatedCompletionDate() {
      this.loadingCompletionDate = true;
      try {
        const response = await axios.get(route('catalog-product-company-sale.get-estimated-completion-date', this.catalog_product_company_sale.id));

        if (response.status === 200) {
          this.estimatedCompletionDate = response.data.item;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.loadingCompletionDate = false;
      }
    },
    openImage(url) {
      window.open(url, '_blank');
    },
    showOverlay() {
      this.imageHovered = true;
    },
    hideOverlay() {
      this.imageHovered = false;
    },
  },
  computed: {
    formattedLastUpdate() {
        const { new_date, old_date, new_updated_by } = this.catalog_product_company_sale.catalog_product_company;
        const lastDate = new_date || old_date;

        if (!lastDate) {
          return 'No disponible';
        }

        const now = new Date();
        const lastUpdate = new Date(lastDate);

        // Calcula la diferencia en meses y días
        const monthsDifference = differenceInMonths(now, lastUpdate);
        const daysDifference = differenceInDays(now, lastUpdate);

        let timeText = '';

        if (monthsDifference > 0) {
          // Si hay más de 0 meses, muestra solo los meses
          timeText = `hace ${monthsDifference} mes${monthsDifference !== 1 ? 'es' : ''}`;
        } else {
          // Si hay menos de un mes, muestra solo los días
          timeText = `hace ${daysDifference} día${daysDifference !== 1 ? 's' : ''}`;
        }

        // Incluye el usuario que realizó el cambio si existe
        return new_updated_by ? `${timeText} por ${new_updated_by}` : timeText;
      },
    priceChangePercentage() {
      const oldPrice = this.catalog_product_company_sale.catalog_product_company?.old_price;
      const newPrice = this.catalog_product_company_sale.catalog_product_company?.new_price;

      if (oldPrice && newPrice) {
        const percentageChange = ((newPrice - oldPrice) / oldPrice) * 100;
        return percentageChange.toFixed(2);
      }

      return null;
    },
    priceChangeClass() {
      if (this.priceChangePercentage > 0) return 'text-green-700';
      if (this.priceChangePercentage < 0) return 'text-red-700';
      return 'text-gray-600'; // color gris si no hay cambio en el precio
    },
    priceChangeIcon() {
      if (this.priceChangePercentage > 0) return 'fa-solid fa-arrow-up-long';
      if (this.priceChangePercentage < 0) return 'fa-solid fa-arrow-down-long';
      return null; // sin icono si el precio no cambia
    }
  },
  mounted() {
    this.fetchUsers();
    this.fetchCatalogProuctShippingRates(); //recupera las tarifas del catalog_product para no cargarlo desde la vista show
    this.fetchSaleInfo();
    this.fetchEstimatedCompletionDate();
  }
};
</script>