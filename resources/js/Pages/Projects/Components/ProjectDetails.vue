<script setup>
const props = defineProps({
  project: Object,
});
</script>

<template>
  <h2 class="text-xl font-bold dark:text-gray-100">{{ trans('words.basic_data') }}</h2>
  <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
    <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.project_id') }}: {{ project.id }}</p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.description') }}: {{ project.description }}</p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      {{ trans('words.clients') }}:
      <span v-for="(client, index) in project.clients" :key="client.id">
        {{ client.name }}<span v-if="index < project.clients.length - 1">, </span>
      </span>
    </p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.status') }}: {{ project.status?.name }}</p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.priority') }}: {{ project.priority?.name }}</p>
    <div class="mb-2">
      <p class="text-gray-700 dark:text-gray-300">
        {{ trans('words.start_date') }}: {{ new Date(project.start_date).toLocaleDateString() }}
      </p>
      <p class="text-gray-700 dark:text-gray-300">
        {{ trans('words.end_date') }}: {{ new Date(project.end_date).toLocaleDateString() }}
      </p>
    </div>
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      {{ trans('words.created_at') }}: {{ new Date(project.created_at).toLocaleString() }}
    </p>
  </div>
</template>
