<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import vSelect from 'vue-select';

const props = defineProps({
  users: Array,
  projects: Array,
  reportData: Object,
  filters: Object,
  canExport: Boolean,
});

const form = useForm({
  period: props.filters?.period || 'monthly',
  start_date: props.filters?.start_date || '',
  end_date: props.filters?.end_date || '',
  user_ids: props.filters?.user_ids || [],
  project_ids: props.filters?.project_ids || [],
  report_type: props.filters?.report_type || 'summary',
});

const isLoading = ref(false);
const activeChart = ref('hours');

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount || 0);
};

const formatHours = (hours) => {
  return parseFloat(hours || 0).toFixed(2);
};

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString() : 'N/A';
};

const generateReport = () => {
  isLoading.value = true;
  form.get(route('payroll.reports'), {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false;
    },
  });
};

const exportReport = (format) => {
  const params = new URLSearchParams(form.data());
  params.append('export', format);
  window.open(`${route('payroll.reports.export')}?${params.toString()}`);
};

const totalHours = computed(() => {
  return props.reportData?.summary?.total_hours || 0;
});

const totalPayroll = computed(() => {
  return props.reportData?.summary?.total_payroll || 0;
});

const averageHourlyRate = computed(() => {
  return totalHours.value > 0 ? totalPayroll.value / totalHours.value : 0;
});

// Auto-generate report when filters change
watch(
  () => [form.period, form.start_date, form.end_date],
  () => {
    if (form.start_date && form.end_date) {
      generateReport();
    }
  },
  { deep: true }
);
</script>

<template>
  <Head title="Payroll Reports" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          {{ $t('payroll.reports.title') }}
        </h1>
        <div class="flex space-x-3" v-if="canExport && reportData">
          <PrimaryButton @click="exportReport('pdf')" :disabled="isLoading">
            {{ $t('payroll.reports.export_pdf') }}
          </PrimaryButton>
          <PrimaryButton @click="exportReport('excel')" :disabled="isLoading" class="bg-green-600 hover:bg-green-700">
            {{ $t('payroll.reports.export_excel') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Filter Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ $t('payroll.reports.filters') }}
          </h2>
        </div>
        <form @submit.prevent="generateReport" class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Report Type -->
            <div>
              <InputLabel for="report_type" :value="$t('payroll.reports.report_type')" />
              <select
                id="report_type"
                v-model="form.report_type"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
              >
                <option value="summary">{{ $t('payroll.reports.summary') }}</option>
                <option value="detailed">{{ $t('payroll.reports.detailed') }}</option>
                <option value="by_employee">{{ $t('payroll.reports.by_employee') }}</option>
                <option value="by_project">{{ $t('payroll.reports.by_project') }}</option>
              </select>
            </div>

            <!-- Period -->
            <div>
              <InputLabel for="period" :value="$t('payroll.reports.period')" />
              <select
                id="period"
                v-model="form.period"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
              >
                <option value="weekly">{{ $t('payroll.reports.weekly') }}</option>
                <option value="monthly">{{ $t('payroll.reports.monthly') }}</option>
                <option value="quarterly">{{ $t('payroll.reports.quarterly') }}</option>
                <option value="yearly">{{ $t('payroll.reports.yearly') }}</option>
                <option value="custom">{{ $t('payroll.reports.custom') }}</option>
              </select>
            </div>

            <!-- Start Date -->
            <div>
              <InputLabel for="start_date" :value="$t('payroll.reports.start_date')" />
              <TextInput
                id="start_date"
                type="date"
                v-model="form.start_date"
                class="mt-1 block w-full"
                :disabled="form.period !== 'custom'"
              />
            </div>

            <!-- End Date -->
            <div>
              <InputLabel for="end_date" :value="$t('payroll.reports.end_date')" />
              <TextInput
                id="end_date"
                type="date"
                v-model="form.end_date"
                class="mt-1 block w-full"
                :disabled="form.period !== 'custom'"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Employees Filter -->
            <div>
              <InputLabel for="users" :value="$t('payroll.reports.employees')" />
              <vSelect
                id="users"
                v-model="form.user_ids"
                :options="users"
                :reduce="(user) => user.id"
                label="name"
                multiple
                placeholder="Select employees..."
                class="mt-1"
              />
            </div>

            <!-- Projects Filter -->
            <div>
              <InputLabel for="projects" :value="$t('payroll.reports.projects')" />
              <vSelect
                id="projects"
                v-model="form.project_ids"
                :options="projects"
                :reduce="(project) => project.id"
                label="name"
                multiple
                placeholder="Select projects..."
                class="mt-1"
              />
            </div>
          </div>

          <div class="flex justify-end">
            <PrimaryButton type="submit" :disabled="isLoading">
              <span v-if="isLoading" class="mr-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
              </span>
              {{ $t('payroll.reports.generate') }}
            </PrimaryButton>
          </div>
        </form>
      </div>

      <!-- Report Results -->
      <div v-if="reportData" class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                      {{ $t('payroll.reports.total_hours') }}
                    </dt>
                    <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ formatHours(totalHours) }}h</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                      {{ $t('payroll.reports.total_payroll') }}
                    </dt>
                    <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ formatCurrency(totalPayroll) }}
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                      {{ $t('payroll.reports.average_rate') }}
                    </dt>
                    <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ formatCurrency(averageHourlyRate) }}/h
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
              <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $t('payroll.reports.charts') }}
              </h2>
              <div class="flex space-x-2">
                <button
                  @click="activeChart = 'hours'"
                  :class="[
                    activeChart === 'hours'
                      ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200'
                      : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200',
                    'px-3 py-2 rounded-md text-sm font-medium',
                  ]"
                >
                  {{ $t('payroll.reports.hours_chart') }}
                </button>
                <button
                  @click="activeChart = 'payroll'"
                  :class="[
                    activeChart === 'payroll'
                      ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200'
                      : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200',
                    'px-3 py-2 rounded-md text-sm font-medium',
                  ]"
                >
                  {{ $t('payroll.reports.payroll_chart') }}
                </button>
              </div>
            </div>
          </div>
          <div class="p-6">
            <!-- Chart placeholder - would integrate with Chart.js or similar -->
            <div class="h-64 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
              <div class="text-center text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                  />
                </svg>
                <p class="text-sm">{{ $t('payroll.reports.chart_placeholder') }}</p>
                <p class="text-xs mt-1">{{ activeChart === 'hours' ? 'Hours' : 'Payroll' }} data visualization</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Data Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ $t('payroll.reports.detailed_data') }}
            </h2>
          </div>

          <div v-if="reportData.data && reportData.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th
                    v-if="form.report_type === 'by_employee' || form.report_type === 'detailed'"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ $t('payroll.reports.employee') }}
                  </th>
                  <th
                    v-if="form.report_type === 'by_project' || form.report_type === 'detailed'"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ $t('payroll.reports.project') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ $t('payroll.reports.hours') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ $t('payroll.reports.gross_pay') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ $t('payroll.reports.deductions') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                  >
                    {{ $t('payroll.reports.net_pay') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="row in reportData.data" :key="row.id || row.employee_id || row.project_id">
                  <td
                    v-if="form.report_type === 'by_employee' || form.report_type === 'detailed'"
                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
                  >
                    {{ row.employee_name || row.user?.name || '-' }}
                  </td>
                  <td
                    v-if="form.report_type === 'by_project' || form.report_type === 'detailed'"
                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300"
                  >
                    {{ row.project_name || row.project?.name || '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    {{ formatHours(row.total_hours || row.hours_worked) }}h
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    {{ formatCurrency(row.gross_pay || row.gross_amount) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    {{ formatCurrency(row.total_deductions || row.deductions) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ formatCurrency(row.net_pay) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ $t('payroll.reports.no_data') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $t('payroll.reports.no_data_description') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
