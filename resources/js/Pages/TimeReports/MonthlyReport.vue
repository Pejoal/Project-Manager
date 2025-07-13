<template>
  <AppLayout title="Monthly Time Report">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Monthly Time Report</h2>
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
        <!-- Month Navigation -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ getMonthName(report.start_date) }} {{ new Date(report.start_date).getFullYear() }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ report.project.name }}</p>
              </div>
              <div class="flex space-x-2">
                <button
                  @click="navigateMonth(-1)"
                  class="p-2 text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  ← Previous Month
                </button>
                <button
                  @click="navigateMonth(1)"
                  class="p-2 text-white border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  Next Month →
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ report.total_hours }}h</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Hours</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                  {{ report.breakdown.work_logs?.total_hours || 0 }}h
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Work Log Hours</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                  {{ report.breakdown.time_entries?.total_hours?.toFixed(2) || 0 }}h
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Time Entry Hours</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                  {{ getWorkingDays() }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Working Days</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Monthly Calendar View -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Monthly Calendar</h3>
            <div class="grid grid-cols-7 gap-2">
              <!-- Day headers -->
              <div
                v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                :key="day"
                class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2"
              >
                {{ day }}
              </div>

              <!-- Calendar days -->
              <div
                v-for="day in getCalendarDays()"
                :key="day.date"
                :class="[
                  'p-2 text-center border rounded-lg',
                  day.isCurrentMonth
                    ? 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600'
                    : 'bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 text-gray-400 dark:text-gray-500',
                  day.hours > 0 ? 'ring-2 ring-blue-500' : '',
                ]"
              >
                <div class="text-sm font-medium">{{ day.dayNumber }}</div>
                <div v-if="day.hours > 0" class="text-xs text-blue-600 dark:text-blue-400 font-bold">
                  {{ day.hours }}h
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Weekly Breakdown -->
        <div
          class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6 border border-gray-200 dark:border-gray-700"
        >
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Weekly Breakdown</h3>
            <div class="space-y-4">
              <div
                v-for="week in getWeeklyBreakdown()"
                :key="week.weekNumber"
                class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
              >
                <div class="flex justify-between items-center">
                  <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">Week {{ week.weekNumber }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ week.startDate }} - {{ week.endDate }}
                    </div>
                  </div>
                  <div class="text-right">
                    <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ week.totalHours }}h</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Avg: {{ week.averageHours }}h/day</div>
                  </div>
                </div>
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
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ ((hours / report.total_hours) * 100).toFixed(1) }}%
                </div>
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
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                      Percentage
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
                      {{ task.totalHours.toFixed(2) }}h
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ task.percentage }}%
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

const getMonthName = (date) => {
  return new Date(date).toLocaleDateString('en', { month: 'long' });
};

const getWorkingDays = () => {
  const workingDays = Object.keys(props.report.breakdown.work_logs?.by_date || {}).length;
  const timeEntryDays = Object.keys(props.report.breakdown.time_entries?.by_date || {}).length;
  return Math.max(workingDays, timeEntryDays);
};

const getCalendarDays = () => {
  const days = [];
  const startDate = new Date(props.report.start_date);
  const endDate = new Date(props.report.end_date);

  // Get first day of month and adjust to start of calendar week
  const firstDay = new Date(startDate.getFullYear(), startDate.getMonth(), 1);
  const calendarStart = new Date(firstDay);
  calendarStart.setDate(firstDay.getDate() - firstDay.getDay());

  // Get last day of month and adjust to end of calendar week
  const lastDay = new Date(endDate.getFullYear(), endDate.getMonth() + 1, 0);
  const calendarEnd = new Date(lastDay);
  calendarEnd.setDate(lastDay.getDate() + (6 - lastDay.getDay()));

  for (let d = new Date(calendarStart); d <= calendarEnd; d.setDate(d.getDate() + 1)) {
    const dateStr = d.toISOString().split('T')[0];
    const isCurrentMonth = d.getMonth() === startDate.getMonth();
    const workLogHours = props.report.breakdown.work_logs?.by_date?.[dateStr] || 0;
    const timeEntryHours = props.report.breakdown.time_entries?.by_date?.[dateStr] || 0;
    const totalHours = workLogHours + timeEntryHours;

    days.push({
      date: dateStr,
      dayNumber: d.getDate(),
      isCurrentMonth,
      hours: totalHours > 0 ? totalHours.toFixed(1) : 0,
    });
  }

  return days;
};

const getWeeklyBreakdown = () => {
  const weeks = [];
  const startDate = new Date(props.report.start_date);
  const endDate = new Date(props.report.end_date);

  let currentWeekStart = new Date(startDate);
  currentWeekStart.setDate(startDate.getDate() - startDate.getDay());

  let weekNumber = 1;

  while (currentWeekStart <= endDate) {
    const weekEnd = new Date(currentWeekStart);
    weekEnd.setDate(currentWeekStart.getDate() + 6);

    // Calculate hours for this week
    let weekHours = 0;
    let workingDays = 0;

    for (let d = new Date(currentWeekStart); d <= weekEnd; d.setDate(d.getDate() + 1)) {
      const dateStr = d.toISOString().split('T')[0];
      const workLogHours = props.report.breakdown.work_logs?.by_date?.[dateStr] || 0;
      const timeEntryHours = props.report.breakdown.time_entries?.by_date?.[dateStr] || 0;
      const dayTotal = workLogHours + timeEntryHours;

      if (dayTotal > 0) {
        weekHours += dayTotal;
        workingDays++;
      }
    }

    weeks.push({
      weekNumber,
      startDate: currentWeekStart.toLocaleDateString(),
      endDate: weekEnd.toLocaleDateString(),
      totalHours: weekHours.toFixed(1),
      averageHours: workingDays > 0 ? (weekHours / workingDays).toFixed(1) : '0.0',
    });

    currentWeekStart.setDate(currentWeekStart.getDate() + 7);
    weekNumber++;
  }

  return weeks;
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

  // Add percentage calculation
  return Array.from(tasks.values()).map((task) => ({
    ...task,
    percentage: ((task.totalHours / props.report.total_hours) * 100).toFixed(1),
  }));
};

const navigateMonth = (direction) => {
  const currentDate = new Date(props.report.start_date);
  const newDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + direction, 1);

  router.get(route('time-reports.monthly'), {
    project_id: props.report.project_id,
    month: newDate.getMonth() + 1,
    year: newDate.getFullYear(),
  });
};

const exportReport = (format) => {
  router.post(route('time-reports.export', props.report.id), { format });
};

const goBack = () => {
  router.visit(route('time-reports.index'));
};
</script>
