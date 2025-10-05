<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  isCollapsed: Boolean,
});

const emit = defineEmits(['toggle-sidebar']);

const IconChevronLeft = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>`,
};
</script>

<template>
  <aside
    class="hidden md:flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300"
    :class="isCollapsed ? 'w-20' : 'w-64'"
  >
    <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
      <Link :href="route('dashboard')">
        <ApplicationMark class="block h-8 w-auto" />
      </Link>
    </div>

    <nav class="flex-1 px-3 py-4 overflow-y-auto">
      <ul class="space-y-2">
        <slot />
      </ul>
    </nav>

    <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
      <button
        @click="emit('toggle-sidebar')"
        class="w-full flex items-center justify-center p-2 text-gray-500 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
        :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
      >
        <component
          :is="IconChevronLeft"
          class="w-6 h-6 transition-transform duration-300"
          :class="{ 'rotate-180': isCollapsed }"
        />
      </button>
    </div>
  </aside>
</template>
