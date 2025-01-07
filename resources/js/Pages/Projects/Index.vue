<template>
  <Head title="Projects" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Projects
      </h1>
    </template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <ul class="mb-4 space-y-4">
        <li
          v-for="project in projects"
          :key="project.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>
            <Link
              :href="route('projects.show', project.id)"
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              {{ project.name }}
            </Link>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
              Created at: {{ new Date(project.created_at).toLocaleString() }}
            </div>
          </div>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            Project ID: {{ project.id }}
          </div>
        </li>
      </ul>
      <Link
        :href="route('projects.create')"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Project
      </Link>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  projects: Array,
});
</script>
