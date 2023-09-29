<template>

<div v-if="pagination.meta.links.length > 3" class="overflow-x-auto flex items-center justify-between px-5 my-1 sm:px-6">
  <!-- <div class="flex flex-1 justify-between sm:hidden">
    <a href="#" class="relative inline-flex items-center rounded-md border border-indigo-600 bg-gray-300 px-4 py-2 text-sm font-medium text-black hover:bg-gray-50">Anterior</a>
    <a href="#" class="relative ml-3 inline-flex items-center rounded-md border border-indigo-600 bg-gray-300 px-4 py-2 text-sm font-medium text-black hover:bg-gray-50">Siguiente</a>
  </div> -->
  <div class="flex flex-1 items-center justify-between">
    <div>
      <nav class="isolate inline-flex -space-x-px rounded-lg" aria-label="Pagination">
        
        <template v-for="(link,key) in pagination.meta.links">
            <div :key="key" v-if="link.url == null" class="rounded-sm relative inline-flex items-center border border-gray-300 bg-gray-200 px-2 py-1 text-sm font-medium text-gray-600 hover:bg-gray-50 focus:z-20 " v-html="link.label">

            </div>

            <Link :key="'link-'+ key" 
            v-else 
            :href="link.url" 
            v-html="link.label" 
            class="relative inline-flex items-center border px-3 py-1 text-sm rounded-sm"
            :class="link.active ? 'z-10 bg-blue-100 border-2 border-blue-600 text-blue-600 font-bold' : 'bg-gray-200 border-gray-400 text-black hover:bg-gray-200 hover:border-blue-600'"
            />
            

        </template>

        <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
        <!-- <a href="#" v-html="link.label" class="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20">2</a> -->
       
      </nav>
    </div>
  </div>
</div>

</template>

    
    <script>
    import { Link } from "@inertiajs/vue3";
    
    export default {
        data(){
            return {
    
            }
        },
        props:{
            pagination: Object,
            
        },
        components:{
            Link,
        },
    }
    </script>