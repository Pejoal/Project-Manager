<template>
  <AppLayout title="Time Report Details">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Time Report Details</h2>
        <div class="flex space-x-2">
          <button
            @click="exportReport('csv')"
            class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            Export CSV
          </button>
          <button
            @click="exportReport('json')"
            class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            Export JSON
          </button>
          <button
            @click="goBack"
            class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
          >
            Back to Reports
          </button>
        </div>
      </div>
    </template>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Report Overview -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ timeReport.total_hours }}h</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Hours</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                  {{ timeReport.project.name }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Project</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                  {{ timeReport.period_type }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Period Type</div>
              </div>
              <div class="text-center">
                <div class="text-lg font-bold text-gray-700 dark:text-gray-300">
                  {{ formatDateRange(timeReport.start_date, timeReport.end_date) }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Date Range</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Work Logs Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
          v-if="timeReport.breakdown.work_logs"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Work Logs</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Total Hours</h4>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                  {{ timeReport.breakdown.work_logs.total_hours }}h
                </div>
              </div>

              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Entries</h4>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                  {{
                    Object.values(timeReport.breakdown.work_logs.by_task || {}).reduce(
                      (sum, task) => sum + task.entries,
                      0
                    )
                  }}
                </div>
              </div>
            </div>

            <!-- By Task -->
            <div class="mb-6" v-if="timeReport.breakdown.work_logs.by_task">
              <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">By Task</h4>
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
                        Hours
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Entries
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr v-for="task in timeReport.breakdown.work_logs.by_task" :key="task.task_name">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ task.task_name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ task.hours }}h
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ task.entries }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- By Work Type -->
            <div class="mb-6" v-if="timeReport.breakdown.work_logs.by_work_type">
              <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">By Work Type</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="(hours, workType) in timeReport.breakdown.work_logs.by_work_type"
                  :key="workType"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{ workType }}</div>
                  <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ hours }}h</div>
                </div>
              </div>
            </div>

            <!-- By Date -->
            <div v-if="timeReport.breakdown.work_logs.by_date">
              <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">By Date</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="(hours, date) in timeReport.breakdown.work_logs.by_date"
                  :key="date"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="font-medium text-gray-900 dark:text-gray-100">{{ formatDate(date) }}</div>
                  <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ hours }}h</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Time Entries Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
          v-if="timeReport.breakdown.time_entries"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Time Entries</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Total Hours</h4>
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                  {{ timeReport.breakdown.time_entries.total_hours }}h
                </div>
              </div>

              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Entries</h4>
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                  {{
                    Object.values(timeReport.breakdown.time_entries.by_task || {}).reduce(
                      (sum, task) => sum + task.entries,
                      0
                    )
                  }}
                </div>
              </div>
            </div>

            <!-- By Task -->
            <div class="mb-6" v-if="timeReport.breakdown.time_entries.by_task">
              <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">By Task</h4>
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
                        Hours
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Entries
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr v-for="task in timeReport.breakdown.time_entries.by_task" :key="task.task_name">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ task.task_name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ task.hours }}h
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ task.entries }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- By Date -->
            <div v-if="timeReport.breakdown.time_entries.by_date">
              <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">By Date</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="(hours, date) in timeReport.breakdown.time_entries.by_date"
                  :key="date"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="font-medium text-gray-900 dark:text-gray-100">{{ formatDate(date) }}</div>
                  <div class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ hours }}h</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Report Metadata -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Report Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Generated By</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ timeReport.user.name }}</p>
              </div>

              <div>
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Generated At</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ formatDateTime(timeReport.created_at) }}</p>
              </div>

              <div>
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Period Type</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ timeReport.period_type }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  timeReport: Object,
});

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString();
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

const formatDateRange = (startDate, endDate) => {
  const start = new Date(startDate).toLocaleDateString();
  const end = new Date(endDate).toLocaleDateString();
  return `${start} - ${end}`;
};

const exportReport = (format) => {
  router.post(route('time-reports.export', props.timeReport.id), { format });
};

const goBack = () => {
  router.visit(route('time-reports.index'));
};
</script>
