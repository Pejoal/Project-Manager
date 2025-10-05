<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  href: {
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

const linkClasses = computed(() => [
  'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
  props.active
    ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200'
    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
]);
</script>

<template>
  <Link :href="href" :class="linkClasses">
    <div class="flex items-center w-full">
      <div class="w-6 h-6 mr-3 flex-shrink-0">
        <slot name="icon" />
      </div>
      <span v-if="!isCollapsed" class="truncate">
        <slot />
      </span>
    </div>
  </Link>
</template>
