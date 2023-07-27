<template>
  <div class="flex">
    <span
      class="font-bold text-[16px] inline-flex items-center text-gray-600 border border-r-8 border-transparent rounded-l-md h-9"
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
