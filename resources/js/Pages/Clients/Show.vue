<template>
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ client.name }}
      </h1>
    </template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <p class="mb-2 text-gray-700 dark:text-gray-300">
        Email: {{ client.email }}
      </p>
      <p class="mb-4 text-gray-700 dark:text-gray-300">
        Phone: {{ client.phone }}
      </p>
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

<script setup>
import { defineProps } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  client: Object,
});

const form = useForm({
  client: props.client,
});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(route('clients.destroy', props.client.id));
  }
};
</script>
