<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import CreateTaskModal from '@/Pages/Tasks/CreateTaskModal.vue';

const props = defineProps({
  project: Object,
  users: Array,
  taskPriorities: Array,
  taskStatuses: Array,
});

const emit = defineEmits(['destroy']);

const showModal = ref(false);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};
</script>

<template>
  <section class="flex flex-wrap gap-2 m-2">
    <Link :href="route('projects.index')" class="text-blue-500 dark:text-blue-400 hover:underline">
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

    <CreateTaskModal
      :show="showModal"
      :users="users"
      :project="project"
      :statuses="taskStatuses"
      :priorities="taskPriorities"
      @close="closeModal"
    />
    <button @click="openModal" class="text-blue-500 dark:text-blue-400 hover:underline">Create Task</button>

    <button @click="$emit('destroy')" class="ml-4 text-red-500 dark:text-red-400 hover:underline">Delete</button>
  </section>
</template>
