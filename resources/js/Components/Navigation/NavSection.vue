<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

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
  expandedSections: {
    type: Set,
    required: true,
  },
});

const emit = defineEmits(['toggle-section', 'close-mobile-menu']);

const isExpanded = computed(() => props.expandedSections.has(props.item.name));

const handleToggle = () => {
  emit('toggle-section', props.item.name);
};
</script>

<template>
  <div class="space-y-1">
    <!-- Parent Item (Toggle) -->
    <div
      :class="[
        item.current
          ? 'bg-gray-900 dark:bg-gray-700 text-white dark:text-gray-100'
          : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
        'group flex items-center px-2 py-2 text-sm font-medium rounded-md cursor-pointer transition-colors duration-200',
      ]"
      :title="isCollapsed ? item.name : ''"
      @click="handleToggle"
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
      <component
        v-if="!isCollapsed"
        :is="icons.chevronDown"
        :class="[
          item.current
            ? 'text-white dark:text-gray-100'
            : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300',
          'ml-auto h-5 w-5 transform transition-transform duration-200',
          { 'rotate-180': isExpanded },
        ]"
      />
    </div>

    <!-- Sub-items (Toggleable) -->
    <div v-if="!isCollapsed && isExpanded" class="ml-6 space-y-1">
      <Link
        v-for="subItem in item.children"
        :key="subItem.name"
        :href="subItem.href"
        :class="[
          subItem.current
            ? 'bg-gray-700 dark:bg-gray-600 text-white dark:text-gray-100'
            : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white',
          'group flex items-center px-2 py-1 text-sm font-medium rounded-md transition-colors duration-200',
        ]"
        @click="$emit('close-mobile-menu')"
      >
        {{ subItem.name }}
      </Link>
    </div>
  </div>
</template>
