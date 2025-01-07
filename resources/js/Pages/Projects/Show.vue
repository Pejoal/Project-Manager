<template>
  <Head :title="`Project - ${project.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ project.name }}
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          {{ project.description }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Created at: {{ new Date(project.created_at).toLocaleString() }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Project ID: {{ project.id }}
        </p>
      </div>
      <Link
        :href="route('projects.edit', { project: project.id })"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Edit
      </Link>
      <Link
        :href="route('tasks.index', { project: project.id })"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        View Tasks
      </Link>
      <button
        @click="destroy"
        class="ml-4 text-red-500 dark:text-red-400 hover:underline"
      >
        Delete
      </button>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  project: Object,
});

const form = useForm({
  project: props.project,
});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(route('projects.destroy', { project: props.project.id }));
  }
};
</script>
