<template>
  <AppLayout title="Work Logs">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Work Logs</h2>
    </template>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Today's Summary -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Today's Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ todaysSummary.total_hours }}h</div>
                <div class="text-sm text-blue-500 dark:text-blue-400">Total Hours</div>
              </div>
              <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg border border-green-100 dark:border-green-800">
                <div class="text-2xl font-bold text-green-600 dark:text-green-300">
                  {{ todaysSummary.total_entries }}
                </div>
                <div class="text-sm text-green-500 dark:text-green-400">Entries</div>
              </div>
              <div
                class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg border border-purple-100 dark:border-purple-800"
              >
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-300">{{ getMostWorkedType() }}</div>
                <div class="text-sm text-purple-500 dark:text-purple-400">Most Worked</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Add Work Log -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add Work Log</h3>
              <button
                @click="showAddModal = true"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
              >
                Add Work Log
              </button>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Type</label>
                <select
                  v-model="filters.work_type"
                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                >
                  <option value="">All Types</option>
                  <option v-for="(label, key) in workTypes" :key="key" :value="key">
                    {{ label }}
                  </option>
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
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                <input
                  type="date"
                  v-model="filters.date_to"
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

        <!-- Work Logs List -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Work Logs</h3>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Date
                    </th>
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
                      Hours
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Work Type
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                  <tr v-for="log in workLogs.data" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">{{ formatDate(log.date) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ log.task.name }}</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">{{ log.description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">{{ log.project.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">{{ log.hours_worked }}h</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200"
                      >
                        {{ workTypes[log.work_type] }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        @click="editLog(log)"
                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteLog(log)"
                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Work Log Modal -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50"
    >
      <div
        class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-600 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800"
      >
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add Work Log</h3>
          <form @submit.prevent="addWorkLog">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Task</label>
              <select
                v-model="addForm.task_id"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Select a task</option>
                <option v-for="task in tasks" :key="task.id" :value="task.id">
                  {{ task.name }} - {{ task.project.name }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
              <input
                type="date"
                v-model="addForm.date"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hours Worked</label>
              <input
                type="number"
                v-model="addForm.hours_worked"
                step="0.1"
                min="0.1"
                max="24"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Type</label>
              <select
                v-model="addForm.work_type"
                required
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
              >
                <option value="">Select work type</option>
                <option v-for="(label, key) in workTypes" :key="key" :value="key">
                  {{ label }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
              <textarea
                v-model="addForm.description"
                rows="3"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                placeholder="What did you work on?"
              ></textarea>
            </div>
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="showAddModal = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-500 dark:bg-blue-600 rounded-md hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors"
              >
                Add Work Log
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  workLogs: Object,
  projects: Array,
  tasks: Array,
  workTypes: Object,
  todaysSummary: Object,
  filters: Object,
});

const showAddModal = ref(false);

const addForm = reactive({
  task_id: '',
  date: new Date().toISOString().split('T')[0],
  hours_worked: '',
  work_type: '',
  description: '',
});

const filters = reactive({
  project_id: props.filters.project_id || '',
  task_id: props.filters.task_id || '',
  work_type: props.filters.work_type || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
});

const getMostWorkedType = () => {
  if (!props.todaysSummary.by_work_type) return 'None';

  const types = Object.entries(props.todaysSummary.by_work_type);
  if (types.length === 0) return 'None';

  const mostWorked = types.reduce((a, b) => (a[1] > b[1] ? a : b));
  return props.workTypes[mostWorked[0]] || 'None';
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

const addWorkLog = () => {
  router.post(route('work-logs.store'), addForm, {
    onSuccess: () => {
      showAddModal.value = false;
      Object.keys(addForm).forEach((key) => {
        if (key === 'date') {
          addForm[key] = new Date().toISOString().split('T')[0];
        } else {
          addForm[key] = '';
        }
      });
    },
  });
};

const editLog = (log) => {
  // Implementation for editing log
  console.log('Edit log:', log);
};

const deleteLog = (log) => {
  if (confirm('Are you sure you want to delete this work log?')) {
    router.delete(route('work-logs.destroy', log.id));
  }
};

const applyFilters = () => {
  router.get(route('work-logs.index'), filters, {
    preserveState: true,
  });
};

const clearFilters = () => {
  Object.keys(filters).forEach((key) => {
    filters[key] = '';
  });
  applyFilters();
};
</script>
