<script setup>
import { defineProps } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProjectDetails from './Components/ProjectDetails.vue';
import ProjectActions from './Components/ProjectActions.vue';
import KanbanView from './Components/KanbanView.vue';
import ProjectProgress from './Components/ProjectProgress.vue';

const props = defineProps({
  project: Object,
  totalTasks: Number,
  completedTasks: Number,
  completedStatusColor: String,
  users: Array,
  taskStatuses: Array,
  taskPriorities: Array,
});

const form = useForm({});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(route('projects.destroy', { project: props.project.slug }));
  }
};
</script>

<template>
  <Head :title="`Project - ${project.name}`" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ project.name }}
      </h1>
    </template>
    <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
      <ProjectDetails :project="project" />
      <ProjectProgress
        :totalTasks="totalTasks"
        :completedTasks="completedTasks"
        :completedStatusColor="completedStatusColor"
      />
      <ProjectActions
        :users="users"
        :taskStatuses="taskStatuses"
        :taskPriorities="taskPriorities"
        :project="project"
        @destroy="destroy"
      />
      <KanbanView :project="project" />
    </div>
  </AppLayout>
</template>
