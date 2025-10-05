<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close-mobile-menu']);
</script>

<template>
  <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900 dark:bg-gray-800">
    <Link :href="route('dashboard')" class="flex items-center" @click="$emit('close-mobile-menu')">
      <img v-if="!isCollapsed" class="h-8 w-auto" src="/favicon.ico" alt="Company Logo" />
      <span v-if="!isCollapsed" class="ml-3 text-white font-semibold text-lg">Project Manager</span>
    </Link>
  </div>
</template>
