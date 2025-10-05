<script setup>
import Banner from '@/Components/Banner.vue';
import Sidebar from '@/Components/Navigation/Sidebar.vue';
import TopBar from '@/Components/Navigation/TopBar.vue';
import { ref } from 'vue';

// Layout state
const showingMobileMenu = ref(false);
const isSidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true');
const isDarkMode = ref(localStorage.getItem('theme') === 'dark');

// Initialize dark mode
document.documentElement.classList.toggle('dark', isDarkMode.value);

// Event handlers
const toggleSidebarCollapse = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value;
  localStorage.setItem('sidebarCollapsed', isSidebarCollapsed.value);
};

const toggleMobileMenu = () => {
  showingMobileMenu.value = !showingMobileMenu.value;
};

const closeMobileMenu = () => {
  showingMobileMenu.value = false;
};

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  const theme = isDarkMode.value ? 'dark' : 'light';
  document.documentElement.classList.toggle('dark', isDarkMode.value);
  localStorage.setItem('theme', theme);
};
</script>

<template>
  <div>
    <Banner />

    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
      <!-- Sidebar Component -->
      <Sidebar
        :is-sidebar-collapsed="isSidebarCollapsed"
        :showing-mobile-menu="showingMobileMenu"
        @toggle-sidebar="toggleSidebarCollapse"
        @close-mobile-menu="closeMobileMenu"
      />

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <TopBar
          :showing-mobile-menu="showingMobileMenu"
          :is-dark-mode="isDarkMode"
          @toggle-mobile-menu="toggleMobileMenu"
          @toggle-dark-mode="toggleDarkMode"
        />

        <!-- Main Scrollable Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto">
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot />
          </div>
        </main>
      </div>
    </div>
  </div>
</template>
