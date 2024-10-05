<template>
    <div class="flex items-center justify-center">
        <div v-for="(step, index) in steps" :key="index" class="flex items-center w-32">
            <div class="flex flex-col justify-center items-center relative">
                <div class="flex items-center justify-center size-8 border rounded-full"
                    :class="{
                    'border-green-500': step.completed,
                    'border-gray-400': !step.completed,
                    'text-green-500': step.completed,
                    'text-gray-800': !step.completed
                    }"
                >
                    <template v-if="step.completed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <template v-else>
                    {{ index + 1 }}
                    </template>
                </div>
                
                <div class="text-center mt-2 text-sm absolute -bottom-5 w-36">
                    <span class="block mt-1">{{ step.label }}</span>
                </div>
            </div>
            <div v-if="index !== steps.length - 1" class="w-full h-[2px] mb-1" 
                :class="{
                'bg-green-500': step.completed && index !== steps.length - 1,
                'bg-gray-500': !step.completed || index === steps.length - 2
                }"
            ></div>
        </div>

    </div>
</template>

<script>
export default {
  data() {
    return {
      steps: [
        { label: "Produc. terminada", completed: true },
        { label: "Empaquetado", completed: true },
        { label: "Enviado", completed: false }
      ]
    };
  }
};
</script>

