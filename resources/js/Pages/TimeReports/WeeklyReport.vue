<template>
  <AppLayout title="Weekly Time Report">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Weekly Time Report</h2>
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
        <!-- Week Navigation -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  Week of {{ formatDate(report.start_date) }} - {{ formatDate(report.end_date) }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ report.project.name }}</p>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="navigateWeek(-1)"
                  class="p-2 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  ← Previous Week
                </button>
                <button
                  @click="navigateWeek(1)"
                  class="p-2 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  Next Week →
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Report Overview -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ report.total_hours }}h</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Hours</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                  {{ report.breakdown.work_logs?.total_hours?.toFixed(2) || 0 }}h
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Work Log Hours</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                  {{ report.breakdown.time_entries?.total_hours?.toFixed(2) || 0 }}h
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Time Entry Hours</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Daily Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Daily Breakdown</h3>
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
              <div
                v-for="day in getDailyBreakdown()"
                :key="day.date"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center"
              >
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ day.dayName }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(day.date) }}</div>
                <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-2">{{ day.hours }}h</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Work Type Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
          v-if="report.breakdown.work_logs?.by_work_type"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Work Type Breakdown</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div
                v-for="(hours, workType) in report.breakdown.work_logs.by_work_type"
                :key="workType"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
              >
                <div class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{ workType }}</div>
                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ hours }}h</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Task Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-200 dark:border-gray-700"
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
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                  <tr v-for="task in getTaskBreakdown()" :key="task.name">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ task.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                      {{ task.workLogHours?.toFixed(2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                      {{ task.timeEntryHours?.toFixed(2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ task.totalHours?.toFixed(2) }}h
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
  report: Object,
  projects: Array,
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString();
};

const getDailyBreakdown = () => {
  const days = [];
  const start = new Date(props.report.start_date);
  const end = new Date(props.report.end_date);

  for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
    const dateStr = d.toISOString().split('T')[0];
    const workLogHours = props.report.breakdown.work_logs?.by_date?.[dateStr] || 0;
    const timeEntryHours = props.report.breakdown.time_entries?.by_date?.[dateStr] || 0;

    days.push({
      date: dateStr,
      dayName: d.toLocaleDateString('en', { weekday: 'short' }),
      hours: (workLogHours + timeEntryHours).toFixed(2),
    });
  }

  return days;
};

const getTaskBreakdown = () => {
  const tasks = new Map();

  // Process work logs
  if (props.report.breakdown.work_logs?.by_task) {
    Object.values(props.report.breakdown.work_logs.by_task).forEach((task) => {
      tasks.set(task.task_name, {
        name: task.task_name,
        workLogHours: task.hours,
        timeEntryHours: 0,
        totalHours: task.hours,
      });
    });
  }

  // Process time entries
  if (props.report.breakdown.time_entries?.by_task) {
    Object.values(props.report.breakdown.time_entries.by_task).forEach((task) => {
      if (tasks.has(task.task_name)) {
        const existing = tasks.get(task.task_name);
        existing.timeEntryHours = task.hours;
        existing.totalHours = existing.workLogHours + task.hours;
      } else {
        tasks.set(task.task_name, {
          name: task.task_name,
          workLogHours: 0,
          timeEntryHours: task.hours,
          totalHours: task.hours,
        });
      }
    });
  }

  return Array.from(tasks.values());
};

const navigateWeek = (direction) => {
  const currentStart = new Date(props.report.start_date);
  const newStart = new Date(currentStart);
  newStart.setDate(currentStart.getDate() + direction * 7);

  router.get(route('time-reports.weekly'), {
    project_id: props.report.project_id,
    week_start: newStart.toISOString().split('T')[0],
  });
};

const exportReport = (format) => {
  router.post(route('time-reports.export', props.report.id), { format });
};

const goBack = () => {
  router.visit(route('time-reports.index'));
};
</script>
