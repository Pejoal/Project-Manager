<script setup>
import { defineProps, ref, watch } from 'vue';
import { Link, Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTaskModal from './CreateTaskModal.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';

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

const filtersVisible = ref(true);

const toggleFilters = () => {
  filtersVisible.value = !filtersVisible.value;
};

const form = useForm({
  assigned_to_me: false,
  search: '',
  perPage: 10,
  status: [],
  priority: [],
  assigned_to: [],
  projects: [],
});

watch(
  () => [form.status, form.priority, form.assigned_to, form.projects],
  () => {
    applyFilters();
  },
  { deep: true }
);

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
</script>

<template>
  <Head title="Tasks" />
  <AppLayout>
    <template #header>
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        <section v-if="project">
          Tasks for
          <Link
            :href="route('projects.show', { project: project.slug })"
            class="text-blue-500 dark:text-blue-400 hover:underline"
          >
            {{ project.name }}
          </Link>
        </section>
        <p v-else>All Tasks</p>
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
          <main
            v-if="filtersVisible"
            class="p-2 m-1 dark:bg-gray-700 bg-slate-100 rounded-lg space-y-2"
          >
            <!-- Search Filter -->
            <section>
              <input
                v-model="form.search"
                @input="applyFilters"
                type="text"
                placeholder="Search by name..."
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>

            <!-- Per Page Filter -->
            <section class="flex items-center">
              <select
                v-model="form.perPage"
                id="perPage"
                @change="applyFilters"
                class="rounded-lg text-black"
              >
                <option :value="5">5</option>
                <option :value="10">10</option>
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>

              <label for="perPage" class="font-bold"> Items Per Page </label>
            </section>

            <!-- Status Filter -->
            <section>
              <vSelect
                v-model="form.status"
                :options="props.statuses"
                :reduce="(status) => status.id"
                label="name"
                multiple
                placeholder="Select Status"
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>
            <!-- Priority Filter -->
            <section>
              <vSelect
                v-model="form.priority"
                :options="props.priorities"
                :reduce="(priority) => priority.id"
                label="name"
                multiple
                placeholder="Select Priority"
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>

            <!-- Assigned to Filter -->
            <section>
              <vSelect
                v-model="form.assigned_to"
                :options="props.users"
                :reduce="(user) => user.id"
                label="name"
                multiple
                placeholder="Select Assigned to"
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>

            <!-- Projects Filter -->
            <section v-if="!props.project">
              <vSelect
                v-model="form.projects"
                :options="props.projects"
                :reduce="(project) => project.id"
                label="name"
                multiple
                placeholder="Select Projects"
                class="w-full text-zinc-900 border rounded-lg p-2"
              />
            </section>

            <!-- Assigned to me Filter -->
            <section class="flex items-center">
              <InputLabel for="assigned_to_me" value="Assigned to me" />

              <Checkbox
                class="ml-4 w-6 h-6 rounded-lg transition duration-150 ease-in-out"
                @change="applyFilters"
                id="assigned_to_me"
                v-model:checked="form.assigned_to_me"
                name="assigned_to_me"
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
            <p>
              Task:
              <Link
                :href="
                  route('tasks.show', {
                    project: project ? project.slug : task.project.slug,
                    task: task.id,
                  })
                "
                class="text-blue-500 dark:text-blue-400 hover:underline"
                v-html="task.name"
              >
              </Link>
            </p>
            <p v-html="task.description">
            </p>
            <p>
              Project:
              <Link
                :href="
                  route('projects.show', {
                    project: project ? project.slug : task.project.slug,
                  })
                "
                class="text-blue-500 dark:text-blue-400 hover:underline"
              >
                {{ project ? project.name : task.project.name }}
              </Link>
            </p>
            <div class="space-y-1">
              <p>
                Status:
                <span :style="{ color: task.status?.color }">{{
                  task.status?.name
                }}</span>
              </p>
              <p>
                Priority:
                <span :style="{ color: task.priority.color }">{{
                  task.priority.name
                }}</span>
              </p>
              <p class="mb-2 text-gray-700 dark:text-gray-300">
                assigned to:
                <span
                  v-for="(assigned_to_user, index) in task.assigned_to"
                  :key="assigned_to_user.id"
                >
                  {{ assigned_to_user.name
                  }}<span v-if="index < task.assigned_to.length - 1">, </span>
                </span>
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
