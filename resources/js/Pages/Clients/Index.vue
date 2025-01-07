<template>
  <Head title="Clients" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Clients
      </h1>
    </template>
    <section class="p-2">
      <ul class="mb-4">
        <li
          v-for="client in clients"
          :key="client.id"
          class="mb-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-md"
        >
          <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <Link
              :href="route('clients.show', client.id)"
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              {{ client.name }}
            </Link>
            <p class="text-gray-600 dark:text-gray-400">ID: {{ client.id }}</p>
            <p class="text-gray-600 dark:text-gray-400">
              Created At: {{ new Date(client.created_at).toLocaleString() }}
            </p>
          </div>
        </li>
      </ul>
      <Link
        :href="route('clients.create')"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Add New Client
      </Link>
    </section>
  </AppLayout>
</template>

<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  clients: Array,
});
</script>
