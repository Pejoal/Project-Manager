<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Breadcrumps from '@/Components/Breadcrumps.vue';
import Navigation from '@/Components/Navigation/Navigation.vue';
import ResponsiveNavigation from '@/Components/Navigation/ResponsiveNavigation.vue';
import SettingsDropdown from '@/Components/Navigation/SettingsDropdown.vue';
import TeamsDropdown from '@/Components/Navigation/TeamsDropdown.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);

const isDarkMode = ref(localStorage.getItem('theme') === 'dark');
document.documentElement.classList.toggle('dark', isDarkMode.value);

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  const theme = isDarkMode.value ? 'dark' : 'light';
  document.documentElement.classList.toggle('dark', isDarkMode.value);
  localStorage.setItem('theme', theme);
};

// console.log(route().current());
</script>

<template>
  <div>
    <Banner />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
      <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-1 sm:px-2 lg:px-3">
          <div class="flex justify-between h-16">
            <div class="flex">
              <!-- Logo -->
              <div class="shrink-0 flex items-center">
                <Link :href="route('welcome')">
                  <ApplicationMark class="block h-8 w-auto" />
                </Link>
              </div>

              <!-- Navigation Links -->
              <Navigation :is-dark-mode="isDarkMode" @toggle-dark-mode="toggleDarkMode" />
            </div>

            <!-- Teams & Settings Dropdowns -->
            <div class="hidden md:flex sm:items-center sm:ms-6">
              <div class="ms-3 relative">
                <TeamsDropdown />
              </div>

              <!-- Settings Dropdown -->
              <div class="ms-3 relative">
                <SettingsDropdown />
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
              <button
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                @click="showingNavigationDropdown = !showingNavigationDropdown"
              >
                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <ResponsiveNavigation
          :showing-navigation-dropdown="showingNavigationDropdown"
          :is-dark-mode="isDarkMode"
          @toggle-dark-mode="toggleDarkMode"
        />
      </nav>
      <Breadcrumps /> 

      <!-- Page Content -->
      <main class="pb-6">
        <slot />
      </main>
    </div>
  </div>
</template>
