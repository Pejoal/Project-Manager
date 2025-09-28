<template>
  <Head :title="trans('words.project_statuses')" />

  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ trans('words.project_statuses') }}
        </h2>
        <button @click="openCreateModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          {{ trans('words.create_status') }}
        </button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
              {{ trans('words.manage_statuses') }}
            </h3>

            <div v-if="statuses.data.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">{{ trans('words.no_statuses') }}</p>
            </div>

            <div v-else class="grid gap-4">
              <div
                v-for="status in statuses.data"
                :key="status.id"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex justify-between items-center"
              >
                <div class="flex items-center space-x-3">
                  <div class="w-4 h-4 rounded-full" :style="{ backgroundColor: status.color }"></div>
                  <div>
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                      {{ status.name }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                      {{ trans('words.order') }}: {{ status.order }}
                    </p>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                    {{ trans('words.edit') }}
                  </button>
                  <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                    {{ trans('words.delete') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <CreateProjectStatusModal :show="showCreateModal" @close="closeCreateModal" />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateProjectStatusModal from './CreateProjectStatusModal.vue';

defineProps({
  statuses: Object,
});

const showCreateModal = ref(false);

const openCreateModal = () => {
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
};
</script>
