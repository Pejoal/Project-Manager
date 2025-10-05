<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  active: {
    type: Boolean,
    default: false,
  },
  isCollapsed: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['toggled']);

const isOpen = ref(props.active);

const toggle = () => {
  if (props.isCollapsed) {
    emit('toggled');
  }
  isOpen.value = !isOpen.value;
};

const buttonClasses = computed(() => [
  'group flex items-center w-full px-3 py-2 text-sm font-medium text-left rounded-md transition-colors duration-200',
  props.active
    ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200'
    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
]);
</script>

<template>
  <div>
    <button @click="toggle" :class="buttonClasses">
      <div class="flex items-center w-full">
        <div class="w-6 h-6 mr-3 flex-shrink-0">
          <slot name="icon" />
        </div>
        <span v-if="!isCollapsed" class="flex-1 truncate">
          {{ title }}
        </span>
        <span v-if="!isCollapsed" class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-180': isOpen }">
          <svg fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
              clip-rule="evenodd"
            ></path>
          </svg>
        </span>
      </div>
    </button>
    <ul v-show="isOpen && !isCollapsed" class="py-2 space-y-2 pl-4 border-l border-gray-200 dark:border-gray-700 ml-3">
      <slot />
    </ul>
  </div>
</template>
