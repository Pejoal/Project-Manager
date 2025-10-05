<script setup>
const emit = defineEmits(['close-mobile-menu']);
</script>

<template>
  <div class="absolute top-0 right-0 -mr-12 pt-2">
    <button
      @click="$emit('close-mobile-menu')"
      class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
    >
      <span class="sr-only">Close sidebar</span>
      <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
  </div>
</template>
