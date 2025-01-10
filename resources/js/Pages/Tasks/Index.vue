<script setup>
import { defineProps, ref, computed } from 'vue';
import { Link, Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTaskModal from './CreateTaskModal.vue';

const props = defineProps({
  users: Array,
  tasks: Array,
  project: Object,
  projects: Array,
  statuses: Array,
  priorities: Array,
});

const showModal = ref(false);

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const title = computed(() =>
  props.project ? `Tasks for ${props.project.name}` : 'All Tasks'
);

const filtersVisible = ref(false);

const toggleFilters = () => {
  filtersVisible.value = !filtersVisible.value;
};

const form = useForm({
  search: '',
  perPage: 5,
});

const applyFilters = () => {
  if (props.project) {
    form.get(route('tasks.index'), {
      preserveState: true,
      preserveScroll: true,
    });
  } else {
    form.get(route('tasks.all'), {
      preserveState: true,
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head :title="title" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        {{ title }}
        <template v-if="project">
          <Link
            :href="route('projects.show', { project: project.slug })"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ project.name }}
          </Link>
        </template>
      </h1>
    </template>
    <div
      class="p-2 my-1 dark:text-gray-200 bg-white dark:bg-gray-800 rounded-lg shadow-md"
    >
      <CreateTaskModal
        :show="showModal"
        :users="users"
        :project="project"
        :projects="projects"
        :statuses="statuses"
        :priorities="priorities"
        @close="closeModal"
      />
      <button
        @click="openModal"
        class="text-blue-500 dark:text-blue-400 hover:underline"
      >
        Create Task
      </button>

      <section class="px-2 pt-1">
        <button @click="toggleFilters" class="btn btn-primary">
          Toggle Filters
        </button>

        <transition name="slide-down">
          <section v-if="filtersVisible" class="p-2 m-1 bg-zinc-700 rounded-lg">
            <!-- Search Filter -->
            <div class="mb-4">
              <input
                v-model="form.search"
                @input="applyFilters"
                type="text"
                placeholder="Search by name..."
                class="w-full border rounded-lg p-2"
              />
            </div>
          </section>
        </transition>
      </section>
      <ul class="my-2 space-y-4">
        <li
          v-for="task in tasks"
          :key="task.id"
          class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between items-center"
        >
          <div>
            <Link
              :href="
                route('tasks.show', {
                  project: project ? project.slug : task.project.slug,
                  task: task.id,
                })
              "
              class="text-blue-500 dark:text-blue-400 hover:underline"
            >
              {{ task.name }} - {{ project ? project.name : task.project.name }}
            </Link>
            <p>Status: {{ task.status.name }}</p>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
              Created at: {{ new Date(task.created_at).toLocaleString() }}
            </div>
          </div>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            Task ID: {{ task.id }}
          </div>
        </li>
      </ul>
    </div>
  </AppLayout>
</template>
