<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const page = usePage();
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
  isLocalesOpen.value = false;
};

const handleClickOutside = (event) => {
  if (localesDropdownRef.value && !localesDropdownRef.value.contains(event.target)) {
    isLocalesOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
  <div ref="localesDropdownRef" class="relative">
    <button
      @click="isLocalesOpen = !isLocalesOpen"
      type="button"
      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition-colors duration-200"
    >
      <span
        v-if="$page.props.locales && $page.props.locales[$page.props.active_locale_code]"
        class="fi"
        :class="`fi-${$page.props.locales[$page.props.active_locale_code].flag}`"
      ></span>
      <svg
        class="ms-2 -me-0.5 h-4 w-4"
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
      class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50"
    >
      <div class="py-1" role="none">
        <button
          v-for="locale in $page.props.locales"
          :key="locale.code"
          @click.prevent="handleLocaleClick(locale.code, locale.url)"
          class="w-full text-left flex items-center px-4 py-2 text-sm transition-colors duration-200"
          :class="[
            activeLocaleCode === locale.code
              ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white'
              : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800',
          ]"
        >
          <span class="fi mr-3" :class="`fi-${locale.flag}`"></span>
          {{ locale.native }}
        </button>
      </div>
    </div>
  </div>
</template>
