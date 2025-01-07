<template>
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Clients</h1>
    </template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <ul class="mb-4">
        <li v-for="client in clients" :key="client.id" class="mb-2">
          <Link
            :href="route('clients.show', client.id)"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ client.name }}
          </Link>
        </li>
      </ul>
      <Link
        :href="route('clients.create')"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Add New Client
      </Link>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  clients: Array,
});
</script>
