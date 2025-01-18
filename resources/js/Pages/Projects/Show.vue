<script setup>
import { defineProps, ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Draggable from 'vuedraggable';

const props = defineProps({
  project: Object,
});

// const project = ref(props.project);

const form = useForm({});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(route('projects.destroy', { project: props.project.slug }));
  }
};

const phaseMoved = (evt) => {
  // const { from, to, item } = evt;
  axios.put(route('phases.sync', props.project.slug), {
    phases: props.project.phases,
  });
};

const taskMoved = (evt) => {
  axios.put(route('tasks.sync', props.project.slug), {
    phases: props.project.phases,
  });
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
      <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Project ID: {{ project.id }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Description: {{ project.description }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Clients:
          <span v-for="(client, index) in project.clients" :key="client.id">
            {{ client.name
            }}<span v-if="index < project.clients.length - 1">, </span>
          </span>
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Status: {{ project.status?.name }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Priority: {{ project.priority?.name }}
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
          Created at: {{ new Date(project.created_at).toLocaleString() }}
        </p>
      </div>

      <section class="space-x-2 my-2">
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
          :href="route('tasks.index', { project: project.slug })"
          class="text-blue-500 dark:text-blue-400 hover:underline"
        >
          View Tasks
        </Link>
        <button
          @click="destroy"
          class="ml-4 text-red-500 dark:text-red-400 hover:underline"
        >
          Delete
        </button>
      </section>

      <section class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <Draggable
          v-model="project.phases"
          group="phases"
          @move="phaseMoved"
          item-key="id"
          class="flex gap-4"
        >
          <template #item="{ element }">
            <div
              class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md w-80"
            >
              <h2
                class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
              >
                {{ element.name }}
              </h2>
              <Draggable
                v-model="element.tasks"
                group="tasks"
                @move="taskMoved"
                item-key="id"
              >
                <template #item="{ element: task }">
                  <div
                    class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2 mb-2 shadow-sm text-gray-800 dark:text-gray-200"
                  >
                    {{ task.name }}
                  </div>
                </template>
              </Draggable>
            </div>
          </template>
        </Draggable>
      </section>
    </div>
  </AppLayout>
</template>
