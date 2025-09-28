<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import CreateTeamForm from './Partials/CreateTeamForm.vue';
</script>

<template>
  <Head :title="trans('words.create_team')" />

  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ trans('words.create_team') }}
      </h2>
    </template>

    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <CreateTeamForm />
      </div>
    </div>
  </AppLayout>
</template>
