<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import TaskModal from './TaskModal.vue';

const props = defineProps({
  task: Object,
});

const form = useForm({});
const showTaskModal = ref(false);

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(
      route('tasks.destroy', {
        project: props.task.project.slug,
        task: props.task.id,
      })
    );
  }
};

const refreshTask = async () => {
  // In a real implementation, you might want to refresh the task data
  // For now, we'll just close the modal since this is a demo
  console.log('Task refresh requested');
};
</script>

<template>
  <Head :title="`Task - ${task.name}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ task.name }}
      </h1>
    </div>
  </header>

  <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
      <p class="mb-2 text-gray-700 dark:text-gray-300">Task ID: {{ task.id }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">Description: {{ task.description }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">
        Project:
        <Link
          :href="route('projects.show', { project: task.project.slug })"
          class="text-blue-500 dark:text-blue-400 hover:underline"
        >
          {{ task.project.name }} #{{ task.project.id }}
        </Link>
      </p>
      <p v-if="task.phase?.id" class="mb-2 text-gray-700 dark:text-gray-300">
        Phase:
        <Link
          :href="
            route('phases.show', {
              project: task.project.slug,
              phase: task.phase?.id,
            })
          "
          class="text-blue-500 dark:text-blue-400 hover:underline"
        >
          {{ task.phase?.name }} #{{ task.phase?.id }}
        </Link>
      </p>
      <p v-if="task.milestone?.id" class="mb-2 text-gray-700 dark:text-gray-300">
        Milestone:
        <Link
          :href="
            route('milestones.show', {
              project: task.project.slug,
              milestone: task.milestone?.id,
            })
          "
          class="text-blue-500 dark:text-blue-400 hover:underline"
        >
          {{ task.milestone?.name }} #{{ task.milestone?.id }}
        </Link>
      </p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">
        Assigned to:
        <span v-for="(user, index) in task.assigned_to" :key="user.id">
          {{ user.name }}<span v-if="index < task.assigned_to.length - 1">, </span>
        </span>
      </p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">Status: {{ task.status?.name }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">Priority: {{ task.priority?.name }}</p>

      <div class="mb-2">
        <p class="text-gray-700 dark:text-gray-300">Start Date: {{ new Date(task.start_datetime).toLocaleString() }}</p>
        <p class="text-gray-700 dark:text-gray-300">End Date: {{ new Date(task.end_datetime).toLocaleString() }}</p>
      </div>

      <p class="mb-2 text-gray-700 dark:text-gray-300">Created at: {{ new Date(task.created_at).toLocaleString() }}</p>
    </div>
    <Link
      :href="route('tasks.edit', { project: task.project.slug, task: task.id })"
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      Edit
    </Link>
    <button @click="destroy" class="ml-4 text-red-500 dark:text-red-400 hover:underline">Delete</button>

    <!-- Example: Quick access button to open task details -->
    <div v-if="task" class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Task Details</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
        View comprehensive task information including attachments, assignments, and project relationships.
      </p>
      <button
        @click="showTaskModal = true"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
          />
        </svg>
        View Task Details & Attachments
      </button>
    </div>

    <!-- TaskModal Component -->
    <TaskModal :show="showTaskModal" :task="task" @close="showTaskModal = false" @refresh="refreshTask" />
  </div>
</template>
