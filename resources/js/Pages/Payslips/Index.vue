<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import vSelect from 'vue-select';

const props = defineProps({
  payslips: Object,
  filters: Object,
  users: Array,
  canGeneratePayslips: Boolean,
});

const selectedPayslips = ref([]);
const filtersVisible = ref(true);
const showGenerateForm = ref(false);

const form = useForm({
  user_id: props.filters.user_id || '',
  pay_period_start: props.filters.pay_period_start || '',
  pay_period_end: props.filters.pay_period_end || '',
  status: props.filters.status || '',
});

const generateForm = useForm({
  user_ids: [],
  pay_period_start: '',
  pay_period_end: '',
});

const bulkForm = useForm({
  payslip_ids: [],
  action: '',
});

watch(
  () => [form.user_id, form.pay_period_start, form.pay_period_end, form.status],
  () => {
    applyFilters();
  },
  { deep: true }
);

const applyFilters = () => {
  const params = {
    user_id: form.user_id,
    pay_period_start: form.pay_period_start,
    pay_period_end: form.pay_period_end,
    status: form.status,
  };

  form.get(route('payslips.index'), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    data: params,
  });
};

const toggleFilters = () => {
  filtersVisible.value = !filtersVisible.value;
};

const toggleGenerateForm = () => {
  showGenerateForm.value = !showGenerateForm.value;
};

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

const togglePayslipSelection = (payslipId) => {
  const index = selectedPayslips.value.indexOf(payslipId);
  if (index > -1) {
    selectedPayslips.value.splice(index, 1);
  } else {
    selectedPayslips.value.push(payslipId);
  }
};

const toggleSelectAll = () => {
  if (selectedPayslips.value.length === props.payslips.data.length) {
    selectedPayslips.value = [];
  } else {
    selectedPayslips.value = props.payslips.data.map((payslip) => payslip.id);
  }
};

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

const bulkApprove = () => {
  if (selectedPayslips.value.length === 0) {
    alert('Please select payslips to approve');
    return;
  }

  if (confirm(`Are you sure you want to approve ${selectedPayslips.value.length} payslips?`)) {
    bulkForm.payslip_ids = selectedPayslips.value;
    bulkForm.action = 'approve';
    bulkForm.post(route('payslips.bulk-update'), {
      preserveState: true,
      onSuccess: () => {
        selectedPayslips.value = [];
        bulkForm.reset();
      },
    });
  }
};

const approvePayslip = (payslipId) => {
  if (confirm('Are you sure you want to approve this payslip?')) {
    useForm().patch(route('payslips.approve', payslipId), {
      preserveState: true,
    });
  }
};

const pagination = computed(() => {
  return {
    prev_page_url: props.payslips.prev_page_url,
    current_page: props.payslips.current_page,
    last_page: props.payslips.last_page,
    total: props.payslips.total,
    next_page_url: props.payslips.next_page_url,
  };
});
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
          <PrimaryButton
            v-if="canGeneratePayslips && selectedPayslips.length > 0"
            @click="bulkApprove"
            :disabled="bulkForm.processing"
            class="bg-blue-500 hover:bg-blue-700"
          >
            {{ $t('payroll.payslips.bulk_approve') }} ({{ selectedPayslips.length }})
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Generate Payslips Form -->
      <div
        v-if="showGenerateForm"
        class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6"
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

      <!-- Filters Section -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <PrimaryButton @click="toggleFilters">
            {{ filtersVisible ? 'Hide Filters' : 'Show Filters' }}
          </PrimaryButton>
        </div>

        <transition name="slide-down">
          <div v-if="filtersVisible" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- User Filter -->
              <div v-if="canGeneratePayslips">
                <InputLabel for="user_filter" value="Employee" />
                <vSelect
                  id="user_filter"
                  v-model="form.user_id"
                  :options="users"
                  :reduce="(user) => user.id"
                  label="name"
                  placeholder="All Employees"
                  class="text-gray-700"
                />
              </div>

              <!-- Pay Period Start -->
              <div>
                <InputLabel for="pay_period_start_filter" value="Pay Period Start" />
                <TextInput id="pay_period_start_filter" v-model="form.pay_period_start" type="date" class="w-full" />
              </div>

              <!-- Pay Period End -->
              <div>
                <InputLabel for="pay_period_end_filter" value="Pay Period End" />
                <TextInput id="pay_period_end_filter" v-model="form.pay_period_end" type="date" class="w-full" />
              </div>

              <!-- Status Filter -->
              <div>
                <InputLabel for="status_filter" value="Status" />
                <select
                  id="status_filter"
                  v-model="form.status"
                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                >
                  <option value="">All Status</option>
                  <option value="draft">{{ $t('payroll.payslips.draft') }}</option>
                  <option value="approved">{{ $t('payroll.payslips.approved') }}</option>
                  <option value="paid">{{ $t('payroll.payslips.paid') }}</option>
                </select>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Payslips Table -->
      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  v-if="canGeneratePayslips"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  <Checkbox
                    :checked="selectedPayslips.length === payslips.data.length && payslips.data.length > 0"
                    @change="toggleSelectAll"
                  />
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.employee') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.payslips.pay_period') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.payslips.total_hours') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.payslips.gross_pay') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.payslips.total_deductions') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.payslips.net_pay') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.payslips.status') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('words.actions_column') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="payslip in payslips.data" :key="payslip.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td v-if="canGeneratePayslips" class="px-6 py-4 whitespace-nowrap">
                  <Checkbox
                    :checked="selectedPayslips.includes(payslip.id)"
                    @change="togglePayslipSelection(payslip.id)"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                          {{ payslip.user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ payslip.user.name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  <div>{{ formatDate(payslip.pay_period_start) }} -</div>
                  <div>
                    {{ formatDate(payslip.pay_period_end) }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  <div>{{ $t('payroll.payslips.regular') }}: {{ formatHours(payslip.regular_hours) }}h</div>
                  <div v-if="payslip.overtime_hours > 0" class="text-orange-600 dark:text-orange-400">
                    {{ $t('payroll.payslips.overtime') }}: {{ formatHours(payslip.overtime_hours) }}h
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatCurrency(payslip.gross_pay) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatCurrency(payslip.total_deductions) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-semibold">
                  {{ formatCurrency(payslip.net_pay) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="payslip.status === 'approved'"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100"
                  >
                    {{ $t('payroll.payslips.approved') }}
                  </span>
                  <span
                    v-else-if="payslip.status === 'paid'"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100"
                  >
                    {{ $t('payroll.payslips.paid') }}
                  </span>
                  <span
                    v-else
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100"
                  >
                    {{ $t('payroll.payslips.draft') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <Link
                      :href="route('payslips.show', payslip.id)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                      {{ $t('words.view') }}
                    </Link>
                    <Link
                      :href="route('payslips.download', payslip.id)"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    >
                      {{ $t('payroll.payslips.download') }}
                    </Link>
                    <button
                      v-if="canGeneratePayslips && payslip.status === 'draft'"
                      @click="approvePayslip(payslip.id)"
                      class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      {{ $t('payroll.payslips.approve') }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="payslips.data.length === 0">
                <td
                  :colspan="canGeneratePayslips ? 9 : 8"
                  class="px-6 py-4 text-center text-gray-500 dark:text-gray-400"
                >
                  No payslips found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :pagination="pagination" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: max-height 0.3s ease-in-out;
}

.slide-down-enter-from,
.slide-down-leave-to {
  max-height: 0;
  overflow: hidden;
}

.slide-down-enter-to,
.slide-down-leave-from {
  max-height: 50rem;
  overflow: auto;
}
</style>
