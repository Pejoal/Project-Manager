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
        <!-- Report Header -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ timeReport.project.name }} - {{ timeReport.period_display }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                  {{ getPeriodTypeDisplay(timeReport.period_type) }} Report
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                  Generated on {{ formatDateTime(timeReport.created_at) }}
                </p>
              </div>
              <div class="text-right">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ timeReport.total_hours }}h</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Hours</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6 text-center">
              <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ timeReport.breakdown.work_logs?.total_hours || 0 }}h
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Work Log Hours</div>
            </div>
          </div>

          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6 text-center">
              <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                {{ timeReport.breakdown.time_entries?.total_hours?.toFixed(2) || 0 }}h
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Time Entry Hours</div>
            </div>
          </div>

          <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
          >
            <div class="p-6 text-center">
              <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                {{ getTotalEntries() }}
              </div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Total Entries</div>
            </div>
          </div>
        </div>

        <!-- Work Type Breakdown -->
        <div
          v-if="timeReport.breakdown.work_logs?.by_work_type"
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Work Type Breakdown</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div
                v-for="(hours, workType) in timeReport.breakdown.work_logs.by_work_type"
                :key="workType"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
              >
                <div class="font-medium text-gray-900 dark:text-gray-100 capitalize">
                  {{ workType.replace('_', ' ') }}
                </div>
                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ hours }}h</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ ((hours / timeReport.total_hours) * 100).toFixed(1) }}% of total
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Task Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Task Breakdown</h3>
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
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Entries
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                  <tr v-for="task in getTaskBreakdown()" :key="task.name">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ task.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                      {{ task.workLogHours }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                      {{ task.timeEntryHours?.toFixed(2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ task.totalHours?.toFixed(2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ task.totalEntries }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Daily Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daily Breakdown</h3>
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
                  <tr v-for="day in getDailyBreakdown()" :key="day.date">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ formatDate(day.date) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                      {{ day.workLogHours }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                      {{ day.timeEntryHours?.toFixed(2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ day.totalHours?.toFixed(2) }}h
                    </td>
                  </tr>
                </tbody>
              </table>
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString();
};

const getPeriodTypeDisplay = (periodType) => {
  const types = {
    daily: 'Daily',
    weekly: 'Weekly',
    monthly: 'Monthly',
    custom: 'Custom',
  };
  return types[periodType] || 'Custom';
};

const getTotalEntries = () => {
  const workLogEntries = Object.values(props.timeReport.breakdown.work_logs?.by_task || {}).reduce(
    (sum, task) => sum + task.entries,
    0
  );
  const timeEntryEntries = Object.values(props.timeReport.breakdown.time_entries?.by_task || {}).reduce(
    (sum, task) => sum + task.entries,
    0
  );
  return workLogEntries + timeEntryEntries;
};

const getTaskBreakdown = () => {
  const tasks = new Map();

  // Process work logs
  if (props.timeReport.breakdown.work_logs?.by_task) {
    Object.values(props.timeReport.breakdown.work_logs.by_task).forEach((task) => {
      tasks.set(task.task_name, {
        name: task.task_name,
        workLogHours: task.hours,
        timeEntryHours: 0,
        totalHours: task.hours,
        workLogEntries: task.entries,
        timeEntryEntries: 0,
        totalEntries: task.entries,
      });
    });
  }

  // Process time entries
  if (props.timeReport.breakdown.time_entries?.by_task) {
    Object.values(props.timeReport.breakdown.time_entries.by_task).forEach((task) => {
      if (tasks.has(task.task_name)) {
        const existing = tasks.get(task.task_name);
        existing.timeEntryHours = task.hours;
        existing.totalHours = existing.workLogHours + task.hours;
        existing.timeEntryEntries = task.entries;
        existing.totalEntries = existing.workLogEntries + task.entries;
      } else {
        tasks.set(task.task_name, {
          name: task.task_name,
          workLogHours: 0,
          timeEntryHours: task.hours,
          totalHours: task.hours,
          workLogEntries: 0,
          timeEntryEntries: task.entries,
          totalEntries: task.entries,
        });
      }
    });
  }

  return Array.from(tasks.values());
};

const getDailyBreakdown = () => {
  const days = new Map();

  // Process work log dates
  if (props.timeReport.breakdown.work_logs?.by_date) {
    Object.entries(props.timeReport.breakdown.work_logs.by_date).forEach(([date, hours]) => {
      days.set(date, {
        date,
        workLogHours: hours,
        timeEntryHours: 0,
        totalHours: hours,
      });
    });
  }

  // Process time entry dates
  if (props.timeReport.breakdown.time_entries?.by_date) {
    Object.entries(props.timeReport.breakdown.time_entries.by_date).forEach(([date, hours]) => {
      if (days.has(date)) {
        const existing = days.get(date);
        existing.timeEntryHours = hours;
        existing.totalHours = existing.workLogHours + hours;
      } else {
        days.set(date, {
          date,
          workLogHours: 0,
          timeEntryHours: hours,
          totalHours: hours,
        });
      }
    });
  }

  return Array.from(days.values()).sort((a, b) => new Date(a.date) - new Date(b.date));
};

const exportReport = (format) => {
  router.post(route('time-reports.export', props.timeReport.id), { format });
};

const goBack = () => {
  router.visit(route('time-reports.index'));
};
</script>
