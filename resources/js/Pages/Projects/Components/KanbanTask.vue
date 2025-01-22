
<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  task: Object,
  project: Object,
  filtersForm: Object,
});
</script>

<template>
  <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2 mb-2 shadow-sm text-gray-800 dark:text-gray-100">
    <Link
      :href="
        route('tasks.show', {
          project: project.slug,
          task: task.id,
        })
      "
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      {{ task.name }} #{{ task.id }}
    </Link>
    <p v-if="filtersForm.show_description" class="dark:text-gray-400">
      Description: {{ task.description }}
    </p>
    <p v-if="filtersForm.show_status">
      Status:
      <span :style="{ color: task.status?.color }">{{ task.status?.name }}</span>
    </p>
    <p v-if="filtersForm.show_priority">
      Priority:
      <span :style="{ color: task.priority?.color }">{{ task.priority?.name }}</span>
    </p>
    <p v-if="filtersForm.show_assigned_to" class="dark:text-gray-200">
      Assigned to:
      <span v-for="(user, index) in task.assigned_to" :key="user.id">
        {{ user.name }}<span v-if="index < task.assigned_to.length - 1">, </span>
      </span>
    </p>
  </div>
</template>