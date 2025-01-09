<template>
  <Head title="Task Statuses" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Task Statuses
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <CreateTaskStatusModal :show="showModal" @close="closeModal" />
      <button
        @click="openModal"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Task Status
      </button>
      <ul class="my-2 space-y-4">
        <li
          v-for="status in statuses"
          :key="status.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>{{ status.name }}</div>
          <div>
            <inertia-link :href="`/task-statuses/${status.id}/edit`" class="text-blue-500 dark:text-blue-400 hover:underline">Edit</inertia-link>
            <button @click="deleteStatus(status)" class="text-red-500 dark:text-red-400 hover:underline ml-2">Delete</button>
          </div>
        </li>
      </ul>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTaskStatusModal from './CreateTaskStatusModal.vue';

const props = defineProps({
  statuses: Array,
});

const showModal = ref(false);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const deleteStatus = (status) => {
  if (confirm('Are you sure you want to delete this status?')) {
    // logic to delete status
  }
};

</script>
