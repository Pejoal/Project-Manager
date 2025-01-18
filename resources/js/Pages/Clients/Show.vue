<script setup>
import { defineProps } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  client: Object,
});

const form = useForm({});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(route('clients.destroy', props.client.id));
  }
};
</script>

<template>
  <Head :title="`Client - ${client.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ client.name }}
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <p class="mb-2 text-gray-700 dark:text-gray-300">ID: {{ client.id }}</p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Email: {{ client.email }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Phone: {{ client.phone }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Projects:
          <span v-for="(project, index) in client.projects" :key="project.id">
            {{ project.name
            }}<span v-if="index < client.projects.length - 1">, </span>
          </span>
        </p>
      </div>
      <Link
        :href="route('clients.edit', client.id)"
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
