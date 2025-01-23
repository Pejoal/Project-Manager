<script setup>
import { defineProps, defineEmits } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  project: Object,
});

const emit = defineEmits(['destroy']);
</script>

<template>
  <section class="flex flex-wrap gap-2 m-2">
    <Link
      :href="route('projects.index')"
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      View All Projects
    </Link>

    <Link
      :href="route('projects.edit', { project: project.slug })"
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      Edit
    </Link>
    <Link
      :href="route('phases.index', { project: project.slug })"
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      View Phases
    </Link>
    <Link
      :href="route('milestones.index', { project: project.slug })"
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      View Milestones
    </Link>
    <Link
      :href="route('tasks.index', { project: project.slug })"
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      View Tasks
    </Link>
    <button
      @click="$emit('destroy')"
      class="ml-4 text-red-500 dark:text-red-400 hover:underline"
    >
      Delete
    </button>
  </section>
</template>
