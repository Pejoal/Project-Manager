<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  pagination: Object,
});

const form = useForm({});

const fetchPage = (url) => {
  const params = new URLSearchParams(window.location.search);
  form.get(url, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    data: Object.fromEntries(params.entries()),
  });
};

const pageNumbers = computed(() => {
  const pages = [];
  const current = props.pagination.current_page;
  const last = props.pagination.last_page;

  // Show max 7 page numbers
  const maxVisible = 7;
  let start = Math.max(1, current - Math.floor(maxVisible / 2));
  let end = Math.min(last, start + maxVisible - 1);

  // Adjust start if we're near the end
  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1);
  }

  // Always show first page
  if (start > 1) {
    pages.push(1);
    if (start > 2) {
      pages.push('...');
    }
  }

  // Show page numbers
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  // Always show last page
  if (end < last) {
    if (end < last - 1) {
      pages.push('...');
    }
    pages.push(last);
  }

  return pages;
});

const getPageUrl = (pageNumber) => {
  const baseUrl = props.pagination.prev_page_url || props.pagination.next_page_url;
  if (!baseUrl) return null;

  const url = new URL(baseUrl);
  url.searchParams.set('page', pageNumber);
  return url.toString();
};
</script>

<template>
  <!-- Pagination Controls -->
  <section>
    <div class="flex items-center justify-center gap-2">
      <button
        v-for="page in pageNumbers"
        :key="page"
        @click="page !== '...' && page !== pagination.current_page ? fetchPage(getPageUrl(page)) : null"
        :disabled="page === '...' || page === pagination.current_page"
        :class="[
          'px-3 py-1 rounded-lg transition',
          page === pagination.current_page
            ? 'bg-blue-500 text-white cursor-default'
            : page === '...'
              ? 'cursor-default dark:text-white'
              : 'bg-gray-200 dark:bg-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600',
        ]"
      >
        {{ page }}
      </button>
    </div>

    <div class="flex items-center justify-center gap-2 my-2">
      <button
        v-if="pagination.prev_page_url"
        @click="fetchPage(pagination.prev_page_url)"
        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
      >
        {{ trans('words.previous') }}
      </button>

      <span class="dark:text-white">{{ trans('words.total') }}: {{ pagination.total }}</span>
      <button
        v-if="pagination.next_page_url"
        @click="fetchPage(pagination.next_page_url)"
        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
      >
        {{ trans('words.next') }}
      </button>
    </div>
  </section>
</template>
