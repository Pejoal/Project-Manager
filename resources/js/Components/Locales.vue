<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import { usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';

const props = defineProps({
  horizontal: {
    tpye: Boolean,
    default: false,
  },
  vertical: {
    tpye: Boolean,
    default: false,
  },
});

const page = usePage().props;
if (page.active_locale_code) {
  loadLanguageAsync(page.active_locale_code);
}

const active_locale = (locale, url) => {
  location.href = url;
};
</script>
<template>
  <div
    :class="{
      'hidden md:flex items-center space-x-2 relative': props.horizontal,
      'py-2 my-2 border-b flex justify-between items-center': props.vertical,
    }"
  >
    <p>{{ trans('words.choose_locale') }}</p>
    <Dropdown width="48">
      <template #trigger>
        <span class="inline-flex rounded-md">
          <button
            type="button"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
          >
            <svg
              class="ml-2 -mr-0.5 h-4 w-4"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd"
              />
            </svg>
          </button>
        </span>
      </template>

      <template #content>
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
      </template>
    </Dropdown>
  </div>
</template>
