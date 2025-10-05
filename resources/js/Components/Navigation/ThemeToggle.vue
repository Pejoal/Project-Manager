<script setup>
import { ref } from 'vue';

// Dark Mode Logic
const isDarkMode = ref(localStorage.getItem('theme') === 'dark');
document.documentElement.classList.toggle('dark', isDarkMode.value);

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  const theme = isDarkMode.value ? 'dark' : 'light';
  document.documentElement.classList.toggle('dark', isDarkMode.value);
  localStorage.setItem('theme', theme);
};

// SVG Icons
const IconSun = {
  template: `<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-15.66l-.707.707M4.34 19.66l-.707.707M21 12h-1M4 12H3m15.66 8.66l-.707-.707M4.34 4.34l-.707-.707"></path></svg>`,
};
const IconMoon = {
  template: `<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>`,
};
</script>

<template>
  <button
    @click="toggleDarkMode"
    class="p-2 rounded-lg text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
    :title="isDarkMode ? $t('words.light_mode') : $t('words.dark_mode')"
  >
    <IconSun v-if="isDarkMode" />
    <IconMoon v-else />
  </button>
</template>
