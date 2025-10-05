<!-- SidebarCollapseToggle.vue (New: Extracted Toggle Button) -->
<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false,
  },
  icons: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['toggle-sidebar']);
</script>

<template>
  <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
    <button
      @click="$emit('toggle-sidebar')"
      class="w-full flex items-center justify-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
      :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
    >
      <component
        :is="props.icons.chevronLeft"
        class="w-6 h-6 transition-transform duration-300"
        :class="{ 'rotate-180': isCollapsed }"
      />
    </button>
  </div>
</template>
