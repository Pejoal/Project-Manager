<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
  employees: Array,
  stats: Object,
  payrollSettings: Object,
});


const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: props.payrollSettings?.currency || 'USD',
  }).format(amount || 0);
};

const formatHours = (hours) => {
  return `${Number(hours || 0).toFixed(2)} hrs`;
};

const generateTimeEntriesForm = useForm({});

const generateTimeEntries = () => {
  generateTimeEntriesForm.post(route('payroll.generate-time-entries'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head :title="trans('payroll.payroll_system')" />

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
            {{ trans('payroll.payroll_system') }}
          </h2>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ trans('payroll.manage_payroll_description') }}
          </p>
        </div>
        <div class="flex gap-3">
          <PrimaryButton
            @click="generateTimeEntries"
            :disabled="generateTimeEntriesForm.processing"
            :class="{ 'opacity-25': generateTimeEntriesForm.processing }"
          >
            {{ trans('payroll.time_entries.generate_from_tasks') }}
          </PrimaryButton>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
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
                {{ trans('payroll.stats.total_employees') }}
              </p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ stats.total_employees }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
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
                {{ trans('payroll.stats.pending_approvals') }}
              </p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ stats.pending_approvals }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
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
                {{ trans('payroll.stats.current_month_payroll') }}
              </p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ formatCurrency(stats.current_month_payroll) }}
              </p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
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
                {{ trans('payroll.stats.pending_payslips') }}
              </p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ stats.pending_payslips }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          {{ trans('payroll.quick_actions') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Link
            :href="route('time-entries.index')"
            class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition"
          >
            <svg class="h-8 w-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">
                {{ trans('payroll.time_entries.title') }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ trans('payroll.manage_time_entries') }}
              </p>
            </div>
          </Link>

          <Link
            :href="route('payslips.generate-bulk')"
            class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-500 dark:hover:border-green-500 transition"
          >
            <svg class="h-8 w-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">
                {{ trans('payroll.payslips.generate_bulk') }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ trans('payroll.generate_payslips_description') }}
              </p>
            </div>
          </Link>

          <Link
            :href="route('payroll.reports')"
            class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:border-purple-500 dark:hover:border-purple-500 transition"
          >
            <svg class="h-8 w-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
              />
            </svg>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">
                {{ trans('payroll.reports.title') }}
              </p>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ trans('payroll.view_reports_description') }}
              </p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Employees List -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ trans('payroll.employees.title') }}
          </h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ trans('payroll.employees.name') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ trans('payroll.employees.employee_id') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ trans('payroll.employees.hourly_rate') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ trans('payroll.stats.current_month_hours') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ trans('payroll.stats.current_month_earnings') }}
                  </th>
                  <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                  >
                    {{ trans('payroll.employees.status') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="employee in employees" :key="employee.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ employee.name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ employee.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ employee.employee_id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ formatCurrency(employee.hourly_rate) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ formatHours(employee.current_month_hours) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ formatCurrency(employee.current_month_earnings) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        employee.is_active
                          ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                          : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                      ]"
                    >
                      {{ employee.is_active ? trans('payroll.employees.active') : trans('payroll.employees.inactive') }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
