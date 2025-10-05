<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const locales = computed(() => page.props.locales || []);
const activeLocale = computed(() => page.props.active_locale_code || 'en');

const getCurrentLocale = computed(() => {
  return (
    locales.value.find((locale) => locale.code === activeLocale.value) || { code: 'en', native: 'English', flag: '🇺🇸' }
  );
});

// SVG Icon
const IconGlobe = {
  template: `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9-3-9m-9 9a9 9 0 019-9"></path></svg>`,
};
</script>

<template>
  <Dropdown align="right" width="48">
    <template #trigger>
      <button
        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
      >
        <span class="mr-2">{{ getCurrentLocale.flag }}</span>
        <span class="hidden sm:inline">{{ getCurrentLocale.native }}</span>
        <IconGlobe class="ml-2 -mr-0.5 h-4 w-4 sm:hidden" />
        <svg class="ml-2 -mr-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
          <path
            fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
    </template>

    <template #content>
      <div class="px-4 py-2 text-xs text-gray-400 border-b border-gray-200 dark:border-gray-600">
        {{ $t('words.select_language') }}
      </div>
      <DropdownLink
        v-for="locale in locales"
        :key="locale.code"
        :href="locale.url"
        :class="{ 'bg-gray-100 dark:bg-gray-700': locale.code === activeLocale }"
      >
        <div class="flex items-center">
          <span class="mr-3">{{ locale.flag }}</span>
          <span>{{ locale.native }}</span>
          <svg
            v-if="locale.code === activeLocale"
            class="ml-auto h-4 w-4 text-green-500"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
      </DropdownLink>
    </template>
  </Dropdown>
</template>
