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
</script>

<template>
  <button @click="toggleDarkMode" class="p-2 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none">
    <template v-if="isDarkMode">
      {{ $t('words.light_mode') }}
    </template>
    <template v-else>
      {{ $t('words.dark_mode') }}
    </template>
    <!-- <IconSun v-if="isDarkMode" />
                  <IconMoon v-else /> -->
  </button>
</template>
