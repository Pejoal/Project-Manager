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
        <div>
          <ul>
            <li v-for="locale in $page.props.locales" :key="locale.code">
              <a
                :href="locale.url"
                :hreflang="locale.code"
                :class="{ 'font-bold': locale.code === activeLocaleCode }"
              >
                {{ locale.native }}
              </a>
            </li>
          </ul>
        </div>
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
