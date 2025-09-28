<script setup>
import { Head } from '@inertiajs/vue3';
import CreateTeamForm from './Partials/CreateTeamForm.vue';
</script>

<template>
  <Head :title="trans('words.create_team')" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ trans('words.create_team') }}
      </h2>
    </div>
  </header>

  <div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <CreateTeamForm />
    </div>
  </div>
</template>
