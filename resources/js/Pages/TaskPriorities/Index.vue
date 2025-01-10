<template>
  <Head title="Task Priorities" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Task Priorities
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <CreateTaskPriorityModal
        :show="showModal"
        @close="closeModal"
        :priority="selectedPriority"
      />
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
          <div class="flex items-center gap-2">
            <p class="text-white">{{ priority.name }}</p>
            <div
              class="w-16 h-6"
              :style="{ backgroundColor: priority.color }"
            ></div>
          </div>

          <div>
            <button
              @click="() => updatePriority(priority)"
              class="ml-4 text-green-500 dark:text-green-400 hover:underline"
            >
              Update
            </button>
            <button
              @click="() => destroy(priority.id)"
              class="ml-4 text-red-500 dark:text-red-400 hover:underline"
            >
              Delete
            </button>
          </div>
        </li>
      </ul>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTaskPriorityModal from './CreateTaskPriorityModal.vue';

const props = defineProps({
  priorities: Array,
});

const showModal = ref(false);
const selectedPriority = ref(null);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const form = useForm({
  name: null,
});

const destroy = (id) => {
  if (confirm('Are you sure?')) {
    form.delete(route('task-priorities.destroy', id));
  }
};

const updatePriority = (priority = null) => {
  selectedPriority.value = priority;
  openModal();
};
</script>
