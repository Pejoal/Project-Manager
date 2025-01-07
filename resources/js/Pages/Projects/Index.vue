<template>
  <Head title="Projects" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Projects
      </h1>
    </template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <ul class="mb-4">
        <li v-for="project in projects" :key="project.id" class="mb-2">
          <Link
            :href="route('projects.show', project.id)"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ project.name }}
          </Link>
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
