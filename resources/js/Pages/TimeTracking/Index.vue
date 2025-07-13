<template>
  <AppLayout title="Time Tracking">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Time Tracking & Productivity</h2>
    </template>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Running Timer Widget -->
        <div
          v-if="runningTimer"
          class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
              <div>
                <h3 class="text-lg font-medium text-green-800 dark:text-green-200">Timer Running</h3>
                <p class="text-sm text-green-600 dark:text-green-300">
                  {{ runningTimer?.task?.name || 'Unknown Task' }} - {{ runningTimer?.project?.name || 'Unknown Project' }}
                </p>
                <p class="text-xs text-green-500 dark:text-green-400 mt-1">
                  Started: {{ formatDateTime(runningTimer.start_time) }}
                </p>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <div class="text-2xl font-bold text-green-800 dark:text-green-200">{{ currentDuration }}</div>
                <div class="text-xs text-green-600 dark:text-green-400">Duration</div>
              </div>
              <button
                @click="stopTimer"
                class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                Stop Timer
              </button>
            </div>
          </div>
        </div>

        <!-- Quick Start Timer -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Quick Start Timer</h3>
              <button
                @click="showStartModal = true"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                Start New Timer
              </button>
            </div>

            <!-- Recent Tasks for Quick Start -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="task in recentTasks"
                :key="task.id"
                class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors bg-gray-50 dark:bg-gray-700"
                @click="quickStartTimer(task.id)"
              >
                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ task.name || 'Unknown Task' }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ task.project?.name || 'Unknown Project' }}</p>
                <div class="mt-2 flex items-center text-xs text-gray-400 dark:text-gray-500">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    ></path>
                  </svg>
                  {{ task.total_tracked_hours }}h tracked
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Project</label>
                <select
                  v-model="filters.project_id"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">All Projects</option>
                  <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Task</label>
                <select
                  v-model="filters.task_id"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">All Tasks</option>
                  <option v-for="task in tasks" :key="task.id" :value="task.id">
                    {{ task.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select
                  v-model="filters.status"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">All</option>
                  <option value="running">Running</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                <input
                  type="date"
                  v-model="filters.date_from"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
            </div>
            <div class="mt-4 flex justify-end">
              <button
                @click="applyFilters"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors mr-2"
              >
                Apply Filters
              </button>
              <button
                @click="clearFilters"
                class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                Clear
              </button>
            </div>
          </div>
        </div>

        <!-- Time Entries List -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Time Entries</h3>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Task
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Project
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Duration
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Start Time
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Status
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                  <tr v-for="entry in timeEntries.data" :key="entry.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ entry?.task?.name || 'Unknown Task' }}</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">{{ entry.description || '' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">{{ entry?.project?.name || 'Unknown Project' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">{{ entry.formatted_duration || '0:00' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">{{ formatDateTime(entry.start_time) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="
                          entry.is_running
                            ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                        "
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      >
                        {{ entry.is_running ? 'Running' : 'Completed' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        v-if="entry.is_running"
                        @click="stopSpecificTimer(entry)"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 mr-2"
                      >
                        Stop
                      </button>
                      <button
                        v-else
                        @click="editEntry(entry)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteEntry(entry)"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-between items-center">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing {{ timeEntries.from }} to {{ timeEntries.to }} of {{ timeEntries.total }} results
              </div>
              <div class="flex space-x-2">
                <button
                  v-for="link in timeEntries.links"
                  :key="link.label"
                  @click="changePage(link.url)"
                  :disabled="!link.url"
                  :class="[
                    'px-3 py-1 text-sm border rounded transition-colors',
                    link.active
                      ? 'bg-blue-500 text-white border-blue-500 dark:bg-blue-600'
                      : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600',
                    !link.url ? 'opacity-50 cursor-not-allowed' : '',
                  ]"
                  v-html="link.label"
                ></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Timer Modal -->
    <div
      v-if="showStartModal"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50"
    >
      <div
        class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-600 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800"
      >
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Start New Timer</h3>
          <form @submit.prevent="startTimer">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Task</label>
              <select
                v-model="startForm.task_id"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Select a task</option>
                <option v-for="task in tasks" :key="task.id" :value="task.id">
                  {{ task.name }} - {{ task.project?.name || 'Unknown Project' }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                >Description (Optional)</label
              >
              <textarea
                v-model="startForm.description"
                rows="3"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                placeholder="What are you working on?"
              ></textarea>
            </div>
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="showStartModal = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-500 dark:bg-blue-600 rounded-md hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors"
              >
                Start Timer
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  timeEntries: Object,
  projects: Array,
  tasks: Array,
  runningTimer: Object,
  filters: Object,
});

const showStartModal = ref(false);
const currentDuration = ref('00:00:00');
const timerInterval = ref(null);

const startForm = reactive({
  task_id: '',
  description: '',
});

const filters = reactive({
  project_id: props.filters.project_id || '',
  task_id: props.filters.task_id || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
});

const recentTasks = computed(() => {
  // Get recent tasks from time entries
  if (!props.timeEntries?.data) return [];
  
  const taskIds = new Set();
  return props.timeEntries.data
    .filter((entry) => {
      if (!entry?.task_id || !entry?.task || taskIds.has(entry.task_id)) return false;
      taskIds.add(entry.task_id);
      return true;
    })
    .slice(0, 6)
    .map((entry) => ({
      id: entry.task_id,
      name: entry.task?.name || 'Unknown Task',
      project: entry.project || { name: 'Unknown Project' },
      total_tracked_hours: '0', // This would be calculated from the backend
    }));
});

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString();
};

const formatDuration = (minutes) => {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  const secs = 0; // For running timers, we'd calculate seconds
  return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

const updateCurrentDuration = () => {
  if (props.runningTimer) {
    const start = new Date(props.runningTimer.start_time);
    const now = new Date();
    const diffMs = now - start;
    const diffMins = Math.floor(diffMs / 60000);
    const diffSecs = Math.floor((diffMs % 60000) / 1000);
    const hours = Math.floor(diffMins / 60);
    const minutes = diffMins % 60;
    currentDuration.value = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${diffSecs.toString().padStart(2, '0')}`;
  }
};

const startTimer = () => {
  router.post(route('time-tracking.start'), startForm, {
    onSuccess: () => {
      showStartModal.value = false;
      startForm.task_id = '';
      startForm.description = '';
    },
  });
};

const quickStartTimer = (taskId) => {
  router.post(route('time-tracking.quick-start'), { task_id: taskId });
};

const stopTimer = () => {
  if (props.runningTimer) {
    router.post(route('time-tracking.stop', props.runningTimer.id));
  }
};

const stopSpecificTimer = (entry) => {
  router.post(route('time-tracking.stop', entry.id));
};

const editEntry = (entry) => {
  // Implementation for editing entry
  console.log('Edit entry:', entry);
};

const deleteEntry = (entry) => {
  if (confirm('Are you sure you want to delete this time entry?')) {
    router.delete(route('time-tracking.destroy', entry.id));
  }
};

const applyFilters = () => {
  router.get(route('time-tracking.index'), filters, {
    preserveState: true,
  });
};

const clearFilters = () => {
  Object.keys(filters).forEach((key) => {
    filters[key] = '';
  });
  applyFilters();
};

const changePage = (url) => {
  if (url) {
    router.visit(url);
  }
};

onMounted(() => {
  updateCurrentDuration();
  timerInterval.value = setInterval(updateCurrentDuration, 1000);
});

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
  }
});
</script>
