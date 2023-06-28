<template>
   <div>
   <AppLayout title="Clientes">
      <template #header>
        <div class="flex justify-between">
          <div class="flex items-center space-x-2">
            <Link
              :href="route('companies.index')"
              class="hover:bg-gray-100/50 rounded-full w-10 h-10 flex justify-center items-center"
            >
              <i class="fa-solid fa-chevron-left"></i>
            </Link>
            <h2 class="font-semibold text-xl leading-tight">
             Clientes
            </h2>
          </div>
        </div>
      </template>

         <div class="flex flex-col md:mx-9 md:my-7 space-y-3 m-1">
        <div>
          <label>Cliente</label>
        </div>
        <div class="flex justify-between">
          <el-select
            v-model="company_selected_id"
            multiple
            filterable
            allow-create
            default-first-option
            :reserve-keyword="false"
            placeholder="Buscar cliente (escribe la razon social o RFC)"
          >
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            />
          </el-select>
          <div class="flex">
            <i class="fa-solid fa-pencil mr-3 rounded-lg text-gray-800 bg-[#cccccc] p-2 cursor-pointer"></i>
            <div class="flex items-center">
              <span class="rounded-lg text-gray-800 bg-[#cccccc] p-2 cursor-pointer text-sm">Más <i class="fa-solid fa-angle-down text-xs"></i></span>
            </div>
          </div>
        </div>
      </div>
      <p class="text-center font-bold text-lg mb-4">{{ company.business_name }}</p>


       <!-- ------------- tabs section starts ------------- -->
      <div
        class="border-y-2 border-[#cccccc] flex justify-between items-center py-2"
      >
        <div class="flex">
          <p
            @click="tabs = 1"
            :class="
              tabs == 1 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="h-10 p-2 cursor-pointer ml-5 transition duration-300 ease-in-out"
          >
            Información general
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="tabs = 2"
            :class="
              tabs == 2 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out"
          >
            Sucursales
          </p>
          <div class="border-r-2 border-[#cccccc] h-10 ml-3"></div>
          <p
            @click="tabs = 3"
            :class="
              tabs == 3 ? 'bg-secondary-gray rounded-xl text-primary' : ''
            "
            class="ml-3 h-10 p-2 cursor-pointer transition duration-300 ease-in-out"
          >
            Matriz
          </p>
        </div> 
      </div>
      <!-- ------------- tabs section ends ------------- -->


      <!-- ------------- Informacion general Starts 1 ------------- -->
      <div v-if="tabs == 1" class="md:w-1/2 grid grid-cols-2 text-left p-4 md:ml-20">
            <span class="text-gray-500">ID</span>
            <span>{{company.id}}</span>
            
            <span class="col-span-2 mt-8 mb-2">Datos fiscales</span>

              <span class="text-gray-500 my-2">Razón social</span>
              <span>{{company.business_name}}</span>
              <span class="text-gray-500 my-2">RFC</span>
              <span>{{company.rfc}}</span>
              <span class="text-gray-500 my-2">Código postal</span>
              <span>{{company.post_code}}</span>
              <span class="text-gray-500 my-2">Dirección</span>
              <span>{{company.fiscal_address}}</span>
      </div>
      <!-- ------------- Informacion general ends 1 ------------- -->


      <!-- -------------Sucursales starts 2 ------------- -->
      <div  v-if="tabs == 2" class="lg:grid grid-cols-2 gap-8 md:mt-12 md:px-14">
        <CompanyBranchCard :company_branches="company.company_branches" />
      </div>

      <!-- ------------- Sucursales ends 2 ------------- -->


      <!-- -------------Matriz starts 3 ------------- -->
      <div  v-if="tabs == 3" class="p-7 ">
        <p class="text-secondary ">Productos registrados</p>
        <div>
          <CompanyProductCard v-for="company_product in company_products" :key="company_product.id" :company_product="company_product" />
        </div>
      </div>

      <!-- ------------- Matriz ends 3 ------------- -->

</AppLayout>
   </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import CompanyBranchCard from "@/Components/MyComponents/CompanyBranchCard.vue";
import CompanyProductCard from "@/Components/MyComponents/CompanyProductCard.vue";


export default {
    data(){
      return{
         company_selected_id: null,
         tabs: 1,
      }
    },
    props:{
      company: Object,
      company_products: Array
    },
    components:{
      AppLayout,
      CompanyBranchCard,
      CompanyProductCard,
    },
    methods:{

    },
}
</script>
<style>
    
</style>