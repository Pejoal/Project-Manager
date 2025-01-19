<script setup>
import { defineProps } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  milestone: Object,
});

const form = useForm({});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(
      route('milestones.destroy', {
        project: props.milestone.project.slug,
        milestone: props.milestone.id,
      })
    );
  }
};
</script>

<template>
  <Head :title="`Milestone - ${milestone.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ milestone.name }}
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Milestone ID: {{ milestone.id }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Description: {{ milestone.description }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Project ID: {{ milestone.project.id }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Project:
          <Link
            :href="route('projects.show', { project: milestone.project.slug })"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ milestone.project.name }}
          </Link>
        </p>

        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Created at: {{ new Date(milestone.created_at).toLocaleString() }}
        </p>
      </div>
      <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
          Tasks
        </h2>
        <ul class="list-disc list-inside">
          <li
            v-for="task in milestone.tasks"
            :key="task.id"
            class="mb-2 text-gray-700 dark:text-gray-300"
          >
            <Link
              :href="
                route('tasks.show', {
                  project: milestone.project.slug,
                  task: task.id,
                })
              "
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              {{ task.name }}
            </Link>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Assigned to:
              <span class="text-gray-200" v-for="(user, index) in task.assigned_to" :key="user.id">
                {{ user.name
                }}<span v-if="index < task.assigned_to.length - 1">, </span>
              </span>
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Status:
              <span :style="{ color: task.status?.color }">{{
                task.status?.name
              }}</span>
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Priority:
              <span :style="{ color: task.priority?.color }">
                {{ task.priority?.name }}</span
              >
            </p>
          </li>
        </ul>
      </div>
      <Link
        :href="
          route('milestones.edit', { project: milestone.project.slug, milestone: milestone.id })
        "
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Edit
      </Link>
      <button
        @click="destroy"
        class="ml-4 text-red-500 dark:text-red-400 hover:underline"
      >
        Delete
      </button>
    </div>
  </AppLayout>
</template>
