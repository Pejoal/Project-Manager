<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  title: String,
  active: Boolean,
  isCollapsed: Boolean,
});
const emit = defineEmits(['toggled']);


const isOpen = ref(props.active);

const toggle = () => {
  // The dropdown can only be opened/closed when the sidebar is expanded
  if (!props.isCollapsed) {
    isOpen.value = !isOpen.value;
  }
  emit('toggled');
};

// If the route becomes active (e.g. through navigation),
// automatically open the dropdown to show the active link.
watch(
  () => props.active,
  (newVal) => {
    if (newVal) {
      isOpen.value = true;
    }
  }
);
</script>

<template>
  <div>
    <button
      @click="toggle"
      type="button"
      class="flex items-center w-full p-2 text-base font-normal rounded-lg transition-colors duration-200"
      :class="[
        active
          ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100'
          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
        isCollapsed ? 'justify-center' : '',
      ]"
    >
      <span class="w-6 h-6 flex-shrink-0">
        <slot name="icon" />
      </span>
      <span v-show="!isCollapsed" class="flex-1 ml-3 text-left whitespace-nowrap">{{ title }}</span>
      <span v-show="!isCollapsed" class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-180': isOpen }">
        <svg fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd"
          ></path>
        </svg>
      </span>
    </button>
    <ul v-show="isOpen && !isCollapsed" class="py-2 space-y-2 pl-4 border-l border-gray-200 dark:border-gray-700 ml-3">
      <slot />
    </ul>
  </div>
</template>
