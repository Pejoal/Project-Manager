<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({
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
</script>

<template>
  <!-- Pagination Controls -->
  <section class="flex items-center justify-between my-2">
    <button
      v-if="pagination.prev_page_url"
      @click="fetchPage(pagination.prev_page_url)"
      class="px-4 py-2 bg-blue-500 dark:text-white rounded-lg"
    >
      Previous
    </button>
    <span class="mx-2 dark:text-white"
      >{{ pagination.current_page }} / {{ pagination.last_page }}</span
    >
    <span class="mx-2 dark:text-white">Total: {{ pagination.total }}</span>
    <button
      v-if="pagination.next_page_url"
      @click="fetchPage(pagination.next_page_url)"
      class="px-4 py-2 bg-blue-500 dark:text-white rounded-lg"
    >
      Next
    </button>
  </section>
</template>
