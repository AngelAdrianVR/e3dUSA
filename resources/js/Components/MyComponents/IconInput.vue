<template>
  <div class="flex">
    <span
      class="font-bold text-xl inline-flex items-center px-3 text-gray-600 bg-bg-[#CCCCCC] border border-r-8 border-transparent rounded-l-md h-9 w-12 darkk:bg-gray-600 darkk:text-gray-400 darkk:border-gray-600"
    >
      <slot />
    </span>
    <input
      :type="inputType"
      class="input"
      :placeholder="inputPlaceholder"
      :value="modelValue"
      :step="inputStep"
      @input="updateModelValue"
    />
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'CustomComponent',
  props: {
    inputPlaceholder: {
      type: String,
      default: 'Colocar placeholder',
    },
    inputType: {
      type: String,
      default: 'text',
    },
    inputStep: {
      type: Number,
      default: '1',
    },
    modelValue: {
      type: String,
      required: true,
    },

  },
    emits: ['update:modelValue'],
  setup(props, { emit }) {

    const updateModelValue = (event) => {
      emit('update:modelValue', event.target.value);
    };
    

    return {
      updateModelValue,
    };
  },
};
</script>
