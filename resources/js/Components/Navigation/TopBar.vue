<script setup>
import Breadcrumps from '@/Components/Breadcrumps.vue';
import SettingsDropdown from '@/Components/Navigation/SettingsDropdown.vue';
import TeamsDropdown from '@/Components/Navigation/TeamsDropdown.vue';
import { router, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import DarkModeToggle from './DarkModeToggle.vue';

const page = usePage();

const props = defineProps({
  showingMobileMenu: {
    type: Boolean,
    default: false,
  },
  isDarkMode: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['toggle-mobile-menu', 'toggle-dark-mode']);

// Locales Dropdown Logic
const isLocalesOpen = ref(false);
const localesDropdownRef = ref(null);
const activeLocaleCode = ref(page.props.active_locale_code);

if (activeLocaleCode.value) {
  loadLanguageAsync(activeLocaleCode.value);
}

const handleLocaleClick = (code, url) => {
  activeLocaleCode.value = code;
  loadLanguageAsync(code);
  router.visit(url, { preserveState: true });
};

const handleClickOutsideLocales = (event) => {
  if (localesDropdownRef.value && !localesDropdownRef.value.contains(event.target)) {
    isLocalesOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutsideLocales));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutsideLocales));
</script>

<template>
  <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Left side - Hamburger for Mobile and Breadcrumbs -->
        <div class="flex items-center">
          <!-- Hamburger for Mobile -->
          <button
            @click="emit('toggle-mobile-menu')"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
          >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path
                :class="{ hidden: showingMobileMenu, 'inline-flex': !showingMobileMenu }"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                :class="{ hidden: !showingMobileMenu, 'inline-flex': showingMobileMenu }"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>

          <!-- Breadcrumbs for Desktop -->
          <div class="hidden md:flex items-center justify-center">
            <Breadcrumps />
          </div>
        </div>

        <!-- Right side controls -->
        <div class="flex items-center space-x-3">
          <!-- Dark Mode Toggle -->
          <DarkModeToggle  />

          <!-- Locales Dropdown -->
          <div ref="localesDropdownRef" class="relative">
            <button
              @click="isLocalesOpen = !isLocalesOpen"
              type="button"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
            >
              <span
                v-if="$page.props.locales && $page.props.locales[$page.props.active_locale_code]"
                class="fi mr-2"
                :class="`fi-${$page.props.locales[$page.props.active_locale_code].flag}`"
              ></span>
              <span class="hidden sm:inline">
                {{ $page.props.locales && $page.props.locales[$page.props.active_locale_code]?.native }}
              </span>
              <svg
                class="ml-2 -mr-0.5 h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
              </svg>
            </button>

            <div
              v-show="isLocalesOpen"
              class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
            >
              <div class="py-1" role="menu" aria-orientation="vertical">
                <button
                  v-for="locale in $page.props.locales"
                  :key="locale.code"
                  @click.prevent="handleLocaleClick(locale.code, locale.url)"
                  class="w-full text-left flex items-center px-4 py-2 text-sm transition duration-150 ease-in-out"
                  :class="[
                    activeLocaleCode === locale.code
                      ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white'
                      : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800',
                  ]"
                  role="menuitem"
                >
                  <span class="fi mr-3" :class="`fi-${locale.flag}`"></span>
                  {{ locale.native }}
                </button>
              </div>
            </div>
          </div>

          <!-- Teams Dropdown -->
          <div class="relative">
            <TeamsDropdown />
          </div>

          <!-- Settings Dropdown -->
          <div class="relative">
            <SettingsDropdown />
          </div>
        </div>
      </div>

      <!-- Breadcrumbs for Mobile -->
      <div class="md:hidden">
        <Breadcrumps />
      </div>
    </div>
  </header>
</template>
