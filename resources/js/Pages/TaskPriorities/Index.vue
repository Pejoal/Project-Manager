<template>
  <Head title="Task Priorities" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Task Priorities
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <CreateTaskPriorityModal :show="showModal" @close="closeModal" />
      <button
        @click="openModal"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Task Priority
      </button>
      <ul class="my-2 space-y-4">
        <li
          v-for="priority in priorities"
          :key="priority.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>{{ priority.name }}</div>
          <div>
            <inertia-link :href="`/task-priorities/${priority.id}/edit`" class="text-blue-500 dark:text-blue-400 hover:underline">Edit</inertia-link>
            <button @click="deletePriority(priority)" class="text-red-500 dark:text-red-400 hover:underline ml-2">Delete</button>
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
import CreateTaskPriorityModal from './CreateTaskPriorityModal.vue';

const props = defineProps({
  priorities: Array,
});

const showModal = ref(false);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const deletePriority = (priority) => {
  if (confirm('Are you sure you want to delete this priority?')) {
    // logic to delete priority
  }
};
</script>
