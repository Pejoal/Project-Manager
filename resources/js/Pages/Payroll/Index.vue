<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  employees: {
    type: Array,
    required: true,
    default: () => [],
  },
  stats: {
    type: Object,
    required: true,
    default: () => ({
      total_employees: 0,
      pending_approvals: 0,
      current_month_payroll: 0,
      pending_payslips: 0,
    }),
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

// Search and filter state
const searchQuery = ref('');
const statusFilter = ref('all');
const sortColumn = ref('name');
const sortDirection = ref('asc');

// Computed filtered and sorted employees
const filteredEmployees = computed(() => {
  let filtered = props.employees || [];

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (emp) =>
        emp.name?.toLowerCase().includes(query) ||
        emp.email?.toLowerCase().includes(query) ||
        emp.employee_id?.toLowerCase().includes(query)
    );
  }

  // Apply status filter
  if (statusFilter.value !== 'all') {
    const isActive = statusFilter.value === 'active';
    filtered = filtered.filter((emp) => emp.is_active === isActive);
  }

  // Apply sorting
  filtered.sort((a, b) => {
    let aVal = a[sortColumn.value];
    let bVal = b[sortColumn.value];

    // Handle numeric sorting
    if (
      sortColumn.value === 'hourly_rate' ||
      sortColumn.value === 'current_month_hours' ||
      sortColumn.value === 'current_month_earnings'
    ) {
      aVal = parseFloat(aVal) || 0;
      bVal = parseFloat(bVal) || 0;
    }

    // Handle string sorting
    if (typeof aVal === 'string') {
      aVal = aVal.toLowerCase();
      bVal = bVal.toLowerCase();
    }

    if (sortDirection.value === 'asc') {
      return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
    } else {
      return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
    }
  });

  return filtered;
});

// Statistics computed values
const activeEmployeesCount = computed(() => {
  return props.employees?.filter((emp) => emp.is_active).length || 0;
});

const inactiveEmployeesCount = computed(() => {
  return props.employees?.filter((emp) => !emp.is_active).length || 0;
});

const totalMonthlyHours = computed(() => {
  return props.employees?.reduce((sum, emp) => sum + parseFloat(emp.current_month_hours || 0), 0) || 0;
});

const averageHourlyRate = computed(() => {
  const employees = props.employees?.filter((emp) => emp.is_active) || [];
  if (employees.length === 0) return 0;
  const total = employees.reduce((sum, emp) => sum + parseFloat(emp.hourly_rate || 0), 0);
  return total / employees.length;
});

// Formatting functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: props.payrollSettings?.currency || 'USD',
  }).format(amount || 0);
};

const formatHours = (hours) => {
  return `${Number(hours || 0).toFixed(2)} hrs`;
};

const formatNumber = (number) => {
  return new Intl.NumberFormat('en-US').format(number || 0);
};

// Sorting function
const sortBy = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column;
    sortDirection.value = 'asc';
  }
};

// Get sort icon
const getSortIcon = (column) => {
  if (sortColumn.value !== column) {
    return '↕';
  }
  return sortDirection.value === 'asc' ? '↑' : '↓';
};

// Generate time entries form
const generateTimeEntriesForm = useForm({});

const generateTimeEntries = () => {
  generateTimeEntriesForm.post(route('payroll.generate-time-entries'), {
    preserveScroll: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Navigate to employee profile
const viewEmployeeProfile = (employeeId) => {
  router.visit(route('employee-profiles.show', employeeId));
};

// Get percentage change indicator
const getPercentageChange = (current, previous) => {
  if (!previous || previous === 0) return null;
  const change = ((current - previous) / previous) * 100;
  return {
    value: Math.abs(change).toFixed(1),
    isPositive: change > 0,
  };
};
</script>

<template>
  <Head :title="$t('payroll.payroll_system')" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ $t('payroll.payroll_system') }}
          </h2>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ payrollSettings?.company_name || $t('payroll.manage_payroll_description') }}
          </p>
        </div>
        <div class="flex gap-3">
          <Link
            :href="route('payroll.settings')"
            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
            {{ $t('payroll.settings.title') }}
          </Link>
          <PrimaryButton
            @click="generateTimeEntries"
            :disabled="generateTimeEntriesForm.processing"
            :class="{ 'opacity-25': generateTimeEntriesForm.processing }"
          >
            <svg
              v-if="!generateTimeEntriesForm.processing"
              class="w-4 h-4 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            {{ $t('payroll.time_entries.generate_from_tasks') }}
          </PrimaryButton>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Employees -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                  />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                  {{ $t('payroll.stats.total_employees') }}
                </p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ formatNumber(stats.total_employees) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                  {{ activeEmployeesCount }} active · {{ inactiveEmployeesCount }} inactive
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                  {{ $t('payroll.stats.pending_approvals') }}
                </p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ formatNumber(stats.pending_approvals) }}
                </p>
                <Link
                  v-if="stats.pending_approvals > 0"
                  :href="route('time-entries.index', { status: 'pending' })"
                  class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline mt-1 inline-block"
                >
                  {{ $t('payroll.actions.view') }} →
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Current Month Payroll -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                  {{ $t('payroll.stats.current_month_payroll') }}
                </p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ formatCurrency(stats.current_month_payroll) }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ formatHours(totalMonthlyHours) }} total</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Payslips -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                  {{ $t('payroll.stats.pending_payslips') }}
                </p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ formatNumber(stats.pending_payslips) }}
                </p>
                <Link
                  v-if="stats.pending_payslips > 0"
                  :href="route('payslips.index', { status: 'draft' })"
                  class="text-xs text-red-600 dark:text-red-400 hover:underline mt-1 inline-block"
                >
                  {{ $t('payroll.actions.view') }} →
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          {{ $t('payroll.quick_actions') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Link
            :href="route('time-entries.index')"
            class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition duration-150"
          >
            <svg class="h-8 w-8 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">
                {{ $t('payroll.time_entries.title') }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('payroll.manage_time_entries') }}
              </p>
            </div>
          </Link>

          <Link
            :href="route('payslips.generate-bulk')"
            class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-500 dark:hover:border-green-500 transition duration-150"
          >
            <svg
              class="h-8 w-8 text-green-500 mr-3 flex-shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">
                {{ $t('payroll.payslips.generate_bulk') }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('payroll.generate_payslips_description') }}
              </p>
            </div>
          </Link>

          <Link
            :href="route('payroll.reports')"
            class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-purple-500 dark:hover:border-purple-500 transition duration-150"
          >
            <svg
              class="h-8 w-8 text-purple-500 mr-3 flex-shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">
                {{ $t('payroll.reports.title') }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('payroll.view_reports_description') }}
              </p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Employees List -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
          <!-- Header with search and filters -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ $t('payroll.employees.title') }}
              <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                ({{ filteredEmployees.length }} {{ filteredEmployees.length === 1 ? 'employee' : 'employees' }})
              </span>
            </h3>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
              <!-- Search -->
              <div class="relative flex-1 sm:flex-initial">
                <input
                  v-model="searchQuery"
                  type="text"
                  :placeholder="$t('payroll.filters.search')"
                  class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
                />
                <svg
                  class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
              </div>

              <!-- Status Filter -->
              <select
                v-model="statusFilter"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="all">{{ $t('payroll.filters.all') }}</option>
                <option value="active">{{ $t('payroll.employees.active') }}</option>
                <option value="inactive">{{ $t('payroll.employees.inactive') }}</option>
              </select>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th
                    @click="sortBy('name')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
                  >
                    {{ $t('payroll.employees.name') }}
                    <span class="ml-1">{{ getSortIcon('name') }}</span>
                  </th>
                  <th
                    @click="sortBy('employee_id')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
                  >
                    {{ $t('payroll.employees.employee_id') }}
                    <span class="ml-1">{{ getSortIcon('employee_id') }}</span>
                  </th>
                  <th
                    @click="sortBy('hourly_rate')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
                  >
                    {{ $t('payroll.employees.hourly_rate') }}
                    <span class="ml-1">{{ getSortIcon('hourly_rate') }}</span>
                  </th>
                  <th
                    @click="sortBy('current_month_hours')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
                  >
                    {{ $t('payroll.stats.current_month_hours') }}
                    <span class="ml-1">{{ getSortIcon('current_month_hours') }}</span>
                  </th>
                  <th
                    @click="sortBy('current_month_earnings')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
                  >
                    {{ $t('payroll.stats.current_month_earnings') }}
                    <span class="ml-1">{{ getSortIcon('current_month_earnings') }}</span>
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ $t('payroll.employees.status') }}
                  </th>
                  <th
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ $t('payroll.actions.view') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-if="filteredEmployees.length === 0" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                      />
                    </svg>
                    <p class="mt-2">{{ $t('payroll.employees.no_employees_found') }}</p>
                  </td>
                </tr>
                <tr
                  v-for="employee in filteredEmployees"
                  :key="employee.id"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div
                          class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold"
                        >
                          {{ employee.name?.charAt(0).toUpperCase() || '?' }}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ employee.name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ employee.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-white font-mono">
                      {{ employee.employee_id }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ formatCurrency(employee.hourly_rate) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      {{ $t('payroll.employee_profiles.hourly') }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ formatHours(employee.current_month_hours) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ formatCurrency(employee.current_month_earnings) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                        employee.is_active
                          ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                          : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                      ]"
                    >
                      {{ employee.is_active ? $t('payroll.employees.active') : $t('payroll.employees.inactive') }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('employee-profiles.show', employee.id)"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 inline-flex items-center"
                    >
                      {{ $t('payroll.actions.view') }}
                      <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Summary footer -->
          <div v-if="filteredEmployees.length > 0" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
              <div>
                <p class="text-gray-500 dark:text-gray-400">{{ $t('payroll.stats.total_employees') }}</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ filteredEmployees.length }}
                </p>
              </div>
              <div>
                <p class="text-gray-500 dark:text-gray-400">{{ $t('payroll.reports.average_rate') }}</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatCurrency(averageHourlyRate) }}
                </p>
              </div>
              <div>
                <p class="text-gray-500 dark:text-gray-400">{{ $t('payroll.stats.total_hours') }}</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatHours(totalMonthlyHours) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
