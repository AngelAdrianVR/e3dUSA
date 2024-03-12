<template>
    <Head :title="'HV-' + String(cpcs.id).padStart(5, '0')" />
    <Loading v-if="loading" class="my-36" />
    <div v-else class="text-[11px]">
        <header class="bg-[#373737] h-24 flex justify-between items-center pl-5 pr-20 text-white">
            <ApplicationLogo class="h-auto w-1/6 inline-block" />
            <h1 class="flex flex-col items-center text-sm">
                <span>Hoja viajera</span>
                <b>llaveros</b>
            </h1>
            <div class="flex flex-col w-1/3">
                <div class="flex justify-between border-b border-white">
                    <span>Código:</span>
                    <span class="text-center">{{'HV-' + String(cpcs.id).padStart(5, '0')}}</span>
                </div>
                <div class="flex justify-between">
                    <span>No. de revisión:</span>
                    <span class="text-center">01</span>
                </div>
                <div class="flex justify-between">
                    <span>Fecha de elaboración:</span>
                    <span class="text-center">{{ dateFormat(createdAt) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Vigencia:</span>
                    <span class="text-center">{{ dateFormat(new Date(createdAt.getFullYear() + 2, createdAt.getMonth(),
                        createdAt.getDate())) }}</span>
                </div>
            </div>
        </header>
        <main class="my-5">
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3 mx-5 relative">
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 1</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de almacén de llaveros</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha de entrega</td>
                            <td v-if="$page.props.auth.user.permissions.includes('Editar hojas viajeras')">
                                <input @keydown.enter="storeInputValues()" v-model="travelerData[4].date" type="text"
                                    class="bg-transparent border-none focus:ring-0 h-4 py-2 text-[10px] w-full lg:w-3/4">
                                <el-tooltip placement="right"
                                    content="Este campo se puede editar. Escribe y Presionar enter para guardar">
                                    <i
                                        class="fa-solid fa-circle-info w-1/4 pr-3 text-gray-500 text-right text-[9px] hidden lg:inline"></i>
                                </el-tooltip>
                            </td>
                            <td v-else>
                                <p>{{ travelerData[4].date }}</p>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de llaveros</td>
                            <td>
                                {{ getFirstStageProductions.keyChains[0]?.pivot.quantity * (cpcs.quantity + 5) }} unidades
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones</td>
                            <td>
                                {{ getFirstStageProductions.medallions[0]?.pivot.quantity * (cpcs.quantity + 5) }} unidades
                            </td>
                        </tr>   
                        <tr class="*:border *:border-gray-400">
                            <td>Entregó</td>
                            <td v-if="$page.props.auth.user.permissions.includes('Editar hojas viajeras')">
                                <input @keydown.enter="storeInputValues()" v-model="travelerData[4].supplier" type="text"
                                    class="bg-transparent border-none focus:ring-0 h-4 py-2 text-[10px] w-full lg:w-3/4">
                                <el-tooltip placement="right"
                                    content="Este campo se puede editar. Escribe y Presionar enter para guardar">
                                    <i
                                        class="fa-solid fa-circle-info w-1/4 pr-3 text-gray-500 text-right text-[9px] hidden lg:inline"></i>
                                </el-tooltip>
                            </td>
                            <td v-else>
                                <p>{{ travelerData[4].supplier }}</p>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Recibió</td>
                            <td v-if="$page.props.auth.user.permissions.includes('Editar hojas viajeras')">
                                <input @keydown.enter="storeInputValues()" v-model="travelerData[4].receiver" type="text"
                                    class="bg-transparent border-none focus:ring-0 h-4 py-2 text-[10px] w-full lg:w-3/4">
                                <el-tooltip placement="right"
                                    content="Este campo se puede editar. Escribe y Presionar enter para guardar">
                                    <i
                                        class="fa-solid fa-circle-info w-1/4 pr-3 text-gray-500 text-right text-[9px] hidden lg:inline"></i>
                                </el-tooltip>
                            </td>
                            <td v-else>
                                <p>{{ travelerData[4].receiver }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de almacén de emblemas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha de entrega</td>
                            <td v-if="$page.props.auth.user.permissions.includes('Editar hojas viajeras')">
                                <input @keydown.enter="storeInputValues()" v-model="travelerData[5].date" type="text"
                                    class="bg-transparent border-none focus:ring-0 h-4 py-2 text-[10px] w-full lg:w-3/4">
                                <el-tooltip placement="right"
                                    content="Este campo se puede editar. Escribe y Presionar enter para guardar">
                                    <i
                                        class="fa-solid fa-circle-info w-1/4 pr-3 text-gray-500 text-right text-[9px] hidden lg:inline"></i>
                                </el-tooltip>
                            </td>
                            <td v-else>
                                <p>{{ travelerData[5].date }}</p>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Logo / Diseño de emblema:</td>
                            <td>
                                {{ getFirstStageProductions.emblems.length ? getFirstStageProductions.emblems[0].name : '-'
                                }}
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de emblemas</td>
                            <td>
                                {{ getFirstStageProductions.emblems.length ?
                                    getFirstStageProductions.emblems[0]?.pivot.quantity * (cpcs.quantity + 5) + ' unidades' :
                                    '-'
                                }}
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Entregó</td>
                            <td v-if="$page.props.auth.user.permissions.includes('Editar hojas viajeras')">
                                <input @keydown.enter="storeInputValues()" v-model="travelerData[5].supplier" type="text"
                                    class="bg-transparent border-none focus:ring-0 h-4 py-2 text-[10px] w-full lg:w-3/4">
                                <el-tooltip placement="right"
                                    content="Este campo se puede editar. Escribe y Presionar enter para guardar">
                                    <i
                                        class="fa-solid fa-circle-info w-1/4 pr-3 text-gray-500 text-right text-[9px] hidden lg:inline"></i>
                                </el-tooltip>
                            </td>
                            <td v-else>
                                <p>{{ travelerData[5].supplier }}</p>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Recibió</td>
                            <td v-if="$page.props.auth.user.permissions.includes('Editar hojas viajeras')">
                                <input @keydown.enter="storeInputValues()" v-model="travelerData[5].receiver" type="text"
                                    class="bg-transparent border-none focus:ring-0 h-4 py-2 text-[10px] w-full lg:w-3/4">
                                <el-tooltip placement="right"
                                    content="Este campo se puede editar. Escribe y Presionar enter para guardar">
                                    <i
                                        class="fa-solid fa-circle-info w-1/4 pr-3 text-gray-500 text-right text-[9px] hidden lg:inline"></i>
                                </el-tooltip>
                            </td>
                            <td v-else>
                                <p>{{ travelerData[5].receiver }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Criterio de aceptación</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[0]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(0, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[0][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[0][index].userId)?.name }}<br />
                                            {{ travelerData[0][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[0][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(0, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[0][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[0][index].userId)?.name }}<br />
                                            {{ travelerData[0][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[0][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3 px-5 relative">
                <div v-if="!isStageOneCompleted" class="inset-0 absolute bg-gray-200 opacity-60 cursor-not-allowed z-10"></div>
                <p v-if="!isStageOneCompleted" class="z-20 absolute right-5 bottom-8 text-primary font-bold px-2 py-1 bg-primarylight rounded-lg">Se habilitará esta etapa al completar criterios de aceptación de la etapa anterior</p>
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 2</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de grabado láser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Responsable</td>
                            <td>
                                <h2 class="font-bold mb-1">Llaveros:</h2>
                                <ul v-for="(item, index) in getSecondStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ users.find(user => user.id == item.operator_id)?.name }}
                                        </span>
                                    </li>
                                </ul>
                                <h2 class="font-bold py-1">Mini medallones:</h2>
                                <ul v-for="(item, index) in getSecondStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ users.find(user => user.id == item.operator_id)?.name }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Fecha</td>
                            <td>
                                <h2 class="font-bold mb-1">Llaveros:</h2>
                                <ul v-for="(item, index) in getSecondStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.started_at ? dateFormat(new Date(item.started_at)) : '-' }}
                                        </span>
                                    </li>
                                </ul>
                                <h2 class="font-bold py-1">Mini medallones:</h2>
                                <ul v-for="(item, index) in getSecondStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.started_at ? dateFormat(new Date(item.started_at)) : '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de llaveros grabados</td>
                            <td>
                                <ul v-for="(item, index) in getSecondStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.good_units ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td>
                                <ul v-for="(item, index) in getSecondStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.scrap ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones grabados</td>
                            <td>
                                <ul v-for="(item, index) in getSecondStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.good_units ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td>
                                <ul v-for="(item, index) in getSecondStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.scrap ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Motivo de merma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>
                                <h2 class="font-bold mb-1">Llaveros:</h2>
                                <ul v-for="(item, index) in getSecondStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[15%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[85%]">
                                            {{ item.scrap_reason ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                                <h2 class="font-bold py-1">Mini medallones:</h2>
                                <ul v-for="(item, index) in getSecondStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[15%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[85%]">
                                            {{ item.scrap_reason ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Criterio de aceptación</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[1]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(1, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[1][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[1][index].userId)?.name }}<br />
                                            {{ travelerData[1][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[1][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(1, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[1][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[1][index].userId)?.name }}<br />
                                            {{ travelerData[1][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[1][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-3 gap-x-4 gap-y-3 my-3 px-5 relative">
                <div v-if="!isStageTwoCompleted" class="inset-0 absolute bg-gray-200 opacity-60 cursor-not-allowed z-10"></div>
                <p v-if="!isStageTwoCompleted" class="z-20 absolute right-5 bottom-8 text-primary font-bold px-2 py-1 bg-primarylight rounded-lg">Se habilitará esta etapa al completar criterios de aceptación de la etapa anterior</p>
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 3</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de aplicación de emblemas y medallones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td>
                                <h2 class="font-bold mb-1">Llaveros:</h2>
                                <ul v-for="(item, index) in getThirdStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ users.find(user => user.id == item.operator_id)?.name }}
                                        </span>
                                    </li>
                                </ul>
                                <h2 class="font-bold py-1">Mini medallones:</h2>
                                <ul v-for="(item, index) in getThirdStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ users.find(user => user.id == item.operator_id)?.name }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td>
                                <h2 class="font-bold mb-1">Llaveros:</h2>
                                <ul v-for="(item, index) in getThirdStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.started_at ? dateFormat(new Date(item.started_at)) : '-' }}
                                        </span>
                                    </li>
                                </ul>
                                <h2 class="font-bold py-1">Mini medallones:</h2>
                                <ul v-for="(item, index) in getThirdStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.started_at ? dateFormat(new Date(item.started_at)) : '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de emblemas</td>
                            <td>
                                <ul v-for="(item, index) in getThirdStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.good_units ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td>
                                <ul v-for="(item, index) in getThirdStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.scrap ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de mini medallones aplicados</td>
                            <td>
                                <ul v-for="(item, index) in getThirdStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.good_units ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>Número de merma</td>
                            <td>
                                <ul v-for="(item, index) in getThirdStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[30%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[70%]">
                                            {{ item.scrap ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Motivo de merma</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>
                                <h2 class="font-bold mb-1">Llaveros:</h2>
                                <ul v-for="(item, index) in getThirdStageProductions.keyChains" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[15%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[85%]">
                                            {{ item.scrap_reason ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                                <h2 class="font-bold py-1">Mini medallones:</h2>
                                <ul v-for="(item, index) in getThirdStageProductions.medallions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[15%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[85%]">
                                            {{ item.scrap_reason ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Criterio de aceptación</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[2]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(2, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[2][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[2][index].userId)?.name }}<br />
                                            {{ travelerData[2][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[2][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(2, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[2][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[2][index].userId)?.name }}<br />
                                            {{ travelerData[2][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[2][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
            <section class="grid grid-cols-2 gap-x-6 gap-y-3 my-3 px-5 relative">
                <div v-if="!isStageThreeCompleted" class="inset-0 absolute bg-gray-200 opacity-60 cursor-not-allowed z-10"></div>
                <p v-if="!isStageThreeCompleted" class="z-20 absolute right-5 bottom-8 text-primary font-bold px-2 py-1 bg-primarylight rounded-lg">Se habilitará esta etapa al completar criterios de aceptación de la etapa anterior</p>
                <h1 class="col-span-full text-base -rotate-90 absolute top-14 -left-10">Etapa 4</h1>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th colspan="2">Control de empaque</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="*:border *:border-gray-400">
                            <td>Responsable</td>
                            <td>
                                <ul v-for="(item, index) in getFourthStageProductions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[15%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[85%]">
                                            {{ users.find(user => user.id == item.operator_id)?.name }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td class="w-1/2">Fecha</td>
                            <td>
                                <ul v-for="(item, index) in getFourthStageProductions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[15%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span class="text-[10px] w-[85%]">
                                            {{ item.started_at ? dateFormat(new Date(item.started_at)) : '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr class="*:border *:border-gray-400">
                            <td>No. de paquete / Pzs. Por paquete</td>
                            <td>
                                <ul v-for="(item, index) in getFourthStageProductions" :key="index">
                                    <li class="flex justify-between mb-1">
                                        <figure class="w-[20%]">
                                            <img :src="users.find(user => user.id == item.operator_id)?.profile_photo_url"
                                                class="size-5 rounded-full object-cover border border-gray-400">
                                        </figure>
                                        <span v-if="!item.packages" class="text-[10px] w-[80%]">-</span>
                                        <ol class="text-[10px] w-[100%]">
                                            <li v-for="(item2, index2) in item.packages" :key="index2" class="w-full">
                                                <b class="text-primary">• Paquete {{ (index2 + 1) }}:</b> {{ item2.quantity
                                                }} pieza(s).
                                            </li>
                                        </ol>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="self-start">
                    <thead>
                        <tr>
                            <th>Control de logística</th>
                            <th>A</th>
                            <th>R</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in criterias[3]" :key="index" class="*:border *:border-gray-400">
                            <td>{{ item }}</td>
                            <td @click="storeCriteriaValue(3, index, true)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[3][index].value == true" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[3][index].userId)?.name }}<br />
                                            {{ travelerData[3][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[3][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-green-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                            <td @click="storeCriteriaValue(3, index, false)" class="w-[10%] cursor-pointer">
                                <figure v-if="travelerData[3][index]?.value == false" class="size-7 mx-auto">
                                    <el-tooltip placement="left">
                                        <template #content>
                                            {{ users.find(item => item.id == travelerData[3][index].userId)?.name }}<br />
                                            {{ travelerData[3][index].timestamp }}
                                        </template>
                                        <img :src="users.find(item => item.id == travelerData[3][index].userId)?.profile_photo_url"
                                            class="size-7 rounded-full object-cover border border-red-600">
                                    </el-tooltip>
                                </figure>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-[9px] text-right">
                                <span class="mr-3"><b>A=</b> Aceptado</span>
                                <span><b>R=</b> Rechazado</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </section>
        </main>
    </div>
</template>
<script>
import { Head } from "@inertiajs/vue3";
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Loading from '@/Components/MyComponents/Loading.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';

export default {
    data() {
        return {
            loading: true,
            createdAt: new Date(this.cpcs.created_at),
            users: [],
            productions: [],
            rawMaterials: [],
            travelerData: [
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
                [
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                    {
                        userId: null, value: null, timestamp: null,
                    },
                ],
                {
                    date: null, supplier: null, receiver: null,
                },
                {
                    date: null, supplier: null, receiver: null,
                }
            ],
            criterias: [
                [
                    'Llaveros',
                    'Mini medallones',
                    'Emblemas',
                ],
                [
                    'Diseño de grabado correcto',
                    'Definición y calidad de grabado',
                    'Cantidad correcta',
                ],
                [
                    'Posición de emblema correcto',
                    'Logo/Diseño de emblema correcto',
                    'Juego de llavero y mini medallón correcto',
                ],
                [
                    'Producto coincide con orden de pedido',
                    'Se tiene guía de paquetería',
                    'Se tienen documentos para entrega',
                ],
            ],
        }
    },
    components: {
        Head,
        ApplicationLogo,
        Loading,
    },
    props: {
        cpcs: Object,
    },
    computed: {
        isStageOneCompleted() {
            return !this.travelerData[0].some(item => item.value === null);
        },
        isStageTwoCompleted() {
            return !this.travelerData[1].some(item => item.value === null);
        },
        isStageThreeCompleted() {
            return !this.travelerData[2].some(item => item.value === null);
        },
        getFirstStageProductions() {
            const keyChains = this.rawMaterials.filter(item =>
                item.part_number.includes('LL-') && !item.name.toLowerCase().includes('medall'));

            const medallions = this.rawMaterials.filter(item =>
                item.part_number.includes('LL-') && item.name.toLowerCase().includes('medall'));

            const emblems = this.rawMaterials.filter(item =>
                item.part_number.includes('EM-'));

            return { keyChains: keyChains, medallions: medallions, emblems: emblems };
        },
        getSecondStageProductions() {
            const keyChains = this.productions.filter(item =>
                item.tasks.toLowerCase().includes('grabado láser llavero'.toLowerCase()));

            const medallions = this.productions.filter(item =>
                item.tasks.toLowerCase().includes('grabado láser mini medallón'.toLowerCase()));

            return { keyChains: keyChains, medallions: medallions };
        },
        getThirdStageProductions() {
            const keyChains = this.productions.filter(item =>
                item.tasks.toLowerCase().includes('aplicación de emblema a llavero'.toLowerCase()));

            const medallions = this.productions.filter(item =>
                item.tasks.toLowerCase().includes('ensamble de mini medallón a llavero'.toLowerCase()));

            return { keyChains: keyChains, medallions: medallions };
        },
        getFourthStageProductions() {
            return this.productions.filter(item =>
                item.tasks.toLowerCase().includes('empaque'.toLowerCase()));;
        },
    },
    methods: {
        dateFormat(date) {
            const formattedDate = format(date, 'dd-MMMM-yyyy', { locale: es });

            return formattedDate;
        },
        async storeCriteriaValue(stage, CriteriaIndex, value) {
            if (this.$page.props.auth.user.permissions.includes('Editar hojas viajeras')) {
                this.travelerData[stage][CriteriaIndex].userId = this.$page.props.auth.user.id;
                this.travelerData[stage][CriteriaIndex].value = value;
                this.travelerData[stage][CriteriaIndex].timestamp = format(new Date(), 'dd-MMM-yyyy h:mm a', { locale: es });
                const notify = !value;
                try {
                    const response = await axios.post(route('catalog-product-company-sale.store-traveler-data', this.cpcs.id), { traveler_data: this.travelerData, notify: notify });
                    
                    if (response.status === 200 && notify) {
                        this.$notify({
                        title: 'Correcto',
                        message: 'Se ha notificado a varios usuarios de este rechazo',
                        type: 'info',
                    })
                    }
                } catch (error) {
                    console.log(error);
                }
            }
        },
        async storeInputValues() {
            try {
                const response = await axios.post(route('catalog-product-company-sale.store-traveler-data', this.cpcs.id), { traveler_data: this.travelerData });
                if (response.status === 200) {
                    this.$notify({
                        title: 'Correcto',
                        message: 'Se ha guardado el texto',
                        type: 'success',
                    })
                }
            } catch (error) {
                console.log(error);
            }
        },
        async fetchUsers() {
            this.loading = true;
            try {
                const response = await axios.get(route('users.get-all'));

                if (response.status === 200) {
                    this.users = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchTravelerData() {
            this.loading = true;
            try {
                const response = await axios.get(route('catalog-product-company-sale.get-traveler-data', this.cpcs.id));

                if (response.status === 200 && response.data.item != null) {
                    this.travelerData = response.data.item;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchCpcsProductions() {
            this.loading = true;
            try {
                const response = await axios.get(route('catalog-product-company-sale.get-productions', this.cpcs.id));

                if (response.status === 200) {
                    this.productions = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async fetchRawMaterials() {
            this.loading = true;
            try {
                const response = await axios.get(route('catalog-product-company-sale.get-raw-materials', this.cpcs.id));

                if (response.status === 200) {
                    this.rawMaterials = response.data.items;
                }
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
    },
    async mounted() {
        await this.fetchUsers();
        await this.fetchTravelerData();
        await this.fetchCpcsProductions();
        await this.fetchRawMaterials();
    }
}
</script>