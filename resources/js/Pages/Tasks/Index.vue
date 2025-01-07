<template>
  <Head :title="`Tasks for ${project.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Tasks for {{ project.name }}
      </h1>
    </template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <ul class="mb-4">
        <li v-for="task in tasks" :key="task.id" class="mb-2 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
          <Link
            :href="route('tasks.show', { project: project.id, task: task.id })"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ task.name }}
          </Link>
          <span class="text-gray-500 dark:text-gray-400">
            - Created at: {{ new Date(task.created_at).toLocaleString() }}
          </span>
        </li>
      </ul>
      <Link
        :href="route('tasks.create', { project: project.id })"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Task
      </Link>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  tasks: Array,
  project: Object,
});
</script>
