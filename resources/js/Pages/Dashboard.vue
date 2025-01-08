<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  translations: Object,
});
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2
        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
      >
        {{ translations.welcome }} {{ $page.props.auth.user.name }}
      </h2>
    </template>

    <div class="max-w-7xl mx-auto sm:px-1 lg:px-8">
      <div
        class="p-2 my-1 bg-white dark:text-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
      >
        content
      </div>
    </div>
  </AppLayout>
</template>
