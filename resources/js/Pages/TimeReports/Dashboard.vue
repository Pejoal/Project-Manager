<template>
  <AppLayout title="Time Reports Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Time Reports Dashboard</h2>
    </template>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ summary.total_hours }}h</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Total Hours</div>
              <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Last 30 days</div>
            </div>
          </div>

          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ summary.work_log_hours }}h</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Work Log Hours</div>
              <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Manual entries</div>
            </div>
          </div>

          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ summary.time_entry_hours }}h</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Timer Hours</div>
              <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tracked time</div>
            </div>
          </div>

          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6 text-center">
              <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ summary.total_entries }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Total Entries</div>
              <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">All time records</div>
            </div>
          </div>
        </div>

        <!-- Running Timer Alert -->
        <div
          v-if="summary.running_timer"
          class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                ></path>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800 dark:text-green-200">
                Timer is running for: {{ summary.running_timer.task?.name || 'Unknown Task' }}
              </p>
              <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                Project: {{ summary.running_timer.project?.name || 'Unknown Project' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Project Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Project Breakdown</h3>
              <div class="space-y-4">
                <div
                  v-for="project in summary.project_breakdown"
                  :key="project.project_id"
                  class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                  <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ project.project_name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      Work: {{ project.work_log_hours }}h • Timer: {{ project.time_entry_hours }}h
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ project.total_hours }}h</div>
                  </div>
                </div>

                <div
                  v-if="summary.project_breakdown.length === 0"
                  class="text-center py-8 text-gray-500 dark:text-gray-400"
                >
                  No project data available for the selected period
                </div>
              </div>
            </div>
          </div>

          <!-- Work Type Breakdown -->
          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Work Type Breakdown</h3>
              <div class="space-y-4">
                <div
                  v-for="(hours, workType) in summary.work_type_breakdown"
                  :key="workType"
                  class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                  <div class="font-medium text-gray-900 dark:text-gray-100 capitalize">
                    {{ workType.replace('_', ' ') }}
                  </div>
                  <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ hours }}h</div>
                </div>

                <div
                  v-if="Object.keys(summary.work_type_breakdown).length === 0"
                  class="text-center py-8 text-gray-500 dark:text-gray-400"
                >
                  No work type data available
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Work Logs -->
          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Work Logs</h3>
              <div class="space-y-3">
                <div
                  v-for="log in summary.recent_work_logs"
                  :key="log.id"
                  class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                  <div class="flex-1">
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                      {{ log.task?.name || 'Unknown Task' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ log.project?.name || 'Unknown Project' }} • {{ formatDate(log.date) }}
                    </div>
                    <div v-if="log.description" class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                      {{ log.description }}
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ log.hours_worked }}h</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ log.work_type }}</div>
                  </div>
                </div>

                <div
                  v-if="summary.recent_work_logs.length === 0"
                  class="text-center py-8 text-gray-500 dark:text-gray-400"
                >
                  No recent work logs
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Time Entries -->
          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Time Entries</h3>
              <div class="space-y-3">
                <div
                  v-for="entry in summary.recent_time_entries"
                  :key="entry.id"
                  class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                >
                  <div class="flex-1">
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                      {{ entry.task?.name || 'Unknown Task' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ entry.project?.name || 'Unknown Project' }} • {{ formatDateTime(entry.start_time) }}
                    </div>
                    <div v-if="entry.description" class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                      {{ entry.description }}
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm font-bold text-purple-600 dark:text-purple-400">
                      {{ formatDuration(entry.duration_minutes) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ entry.is_running ? 'Running' : 'Completed' }}
                    </div>
                  </div>
                </div>

                <div
                  v-if="summary.recent_time_entries.length === 0"
                  class="text-center py-8 text-gray-500 dark:text-gray-400"
                >
                  No recent time entries
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 text-center">
          <div class="inline-flex space-x-4">
            <button
              @click="goToTimeTracking"
              class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors"
            >
              Start Timer
            </button>
            <button
              @click="goToWorkLogs"
              class="bg-green-500 hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors"
            >
              Log Work
            </button>
            <button
              @click="goToReports"
              class="bg-purple-500 hover:bg-purple-600 dark:bg-purple-600 dark:hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition-colors"
            >
              View Reports
            </button>
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
  summary: Object,
  period: Object,
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString();
};

const formatDuration = (minutes) => {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  if (hours > 0) {
    return `${hours}h ${mins}m`;
  }
  return `${mins}m`;
};

const goToTimeTracking = () => {
  router.visit(route('time-tracking.index'));
};

const goToWorkLogs = () => {
  router.visit(route('work-logs.index'));
};

const goToReports = () => {
  router.visit(route('time-reports.index'));
};
</script>
