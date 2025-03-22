<template>
    <label class="customSwitch">
        <input type="checkbox" :checked="modelValue" @change="toggleSwitch">
        <span class="slider"></span>
    </label>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    modelValue: Boolean, // Se enlaza con v-model
});

const emit = defineEmits(["update:modelValue"]);

const toggleSwitch = () => {
    const newValue = !props.modelValue;
    emit("update:modelValue", newValue);
    document.documentElement.classList.toggle("dark", newValue); // Aplica o quita la clase 'dark'
    localStorage.setItem("darkMode", newValue); // Guarda preferencia en localStorage
};
</script>

<style>
    /* The customSwitch - the box around the slider */
.customSwitch {
  display: block;
  --width-of-customSwitch: 3.5em;
  --height-of-customSwitch: 2em;
  /* size of sliding icon -- sun and moon */
  --size-of-icon: 1.4em;
  /* it is like a inline-padding of customSwitch */
  --slider-offset: 0.3em;
  position: relative;
  width: var(--width-of-customSwitch);
  height: var(--height-of-customSwitch);
}

/* Hide default HTML checkbox */
.customSwitch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #e2e2e4;
  transition: .4s;
  border-radius: 30px;
}

.slider:before {
  position: absolute;
  content: "";
  height: var(--size-of-icon,1.4em);
  width: var(--size-of-icon,1.4em);
  border-radius: 20px;
  left: var(--slider-offset,0.3em);
  top: 50%;
  transform: translateY(-50%);
  background: linear-gradient(40deg,#ff0080,#ff8c00 70%);
  ;
 transition: .4s;
}

input:checked + .slider {
  background-color: #303136;
}

input:checked + .slider:before {
  left: calc(100% - (var(--size-of-icon,1.4em) + var(--slider-offset,0.3em)));
  background: #303136;
  /* change the value of second inset in box-shadow to change the angle and direction of the moon  */
  box-shadow: inset -3px -2px 5px -2px #8983f7, inset -10px -4px 0 0 #a3dafb;
}
</style>