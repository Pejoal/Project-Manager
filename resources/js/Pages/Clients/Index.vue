<script setup>
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateClientModal from './CreateClientModal.vue';
import { ref } from 'vue';

const showModal = ref(false);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

defineProps({
  clients: Array,
  projects: Array,
});
</script>

<template>
  <Head title="Clients" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Clients</h1>
    </template>
    <section class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <button @click="openModal" class="text-blue-500 dark:text-blue-400 hover:underline">Add New Client</button>
      <CreateClientModal :show="showModal" @close="closeModal" :projects="projects" />
      <ul class="my-2 space-y-4">
        <li
          v-for="client in clients"
          :key="client.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>
            <Link :href="route('clients.show', client.id)" class="text-blue-500 dark:text-blue-400 hover:underline">
              {{ client.name }}
            </Link>
            <p class="mb-2 text-gray-700 dark:text-gray-300">
              Projects:
              <span v-for="(project, index) in client.projects" :key="project.id">
                {{ project.name }}<span v-if="index < client.projects.length - 1">, </span>
              </span>
            </p>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
              Created At: {{ new Date(client.created_at).toLocaleString() }}
            </div>
          </div>
          <div class="text-gray-500 dark:text-gray-400 text-sm">ID: {{ client.id }}</div>
        </li>
      </ul>
    </section>
  </AppLayout>
</template>
