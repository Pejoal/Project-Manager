<script setup>
import { defineProps, ref, watch } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Draggable from 'vuedraggable';
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
  project: Object,
});

const form = useForm({});

const filtersForm = ref({
  show_description: false,
  show_status: true,
  show_priority: true,
  show_assigned_to: true,
});

const destroy = () => {
  if (confirm('Are you sure?')) {
    form.delete(route('projects.destroy', { project: props.project.slug }));
  }
};

watch(
  () => props.project.phases,
  (newPhases) => {
    axios.put(route('phases.sync', props.project.slug), {
      phases: newPhases,
    });
  },
  { deep: true }
);
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
      <h2 class="text-xl font-bold dark:text-gray-100">Basic Data</h2>
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
          @click="destroy"
          class="ml-4 text-red-500 dark:text-red-400 hover:underline"
        >
          Delete
        </button>
      </section>

      <section class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
        <h2 class="text-xl font-bold dark:text-gray-100">Kanban View</h2>
        <form class="my-2 flex gap-2">
          <section class="flex items-center">
            <InputLabel for="show_description" value="Show Description" />

            <Checkbox
              class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
              id="show_description"
              v-model:checked="filtersForm.show_description"
              name="show_description"
            />
          </section>

          <section class="flex items-center">
            <InputLabel for="show_status" value="Show Status" />

            <Checkbox
              class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
              id="show_status"
              v-model:checked="filtersForm.show_status"
              name="show_status"
            />
          </section>

          <section class="flex items-center">
            <InputLabel for="show_priority" value="Show Priority" />

            <Checkbox
              class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
              id="show_priority"
              v-model:checked="filtersForm.show_priority"
              name="show_priority"
            />
          </section>

          <section class="flex items-center">
            <InputLabel for="show_assigned_to" value="Show Assigned to" />

            <Checkbox
              class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
              id="show_assigned_to"
              v-model:checked="filtersForm.show_assigned_to"
              name="show_assigned_to"
            />
          </section>
        </form>
        <Draggable
          v-model="project.phases"
          group="phases"
          item-key="id"
          class="flex gap-4"
        >
          <template #item="{ element }">
            <div
              class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-md w-80"
            >
              <Link
                :href="
                  route('phases.show', {
                    project: project.slug,
                    phase: element.id,
                  })
                "
                class="text-lg text-blue-500 dark:text-blue-400 hover:underline"
              >
                {{ element.name }}
              </Link>
              <Draggable v-model="element.tasks" group="tasks" item-key="id">
                <template #item="{ element: task }">
                  <div
                    class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2 mb-2 shadow-sm text-gray-800 dark:text-gray-100"
                  >
                    <Link
                      :href="
                        route('tasks.show', {
                          project: project.slug,
                          task: task.id,
                        })
                      "
                      class="text-blue-500 dark:text-blue-400 hover:underline"
                    >
                      {{ task.name }}
                      #{{ task.id }}
                    </Link>
                    <p
                      v-if="filtersForm.show_description"
                      class="dark:text-gray-400"
                    >
                      Description: {{ task.description }}
                    </p>
                    <p v-if="filtersForm.show_status">
                      Status:
                      <span :style="{ color: task.status?.color }">{{
                        task.status?.name
                      }}</span>
                    </p>
                    <p v-if="filtersForm.show_priority">
                      Priority:
                      <span :style="{ color: task.priority?.color }">{{
                        task.priority?.name
                      }}</span>
                    </p>
                    <p
                      v-if="filtersForm.show_assigned_to"
                      class="dark:text-gray-200"
                    >
                      Assigned to:
                      <span
                        v-for="(user, index) in task.assigned_to"
                        :key="user.id"
                        >{{ user.name
                        }}<span v-if="index < task.assigned_to.length - 1"
                          >,
                        </span>
                      </span>
                    </p>
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
