<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { computed } from 'vue';
import { BarChart, DoughnutChart } from 'vue-chart-3';

Chart.register(...registerables);

const props = defineProps({
  stats: {
    type: Object,
    required: true,
    default: () => ({
      current_month: {
        total_hours: 0,
        total_entries: 0,
        total_payroll: 0,
        total_employees: 0,
      },
      previous_month: {
        total_hours: 0,
        total_entries: 0,
        total_payroll: 0,
        total_employees: 0,
      },
      pending: {
        time_entries: 0,
        payslips: 0,
      },
    }),
  },
  recentTimeEntries: {
    type: Array,
    default: () => [],
  },
  recentPayslips: {
    type: Array,
    default: () => [],
  },
  payrollSettings: {
    type: Object,
    required: true,
    default: () => ({
      currency: 'USD',
      company_name: 'Company',
    }),
  },
});

// Calculate percentage changes
const calculateChange = (current, previous) => {
  if (!previous || previous === 0) return { value: 0, isPositive: true };
  const change = ((current - previous) / previous) * 100;
  return {
    value: Math.abs(change).toFixed(1),
    isPositive: change >= 0,
  };
};

const hoursChange = computed(() =>
  calculateChange(props.stats.current_month.total_hours, props.stats.previous_month.total_hours)
);

const payrollChange = computed(() =>
  calculateChange(props.stats.current_month.total_payroll, props.stats.previous_month.total_payroll)
);

const employeesChange = computed(() =>
  calculateChange(props.stats.current_month.total_employees, props.stats.previous_month.total_employees)
);

const entriesChange = computed(() =>
  calculateChange(props.stats.current_month.total_entries, props.stats.previous_month.total_entries)
);

// Formatting functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: props.payrollSettings?.currency || 'USD',
  }).format(amount || 0);
};

const formatNumber = (number) => {
  return new Intl.NumberFormat('en-US').format(number || 0);
};

const formatHours = (hours) => {
  return `${Number(hours || 0).toFixed(1)} hrs`;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

// Chart data
const payrollComparisonData = computed(() => ({
  labels: ['Previous Month', 'Current Month'],
  datasets: [
    {
      label: 'Total Payroll',
      data: [props.stats.previous_month.total_payroll, props.stats.current_month.total_payroll],
      backgroundColor: ['rgba(156, 163, 175, 0.8)', 'rgba(59, 130, 246, 0.8)'],
      borderColor: ['rgb(156, 163, 175)', 'rgb(59, 130, 246)'],
      borderWidth: 2,
    },
  ],
}));

const hoursComparisonData = computed(() => ({
  labels: ['Previous Month', 'Current Month'],
  datasets: [
    {
      label: 'Regular Hours',
      data: [
        props.stats.previous_month.total_hours * 0.85, // Approximate regular hours
        props.stats.current_month.total_hours * 0.85,
      ],
      backgroundColor: 'rgba(34, 197, 94, 0.8)',
      borderColor: 'rgb(34, 197, 94)',
      borderWidth: 2,
    },
    {
      label: 'Overtime Hours',
      data: [
        props.stats.previous_month.total_hours * 0.15, // Approximate overtime
        props.stats.current_month.total_hours * 0.15,
      ],
      backgroundColor: 'rgba(251, 146, 60, 0.8)',
      borderColor: 'rgb(251, 146, 60)',
      borderWidth: 2,
    },
  ],
}));

const pendingItemsData = computed(() => ({
  labels: ['Time Entries', 'Payslips'],
  datasets: [
    {
      data: [props.stats.pending.time_entries, props.stats.pending.payslips],
      backgroundColor: ['rgba(234, 179, 8, 0.8)', 'rgba(239, 68, 68, 0.8)'],
      borderColor: ['rgb(234, 179, 8)', 'rgb(239, 68, 68)'],
      borderWidth: 2,
    },
  ],
}));

// Chart options
const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
      labels: {
        color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151',
        padding: 15,
      },
    },
    tooltip: {
      callbacks: {
        label: function (context) {
          let label = context.dataset.label || '';
          if (label) {
            label += ': ';
          }
          if (context.parsed.y !== null) {
            label += formatCurrency(context.parsed.y);
          }
          return label;
        },
      },
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        callback: function (value) {
          return formatCurrency(value);
        },
        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
      },
      grid: {
        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
      },
    },
    x: {
      ticks: {
        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
      },
      grid: {
        display: false,
      },
    },
  },
};

const hoursChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
      labels: {
        color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151',
        padding: 15,
      },
    },
    tooltip: {
      callbacks: {
        label: function (context) {
          let label = context.dataset.label || '';
          if (label) {
            label += ': ';
          }
          if (context.parsed.y !== null) {
            label += formatHours(context.parsed.y);
          }
          return label;
        },
      },
    },
  },
  scales: {
    y: {
      stacked: true,
      beginAtZero: true,
      ticks: {
        callback: function (value) {
          return formatHours(value);
        },
        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
      },
      grid: {
        color: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
      },
    },
    x: {
      stacked: true,
      ticks: {
        color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280',
      },
      grid: {
        display: false,
      },
    },
  },
};

const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'bottom',
      labels: {
        color: document.documentElement.classList.contains('dark') ? '#E5E7EB' : '#374151',
        padding: 15,
      },
    },
  },
};

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    approved: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
  };
  return classes[status] || classes.draft;
};
</script>

<template>
  <Head :title="$t('payroll.dashboard.title')" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ $t('payroll.dashboard.title') }}
          </h2>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $t('payroll.dashboard.overview_description') }}
          </p>
        </div>
        <div class="flex gap-3">
          <Link
            :href="route('payroll.reports')"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
            {{ $t('payroll.reports.title') }}
          </Link>
        </div>
      </div>

      <!-- Key Metrics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Payroll -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div
              v-if="payrollChange.value > 0"
              :class="[
                'flex items-center text-xs font-medium px-2 py-1 rounded-full',
                payrollChange.isPositive
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
              ]"
            >
              <svg
                v-if="payrollChange.isPositive"
                class="w-3 h-3 mr-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
              </svg>
              <svg v-else class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
              </svg>
              {{ payrollChange.value }}%
            </div>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              {{ $t('payroll.stats.current_month_payroll') }}
            </p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">
              {{ formatCurrency(stats.current_month.total_payroll) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ $t('payroll.dashboard.vs_last_month') }}: {{ formatCurrency(stats.previous_month.total_payroll) }}
            </p>
          </div>
        </div>

        <!-- Total Hours -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div
              v-if="hoursChange.value > 0"
              :class="[
                'flex items-center text-xs font-medium px-2 py-1 rounded-full',
                hoursChange.isPositive
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
              ]"
            >
              <svg
                v-if="hoursChange.isPositive"
                class="w-3 h-3 mr-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
              </svg>
              <svg v-else class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
              </svg>
              {{ hoursChange.value }}%
            </div>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              {{ $t('payroll.stats.total_hours') }}
            </p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">
              {{ formatHours(stats.current_month.total_hours) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ $t('payroll.dashboard.vs_last_month') }}: {{ formatHours(stats.previous_month.total_hours) }}
            </p>
          </div>
        </div>

        <!-- Active Employees -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <div
              v-if="employeesChange.value > 0"
              :class="[
                'flex items-center text-xs font-medium px-2 py-1 rounded-full',
                employeesChange.isPositive
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
              ]"
            >
              <svg
                v-if="employeesChange.isPositive"
                class="w-3 h-3 mr-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
              </svg>
              <svg v-else class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
              </svg>
              {{ employeesChange.value }}%
            </div>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              {{ $t('payroll.stats.active_employees') }}
            </p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">
              {{ formatNumber(stats.current_month.total_employees) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ $t('payroll.dashboard.vs_last_month') }}: {{ formatNumber(stats.previous_month.total_employees) }}
            </p>
          </div>
        </div>

        <!-- Time Entries -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
            </div>
            <div
              v-if="entriesChange.value > 0"
              :class="[
                'flex items-center text-xs font-medium px-2 py-1 rounded-full',
                entriesChange.isPositive
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                  : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
              ]"
            >
              <svg
                v-if="entriesChange.isPositive"
                class="w-3 h-3 mr-1"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
              </svg>
              <svg v-else class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
              </svg>
              {{ entriesChange.value }}%
            </div>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              {{ $t('payroll.stats.time_entries') }}
            </p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">
              {{ formatNumber(stats.current_month.total_entries) }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ stats.pending.time_entries }} {{ $t('payroll.stats.pending') }}
            </p>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payroll Comparison Chart -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t('payroll.dashboard.payroll_comparison') }}
          </h3>
          <div class="h-64">
            <BarChart :data="payrollComparisonData" :options="barChartOptions" />
          </div>
        </div>

        <!-- Hours Breakdown Chart -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t('payroll.dashboard.hours_breakdown') }}
          </h3>
          <div class="h-64">
            <BarChart :data="hoursComparisonData" :options="hoursChartOptions" />
          </div>
        </div>

        <!-- Pending Items Chart -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ $t('payroll.dashboard.pending_items') }}
          </h3>
          <div class="h-64 flex items-center justify-center">
            <div class="w-48">
              <DoughnutChart :data="pendingItemsData" :options="doughnutChartOptions" />
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Time Entries -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $t('payroll.dashboard.recent_time_entries') }}
              </h3>
              <Link
                :href="route('time-entries.index')"
                class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
              >
                {{ $t('payroll.actions.view_all') }} →
              </Link>
            </div>

            <div v-if="recentTimeEntries.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
              <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <p>{{ $t('payroll.dashboard.no_recent_entries') }}</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="entry in recentTimeEntries"
                :key="entry.id"
                class="flex items-start justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition"
              >
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {{ entry.user?.name }}
                    </p>
                    <span
                      :class="[
                        'px-2 py-0.5 text-xs font-medium rounded-full',
                        entry.is_approved
                          ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                          : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                      ]"
                    >
                      {{ entry.is_approved ? $t('payroll.status.approved') : $t('payroll.status.pending') }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-300 truncate">
                    {{ entry.task?.name }} - {{ entry.project?.name }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ formatHours(entry.hours_worked) }} • {{ formatCurrency(entry.gross_amount) }}
                  </p>
                </div>
                <div class="text-right ml-3">
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDate(entry.start_datetime) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Payslips -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $t('payroll.dashboard.recent_payslips') }}
              </h3>
              <Link :href="route('payslips.index')" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                {{ $t('payroll.actions.view_all') }} →
              </Link>
            </div>

            <div v-if="recentPayslips.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
              <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
              <p>{{ $t('payroll.dashboard.no_recent_payslips') }}</p>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="payslip in recentPayslips"
                :key="payslip.id"
                class="flex items-start justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition"
              >
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {{ payslip.user?.name }}
                    </p>
                    <span
                      :class="['px-2 py-0.5 text-xs font-medium rounded-full', getStatusBadgeClass(payslip.status)]"
                    >
                      {{ $t(`payroll.payslips.status.${payslip.status}`) }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-300">
                    {{ payslip.payslip_number }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ formatHours(payslip.regular_hours + payslip.overtime_hours) }} •
                    {{ formatCurrency(payslip.net_pay) }}
                  </p>
                </div>
                <div class="text-right ml-3">
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDate(payslip.pay_date) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions Banner -->
      <div
        class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-lg shadow-xl p-6"
      >
        <div class="flex flex-col md:flex-row items-center justify-between">
          <div class="text-white mb-4 md:mb-0">
            <h3 class="text-xl font-bold mb-2">{{ $t('payroll.dashboard.need_help') }}</h3>
            <p class="text-blue-100">
              {{ $t('payroll.dashboard.quick_actions_description') }}
            </p>
          </div>
          <div class="flex flex-wrap gap-3">
            <Link
              :href="route('time-entries.create')"
              class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-md font-semibold text-sm hover:bg-blue-50 transition"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              {{ $t('payroll.actions.add_time_entry') }}
            </Link>
            <Link
              :href="route('payslips.generate-bulk')"
              class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-md font-semibold text-sm hover:bg-blue-800 transition"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
              {{ $t('payroll.actions.generate_payslips') }}
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
