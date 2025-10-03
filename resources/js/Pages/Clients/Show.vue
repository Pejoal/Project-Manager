<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
  client: Object,
});

const form = useForm({});

const destroy = () => {
  if (confirm(trans('words.are_you_sure'))) {
    form.delete(route('clients.destroy', props.client.id));
  }
};
</script>

<template>
  <Head :title="`${trans('words.client')} - ${client.name}`" />
  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ client.name }}
      </h1>
    </div>
  </header>

  <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
      <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.id') }}: {{ client.id }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.email') }}: {{ client.email }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.phone') }}: {{ client.phone }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">
        {{ trans('words.projects') }}:
        <span v-for="(project, index) in client.projects" :key="project.id">
          {{ project.name }}<span v-if="index < client.projects.length - 1">, </span>
        </span>
      </p>
    </div>
    <Link :href="route('clients.edit', client.id)" class="text-blue-500 dark:text-blue-400 hover:underline">
      {{ trans('words.edit') }}
    </Link>
    <button @click="destroy" class="ml-4 text-red-500 dark:text-red-400 hover:underline">
      {{ trans('words.delete') }}
    </button>
  </div>
</template>
