<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
  employeeProfiles: Object,
  filters: Object,
  users: Array,
});

const selectedProfiles = ref([]);
const filtersVisible = ref(true);
const showBulkActions = ref(false);

const form = useForm({
  search: props.filters.search || '',
  status: props.filters.status || '',
  payment_method: props.filters.payment_method || '',
});

const bulkForm = useForm({
  profile_ids: [],
  action: '',
  hourly_rate: '',
});

watch(
  () => [form.search, form.status, form.payment_method],
  () => {
    applyFilters();
  },
  { deep: true }
);

const applyFilters = () => {
  const params = {
    search: form.search,
    status: form.status,
    payment_method: form.payment_method,
  };

  form.get(route('employee-profiles.index'), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    data: params,
  });
};

const toggleFilters = () => {
  filtersVisible.value = !filtersVisible.value;
};

const toggleBulkActions = () => {
  showBulkActions.value = !showBulkActions.value;
  if (!showBulkActions.value) {
    selectedProfiles.value = [];
  }
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

const toggleProfileSelection = (profileId) => {
  const index = selectedProfiles.value.indexOf(profileId);
  if (index > -1) {
    selectedProfiles.value.splice(index, 1);
  } else {
    selectedProfiles.value.push(profileId);
  }
};

const toggleSelectAll = () => {
  if (selectedProfiles.value.length === props.employeeProfiles?.data.length) {
    selectedProfiles.value = [];
  } else {
    selectedProfiles.value = props.employeeProfiles?.data?.map((profile) => profile.id);
  }
};

const executeBulkAction = () => {
  if (selectedProfiles.value.length === 0) {
    alert('Please select employee profiles first');
    return;
  }

  if (!bulkForm.action) {
    alert('Please select an action');
    return;
  }

  if (bulkForm.action === 'update_rate' && !bulkForm.hourly_rate) {
    alert('Please enter hourly rate for bulk update');
    return;
  }

  const actionText = {
    activate: 'activate',
    deactivate: 'deactivate',
    update_rate: 'update hourly rate for',
  };

  if (
    confirm(
      `Are you sure you want to ${actionText[bulkForm.action]} ${selectedProfiles.value.length} employee profiles?`
    )
  ) {
    bulkForm.profile_ids = selectedProfiles.value;
    bulkForm.post(route('employee-profiles.bulk-update'), {
      preserveState: true,
      onSuccess: () => {
        selectedProfiles.value = [];
        bulkForm.reset();
      },
    });
  }
};

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

const pagination = computed(() => {
  return {
    prev_page_url: props.employeeProfiles.prev_page_url,
    current_page: props.employeeProfiles.current_page,
    last_page: props.employeeProfiles.last_page,
    total: props.employeeProfiles.total,
    next_page_url: props.employeeProfiles.next_page_url,
  };
});
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
          <PrimaryButton @click="toggleBulkActions" :class="{ 'bg-green-500 hover:bg-green-700': showBulkActions }">
            {{ showBulkActions ? 'Hide Bulk Actions' : 'Show Bulk Actions' }}
          </PrimaryButton>
        </div>
      </div>
    </div>
  </header>

  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Bulk Actions Panel -->
      <div
        v-if="showBulkActions"
        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
              {{ selectedProfiles.length }} profiles selected
            </span>
            <select
              v-model="bulkForm.action"
              class="rounded border-yellow-300 dark:border-yellow-700 dark:bg-yellow-900 text-yellow-900 dark:text-yellow-100 text-sm"
            >
              <option value="">Select Action</option>
              <option value="activate">{{ $t('payroll.employee_profiles.activate') }}</option>
              <option value="deactivate">{{ $t('payroll.employee_profiles.deactivate') }}</option>
              <option value="update_rate">{{ $t('payroll.employee_profiles.bulk_update') }} Rate</option>
            </select>
            <TextInput
              v-if="bulkForm.action === 'update_rate'"
              v-model="bulkForm.hourly_rate"
              type="number"
              step="0.01"
              min="0"
              placeholder="New hourly rate"
              class="text-sm w-32"
            />
            <PrimaryButton
              @click="executeBulkAction"
              :disabled="bulkForm.processing || selectedProfiles.length === 0"
              class="text-sm"
            >
              Execute
            </PrimaryButton>
          </div>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Search -->
              <div>
                <InputLabel for="search" value="Search" />
                <TextInput
                  id="search"
                  v-model="form.search"
                  type="text"
                  placeholder="Search by name or email..."
                  class="w-full"
                />
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
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>

              <!-- Payment Method Filter -->
              <div>
                <InputLabel for="payment_method_filter" value="Payment Method" />
                <select
                  id="payment_method_filter"
                  v-model="form.payment_method"
                  class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                >
                  <option value="">All Methods</option>
                  <option value="bank_transfer">
                    {{ $t('payroll.employee_profiles.payment_methods.bank_transfer') }}
                  </option>
                  <option value="cash">{{ $t('payroll.employee_profiles.payment_methods.cash') }}</option>
                  <option value="check">{{ $t('payroll.employee_profiles.payment_methods.check') }}</option>
                </select>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Employee Profiles Table -->
      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  v-if="showBulkActions"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  <Checkbox
                    :checked="
                      selectedProfiles.length === employeeProfiles?.data?.length && employeeProfiles?.data?.length > 0
                    "
                    @change="toggleSelectAll"
                  />
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.reports.employee') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.employee_profiles.employee_id') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.employee_profiles.base_hourly_rate') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.employee_profiles.payment_method') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('payroll.employee_profiles.hire_date') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('words.status') }}
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  {{ $t('words.actions_column') }}
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="profile in employeeProfiles?.data"
                :key="profile.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td v-if="showBulkActions" class="px-6 py-4 whitespace-nowrap">
                  <Checkbox
                    :checked="selectedProfiles.includes(profile.id)"
                    @change="toggleProfileSelection(profile.id)"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                          {{ profile.user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ profile.user.name }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ profile.user.email }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ profile.employee_id || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatCurrency(profile.base_hourly_rate) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100"
                  >
                    {{ $t(`payroll.employee_profiles.payment_methods.${profile.payment_method}`) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(profile.hire_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="profile.is_active"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100"
                  >
                    {{ $t('words.active') }}
                  </span>
                  <span
                    v-else
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100"
                  >
                    {{ $t('words.inactive') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <Link
                      :href="route('employee-profiles.show', profile.id)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                      {{ $t('words.view') }}
                    </Link>
                    <Link
                      :href="route('employee-profiles.edit', profile.id)"
                      class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                    >
                      {{ $t('words.edit') }}
                    </Link>
                    <button
                      v-if="profile.is_active"
                      @click="deactivateProfile(profile.id)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                    >
                      {{ $t('payroll.employee_profiles.deactivate') }}
                    </button>
                    <button
                      v-else
                      @click="activateProfile(profile.id)"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    >
                      {{ $t('payroll.employee_profiles.activate') }}
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="employeeProfiles?.data?.length === 0">
                <td :colspan="showBulkActions ? 8 : 7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  No employee profiles found.
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
