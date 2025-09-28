<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import { ref } from 'vue';

const isOpen = ref(false);

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const page = usePage().props;
if (page.active_locale_code) {
  loadLanguageAsync(page.active_locale_code);
}

const active_locale = (locale, url) => {
  // router.visit(url);
  // router.reload();
  location.href = url;
};
</script>

<template>
  <div class="relative block self-center text-left z-50">
    <div>
      <button
        @click="toggleDropdown"
        type="button"
        class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm p-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        id="options-menu"
        aria-haspopup="true"
        aria-expanded="true"
      >
        {{ trans('words.language') }}
        <svg
          class="-mr-1 ml-2 h-5 w-5"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </button>
    </div>

    <div
      v-show="isOpen"
      class="origin-top-left absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="options-menu"
    >
      <div class="py-1 flex flex-col gap-1" role="none">
        <button
          v-for="(locale, code) in $page.props.locales"
          class="flex justify-between items-center w-full p-2 bg-white hover:bg-zinc-900"
          :class="code === $page.props.active_locale_code ? 'bg-zinc-900 text-white' : 'text-gray-900 hover:text-white'"
          @click="active_locale(code, locale.url)"
          :key="code"
        >
          <span>{{ locale.native }}</span>
          <span>{{ locale.flag }}</span>
        </button>
      </div>
    </div>
  </div>
</template>
