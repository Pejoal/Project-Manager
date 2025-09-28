<script setup>
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  terms: String,
});
</script>

<template>
  <Head :title="trans('words.terms_of_service')" />

  <div class="font-sans text-gray-900 dark:text-gray-100 antialiased">
    <div class="pt-4 bg-gray-100 dark:bg-gray-900">
      <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
        <div>
          <AuthenticationCardLogo />
        </div>

        <div
          class="w-full sm:max-w-2xl mt-6 p-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg prose dark:prose-invert"
          v-html="terms"
        />
      </div>
    </div>
  </div>
</template>
