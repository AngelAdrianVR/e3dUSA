<template>
  <div class="rounded-xl px-7 py-3 relative text-xs shadow-lg bg-[#cccccc]"
    :class="isHighPriority ? 'border-[3px] border-primary' : null">
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
      class="z-20 rounded-[3px] absolute font-bold text-sm bg-primarylight text-primary py-1 px-2 -top-10 -right-4 flex items-center justify-center">
      Diseño nuevo
    </div>
    <!-- selection circle -->
    <div @click="handleSelection" v-if="!is_view_for_seller"
      class="w-5 h-5 border-2 rounded-full absolute top-3 left-3 cursor-pointer flex items-center justify-center"
      :class="selected ? 'border-[#d90537]' : 'border-black'">
      <div class="w-3 h-3 rounded-full" :class="selected ? 'bg-primary' : null"></div>
    </div>

    <!-- card -->
    <p class="text-center font-bold mt-4 text-sm">
      {{ catalog_product_company_sale.catalog_product_company?.catalog_product?.name }}
    </p>
    <span class="font-bold absolute right-5 top-2">{{
      catalog_product_company_sale.catalog_product_company?.catalog_product?.part_number
    }}</span>
    <el-tooltip content="Número de parte" placement="top">
      <i
        class="fa-solid fa-question text-[9px] h-3 w-3 bg-primary-gray rounded-full text-center absolute right-2 top-1"></i>
    </el-tooltip>

    <div class="grid grid-cols-2 gap-x-4">
      <div>
        <figure @mouseover="showOverlay" @mouseleave="hideOverlay" class="bg-[#D9D9D9] w-full h-28 my-3 rounded-[10px] relative">
          <img class="object-contain h-28 mx-auto" :src="catalog_product_company_sale.catalog_product_company?.catalog_product?.media[currentImage]?.original_url" alt="">
          <div v-if="imageHovered" @click="openImage(catalog_product_company_sale.catalog_product_company?.catalog_product?.media[currentImage]?.original_url)"
              class="cursor-pointer h-full w-full absolute top-0 left-0 opacity-50 bg-black flex items-center justify-center rounded-lg transition-all duration-300 ease-in">
              <i class="fa-solid fa-magnifying-glass-plus text-white text-4xl"></i>
          </div>
        </figure>
        <div v-if="catalog_product_company_sale.catalog_product_company?.catalog_product?.media?.length > 1" class="my-3 flex items-center justify-center space-x-3">
            <i @click="currentImage = index" v-for="(image, index) in catalog_product_company_sale.catalog_product_company?.catalog_product?.media?.length" :key="index" 
            :class="index == currentImage ? 'text-black' : 'text-white'" 
            class="fa-solid fa-circle text-[7px] cursor-pointer"></i>
        </div>
      </div>

      <div class="flex flex-col space-y-3">
        <div>
          <p class="text-primary text-left">Caracteristicas</p>
          <li
            v-for="( feature, index ) in  catalog_product_company_sale.catalog_product_company?.catalog_product?.features"
            :key="index" class="text-gray-800 list-disc">{{ feature }}</li>
        </div>

        <!-- Partes que componen el producto  -->
        <div>
          <p class="text-primary text-left">Componentes</p>
          <p v-for="( raw_material, index ) in  catalog_product_company_sale.catalog_product_company?.catalog_product?.raw_materials "
            :key="index" class="text-secondary text-[11px] underline cursor-pointer uppercase">
          <p @click.stop="$inertia.get(route('storages.show', comp_storage.id))"
            v-for="comp_storage in raw_material.storages" :key="comp_storage">•{{ comp_storage.storageable.name }}</p>
          </p>
          <p v-if="catalog_product_company_sale.requires_medallion" class="text-[#37951F] bg-[#ADFEB5] px-1 py-px rounded-[3px] text-center mt-1">Requiere medallón</p>
        </div>
      </div>
    </div>

    <div class="border-b-2 border-[#9a9a9a] pb-1">
      <p class="text-black flex justify-between">
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
      <span class="text-black">{{
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
      <strong class="ml-4 text-black">{{
        (catalog_product_company_sale.quantity -
          catalog_product_company_sale.finished_product_used).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")
      }}</strong>
      </p>
      <p class="text-black flex justify-between">
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
      <span class="text-black">{{
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
        <el-tooltip v-if="getNextAction(production) == 'Finalizar'"
          :content="production.is_paused ? 'Reanudar producción' : 'Pausar producción'" placement="top">
          <button @click="pauseProduction(production)" v-if="production.operator_id == $page.props.auth.user.id"
            class="bg-secondary size-4 rounded-full text-[7px] text-white disabled:opacity-25 disabled:cursor-not-allowed">
            <i v-if="production.is_paused" class="fa-solid fa-play"></i>
            <i v-else class="fa-solid fa-pause"></i>
          </button>
        </el-tooltip>
        <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5"
          :title="getNextAction(production) == 'Finalizar' ? 'Finalizar producción' : 'Iniciar producción'"
          @confirm="confirmedChangeStatus(production)">
          <template #reference>
            <button v-if="production.operator_id == $page.props.auth.user.id"
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
    <div class="bg-[#d9d9d9] rounded-lg p-2 grid grid-cols-2 my-3">
      <span class="">Precio Anterior:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company?.old_price }}
        {{ catalog_product_company_sale.catalog_product_company?.old_currency }}</span>
      <span>Establecido el:</span>
      <span class="text-secondary  mb-3">
        {{ formatDate(catalog_product_company_sale.catalog_product_company?.old_date) }}
      </span>

      <span>Precio Actual:</span>
      <span class="text-secondary ">{{ catalog_product_company_sale.catalog_product_company?.new_price }}
        {{ catalog_product_company_sale.catalog_product_company?.new_currency }}</span>
      <span>Establecido el:</span>
      <span class="text-secondary ">{{ formatDate(catalog_product_company_sale.catalog_product_company?.new_date) }}</span>
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
      </el-tooltip> -->
      <!-- <el-popconfirm confirm-button-text="Si" cancel-button-text="No" icon-color="#0355B5" title="¿Continuar?"
        @confirm="getNextAction() == 'Finalizar' ? showScrapModal = true : changeTaskStatus()">
        <template #reference>
          <button
            v-if="catalog_product_company_sale.productions.some(item => item.operator_id == $page.props.auth.user.id)"
            :disabled="getNextAction() == 'Finalizado'"
            class="bg-primary px-2 rounded-md text-white disabled:opacity-25 disabled:cursor-not-allowed">
            {{ getNextAction() }}
          </button>
        </template>
      </el-popconfirm> -->
    </div>
  </div>

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
          <template v-for="( production, index ) in  catalog_product_company_sale.productions " :key="index">
            <tr v-for=" progress  in  production.progress " :key="progress.id">
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
            Es importante que seas honesto con la cantidad de merma porque se notifica a jefe de producción y a dirección.
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
        <div v-for="quality in qualities.data" :key="quality" class="grid grid-cols-2 my-3 border border-[#9A9A9A] rounded-md p-4">
          <p>Nombre del supervisor:</p>
          <p>{{ quality.supervisor.name }}</p>
          <p>Número de inspección:</p>
          <p @click="$inertia.get(route('qualities.show', quality.id))" class="cursor-pointer text-secondary">{{ quality.id }}</p>
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
            • Paquete {{ (index + 1) }}: {{ item.large + 'cm largo, ' + item.width + 'cm ancho, ' + item.height + 'cm alto. ' + item.weight + ' Kg.' + item.quantity + ' Pzs.' }}
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
          <RichText @submitComment="storeComment(taskComponentLocal)" @content="updateComment($event)" ref="commentEditor"
            class="flex-1" withFooter :userList="users" :disabled="sendingComments" />
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
        del tiempo real de trabajo registrado. Esto nos permite obtener datos exactos sobre la duración de la producción.
      </p>
    </template>
    <template #footer>
      <PrimaryButton @click="showInfoModal = false">Entendido</PrimaryButton>
    </template>
  </DialogModal>
</template>

<script>
import axios from 'axios';
import moment from "moment";
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import CancelButton from "@/Components/MyComponents/CancelButton.vue";
import IconInput from "@/Components/MyComponents/IconInput.vue";
import InputError from "@/Components/InputError.vue";
import RichText from "@/Components/MyComponents/RichText.vue";
import Checkbox from "@/Components/Checkbox.vue";
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

    return {
      form,
      imageHovered: false, //imagen de tarjeta
      currentImage: 0, //imagen de tarjeta
      selected: false,
      showProgressModal: false,
      showInfoModal: false,
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
      users: [],
      // paquetes
      packages: [],
      package: {
        large: null,
        width: null,
        height: null,
        weight: null,
        quantity: null,
      }
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
    DialogModal,
    PrimaryButton,
    SecondaryButton,
    CancelButton,
    IconInput,
    InputError,
    RichText,
    Checkbox
  },
  methods: {
    formatDate(date) {
            const parsedDate = new Date(date);
            return format(parsedDate, 'dd \'de\' MMM, Y', { locale: es }); // Formato personalizado
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
  mounted() {
    this.fetchUsers();
  }
};
</script>