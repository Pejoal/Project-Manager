<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateClientModal from './CreateClientModal.vue';

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
  <Head :title="trans('words.clients')" />
  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ trans('words.clients') }}</h1>
    </div>
  </header>

  <section class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <button @click="openModal" class="text-blue-500 dark:text-blue-400 hover:underline">
      {{ trans('words.add_new_client') }}
    </button>
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
            {{ trans('words.projects') }}:
            <span v-for="(project, index) in client.projects" :key="project.id">
              {{ project.name }}<span v-if="index < client.projects.length - 1">, </span>
            </span>
          </p>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            {{ trans('words.created_at') }}: {{ new Date(client.created_at).toLocaleString() }}
          </div>
        </div>
        <div class="text-gray-500 dark:text-gray-400 text-sm">{{ trans('words.id') }}: {{ client.id }}</div>
      </li>
    </ul>
  </section>
</template>
