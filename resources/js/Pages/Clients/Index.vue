<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateClientModal from './CreateClientModal.vue';

const showCreateModal = ref(false);

const openCreateModal = () => {
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
};

defineProps({
  clients: Object,
});
</script>

<template>
  <Head :title="trans('words.clients')" />
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ trans('words.clients') }}
        </h2>
        <button @click="openCreateModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          {{ trans('words.create_client') }}
        </button>
      </div>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div v-if="clients.data.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">{{ trans('words.no_clients') }}</p>
            </div>
            <div v-else class="grid gap-4">
              <div v-for="client in clients.data" :key="client.id" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                  {{ client.name }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400">{{ client.email }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ client.phone }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ client.company }}</p>
                <div class="mt-3 flex space-x-2">
                  <a
                    :href="route('clients.show', client.id)"
                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                  >
                    {{ trans('words.view') }}
                  </a>
                  <a
                    :href="route('clients.edit', client.id)"
                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                  >
                    {{ trans('words.edit') }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <CreateClientModal :show="showCreateModal" @close="closeCreateModal" />
  </AppLayout>
</template>
