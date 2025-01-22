<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  project: Object,
});
</script>

<template>
  <h2 class="text-xl font-bold dark:text-gray-100">Basic Data</h2>
  <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      Project ID: {{ project.id }}
    </p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      Description: {{ project.description }}
    </p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      Clients:
      <span v-for="(client, index) in project.clients" :key="client.id">
        {{ client.name
        }}<span v-if="index < project.clients.length - 1">, </span>
      </span>
    </p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      Status: {{ project.status?.name }}
    </p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      Priority: {{ project.priority?.name }}
    </p>
    <p class="mb-2 text-gray-700 dark:text-gray-300">
      Created at: {{ new Date(project.created_at).toLocaleString() }}
    </p>
  </div>
</template>
