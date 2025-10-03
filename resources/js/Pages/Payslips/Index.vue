<script setup>
import DataTable from '@/Components/DataTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import vSelect from 'vue-select';

const props = defineProps({
  payslips: Object,
  filters: Object,
  users: Array,
  canGeneratePayslips: Boolean,
});

const showGenerateForm = ref(false);

// Generate payslips form
const generateForm = useForm({
  user_ids: [],
  pay_period_start: '',
  pay_period_end: '',
});

// Column configuration for DataTable
const columns = [
  {
    key: 'user',
    label: 'Employee',
    sortable: true,
    component: (item) => `
      <div class="flex items-center">
        <div class="flex-shrink-0 h-8 w-8">
          <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
              ${item.user.name.charAt(0).toUpperCase()}
            </span>
          </div>
        </div>
        <div class="ml-3">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
            ${item.user.name}
          </div>
        </div>
      </div>
    `,
  },
  {
    key: 'pay_period',
    label: 'Pay Period',
    component: (item) => `
      <div>
        <div class="text-sm text-gray-900 dark:text-gray-100">${formatDate(item.pay_period_start)} -</div>
        <div class="text-sm text-gray-900 dark:text-gray-100">${formatDate(item.pay_period_end)}</div>
      </div>
    `,
  },
  {
    key: 'hours',
    label: 'Total Hours',
    component: (item) => `
      <div>
        <div class="text-sm text-gray-900 dark:text-gray-100">Regular: ${formatHours(item.regular_hours)}h</div>
        ${item.overtime_hours > 0 ? `<div class="text-xs text-orange-600 dark:text-orange-400">Overtime: ${formatHours(item.overtime_hours)}h</div>` : ''}
      </div>
    `,
  },
  {
    key: 'gross_pay',
    label: 'Gross Pay',
    sortable: true,
    component: (item) => formatCurrency(item.gross_pay),
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'total_deductions',
    label: 'Total Deductions',
    sortable: true,
    component: (item) => formatCurrency(item.total_deductions),
    textClass: 'text-gray-900 dark:text-gray-100',
  },
  {
    key: 'net_pay',
    label: 'Net Pay',
    sortable: true,
    component: (item) => formatCurrency(item.net_pay),
    textClass: 'font-semibold text-gray-900 dark:text-gray-100',
  },
  {
    key: 'status',
    label: 'Status',
    component: (item) => {
      const statusClasses = {
        approved: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
        paid: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
        draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
      };
      const statusText = {
        approved: 'Approved',
        paid: 'Paid',
        draft: 'Draft',
      };
      return `
        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClasses[item.status]}">
          ${statusText[item.status]}
        </span>
      `;
    },
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
    key: 'user_id',
    label: 'Employee',
    type: 'vue-select',
    options: props.users,
    reduce: (user) => user.id,
    optionLabel: 'name',
    placeholder: 'All Employees',
  },
  {
    key: 'pay_period_start',
    label: 'Pay Period Start',
    type: 'date',
  },
  {
    key: 'pay_period_end',
    label: 'Pay Period End',
    type: 'date',
  },
  {
    key: 'status',
    label: 'Status',
    type: 'select',
    options: [
      { value: 'draft', label: 'Draft' },
      { value: 'approved', label: 'Approved' },
      { value: 'paid', label: 'Paid' },
    ],
    placeholder: 'All Status',
  },
];

// Bulk actions configuration
const bulkActions = props.canGeneratePayslips
  ? [
      {
        value: 'approve',
        label: 'Approve Payslips',
      },
      {
        value: 'mark_paid',
        label: 'Mark as Paid',
      },
    ]
  : [];

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

const formatHours = (hours) => {
  return parseFloat(hours || 0).toFixed(2);
};

// Handle bulk actions
const handleBulkAction = ({ action, items, data }) => {
  const bulkForm = useForm({
    payslip_ids: items,
    action: action,
    ...data,
  });

  bulkForm.post(route('payslips.bulk-update'), {
    preserveState: true,
    onSuccess: () => {
      // Optionally show success notification
    },
  });
};

// Toggle generate form
const toggleGenerateForm = () => {
  showGenerateForm.value = !showGenerateForm.value;
};

// Generate payslips
const generatePayslips = () => {
  if (!generateForm.pay_period_start || !generateForm.pay_period_end) {
    alert('Please select pay period start and end dates');
    return;
  }

  if (generateForm.user_ids.length === 0) {
    alert('Please select at least one employee');
    return;
  }

  if (confirm(`Generate payslips for ${generateForm.user_ids.length} employees?`)) {
    generateForm.post(route('payslips.generate'), {
      preserveState: true,
      onSuccess: () => {
        generateForm.reset();
        showGenerateForm.value = false;
      },
    });
  }
};

// Handle individual actions
const approvePayslip = (payslipId) => {
  if (confirm('Are you sure you want to approve this payslip?')) {
    useForm().patch(route('payslips.approve', payslipId), {
      preserveState: true,
    });
  }
};
</script>

<template>
  <Head title="Payslips" />

  <header class="bg-white dark:bg-gray-800 shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
          {{ $t('payroll.payslips.title') }}
        </h1>
        <div class="flex space-x-3">
          <PrimaryButton v-if="canGeneratePayslips" @click="toggleGenerateForm" class="bg-green-500 hover:bg-green-700">
            {{ $t('payroll.payslips.generate') }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
      <!-- Generate Payslips Form -->
      <div
        v-if="showGenerateForm"
        class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6"
      >
        <h3 class="text-lg font-medium text-green-800 dark:text-green-200 mb-4">
          {{ $t('payroll.payslips.generate') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <!-- Employee Selection -->
          <div>
            <InputLabel for="user_ids" value="Select Employees" />
            <vSelect
              id="user_ids"
              v-model="generateForm.user_ids"
              :options="users"
              :reduce="(user) => user.id"
              label="name"
              multiple
              placeholder="Select employees"
              class="text-gray-700"
            />
          </div>

          <!-- Pay Period Start -->
          <div>
            <InputLabel for="pay_period_start" value="Pay Period Start" />
            <TextInput
              id="pay_period_start"
              v-model="generateForm.pay_period_start"
              type="date"
              class="w-full"
              required
            />
          </div>

          <!-- Pay Period End -->
          <div>
            <InputLabel for="pay_period_end" value="Pay Period End" />
            <TextInput id="pay_period_end" v-model="generateForm.pay_period_end" type="date" class="w-full" required />
          </div>
        </div>
        <div class="flex space-x-3">
          <PrimaryButton
            @click="generatePayslips"
            :disabled="generateForm.processing"
            class="bg-green-500 hover:bg-green-700"
          >
            {{ $t('payroll.actions.generate') }}
          </PrimaryButton>
          <button
            @click="toggleGenerateForm"
            class="px-4 py-2 bg-gray-500 dark:bg-gray-600 text-white rounded-md hover:bg-gray-600 dark:hover:bg-gray-700"
          >
            {{ $t('words.cancel') }}
          </button>
        </div>
      </div>

      <!-- Payslips DataTable -->
      <DataTable
        :data="payslips"
        :columns="columns"
        :filters="filters"
        :filter-config="filterConfig"
        :bulk-actions="bulkActions"
        :can-bulk-action="canGeneratePayslips"
        route-name="payslips.index"
        :search-placeholder="$t('payroll.filters.search')"
        :empty-state-text="'No payslips found.'"
        @bulk-action="handleBulkAction"
      >
        <!-- Custom Actions Column -->
        <template #cell-actions="{ item }">
          <div class="flex space-x-2">
            <Link
              :href="route('payslips.show', item.id)"
              class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
              {{ $t('words.view') }}
            </Link>
            <Link
              :href="route('payslips.download', item.id)"
              class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
            >
              {{ $t('payroll.payslips.download') }}
            </Link>
            <button
              v-if="canGeneratePayslips && item.status === 'draft'"
              @click.stop="approvePayslip(item.id)"
              class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
            >
              {{ $t('payroll.payslips.approve') }}
            </button>
          </div>
        </template>
      </DataTable>
    </div>
  </div>
</template>
