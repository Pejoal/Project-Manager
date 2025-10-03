<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArcElement, BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js';
import { computed, ref, watch } from 'vue';
import { BarChart, DoughnutChart } from 'vue-chart-3';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend, ArcElement);

const props = defineProps({
  users: Array,
  projects: Array,
  reportData: Object,
  filters: Object,
  canExport: Boolean,
});

const form = useForm({
  period: props.filters.period || 'monthly',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  user_ids: props.filters.user_ids || [],
  project_ids: props.filters.project_ids || [],
  report_type: props.filters.report_type || 'summary',
});

const isLoading = ref(false);
const showFilters = ref(false);

const reportTypes = [
  { value: 'summary', label: 'Summary' },
  { value: 'detailed', label: 'Detailed' },
  { value: 'by_employee', label: 'By Employee' },
  { value: 'by_project', label: 'By Project' },
];

const periods = [
  { value: 'weekly', label: 'This Week' },
  { value: 'monthly', label: 'This Month' },
  { value: 'quarterly', label: 'This Quarter' },
  { value: 'yearly', label: 'This Year' },
  { value: 'custom', label: 'Custom Range' },
];

const isCustomPeriod = computed(() => form.period === 'custom');

const chartData = computed(() => {
  if (!props.reportData?.data) return null;

  switch (props.filters.report_type) {
    case 'by_employee':
      return {
        labels: props.reportData.data.map((item) => item.employee_name),
        datasets: [
          {
            label: 'Total Hours',
            data: props.reportData.data.map((item) => item.total_hours),
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1,
          },
          {
            label: 'Gross Pay (€)',
            data: props.reportData.data.map((item) => item.gross_pay),
            backgroundColor: 'rgba(16, 185, 129, 0.5)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 1,
            yAxisID: 'y1',
          },
        ],
      };

    case 'by_project':
      return {
        labels: props.reportData.data.map((item) => item.project_name),
        datasets: [
          {
            label: 'Total Hours',
            data: props.reportData.data.map((item) => item.total_hours),
            backgroundColor: 'rgba(139, 92, 246, 0.5)',
            borderColor: 'rgba(139, 92, 246, 1)',
            borderWidth: 1,
          },
        ],
      };

    default:
      return null;
  }
});

const chartOptions = computed(() => ({
  responsive: true,
  plugins: {
    legend: {
      position: 'top',
    },
    title: {
      display: true,
      text: `Payroll Report - ${props.filters.report_type.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase())}`,
    },
  },
  scales: {
    y: {
      type: 'linear',
      display: true,
      position: 'left',
    },
    y1: {
      type: 'linear',
      display: true,
      position: 'right',
      grid: {
        drawOnChartArea: false,
      },
    },
  },
}));

const summaryChartData = computed(() => {
  if (!props.reportData?.summary) return null;

  return {
    labels: ['Regular Hours', 'Overtime Hours'],
    datasets: [
      {
        data: [props.reportData.summary.regular_hours || 0, props.reportData.summary.overtime_hours || 0],
        backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(249, 115, 22, 0.8)'],
        borderColor: ['rgba(59, 130, 246, 1)', 'rgba(249, 115, 22, 1)'],
        borderWidth: 1,
      },
    ],
  };
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR',
  }).format(amount);
};

const formatHours = (hours) => {
  return `${parseFloat(hours).toFixed(2)} hrs`;
};

const generateReport = () => {
  isLoading.value = true;
  router.get(route('payroll.reports'), form.data(), {
    preserveState: true,
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

const exportReport = (format) => {
  const exportForm = { ...form.data(), export: format };

  router.get(route('payroll.reports.export'), exportForm, {
    onStart: () => {
      isLoading.value = true;
    },
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

const toggleUserSelection = (userId) => {
  const index = form.user_ids.indexOf(userId);
  if (index > -1) {
    form.user_ids.splice(index, 1);
  } else {
    form.user_ids.push(userId);
  }
};

const toggleProjectSelection = (projectId) => {
  const index = form.project_ids.indexOf(projectId);
  if (index > -1) {
    form.project_ids.splice(index, 1);
  } else {
    form.project_ids.push(projectId);
  }
};

const clearFilters = () => {
  form.reset();
  form.period = 'monthly';
  form.report_type = 'summary';
  generateReport();
};

// Auto-generate report when filters change
watch(
  [() => form.period, () => form.report_type],
  () => {
    if (!isCustomPeriod.value) {
      form.start_date = '';
      form.end_date = '';
    }
    generateReport();
  },
  { deep: true }
);
</script>

<template>
  <Head :title="trans('payroll.reports.title')" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ trans('payroll.reports.title') }}
          </h2>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ trans('payroll.reports.description') }}
          </p>
        </div>

        <div class="flex space-x-3">
          <SecondaryButton @click="showFilters = !showFilters">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
              />
            </svg>
            {{ trans('words.filters') }}
          </SecondaryButton>

          <PrimaryButton @click="generateReport" :disabled="isLoading">
            <svg
              v-if="isLoading"
              class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            {{ trans('payroll.reports.generate') }}
          </PrimaryButton>
        </div>
      </div>

      <!-- Filters Panel -->
      <div v-if="showFilters" class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ trans('payroll.reports.filters') }}
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Period Selection -->
            <div>
              <InputLabel for="period" :value="trans('payroll.reports.period')" />
              <select
                id="period"
                v-model="form.period"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option v-for="period in periods" :key="period.value" :value="period.value">
                  {{ period.label }}
                </option>
              </select>
              <InputError :message="form.errors.period" class="mt-2" />
            </div>

            <!-- Report Type -->
            <div>
              <InputLabel for="report_type" :value="trans('payroll.reports.type')" />
              <select
                id="report_type"
                v-model="form.report_type"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option v-for="type in reportTypes" :key="type.value" :value="type.value">
                  {{ type.label }}
                </option>
              </select>
              <InputError :message="form.errors.report_type" class="mt-2" />
            </div>

            <!-- Custom Date Range -->
            <div v-if="isCustomPeriod">
              <InputLabel for="start_date" :value="trans('payroll.reports.start_date')" />
              <TextInput id="start_date" v-model="form.start_date" type="date" class="mt-1 block w-full" required />
              <InputError :message="form.errors.start_date" class="mt-2" />
            </div>

            <div v-if="isCustomPeriod">
              <InputLabel for="end_date" :value="trans('payroll.reports.end_date')" />
              <TextInput id="end_date" v-model="form.end_date" type="date" class="mt-1 block w-full" required />
              <InputError :message="form.errors.end_date" class="mt-2" />
            </div>
          </div>

          <!-- Employee Filter -->
          <div class="mt-6">
            <InputLabel :value="trans('payroll.reports.employees')" />
            <div class="mt-3 flex flex-wrap gap-2">
              <button
                v-for="user in users"
                :key="user.id"
                type="button"
                @click="toggleUserSelection(user.id)"
                :class="[
                  'px-3 py-1 rounded-full text-sm font-medium transition',
                  form.user_ids.includes(user.id)
                    ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200'
                    : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600',
                ]"
              >
                {{ user.name }}
              </button>
            </div>
          </div>

          <!-- Project Filter -->
          <div class="mt-6">
            <InputLabel :value="trans('payroll.reports.projects')" />
            <div class="mt-3 flex flex-wrap gap-2">
              <button
                v-for="project in projects"
                :key="project.id"
                type="button"
                @click="toggleProjectSelection(project.id)"
                :class="[
                  'px-3 py-1 rounded-full text-sm font-medium transition',
                  form.project_ids.includes(project.id)
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600',
                ]"
              >
                {{ project.name }}
              </button>
            </div>
          </div>

          <!-- Filter Actions -->
          <div class="mt-6 flex justify-end space-x-3">
            <SecondaryButton @click="clearFilters">
              {{ trans('words.clear') }}
            </SecondaryButton>
            <PrimaryButton @click="generateReport" :disabled="isLoading">
              {{ trans('payroll.reports.apply_filters') }}
            </PrimaryButton>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div v-if="reportData?.summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ trans('payroll.reports.total_hours') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ formatHours(reportData.summary.total_hours) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ trans('payroll.reports.total_payroll') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ formatCurrency(reportData.summary.total_payroll) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ trans('payroll.reports.total_employees') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ reportData.summary.total_employees }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ trans('payroll.reports.avg_hourly_rate') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ formatCurrency(reportData.summary.average_hourly_rate) }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Chart -->
        <div v-if="chartData" class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              {{ trans('payroll.reports.chart_title') }}
            </h3>
            <BarChart :chart-data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Hours Breakdown -->
        <div v-if="summaryChartData" class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
              {{ trans('payroll.reports.hours_breakdown') }}
            </h3>
            <DoughnutChart :chart-data="summaryChartData" />
          </div>
        </div>
      </div>

      <!-- Report Data Table -->
      <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              {{ trans('payroll.reports.report_data') }}
            </h3>

            <div v-if="canExport" class="flex space-x-2">
              <SecondaryButton @click="exportReport('pdf')" :disabled="isLoading">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
                {{ trans('words.export_pdf') }}
              </SecondaryButton>
              <SecondaryButton @click="exportReport('excel')" :disabled="isLoading">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
                {{ trans('words.export_excel') }}
              </SecondaryButton>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr v-if="form.report_type === 'summary'">
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.metric') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.value') }}
                </th>
              </tr>

              <tr v-else-if="form.report_type === 'by_employee'">
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('words.employee') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.total_hours') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.gross_pay') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.net_pay') }}
                </th>
              </tr>

              <tr v-else-if="form.report_type === 'by_project'">
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('words.project') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.total_hours') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.employees_count') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.gross_pay') }}
                </th>
              </tr>

              <tr v-else-if="form.report_type === 'detailed'">
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('words.employee') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('words.project') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('words.date') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.hours') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                >
                  {{ trans('payroll.reports.gross_amount') }}
                </th>
              </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <!-- Summary Report -->
              <tr v-if="form.report_type === 'summary'" v-for="item in reportData?.data" :key="item.metric">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  {{ item.metric }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ item.formatted_value }}
                </td>
              </tr>

              <!-- By Employee Report -->
              <tr
                v-else-if="form.report_type === 'by_employee'"
                v-for="employee in reportData?.data"
                :key="employee.employee_id"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.employee_name }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ employee.employee_code }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatHours(employee.total_hours) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatCurrency(employee.gross_pay) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatCurrency(employee.net_pay) }}
                </td>
              </tr>

              <!-- By Project Report -->
              <tr
                v-else-if="form.report_type === 'by_project'"
                v-for="project in reportData?.data"
                :key="project.project_id"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  {{ project.project_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatHours(project.total_hours) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ project.employees_count }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatCurrency(project.gross_pay) }}
                </td>
              </tr>

              <!-- Detailed Report -->
              <tr v-else-if="form.report_type === 'detailed'" v-for="entry in reportData?.data" :key="entry.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                  {{ entry.employee_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ entry.project_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ entry.date }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatHours(entry.hours_worked) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatCurrency(entry.gross_amount) }}
                </td>
              </tr>

              <!-- No Data -->
              <tr v-if="!reportData?.data || reportData.data.length === 0">
                <td
                  :colspan="form.report_type === 'detailed' ? 5 : 4"
                  class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                >
                  {{ trans('payroll.reports.no_data') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
