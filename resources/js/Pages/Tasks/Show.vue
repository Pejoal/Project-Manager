<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  task: Object,
});

const form = useForm({});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(
      route('tasks.destroy', {
        project: props.project.slug,
        task: props.task.id,
      })
    );
  }
};
</script>

<template>
  <Head :title="`Task - ${task.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ task.name }}
      </h1>
    </template>
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
          <p class="text-gray-700 dark:text-gray-300">
            Start Date: {{ new Date(task.start_datetime).toLocaleString() }}
          </p>
          <p class="text-gray-700 dark:text-gray-300">End Date: {{ new Date(task.end_datetime).toLocaleString() }}</p>
        </div>

        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Created at: {{ new Date(task.created_at).toLocaleString() }}
        </p>
      </div>
      <Link
        :href="route('tasks.edit', { project: task.project.slug, task: task.id })"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Edit
      </Link>
      <button @click="destroy" class="ml-4 text-red-500 dark:text-red-400 hover:underline">Delete</button>
    </div>
  </AppLayout>
</template>
