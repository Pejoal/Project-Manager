<template>
  <Head :title="`All Tasks`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        All Tasks
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <CreateTaskModal
        :show="showModal"
        :projects="projects"
        :users="users"
        @close="closeModal"
      />
      <button
        @click="openModal"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Task
      </button>
      <ul class="my-2 space-y-4">
        <li
          v-for="task in tasks"
          :key="task.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>
            <Link
              :href="
                route('tasks.show', { project: task.project.slug, task: task.id })
              "
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              {{ task.name }} - {{ task.project.name }}
            </Link>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
              Created at: {{ new Date(task.created_at).toLocaleString() }}
            </div>
          </div>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            Task ID: {{ task.id }}
          </div>
        </li>
      </ul>
    </div>
  </AppLayout>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTaskModal from './CreateTaskModal.vue';

const props = defineProps({
  users: Array,
  tasks: Array,
  projects: Array,
});

const showModal = ref(false);

const openModal = (project) => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};
</script>
