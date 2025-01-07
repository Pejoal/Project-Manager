<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

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
        Dashboard
        {{ translations.welcome }}
      </h2>

      <div class="flex justify-center space-x-4 mt-4">
        <a
          :href="route('localized', { locale: 'en' })"
          class="px-4 py-2 rounded-md text-white bg-blue-500 hover:bg-blue-600 transition duration-300"
        >
          English
        </a>
        <a
          :href="route('localized', { locale: 'de' })"
          class="px-4 py-2 rounded-md text-white bg-green-500 hover:bg-green-600 transition duration-300"
        >
          Deutsch
        </a>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
        >
          <Welcome />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
