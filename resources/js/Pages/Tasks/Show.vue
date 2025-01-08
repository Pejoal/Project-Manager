<template>
  <Head :title="`Task - ${task.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ task.name }}
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Task ID: {{ task.id }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Project ID: {{ project.id }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Description: {{ task.description }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Project Name: {{ project.name }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Assigned to:
          <span v-for="(user, index) in task.assigned_to" :key="user.id">
            {{ user.name
            }}<span v-if="index < task.assigned_to.length - 1">, </span>
          </span>
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Created at: {{ new Date(task.created_at).toLocaleString() }}
        </p>
      </div>
      <Link
        :href="route('tasks.edit', { project: project.slug, task: task.id })"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Edit
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
  task: Object,
  project: Object,
});

const form = useForm({
  task: props.task,
});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(
      route('tasks.destroy', {
        project: props.project.slug,
        task: props.task.id,
      })
    );
  }
};
</script>
