<template>
  <AppLayout title="Time Reports Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Time Reports Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Period Info -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                Period: {{ formatDate(period.start) }} to {{ formatDate(period.end) }}
              </h3>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
              <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Hours</h4>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                  {{ summary.total_hours.toFixed(2) }}
                </p>
              </div>

              <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-green-600 dark:text-green-400">Work Log Hours</h4>
                <p class="text-3xl font-bold text-green-900 dark:text-green-100">
                  {{ summary.work_log_hours.toFixed(2) }}
                </p>
              </div>

              <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-purple-600 dark:text-purple-400">Time Entry Hours</h4>
                <p class="text-3xl font-bold text-purple-900 dark:text-purple-100">
                  {{ summary.time_entry_hours.toFixed(2) }}
                </p>
              </div>

              <div class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-lg">
                <h4 class="text-sm font-medium text-orange-600 dark:text-orange-400">Total Entries</h4>
                <p class="text-3xl font-bold text-orange-900 dark:text-orange-100">
                  {{ summary.total_entries }}
                </p>
              </div>
            </div>

            <!-- Project Breakdown -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Project Breakdown</h3>
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Project
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Work Log Hours
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Time Entry Hours
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Total Hours
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr v-for="project in summary.project_breakdown" :key="project.project_id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ project.project_name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ project.work_log_hours.toFixed(2) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ project.time_entry_hours.toFixed(2) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ project.total_hours.toFixed(2) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Work Type Breakdown -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Work Type Breakdown</h3>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div
                  v-for="(hours, type) in summary.work_type_breakdown"
                  :key="type"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 capitalize">
                    {{ type }}
                  </h4>
                  <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ hours.toFixed(2) }}h</p>
                </div>
              </div>
            </div>

            <!-- Recent Work Logs -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Work Logs</h3>
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
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
                        Project
                      </th>
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
                        Work Type
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr v-for="log in summary.recent_work_logs" :key="log.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ formatDate(log.date) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ log.project.name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ log.task.name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ log.hours_worked }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 capitalize">
                        {{ log.work_type }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Recent Time Entries -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Time Entries</h3>
              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Start Time
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        End Time
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Project
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Task
                      </th>
                      <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                      >
                        Duration
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr v-for="entry in summary.recent_time_entries" :key="entry.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ formatDateTime(entry.start_time) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ formatDateTime(entry.end_time) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ entry.project.name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                        {{ entry.task.name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ entry.duration_minutes }}m
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8 flex justify-end">
              <Link
                :href="route('time-reports.index')"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Back to Time Reports
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  summary: Object,
  period: Object,
});

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

const formatDateTime = (dateTimeString) => {
  return new Date(dateTimeString).toLocaleString();
};
</script>
