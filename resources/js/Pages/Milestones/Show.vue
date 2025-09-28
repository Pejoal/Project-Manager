<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  milestone: Object,
});

const form = useForm({});

const destroy = () => {
  if (confirm(trans('words.are_you_sure'))) {
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
  <Head :title="`${trans('words.milestone')} - ${milestone.name}`" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ milestone.name }}
      </h1>
    </div>
  </header>

  <div class="p-2 my-1 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
      <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.milestone_id') }}: {{ milestone.id }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.description') }}: {{ milestone.description }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">{{ trans('words.project_id') }}: {{ milestone.project.id }}</p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">
        {{ trans('words.project') }}:
        <Link
          :href="route('projects.show', { project: milestone.project.slug })"
          class="text-blue-500 dark:text-blue-400 hover:underline"
        >
          {{ milestone.project.name }}
        </Link>
      </p>
      <p class="mb-2 text-gray-700 dark:text-gray-300">
        {{ trans('words.phase') }}:
        <Link
          :href="
            route('phases.show', {
              project: milestone.project.slug,
              phase: milestone.phase.id,
            })
          "
          class="text-blue-500 dark:text-blue-400 hover:underline"
        >
          {{ milestone.phase.name }}
        </Link>
      </p>

      <p class="mb-2 text-gray-700 dark:text-gray-300">
        {{ trans('words.created_at') }}: {{ new Date(milestone.created_at).toLocaleString() }}
      </p>
    </div>
    <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
      <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ trans('words.tasks') }}</h2>
      <ul class="list-disc list-inside">
        <li v-for="task in milestone.tasks" :key="task.id" class="mb-2 text-gray-700 dark:text-gray-300">
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
              {{ user.name }}<span v-if="index < task.assigned_to.length - 1">, </span>
            </span>
          </p>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ trans('words.status') }}:
            <span :style="{ color: task.status?.color }">{{ task.status?.name }}</span>
          </p>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ trans('words.priority') }}:
            <span :style="{ color: task.priority?.color }"> {{ task.priority?.name }}</span>
          </p>
        </li>
      </ul>
    </div>
    <Link
      :href="
        route('milestones.edit', {
          project: milestone.project.slug,
          milestone: milestone.id,
        })
      "
      class="text-blue-500 dark:text-blue-400 hover:underline"
    >
      {{ trans('words.edit') }}
    </Link>
    <button @click="destroy" class="ml-4 text-red-500 dark:text-red-400 hover:underline">
      {{ trans('words.delete') }}
    </button>
  </div>
</template>
