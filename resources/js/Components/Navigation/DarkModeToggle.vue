<script setup>
const props = defineProps({
  isDarkMode: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['toggle']);

const toggleDarkMode = () => {
  emit('toggle');
};
</script>

<template>
  <button
    @click="toggleDarkMode"
    class="p-2 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none transition-colors duration-200"
    >
    :title="isDarkMode ? $t('words.light_mode') : $t('words.dark_mode')"
    <svg v-if="isDarkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 3v1m0 16v1m8.66-15.66l-.707.707M4.34 19.66l-.707.707M21 12h-1M4 12H3m15.66 8.66l-.707-.707M4.34 4.34l-.707-.707"
      />
    </svg>
    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
      />
    </svg>
  </button>
</template>
