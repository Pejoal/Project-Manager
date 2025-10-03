<script setup>
import DataTable from '@/Components/DataTable.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
  employeeProfiles: Object,
  filters: Object,
  users: Array,
});

// Column configuration for DataTable
const columns = [
  {
    key: 'user',
    label: 'Employee',
    sortable: true,
    component: (item) => `
      <div class="flex items-center">
        <div class="flex-shrink-0 h-10 w-10">
          <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
              ${item.user.name.charAt(0).toUpperCase()}
            </span>
          </div>
        </div>
        <div class="ml-4">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
            ${item.user.name}
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            ${item.user.email}
          </div>
        </div>
      </div>
    `,
  },
  {
    key: 'employee_id',
    label: 'Employee ID',
    sortable: true,
    component: (item) => item.employee_id || 'N/A',
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'base_hourly_rate',
    label: 'Base Hourly Rate',
    sortable: true,
    component: (item) => formatCurrency(item.base_hourly_rate),
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'payment_method',
    label: 'Payment Method',
    component: (item) => `
      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
        ${trans(`payroll.employee_profiles.payment_methods.${item.payment_method}`)}
      </span>
    `,
  },
  {
    key: 'hire_date',
    label: 'Hire Date',
    sortable: true,
    component: (item) => formatDate(item.hire_date),
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'is_active',
    label: 'Status',
    component: (item) => `
      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
        item.is_active
          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
          : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
      }">
        ${item.is_active ? trans('words.active') : trans('words.inactive')}
      </span>
    `,
  },
  {
    key: 'actions',
    label: 'Actions',
    component: () => '', // Will use slot
  },
];

// Filter configuration
const filterConfig = [
  {
    key: 'search',
    label: 'Search',
    type: 'text',
    placeholder: 'Search by name or email...',
  },
  {
    key: 'status',
    label: 'Status',
    type: 'select',
    options: [
      { value: 'active', label: 'Active' },
      { value: 'inactive', label: 'Inactive' },
    ],
    placeholder: 'All Status',
  },
  {
    key: 'payment_method',
    label: 'Payment Method',
    type: 'select',
    options: [
      { value: 'bank_transfer', label: trans('payroll.employee_profiles.payment_methods.bank_transfer') },
      { value: 'cash', label: trans('payroll.employee_profiles.payment_methods.cash') },
      { value: 'check', label: trans('payroll.employee_profiles.payment_methods.check') },
    ],
    placeholder: 'All Methods',
  },
];

// Bulk actions configuration
const bulkActions = [
  {
    value: 'activate',
    label: trans('payroll.employee_profiles.activate'),
  },
  {
    value: 'deactivate',
    label: trans('payroll.employee_profiles.deactivate'),
  },
  {
    value: 'update_rate',
    label: 'Update Hourly Rate',
    fields: [
      {
        name: 'hourly_rate',
        label: 'New Hourly Rate',
        type: 'number',
        placeholder: 'Enter new rate',
        step: '0.01',
        min: '0',
        required: true,
      },
    ],
  },
];

// Formatting functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount || 0);
};

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString() : 'N/A';
};

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    profile_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('employee-profiles.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Handle individual actions
const activateProfile = (profileId) => {
  if (confirm('Are you sure you want to activate this employee profile?')) {
    useForm().patch(route('employee-profiles.activate', profileId), {
      preserveState: true,
    });
  }
};

const deactivateProfile = (profileId) => {
  if (confirm('Are you sure you want to deactivate this employee profile?')) {
    useForm().patch(route('employee-profiles.deactivate', profileId), {
      preserveState: true,
    });
  }
};

// Handle row clicks
const handleRowClick = (profile) => {
  // Navigate to profile detail page
  window.location.href = route('employee-profiles.show', profile.id);
};
</script>

<template>
  <Head title="Employee Profiles" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('payroll.employee_profiles.title') }}
        </h1>
        <div class="flex space-x-3">
          <Link
            :href="route('employee-profiles.create')"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('payroll.employee_profiles.create') }}
          </Link>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <DataTable
        :data="employeeProfiles"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="true"
        route-name="employee-profiles.index"
        :search-placeholder="$t('payroll.filters.search')"
        :empty-state-text="$t('payroll.employees.no_employees_found')"
        @bulk-action="handleBulkAction"
        @row-click="handleRowClick"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2">
            <Link
              :href="route('employee-profiles.show', item.id)"
              class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              {{ $t('words.view') }}
            </Link>
            <Link
              :href="route('employee-profiles.edit', item.id)"
              class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
            >
              {{ $t('words.edit') }}
            </Link>
            <button
              v-if="item.is_active"
              @click.stop="deactivateProfile(item.id)"
              class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
            >
              {{ $t('payroll.employee_profiles.deactivate') }}
            </button>
            <button
              v-else
              @click.stop="activateProfile(item.id)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('payroll.employee_profiles.activate') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
