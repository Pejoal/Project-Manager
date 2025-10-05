<script setup>
import LocaleSelector from '@/Components/Navigation/LocaleSelector.vue';
import SettingsDropdown from '@/Components/Navigation/SettingsDropdown.vue';
import TeamsDropdown from '@/Components/Navigation/TeamsDropdown.vue';
import ThemeToggle from '@/Components/Navigation/ThemeToggle.vue';

const props = defineProps({
  showingMobileMenu: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['toggle-mobile-menu']);

// SVG Icons
const IconMenu = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>`,
};

const IconX = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>`,
};
</script>

<template>
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Left side - Mobile menu button -->
        <div class="flex items-center md:hidden">
          <button
            @click="emit('toggle-mobile-menu')"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
          >
            <IconMenu v-if="!showingMobileMenu" class="h-6 w-6" />
            <IconX v-else class="h-6 w-6" />
          </button>
        </div>

        <!-- Center - Page title or breadcrumbs could go here -->
        <div class="flex items-center">
          <!-- This space can be used for page titles or breadcrumbs -->
        </div>

        <!-- Right side - Theme toggle, locale selector, and user menu -->
        <div class="flex items-center space-x-4">
          <!-- Theme Toggle -->
          <ThemeToggle />

          <!-- Locale Selector -->
          <LocaleSelector />

          <!-- Teams Dropdown -->
          <div class="relative">
            <TeamsDropdown align="right" width="60" />
          </div>

          <!-- Settings Dropdown -->
          <div class="relative">
            <SettingsDropdown align="right" width="48" />
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
