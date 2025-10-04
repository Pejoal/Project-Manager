<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  href: String,
  active: Boolean,
  isCollapsed: Boolean,
});
</script>

<template>
  <Link
    :href="href"
    class="flex items-center p-2 text-base font-normal rounded-lg transition-colors duration-200"
    :class="[
      active
        ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white'
        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
      isCollapsed ? 'justify-center' : '',
    ]"
  >
    <span class="w-6 h-6 flex-shrink-0">
      <slot name="icon" />
    </span>
    <span v-show="!isCollapsed" class="ml-3 flex-1 whitespace-nowrap">
      <slot />
    </span>
  </Link>
</template>
