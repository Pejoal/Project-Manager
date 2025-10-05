<script setup>
import Breadcrumps from '@/Components/Breadcrumps.vue';
import LocaleSelector from '@/Components/Navigation/LocaleSelector.vue';
import SettingsDropdown from '@/Components/Navigation/SettingsDropdown.vue';
import TeamsDropdown from '@/Components/Navigation/TeamsDropdown.vue';
import ThemeToggle from '@/Components/Navigation/ThemeToggle.vue';

defineEmits(['toggle-mobile-menu']);
</script>

<template>
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Hamburger for Mobile -->
        <div class="flex items-center md:hidden">
          <button
            @click="$emit('toggle-mobile-menu')"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
          >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>

        <!-- Breadcrumbs for Desktop -->
        <div class="hidden md:flex items-center">
          <Breadcrumps />
        </div>

        <!-- Right side of Top Bar -->
        <div class="flex items-center space-x-3">
          <ThemeToggle />
          <LocaleSelector />

          <div class="ms-3 relative">
            <TeamsDropdown />
          </div>

          <div class="ms-3 relative">
            <SettingsDropdown />
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
