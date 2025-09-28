<template>
  <Head :title="trans('words.project_priorities')" />
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ trans('words.project_priorities') }}
        </h2>
        <button @click="openCreateModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          {{ trans('words.create_priority') }}
        </button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
              {{ trans('words.manage_priorities') }}
            </h3>

            <div v-if="priorities.data.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">{{ trans('words.no_priorities') }}</p>
            </div>

            <div v-else class="grid gap-4">
              <div
                v-for="priority in priorities.data"
                :key="priority.id"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex justify-between items-center"
              >
                <div>
                  <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                    {{ priority.name }}
                  </h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ trans('words.order') }}: {{ priority.order }}
                  </p>
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

    <CreateProjectPriorityModal :show="showCreateModal" @close="closeCreateModal" />
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import CreateProjectPriorityModal from './CreateProjectPriorityModal.vue';

const props = defineProps({
  priorities: Object,
});

const showCreateModal = ref(false);

const openCreateModal = () => {
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
};
</script>
