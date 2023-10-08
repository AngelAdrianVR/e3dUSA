<template>
  <div class="rounded-md shadow-md shadow-gray-400/100 py-3 px-4 relative">
        <i v-if="oportunityTask?.finished_at" class="fa-solid fa-check absolute text-lg text-green-500 top-3 right-4"></i>
          <p>{{ oportunityTask?.name }}</p>
          <p class="text-gray-400 mt-3 mb-2">Asignado a</p>
          <figure>
              <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm rounded-full items-center">
                <img class="h-11 w-11 rounded-full object-cover" :src="oportunityTask?.asigned?.profile_photo_url" :alt="oportunityTask?.asigned?.name" />
                <p class="ml-3">{{ oportunityTask?.asigned?.name }}</p>
              </div>
          </figure>
          <div class="flex items-center justify-between mt-3">
            <p class="bg-[#F6F89E] rounded-full px-3 text-sm">{{ oportunityTask?.time }}</p>
            <p class="text-xs text-gray-400">{{ '5' }}<i class="fa-regular fa-comments text-lg rounded-full ml-2"></i></p>
          </div>
       </div>
</template>

<script>
export default {
data(){
    return{

        }
},
components:{

},
props:{
    oportunityTask: Object,
},
methods:{

},

}
</script>

<style>

</style>