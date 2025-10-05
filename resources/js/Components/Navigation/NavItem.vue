<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  isCollapsed: {
    type: Boolean,
    default: false,
  },
  icons: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close-mobile-menu']);
</script>

<template>
  <Link
    :href="item.href"
    :class="[
      item.current
        ? 'bg-gray-900 dark:bg-gray-700 text-white dark:text-gray-100'
        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
      'group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors duration-200',
    ]"
    :title="isCollapsed ? item.name : ''"
    @click="$emit('close-mobile-menu')"
  >
    <component
      :is="icons[item.icon]"
      :class="[
        item.current
          ? 'text-white dark:text-gray-100'
          : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300',
        'flex-shrink-0 h-6 w-6',
        isCollapsed ? '' : 'mr-3',
      ]"
    />
    <span v-if="!isCollapsed" class="flex-1">{{ item.name }}</span>
  </Link>
</template>
