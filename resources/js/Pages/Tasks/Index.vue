<script setup>
import { defineProps, ref, computed, watch } from 'vue';
import { Link, Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTaskModal from './CreateTaskModal.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  users: Array,
  tasks: Object,
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

const filtersVisible = ref(true);

const toggleFilters = () => {
  filtersVisible.value = !filtersVisible.value;
};

const form = useForm({
  search: '',
  perPage: 5,
  status: [],
  priority: [],
});

const applyFilters = () => {
  if (props.project) {
    form.get(route('tasks.index', props.project.slug), {
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

const fetchPage = (url) => {
  form.get(url, {
    preserveState: true,
    preserveScroll: true,
  });
};

watch(
  () => [form.status, form.priority],
  () => {
    applyFilters();
  },
  { deep: true }
);
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

      <section class="dark:bg-gray-900 bg-slate-200 p-2 rounded">
        <PrimaryButton @click="toggleFilters"> Toggle Filters </PrimaryButton>

        <transition name="slide-down">
          <main v-if="filtersVisible" class="p-2 m-1 dark:bg-gray-700 bg-slate-100 rounded-lg">
            <!-- Search Filter -->
            <section class="mb-4">
              <input
                v-model="form.search"
                @input="applyFilters"
                type="text"
                placeholder="Search by name..."
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>
            <!-- Status Filter -->
            <section class="mb-4">
              <vSelect
                v-model="form.status"
                :options="props.statuses"
                :reduce="(status) => status.id"
                label="name"
                multiple
                placeholder="Select status"
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>
            <!-- Priority Filter -->
            <section class="mb-4">
              <vSelect
                v-model="form.priority"
                :options="props.priorities"
                :reduce="(priority) => priority.id"
                label="name"
                multiple
                placeholder="Select priority"
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>
          </main>
        </transition>
      </section>
      <ul class="my-2 space-y-4">
        <li
          v-for="task in tasks.data"
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
            <div class="space-y-1">
              <p>
                Status:
                <span class="p-1" :style="{ color: task.status.color }">{{
                  task.status.name
                }}</span>
              </p>
              <p>
                Priority:
                <span class="p-1" :style="{ color: task.priority.color }">{{
                  task.priority.name
                }}</span>
              </p>
            </div>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
              Created at: {{ new Date(task.created_at).toLocaleString() }}
            </div>
          </div>
          <div class="text-gray-500 dark:text-gray-400 text-sm">
            Task ID: {{ task.id }}
          </div>
        </li>
      </ul>
      <!-- Pagination Controls -->
      <section class="flex items-center justify-between my-2">
        <button
          v-if="props.tasks.prev_page_url"
          @click="fetchPage(props.tasks.prev_page_url)"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg"
        >
          Previous
        </button>
        <span class="mx-2"
          >{{ props.tasks.current_page }} / {{ props.tasks.last_page }}</span
        >
        <span class="mx-2">Total: {{ props.tasks.total }}</span>
        <button
          v-if="props.tasks.next_page_url"
          @click="fetchPage(props.tasks.next_page_url)"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg"
        >
          Next
        </button>
      </section>
    </div>
  </AppLayout>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: max-height 0.3s ease-in-out;
}

.slide-down-enter-from,
.slide-down-leave-to {
  max-height: 0;
  overflow: hidden;
}

.slide-down-enter-to,
.slide-down-leave-from {
  max-height: 50rem;
  overflow: auto;
}
</style>
